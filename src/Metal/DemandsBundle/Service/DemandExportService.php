<?php

namespace Metal\DemandsBundle\Service;

use Doctrine\ORM\EntityManager;
use Metal\CompaniesBundle\Entity\Company;
use Liuggio\ExcelBundle\Factory;
use Metal\DemandsBundle\DataFetching\Spec\DemandFilteringSpec;
use Metal\DemandsBundle\DataFetching\Spec\DemandOrderingSpec;
use Metal\DemandsBundle\Entity\Demand;
use Metal\DemandsBundle\Entity\DemandView;
use Metal\DemandsBundle\Entity\ValueObject\DemandPeriodicityProvider;
use Metal\ProductsBundle\Entity\ValueObject\ProductMeasureProvider;
use Metal\ProjectBundle\Doctrine\Utils;
use Metal\ProjectBundle\Entity\ValueObject\SiteSourceTypeProvider;
use Metal\UsersBundle\Entity\User;
use Sonata\IntlBundle\Templating\Helper\NumberHelper;
use Symfony\Component\HttpFoundation\Request;

class DemandExportService
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var Factory
     */
    private $phpexcel;

    /**
     * @var NumberHelper
     */
    private $numberHelper;

    private $uploadDir;

    /**
     * @var User
     */
    private $currentUser;

    /*
    * Key - demand.id
    */
    private $viewedDemands;

    private $favoriteComments;

    private $tokensSuppliers;

    public function __construct(EntityManager $em, Factory $phpexcel, $uploadDir, NumberHelper $numberHelper, $tokensSuppliers)
    {
        $this->em = $em;
        $this->uploadDir = $uploadDir;
        $this->phpexcel = $phpexcel;
        $this->numberHelper = $numberHelper;
        $this->tokensSuppliers = ucfirst($tokensSuppliers);
    }

    /**
     * @param $demandsIds
     * @param Company $company
     * @param $exportFormat
     * @param $from
     * @param User $currentUser
     * @param DemandFilteringSpec $criteria
     * @param DemandOrderingSpec $orderBy
     * @param Request $request
     *
     * @return string
     */
    public function getExportFileName(
        array $demandsIds,
        Company $company,
        $exportFormat,
        $from,
        User $currentUser,
        DemandFilteringSpec $criteria,
        DemandOrderingSpec $orderBy,
        Request $request = null
    )
    {
        $this->currentUser = $currentUser;

        $unsortedDemands = $this->em->createQueryBuilder()
            ->select('d.id')
            ->addSelect('d.sourceTypeId')
            ->addSelect('d.phone')
            ->addSelect('d.email')
            ->addSelect('d.demandPeriodicityId')
            ->addSelect('d.person')
            ->addSelect('IDENTITY(d.user) as userId')
            ->addSelect('d.createdAt')
            ->addSelect('d.companyTitle')
            ->addSelect(' c.title as cityTitle')
            ->from('MetalDemandsBundle:AbstractDemand', 'd', 'd.id')
            ->leftJoin('d.city', 'c')
            ->where('d.id IN (:demands_ids)')
            ->setParameter('demands_ids', $demandsIds)
            ->getQuery()
            ->getResult();

        // preserve same order that was on input
        $demands = array();
        foreach ($demandsIds as $demandId) {
            if (!empty($unsortedDemands[$demandId])) {
                $demands[] = $unsortedDemands[$demandId];
            }
        }
        unset($unsortedDemands);

        Utils::checkEmConnection($this->em);

        $this->setViewedDemandsIds($company);

        $demandItemRepository = $this->em->getRepository('MetalDemandsBundle:DemandItem');
        $demandItemsResults = $demandItemRepository->createQueryBuilder('di')
            ->select('di.title')
            ->addSelect('IDENTITY(di.demand) as demandId')
            ->addSelect('di.volumeTypeId')
            ->addSelect('IDENTITY(di.category) as categoryId')
            ->addSelect('di.volume')
            ->addSelect('di.size')
            ->groupBy('di.id')
            ->addGroupBy('di.demand')
            ->where('di.demand IN (:demands_ids)')
            ->setParameter('demands_ids', $demandsIds)
            ->getQuery()
            ->getResult();

        $demandItems = array();
        $categoriesIds = array();
        foreach ($demandItemsResults as $demandItemsResult) {
            $demandItems[$demandItemsResult['demandId']][] = $demandItemsResult;
            if (!empty($demandItemsResult['categoryId'])) {
                $categoriesIds[$demandItemsResult['categoryId']] = true;
            }
        }

        $usersIds = array();
        foreach ($demands as $demand) {
            $usersIds[$demand['userId']] = true;
        }

        Utils::checkEmConnection($this->em);

        $users = $this->em
            ->createQueryBuilder()
            ->from('MetalUsersBundle:User', 'u', 'u.id')
            ->select('u.id')
            ->addSelect('createdBy.email AS companyEmail')
            ->addSelect('u.email AS userEmail')
            ->addSelect('u.firstName')
            ->addSelect('u.secondName')
            ->addSelect('company.title AS companyTitle')
            ->leftJoin('u.company', 'company')
            ->leftJoin('company.companyLog', 'companyLog')
            ->leftJoin('companyLog.createdBy', 'createdBy')
            ->andWhere('u.isEnabled = true')
            ->where('u.id IN (:users_ids)')
            ->setParameter('users_ids', array_keys($usersIds))
            ->getQuery()
            ->getResult();
        unset($usersIds);

        $categories = $this->em
            ->createQueryBuilder()
            ->from('MetalCategoriesBundle:Category', 'c', 'c.id')
            ->select('c.title')
            ->addSelect('c.id')
            ->where('c.id IN (:categories_ids)')
            ->setParameter('categories_ids', array_keys($categoriesIds))
            ->getQuery()
            ->getResult();
        unset($categoriesIds);

        $data = array(
            'demands' => $demands,
            'demand_items' => $demandItems,
            'categories' => $categories,
            'users' => $users,
            'company' => $company
        );

        if ('favorite_demands' === $from) {
            $this->initializeFavoriteComments($demandsIds);
        }

        $ip = $request ? $request->getClientIp() : null;

        if ($exportFormat === 'xlsx') {
            $result = $this->recordInXlsx($data, $from, $criteria, $orderBy);
            Utils::checkEmConnection($this->em);

            $this->track($demandsIds, $currentUser, $ip);

            return $result;
        }

        $result = $this->recordInCsv($data, $from);

        Utils::checkEmConnection($this->em);

        $this->track($demandsIds, $currentUser, $ip);

        return $result;
    }

    private function track(array $demandsIds, User $user, $ip)
    {
        $demandViewRepository = $this->em->getRepository('MetalDemandsBundle:DemandView');

        $this->em->beginTransaction();
        foreach ($demandsIds as $demandId) {
            $demandViewRepository->addView($demandId, $user, $ip, true);
        }
        $this->em->commit();
    }

    /**
     * @param array $data
     * @param $from
     * @param DemandFilteringSpec $criteria
     * @param DemandOrderingSpec $orderBy
     *
     * @return string
     */
    public function recordInXlsx(array $data, $from, DemandFilteringSpec $criteria, DemandOrderingSpec $orderBy)
    {
        $demands = $data['demands'];
        $company = $data['company'];
        /* @var $company Company */
        $categories = $data['categories'];
        $demandItems = $data['demand_items'];
        $users = $data['users'];
        $companiesTitle = $this->getCompaniesTitle($demands, $users);

        $fileName = $company->getId().'-'.date('Y-m-d H-i-s').'.xlsx';

        $title = $this->getActiveFiltersTitle($criteria, $orderBy, $from);

        $objPHPExcel = $this->phpexcel->createPHPExcelObject(
            $this->uploadDir.'/import-export-blanks/demands.xlsx'
        );

        $rowNum = 3;
        $activeSheet = $objPHPExcel->setActiveSheetIndex(0);
        $activeSheet->setCellValue('B1', trim(mb_strtoupper($title)));

        $demandPeriodicityProviders= DemandPeriodicityProvider::getAllTypesAsSimpleArray();
        $volumeTypes = ProductMeasureProvider::getAllTypesAsSimpleArray();
        foreach ($demands as $demand) {
            $demandPeriodicityTitle = $demandPeriodicityProviders[$demand['demandPeriodicityId']];

            $user = array();
            if ($demand['userId'] && isset($users[$demand['userId']])) {
                $user = $users[$demand['userId']];
            }

            $demand['demandItems'] = $this->getDemandItemsData($demandItems, $demand, $categories, $volumeTypes);

            $contacts = $this->getContacts($demand, $user);

            //Для избранных дополнительно вывожу комментарий
            if (null !== $this->favoriteComments && !empty($this->favoriteComments[$demand['id']])) {
                $activeSheet->setCellValueExplicit('N'.$rowNum, $this->favoriteComments[$demand['id']], \PHPExcel_Cell_DataType::TYPE_STRING);
            }

            foreach ($demand['demandItems'] as $demandItem) {
                $activeSheet
                    ->setCellValue('A'.$rowNum, $demand['id'])
                    ->setCellValue('B'.$rowNum, $demand['createdAt']->format('d.m.Y'))
                    ->setCellValue('C'.$rowNum, $demandItem['category'])
                    ->setCellValue('D'.$rowNum, $demandItem['title'])

                    ->setCellValue('E'.$rowNum, $demandItem['size'])
                    ->setCellValueExplicit(
                        'F'.$rowNum,
                        $this->numberHelper->formatDecimal($demandItem['volume']) ?: 'договорной',
                        \PHPExcel_Cell_DataType::TYPE_STRING
                    )
                    ->setCellValue('G'.$rowNum, $demandItem['volumeTypeTitle'])
                    ->setCellValue('H'.$rowNum, $contacts['user_title'])
                    ->setCellValue('I'.$rowNum, $companiesTitle[$demand['id']])
                    ->setCellValueExplicit('J'.$rowNum, $contacts['phone'], \PHPExcel_Cell_DataType::TYPE_STRING)
                    ->setCellValue('K'.$rowNum, $contacts['email'])
                    ->setCellValue('L'.$rowNum, $demand['cityTitle'])
                    ->setCellValue('M'.$rowNum, $demandPeriodicityTitle.' потребность');


                $rowNum++;
            }
        }

        $objWriter = $this->phpexcel->createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save($this->uploadDir.'/demands-export/'.$fileName);

        return $fileName;
    }

    public function initializeFavoriteComments(array $demandIds = array())
    {
        $favoriteComments = $this->em->createQueryBuilder()
            ->select('IDENTITY(favorite.demand) AS demand_id')
            ->addSelect('favorite.comment')
            ->from('MetalUsersBundle:Favorite', 'favorite')
            ->where('favorite.demand IN (:demandsIds)')
            ->setParameter('demandsIds', $demandIds)
            ->getQuery()
            ->getArrayResult()
        ;

        foreach ($favoriteComments as $favoriteComment) {
            $this->favoriteComments[$favoriteComment['demand_id']] = $favoriteComment['comment'];
        }
    }

    /**
     * @param array $data
     *
     * @return string
     */
    public function recordInCsv(array $data, $from)
    {
        $demands = $data['demands'];
        $company = $data['company'];
        /* @var $company Company */
        $categories     = $data['categories'];
        $demandItems    = $data['demand_items'];
        $users          = $data['users'];
        $companiesTitle = $this->getCompaniesTitle($demands, $users);
        $delimiter      = ';';

        $csvHeaders = array('Номер', 'Дата', 'Категория', 'Наименование', 'Объем', 'Размер', 'Ед. Изм.', 'Имя', 'Компания', 'Телефон', 'Email', 'Город', 'Тип');

        if ('favorite_demands' === $from) {
            $csvHeaders[] = 'Комментарий';
        }

        $fileName = $company->getId().'-'.date('Y-m-d H-i-s').'.csv';
        $fp = fopen($this->uploadDir.'/demands-export/'.$fileName, 'w');
        fputs($fp, chr(0xEF).chr(0xBB).chr(0xBF));
        fputcsv($fp, $csvHeaders, $delimiter);

        $demandPeriodicityProviders= DemandPeriodicityProvider::getAllTypesAsSimpleArray();
        $volumeTypes = ProductMeasureProvider::getAllTypesAsSimpleArray();
        $i = 0;
        $j = null;
        foreach ($demands as $demand) {
            $demandPeriodicityTitle = $demandPeriodicityProviders[$demand['demandPeriodicityId']];

            $user = array();
            if ($demand['userId'] && !empty($users[$demand['userId']])) {
                $user = $users[$demand['userId']];
            }

            $demand['demandItems'] = $this->getDemandItemsData($demandItems, $demand, $categories, $volumeTypes);

            $contacts = $this->getContacts($demand, $user);
            foreach ($demand['demandItems'] as $demandItem) {
                $content = array(
                    $demand['id'],
                    $demand['createdAt']->format('d.m.Y'),
                    $demandItem['category'],
                    $demandItem['title'],
                    $this->numberHelper->formatDecimal($demandItem['volume']) ?: 'договорной',
                    $demandItem['size'],
                    $demandItem['volumeTypeTitle'],
                    $contacts['user_title'],
                    $companiesTitle[$demand['id']],
                    $contacts['phone'],
                    $contacts['email'],
                    $demand['cityTitle'],
                    $demandPeriodicityTitle.' потребность')
                ;

                //Для избранных дополнительно вывожу комментарий
                if ($i !== $j && null !== $this->favoriteComments && !empty($this->favoriteComments[$demand['id']])) {
                    $content[] = $this->favoriteComments[$demand['id']];
                    $j = $i;
                }

                fputcsv($fp, $content, $delimiter);
            }
            $i++;
        }

        fclose($fp);

        return $fileName;
    }

    /**
     * @param array $demandItems
     * @param array $demand
     * @param array $categories
     * @param array $volumeTypes
     *
     * @return array
     */
    private function getDemandItemsData(array $demandItems, array $demand, array $categories, array $volumeTypes)
    {
        $data = array();
        if (empty($demandItems[$demand['id']])) {
            return $data;
        }

        foreach ($demandItems[$demand['id']] as $demandItem) {
            $categoryTitle = null;
            if ($demandItem['categoryId'] && $categories[$demandItem['categoryId']]) {
                $categoryTitle = $categories[$demandItem['categoryId']]['title'];
            }

            $volumeTypeTitle = '—';
            if ($demandItem['volumeTypeId']) {
                $volumeTypeTitle = $volumeTypes[$demandItem['volumeTypeId']];
            }

            $data[] = array(
                'category' => $categoryTitle,
                'title' => $demandItem['title'],
                'volume' => $demandItem['volume'],
                'size' => $demandItem['size'],
                'volumeTypeTitle' => $volumeTypeTitle
            );
        }

        return $data;
    }

    /**
     * @param array $demands
     * @param array $users
     *
     * @return array, key - demand.id, value - string
     */
    private function getCompaniesTitle(array $demands, array $users)
    {
        $companiesTitle = array();
        foreach ($demands AS $demand) {
            $user = array();
            $companiesTitle[$demand['id']] = '';
            if ($demand['userId'] && !empty($users[$demand['userId']])) {
                $user = $users[$demand['userId']];
            }

            if (!empty($user['companyTitle']) && $demand['sourceTypeId'] != SiteSourceTypeProvider::SOURCE_ADMIN) {
                $companiesTitle[$demand['id']] = $user['companyTitle'];
            } elseif ($demand['companyTitle']) {
                $companiesTitle[$demand['id']] = $demand['companyTitle'];
            }
        }

        return $companiesTitle;
    }

    /**
     * @param array $demand
     * @param array $user
     *
     * @return array
     */
    private function getContacts(array $demand, array $user)
    {
        if (null === $this->viewedDemands) {
            return $this->prepareContacts($demand, $user);
        }

        if (isset($this->viewedDemands[$demand['id']])) {
            return $this->prepareContacts($demand, $user);
        }

        if (count($this->viewedDemands) >= DemandView::MAX_CONTACTS_VIEW_COUNT_FOR_PROMOCODE) {
            return array(
                'phone' => 'Лимит исчерпан',
                'user_title' => 'Лимит исчерпан',
                'email' => 'Лимит исчерпан',
            );
        }

        $this->viewedDemands[$demand['id']] = true;

        return $this->prepareContacts($demand, $user);
    }

    /**
     * @param array $demand
     * @param array $user
     *
     * @return array
     */
    private function prepareContacts(array $demand, array $user)
    {
        return array(
            'phone' => $demand['phone'],
            'user_title' => Demand::getFixedUserTitleForExport($demand['person'], $demand['sourceTypeId'], $user),
            'email' => Demand::getFixedEmailForExport($user, $demand['email'], $demand['sourceTypeId'])
        );
    }

    /**
     * @param Company $company
     */
    private function setViewedDemandsIds(Company $company)
    {
        if (null === $this->viewedDemands && $company->getPromocode()) {
            $demandViewIds = $this->em->createQueryBuilder()
                ->from('MetalDemandsBundle:DemandView', 'demandView')
                ->select('IDENTITY(demandView.demand) AS demand_id')
                ->where('demandView.company = :company')
                ->andWhere('demandView.viewedAt >= :activatedAt')
                ->setParameter('company', $company)
                ->setParameter('activatedAt', $company->getPromocode()->getActivatedAt())
                ->getQuery()
                ->getArrayResult();

            $this->viewedDemands = array_flip(array_column($demandViewIds, 'demand_id'));
        }
    }

    /**
     * @param DemandFilteringSpec $criteria
     * @param DemandOrderingSpec $orderBy
     * @param $from
     * @return string
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    private function getActiveFiltersTitle(DemandFilteringSpec $criteria, DemandOrderingSpec $orderBy, $from)
    {
        $activeFilters = array();
        if (null !== $criteria->cityId) {
            $cityTitle = $this->em->createQueryBuilder()
                ->select('c.title')
                ->from('MetalTerritorialBundle:City', 'c')
                ->where('c.id = :city_id')
                ->setParameter('city_id', $criteria->cityId)
                ->getQuery()
                ->getSingleResult();
            $activeFilters[] = $cityTitle['title'];
        }

        if (null !== $criteria->categoryId) {
            $categoryTitle = $this->em->createQueryBuilder()
                ->select('c.title')
                ->from('MetalCategoriesBundle:Category', 'c')
                ->where('c.id = :category_id')
                ->setParameter('category_id', $criteria->categoryId)
                ->getQuery()
                ->getSingleResult();
            $activeFilters[] = 'Категория - '.$categoryTitle['title'];
        }

        if (null !== $criteria->isRepetitive) {
            $activeFilters[] = $criteria->isRepetitive ? 'Постоянные заявки' : 'Разовые заявки';
        }

        if (null !== $criteria->isWholesale) {
            $activeFilters[] = $criteria->isWholesale ? 'Опт' : 'Розница';
        }

        if (null !== $criteria->authorType) {
            $activeFilters[] = $criteria->authorType == 1 ? $this->tokensSuppliers : 'Конечные потребители';
        }

        $orders = $orderBy->getOrders();
        if (isset($orders['createdAt'])) {
            $activeFilters[] = 'Сортировка - По дате';
        } else {
            if (isset($orders['demandViewsCount'])) {
                $activeFilters[] = 'Сортировка - По популярности';
            }
        }

        if (isset($orders['favoriteComment'])) {
            if ($orders['favoriteComment'] === 'with') {
                $activeFilters[] = 'С пометками';
            } elseif ($orders['favoriteComment'] === 'without') {
                $activeFilters[] = 'Без пометок';
            }
        }

        if (isset($orders['favoriteOrder'])) {
            if ($orders['favoriteOrder'] === 'viewsCount') {
                $activeFilters[] = 'Сортировка - По популярности';
            } elseif ($orders['favoriteOrder'] === 'answersCount') {
                $activeFilters[] = 'Сортировка - По ответам';
            } else {
                $activeFilters[] = 'Сортировка - По дате';
            }
        }

        $fromTitle = array(
            'demands' => 'заявки',
            'subscription_demands' => 'подписки на потребности',
            'favorite_demands' => 'избранные заявки'
        );

        $title = $fromTitle[$from];
        if ($activeFilters) {
            $title = sprintf(
                "%s\nАктивные фильтры:\n%s",
                $fromTitle[$from],
                implode("\r\n", $activeFilters)
            );
        }

        return $title;
    }
}
