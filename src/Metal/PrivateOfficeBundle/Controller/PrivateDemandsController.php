<?php

namespace Metal\PrivateOfficeBundle\Controller;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Metal\DemandsBundle\DataFetching\Spec\DemandFilteringSpec;
use Metal\DemandsBundle\DataFetching\Spec\DemandOrderingSpec;
use Metal\DemandsBundle\Entity\AbstractDemand;
use Metal\DemandsBundle\Helper\DefaultHelper;
use Metal\UsersBundle\Entity\User;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class PrivateDemandsController extends Controller
{
    protected $perPage = 20;

    /**
     * @Security("has_role('ROLE_SUPPLIER')")
     */
    public function listAction(Request $request)
    {
        $user = $this->getUser();
        /* @var $user User */
        $company = $user->getCompany();

        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */

        $privateDemandsRepository = $em->getRepository('MetalDemandsBundle:PrivateDemand');
        $companyCityRepository = $em->getRepository('MetalCompaniesBundle:CompanyCity');

        $privateDemandsQb = $this->prepareCriteria($request);
        $privateDemandsPeriodQb = $this->prepareCriteria($request, null, false);

        $citiesCriteria = array();
        if ($user->requireApplyTerritorialRules()) {
            $citiesCriteria = $companyCityRepository->getCitiesCriteriaForUser($user);
            $companyCityRepository->applyFilterByTerritory('pd', $privateDemandsQb, $citiesCriteria);
            $companyCityRepository->applyFilterByTerritory('pd', $privateDemandsPeriodQb, $citiesCriteria);
        }

        $category = null;
        if ($request->query->get('filterByCategory') && $request->query->get('filterByCategory') !== 'other') {
            $category = $em->getRepository('MetalCategoriesBundle:Category')->find(
                $request->query->get('filterByCategory')
            );
        }

        $viewedFlag = null;
        if ($request->query->get('filter') === 'watched') {
            $viewedFlag = true;
        } elseif ($request->query->get('filter') === null) {
            $viewedFlag = false;
        }

        $pagerfanta = (new Pagerfanta(new DoctrineORMAdapter($privateDemandsQb, false)))
            ->setMaxPerPage($this->perPage)
            ->setCurrentPage($request->query->get('page', 1));

        $privateDemands = iterator_to_array($pagerfanta->getIterator());
        $privateDemandsRepository->attachProductsToPrivateDemands($privateDemands);

        $demandItemRepository = $em->getRepository('MetalDemandsBundle:DemandItem');
        $demandItemRepository->attachDemandItems($privateDemands);

        $demandFileRepository = $em->getRepository('MetalDemandsBundle:DemandFile');
        $demandFileRepository->attachDemandFiles($privateDemands);

        $demandAnswerRepository = $em->getRepository('MetalDemandsBundle:DemandAnswer');
        $demandAnswerRepository->attachDemandAnswers($privateDemands);

        $em->getRepository('MetalDemandsBundle:Demand')->attachCitiesToDemands($privateDemands, true);

        if ($request->isXmlHttpRequest()) {
            return $this->render(
                '@MetalPrivateOffice/partials/private_demands_list.html.twig',
                array(
                    'company' => $company,
                    'pagerfanta' => $pagerfanta,
                )
            );
        }

        $demandsCountByPeriod = $privateDemandsPeriodQb
            ->select('COUNT(pd.createdAt) AS demands_count_total')
            ->addSelect('SUM(IFNULL(pd.createdAt > :year_ago, 0)) AS demands_count_year')
            ->setParameter('year_ago', new \DateTime('-1 year'))
            ->addSelect('SUM(IFNULL(pd.createdAt > :month_ago, 0)) AS demands_count_month')
            ->setParameter('month_ago', new \DateTime('-1 month'))
            ->addSelect('SUM(IFNULL(pd.createdAt > :week_ago, 0)) AS demands_count_week')
            ->setParameter('week_ago', new \DateTime('-1 week'))
            ->addSelect('SUM(IFNULL(pd.createdAt > :day_ago, 0)) AS demands_count_day')
            ->setParameter('day_ago', new \DateTime('-1 day'))
            ->getQuery()
            ->getSingleResult();

        $demandsCountByPeriod['demands_count_custom'] = $pagerfanta->getNbResults();

        $categories = $privateDemandsRepository->getCategoriesForDemands($company, $viewedFlag, $citiesCriteria);
        $cities = $privateDemandsRepository->getCitiesForDemands($company, $viewedFlag, $category, $citiesCriteria);

        list($dateFrom, $dateTo) = DefaultHelper::determinatePeriod($request);

        return $this->render(
            '@MetalPrivateOffice/PrivateDemand/list.html.twig',
            array(
                'company' => $company,
                'pagerfanta' => $pagerfanta,
                'cities' => $cities,
                'categories' => $categories,
                'dateFrom' => $dateFrom,
                'dateTo' => $dateTo,
                'demandsCountByPeriod' => $demandsCountByPeriod,
            )
        );
    }

    /**
     * @param Request $request
     * @param null $indexBy
     *
     * @return QueryBuilder
     */
    private function prepareCriteria(Request $request, $indexBy = null, $dateFilter = true)
    {
        $user = $this->getUser();
        /* @var $user User */
        $company = $user->getCompany();

        $qb = $this->getDoctrine()
            ->getManager()
            ->createQueryBuilder()
            ->select('pd')
            ->from('MetalDemandsBundle:PrivateDemand', 'pd', $indexBy)
            ->andWhere('pd.company = :company')
            ->andWhere('pd.deletedAt IS NULL')
            ->orderBy('pd.createdAt', 'DESC')
            ->setParameter('company', $company);
        /* @var $qb QueryBuilder */

        $queryBag = $request->query;
        $filter = $queryBag->get('filter');
        if ($filter === 'watched') {
            $qb->andWhere('pd.viewedAt IS NOT NULL');
        } elseif ($filter === null) {
            $qb->andWhere('pd.viewedAt IS NULL');
        }

        if ($filterByCity = $queryBag->get('filterByCity')) {
            $qb->andWhere('pd.city = :city_id')
                ->setParameter('city_id', $filterByCity);
        }

        if ($dateFilter) {
            list($dateFrom, $dateTo) = DefaultHelper::determinatePeriod($request);

            if ($dateTo) {
                $qb->andWhere('DATE(pd.createdAt) <= :date_to')
                    ->setParameter('date_to', $dateTo, 'date');
            }

            if ($dateFrom) {
                $qb->andWhere('DATE(pd.createdAt) >= :date_from')
                    ->setParameter('date_from', $dateFrom, 'date');
            }
        }

        // filter by category
        if ($filterByCategory = $queryBag->get('filterByCategory')) {
            if ($filterByCategory === 'other') {
                $qb
                    ->andWhere('pd.category IS NULL');
            } else {
                $qb
                    ->andWhere('pd.category = :category_id')
                    ->setParameter('category_id', $filterByCategory);
            }
        }

        return $qb;
    }

    /**
     * @Security("has_role('ROLE_SUPPLIER') and has_role('ROLE_CONFIRMED_EMAIL')")
     */
    public function exportAction(Request $request, $format)
    {
        set_time_limit(600);
        $user = $this->getUser();
        /* @var $user User */

        $demandExportService = $this->get('metal.demands.demand_export_service');

        $qb = $this->prepareCriteria($request, 'pd.id');

        $demandsIds = array_keys(
            $qb->select('pd.id')->setMaxResults(AbstractDemand::EXPORT_LIMIT)->getQuery()->getArrayResult()
        );

        $fileName = $demandExportService->getExportFileName(
            $demandsIds,
            $user->getCompany(),
            $format,
            'subscription_demands',
            $user,
            new DemandFilteringSpec(),
            new DemandOrderingSpec(),
            $request
        );

        $dir = $this->container->getParameter('upload_dir');

        $response = new BinaryFileResponse($dir.'/demands-export/'.$fileName);
        $response->headers->set('Content-Type', 'application/vnd.ms-excel');
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            sprintf('Потребители %s.%s', $this->container->getParameter('project.title'), $format),
            $fileName
        );

        return $response;
    }
}
