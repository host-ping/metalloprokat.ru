<?php

namespace Metal\CompaniesBundle\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityRepository;
use Metal\CallbacksBundle\Entity\Callback;
use Metal\CompaniesBundle\Entity\Company;
use Metal\ProductsBundle\ChangeSet\ProductsBatchEditStructure;
use Metal\ProductsBundle\Entity\Product;
use Metal\ProjectBundle\Util\InsertUtil;
use Metal\UsersBundle\Entity\User;

class CompanyCounterRepository extends EntityRepository
{
    protected $cachedDemandsCount = array();

    protected $cachedCallbacksCount = array();

    public function synchronizeCompanyCounters()
    {
        $this->_em->getConnection()->executeQuery('
           INSERT INTO company_counter (id, company_id, company_updated_at, products_updated_at, minisite_colors_updated_at)
              SELECT
                c.Message_ID,
                c.Message_ID,
                now(),
                now(),
                now()
              FROM Message75 AS c
                LEFT JOIN company_counter AS cc ON cc.id = c.Message_ID
              WHERE cc.id IS NULL;'
        );

        $this->_em->getConnection()->executeQuery('UPDATE Message75 SET counter_id = Message_ID');
    }

    public function updateReviewsCount(array $companiesIds = array())
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('cr, COUNT(cr) AS r_count')
            ->from('MetalCompaniesBundle:CompanyReview', 'cr')
            ->andWhere('cr.deletedBy IS NULL')
            ->andWhere('cr.moderatedBy IS NOT NULL')
            ->join('cr.company', 'c')
            ->addSelect('c')
            ->groupBy('cr.company');

        if ($companiesIds) {
            $qb->andWhere('c IN (:companies)')
                ->setParameter('companies', $companiesIds);
        }

        $companiesCounts = $qb->getQuery()->getResult();

        $qb = $this->createQueryBuilder('cr');
        $qb->update('MetalCompaniesBundle:CompanyCounter', 'cr')
            ->set('cr.reviewsCount', 0);

        if ($companiesIds) {
            $qb->andWhere('cr.company IN (:companies)')
                ->setParameter('companies', $companiesIds);
        }

        $qb->getQuery()
            ->execute();

        foreach ($companiesCounts as $companyCount) {
            $this->createQueryBuilder('cr')
                ->update('MetalCompaniesBundle:CompanyCounter', 'cr')
                ->set('cr.reviewsCount', $companyCount['r_count'])
                ->where('cr.company = :company')
                ->setParameter('company', $companyCount[0]->getCompany())
                ->getQuery()
                ->execute();
        }
    }

    public function generalUpdateProductsCount(array $companiesIds = array())
    {
        $conn = $this->_em->getConnection();

        //!NB ТУТ МЫ ОБНОВЛЯЕМ КОЛИЧЕСТВО ВСЕХ ПРОДУКТОВ КРОМЕ УДАЛЕННЫХ и ПРЕВЫШАЮЩИХ ЛИМИТ.
        if (!$companiesIds) {
            $qb = $conn->createQueryBuilder()
                ->from('Message75', 'company')
                ->select('company.Message_ID AS id')
                ->where('company.deleted_at_ts = 0')
                ->orderBy('company.code_access', 'DESC')//Сначала крутым фирмам
            ;

            $companiesIds = array_column($qb->execute()->fetchAll(), 'id');
        }

        $i = 0;
        while ($companiesIds) {
            /*
            * Если компании не переданы то мы выгружаем все компании с сортировкай code_access DESC
            * Первые 10 итераций (50 платников) обработаем по пачкам в 5 шт.
            * Следующие 10 итераций по 10 шт. в пачке
            * Для бесплатников лимит 1000
            * */
            $limit = 1000;
            if ($i <= 10) {
                $limit = 5; //50
            } elseif ($i <= 20) {
                $limit = 10; //200
            }

            $processCompaniesIds = array_splice($companiesIds, 0, $limit);

            $companyAllProductsCount = array();
            foreach ($processCompaniesIds as $companyId) {
                $companyAllProductsCount[$companyId] = array('company_id' => $companyId, 'all_products_count' => 0);
            }

            // выбираем главные офисы
            $sQb = $conn->createQueryBuilder()
                ->select('cdc.id AS id')
                ->from('company_delivery_city', 'cdc')
                ->join('cdc', 'Classificator_Region', 'city', 'cdc.city_id = city.Region_ID')
                ->where('cdc.is_main_office = 1')
                ->andWhere('cdc.company_id IN (:companies_ids)')
                ->setParameter('companies_ids', $processCompaniesIds, Connection::PARAM_INT_ARRAY);

            $branchOfficesIds = array_column($sQb->execute()->fetchAll(), 'id');

            $companiesAllProductsCountResult = $conn->createQueryBuilder()
                ->select('p.Company_ID as company_id')
                ->addSelect('COUNT(p.Message_ID) AS all_products_count')
                ->from('Message142', 'p')
                ->andWhere('p.branch_office_id IN (:branch_offices_ids)')
                ->setParameter('branch_offices_ids', $branchOfficesIds, Connection::PARAM_INT_ARRAY)
                ->andWhere('p.Checked <> :checked')
                ->andWhere('p.Checked <> :exceeding')
                ->setParameter('checked', Product::STATUS_DELETED)
                ->setParameter('exceeding', Product::STATUS_LIMIT_EXCEEDING)
                ->andWhere('p.is_virtual = :is_virtual')
                ->setParameter('is_virtual', false)
                ->groupBy('p.Company_ID')
                ->execute()
                ->fetchAll()
            ;

            foreach ($companiesAllProductsCountResult as $companyAllProductsCountResult) {
                $companyAllProductsCount[$companyAllProductsCountResult['company_id']] = $companyAllProductsCountResult;
            }

            if ($companyAllProductsCount) {
                InsertUtil::insertMultipleOrUpdate($conn, 'company_counter', $companyAllProductsCount, array('all_products_count'), $limit);
            }

            $i++;
        }
    }

    public function updateCompaniesComplaintCount(array $companies)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('cm, COUNT(cm) AS c_count')
            ->from('MetalComplaintsBundle:AbstractComplaint', 'cm')
            ->join('cm.company', 'c')
            ->where('cm.viewedAt IS NULL')
            ->addSelect('c')
            ->groupBy('cm.company');

        if ($companies) {
            $qb->andWhere('c IN (:companies)')
                ->setParameter('companies', $companies);
        }

        $companiesCounts = $qb->getQuery()->getResult();

        $qb = $this->createQueryBuilder('cr');
        $qb->update('MetalCompaniesBundle:CompanyCounter', 'cr')
            ->set('cr.newComplaintsCount', 0);

        if ($companies) {
            $qb->andWhere('cr.company IN (:companies)')
                ->setParameter('companies', $companies);
        }

        $qb->getQuery()
            ->execute();

        foreach ($companiesCounts as $companyCount) {
            $this->createQueryBuilder('cr')
                ->update('MetalCompaniesBundle:CompanyCounter', 'cr')
                ->set('cr.newComplaintsCount', $companyCount['c_count'])
                ->where('cr.company = :company')
                ->setParameter('company', $companyCount[0]->getCompany())
                ->getQuery()
                ->execute()
            ;
        }
    }

    public function updateCompaniesNewReviewCount(array $companies)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('cr, COUNT(cr) AS r_count')
            ->from('MetalCompaniesBundle:CompanyReview', 'cr')
            ->join('cr.company', 'c')
            ->where('cr.viewedBy IS NULL')
            ->addSelect('c')
            ->groupBy('cr.company');

        if ($companies) {
            $qb->andWhere('c IN (:companies)')
                ->setParameter('companies', $companies);
        }

        $companiesCounts = $qb->getQuery()->getResult();

        $qb = $this->createQueryBuilder('cr');
        $qb->update('MetalCompaniesBundle:CompanyCounter', 'cr')
            ->set('cr.newCompanyReviewsCount', 0);

        if ($companies) {
            $qb->andWhere('cr.company IN (:companies)')
                ->setParameter('companies', $companies);
        }

        $qb->getQuery()
            ->execute();

        foreach ($companiesCounts as $companyCount) {
            $qb = $this->createQueryBuilder('cr');
            $qb->update('MetalCompaniesBundle:CompanyCounter', 'cr');
            $qb->set('cr.newCompanyReviewsCount', $companyCount['r_count']);
            $qb->where('cr.company = :company')
                ->setParameter('company', $companyCount[0]->getCompany());
            $qb->getQuery()->execute();
        }
    }

    public function updateCompaniesNewCallbacksCount(array $companies)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('cr, COUNT(cr) AS r_count')
            ->from('MetalCallbacksBundle:Callback', 'cr')
            ->join('cr.company', 'c')
            ->where('cr.processedAt IS NULL')
            ->andWhere('cr.kind = :to_company')
            ->setParameter('to_company', Callback::CALLBACK_TO_SUPPLIER)
            ->addSelect('c')
            ->groupBy('cr.company');
        if ($companies) {
            $qb->andWhere('c IN (:companies)')
                ->setParameter('companies', $companies);
        }

        $companiesCounts = $qb->getQuery()->getResult();

        $qb = $this->createQueryBuilder('cr');
        $qb->update('MetalCompaniesBundle:CompanyCounter', 'cr')
            ->set('cr.newCallbacksCount', 0);
        if ($companies) {
            $qb->andWhere('cr.company IN (:companies)')
                ->setParameter('companies', $companies);
        }

        $qb->getQuery()
            ->execute();

        foreach ($companiesCounts as $companyCount) {
            $qb = $this->createQueryBuilder('cr');
            $qb->update('MetalCompaniesBundle:CompanyCounter', 'cr');
            $qb->set('cr.newCallbacksCount', $companyCount['r_count']);
            $qb->where('cr.company = :company')
                ->setParameter('company', $companyCount[0]->getCompany());
            $qb->getQuery()->execute();
        }
    }


    public function attachCompanyCounter($companies)
    {
        if (!count($companies)) {
            return;
        }

        $this->_em->getRepository('MetalCompaniesBundle:CompanyCounter')->findBy(array('company' => $companies));
    }

    public function changeCounter($company, array $fields, $inc = 1)
    {
        $allowedFields = array(
            'newCallbacksCount',
            'newCompanyReviewsCount',
            'newDemandsCount',
            'newComplaintsCount',
            'allProductsCount',
            'reviewsCount'
        );

        foreach ($fields as $field) {
            if (!in_array($field, $allowedFields)) {
                throw new \InvalidArgumentException('Wrong favorite kind');
            }
        }

        $qb = $this->createQueryBuilder('cc')
            ->update();

        $c = (int)$inc;
        if ($inc === false) {
            $c = -1;
        }

        foreach ($fields as $field) {
            $qb->set('cc.'.$field, 'cc.'.$field.' + :changesCount');
        }

        $qb->setParameter('changesCount', $c);

        $qb->where('cc.company = :company')
            ->setParameter('company', $company)
            ->getQuery()
            ->execute();
    }

    public function updateViewDemandCounter(array $companies)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('d, COUNT(d) AS d_count')
            ->from('MetalDemandsBundle:PrivateDemand', 'd')
            ->join('d.company', 'c')
            ->where('d.viewedBy IS NULL')
            ->andWhere('d.deletedAt IS NULL')
            ->addSelect('c')
            ->groupBy('d.company');
        if ($companies) {
            $qb->andWhere('c IN (:companies)')
                ->setParameter('companies', $companies);
        }

        $companiesCounts = $qb->getQuery()->getResult();

        $qb = $this->createQueryBuilder('cr');
        $qb->update('MetalCompaniesBundle:CompanyCounter', 'cr')
            ->set('cr.newDemandsCount', 0);
        if ($companies) {
            $qb->andWhere('cr.company IN (:companies)')
                ->setParameter('companies', $companies);
        }

        $qb->getQuery()
            ->execute();

        foreach ($companiesCounts as $companyCount) {
            $qb = $this->createQueryBuilder('cr');
            $qb->update('MetalCompaniesBundle:CompanyCounter', 'cr');
            $qb->set('cr.newDemandsCount', $companyCount['d_count']);
            $qb->where('cr.company = :company')
                ->setParameter('company', $companyCount[0]->getCompany());
            $qb->getQuery()->execute();
        }
    }

    public function onProductStatusChanging(ProductsBatchEditStructure $structure, $enable)
    {
        $companiesIds = array();
        foreach ($structure->products as $product) {
            $companiesIds[$product['companyId']] = true;
        }

        $this->generalUpdateProductsCount(array_keys($companiesIds));
    }

    public function changeLockStatus($isLocked, array $companiesIds = array())
    {
        $qb = $this->_em
            ->createQueryBuilder()
            ->update('MetalCompaniesBundle:CompanyCounter', 'cc')
            ->set('cc.updateStatistics', ':status')
            ->setParameter('status', $isLocked);

        if ($companiesIds) {
            $qb
                ->where('cc.company IN (:ids)')
                ->setParameter('ids', $companiesIds);
        }

        $qb
            ->getQuery()
            ->execute();
    }

    public function getEmployeeCounters(Company $company)
    {
        $counts = $this->_em->getConnection()->createQueryBuilder()
            ->select('COUNT(IF(e.approved_at IS NULL, 1, NULL)) AS new_employees_count')
            ->addSelect('COUNT(IF(e.approved_at IS NOT NULL, 1, NULL)) AS employees_count')
            ->from('User', 'e')
            ->andWhere('e.ConnectCompany = :company_id')
            ->andWhere('e.Checked = :checked')
            ->setParameter('checked', Product::STATUS_CHECKED)
            ->groupBy('e.ConnectCompany')
            ->setParameter('company_id', $company->getId())
            ->execute()
            ->fetch();

        return $counts;
    }

    public function getNewDemandsCountForUser(User $user)
    {
        $company = $user->getCompany();

        if (!$user->requireApplyTerritorialRules()) {
            return $company->getCounter()->getNewDemandsCount();
        }

        if (isset($this->cachedDemandsCount[$user->getId()])) {
            return $this->cachedDemandsCount[$user->getId()];
        }

        $qb = $this->_em->createQueryBuilder()->select('COUNT(pd.id) AS demands_count')
            ->from('MetalDemandsBundle:PrivateDemand', 'pd')
            ->where('pd.company = :company')
            ->andWhere('pd.viewedBy IS NULL')
            ->andWhere('pd.deletedAt IS NULL')
            ->setParameter('company', $company);

        $companyCityRepository = $this->_em->getRepository('MetalCompaniesBundle:CompanyCity');
        $citiesCriteria = $companyCityRepository->getCitiesCriteriaForUser($user);
        $companyCityRepository->applyFilterByTerritory('pd', $qb, $citiesCriteria);

        return $this->cachedDemandsCount[$user->getId()] = $qb->getQuery()->getSingleScalarResult();
    }

    public function getNewCallbacksCountForUser(User $user)
    {
        $company = $user->getCompany();

        if (!$user->requireApplyTerritorialRules()) {
            return $company->getCounter()->getNewCallbacksCount();
        }

        if (isset($this->cachedCallbacksCount[$user->getId()])) {
            return $this->cachedCallbacksCount[$user->getId()];
        }

        $qb = $this->_em->createQueryBuilder()
            ->select('COUNT(c.id) AS callbacks_count')
            ->from('MetalCallbacksBundle:Callback', 'c')
            ->where('c.company = :company')
            ->andWhere('c.processedBy IS NULL')
            ->andWhere('c.kind = :to_company')
            ->setParameter('company', $company)
            ->setParameter('to_company', Callback::CALLBACK_TO_SUPPLIER);

        $companyCityRepository = $this->_em->getRepository('MetalCompaniesBundle:CompanyCity');
        $citiesCriteria = $companyCityRepository->getCitiesCriteriaForUser($user);
        $companyCityRepository->applyFilterByTerritory('c', $qb, $citiesCriteria);

        return $this->cachedCallbacksCount[$user->getId()] = $qb->getQuery()->getSingleScalarResult();
    }
}
