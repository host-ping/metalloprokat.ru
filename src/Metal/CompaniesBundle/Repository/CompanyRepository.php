<?php

namespace Metal\CompaniesBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Metal\CompaniesBundle\Entity\Company;
use Metal\CompaniesBundle\Entity\PackageChecker;
use Metal\ProductsBundle\Entity\Product;
use Metal\TerritorialBundle\Entity\City;
use Metal\TerritorialBundle\Entity\Country;

class CompanyRepository extends EntityRepository
{
    public function getCompaniesQbBySpecification($specification, $orderBy = array())
    {
        $qb = $this->_em->createQueryBuilder()
            ->select('c')
            ->from($this->_entityName, 'c', !empty($specification['index_by_id']) ? 'c.id' : null);

        $this->applySpecificationToQueryBuilder($qb, $specification);
        $this->applyOrderByToQueryBuilder($qb, $orderBy);

        return $qb;
    }

    private function applyOrderByToQueryBuilder(QueryBuilder $qb, array $orderBy)
    {

    }

    private function applySpecificationToQueryBuilder(QueryBuilder $qb, array $specification)
    {
        if (isset($specification['id'])) {
            $qb->andWhere('c.id IN (:id)')
                ->setParameter('id', $specification['id']);
        }

        if (isset($specification['preload_administrative_center'])) {
            $qb->leftJoin('c.city', 'company_city')
                ->leftJoin('company_city.administrativeCenter', 'company_administrative_center')
                ->addSelect('company_city, company_administrative_center');
        }
    }

    public function attachFavoriteCompanyToCompanies(array $companies, $user)
    {
        if (!count($companies)) {
            return;
        }

        $directedFavoriteCompanies = array();
        /* @var $directedFavoriteCompanies Company[] */
        /* @var $companies Company[] */
        foreach ($companies as $company) {
            $directedFavoriteCompanies[$company->getId()] = $company;
            $company->setAttribute('favoriteCompany', null);
        }

        if (!$user) {
            return;
        }

        $qb = $this->_em->createQueryBuilder()
            ->select('IDENTITY(fc.company) AS companyId, fc AS favoriteCompany')
            ->from('MetalUsersBundle:FavoriteCompany', 'fc')
            ->andWhere('fc.user = :user')
            ->andWhere('fc.company IN (:companies_ids)')
            ->setParameter('user', $user)
            ->setParameter('companies_ids', array_keys($directedFavoriteCompanies));

        $favoriteCompanies = $qb
            ->getQuery()
            ->getResult();

        foreach ($favoriteCompanies as $favoriteCompany) {
            $directedFavoriteCompanies[$favoriteCompany['companyId']]
                ->setAttribute('favoriteCompany', $favoriteCompany['favoriteCompany']);
        }
    }

    /**
     * @param Company[] $companies
     */
    public function attachCompanyReviewToCompanies(array $companies)
    {
        if (!count($companies)) {
            return;
        }

        $directedCompanies = array();
        foreach ($companies as $company) {
            $company->setAttribute('company_client_review', null);
            $directedCompanies[$company->getId()] = $company;
        }

        $qb = $this->_em->createQueryBuilder()
            ->select('IDENTITY(cr.company) AS companyId, cr AS clientReview')
            ->from('MetalCorpsiteBundle:ClientReview', 'cr')
            ->where('cr.moderatedAt IS NOT NULL')
            ->andWhere('cr.deletedAt IS NULL')
            ->andWhere('cr.company IN (:companies_ids)')
            ->setParameter('companies_ids', array_keys($directedCompanies));

        $reviewsCompanies = $qb
            ->getQuery()
            ->getResult();

        foreach ($reviewsCompanies as $reviewCompany) {
            $directedCompanies[$reviewCompany['companyId']]
                ->setAttribute('company_client_review', $reviewCompany['clientReview']);
        }
    }

    /**
     * @param Company[] $companies
     * @param Country $country
     */
    public function attachProductToCompanies(array $companies, Country $country = null)
    {
        if (!$companies) {
            return;
        }

        $productsIds = array();
        foreach ($companies as $company) {
            if ($company->getAttribute('product_id')) {
                $productsIds[] = $company->getAttribute('product_id');
                $company->setAttribute('product_id', null);
            }
        }

        $products = $this->_em->createQueryBuilder()
            ->select('p, i')
            ->from('MetalProductsBundle:Product', 'p')
            ->leftJoin('p.image', 'i')
            ->andWhere('p.id IN (:ids)')
            ->setParameter('ids', $productsIds)
            ->getQuery()
            ->getResult();
        /* @var $products Product[] */

        foreach ($products as $product) {
            $product->getCompany()->setAttribute('product', $product);
        }

        $this->_em->getRepository('MetalProductsBundle:Product')->attachNormalizedPrice($products, $country);
    }

    /**
     * @param Company[] $companies
     */
    public function attachIsParsedCompanyFlag(array $companies)
    {
        if (empty($companies)) {
            return;
        }

        $parsedCompanies = $this->_em->createQueryBuilder()
            ->from('MetalContentBundle:UserRegistrationWithParser', 'userRegistrationWithParser')
            ->select('IDENTITY(userRegistrationWithParser.company) AS company_id')
            ->where('userRegistrationWithParser.company IN(:companies)')
            ->setParameter('companies', $companies)
            ->getQuery()
            ->getResult();

        $parsedCompanies = array_column($parsedCompanies, 'company_id');
        foreach ($companies as $company) {
            if (in_array($company->getId(), $parsedCompanies)) {
                $company->setAttribute('is_parsed', true);
            }
        }
    }

    /**
     * @param Company[] $companies
     */
    public function attachUsersToCompanies(array $companies)
    {
        if (empty($companies)) {
            return;
        }

        $companiesDirected = array();
        foreach ($companies as $company) {
            $company->setAttribute('users', array());
            $companiesDirected[$company->getId()] = $company;
        }

        $users = $this->_em->createQueryBuilder()
            ->select('u AS user, IDENTITY(u.company) AS companyId, IDENTITY(companyLog.createdBy) AS mainUserId')
            ->from('MetalUsersBundle:User', 'u')
            ->join('u.company', 'company')
            ->join('company.companyLog', 'companyLog')
            ->andWhere('u.company IN (:companies_ids)')
            ->andWhere('u.isEnabled = true')
            ->setParameter('companies_ids', $companies)
            ->getQuery()
            ->getResult();

        $usersToCompanies = array();
        foreach ($users as $user) {
            if (!isset($usersToCompanies[$user['companyId']])) {
                $usersToCompanies[$user['companyId']] = array();
            }

            if ($user['user']->getId() == $user['mainUserId']) {
                array_unshift($usersToCompanies[$user['companyId']], $user['user']);
            } else {
                $usersToCompanies[$user['companyId']][] = $user['user'];
            }
        }

        foreach ($usersToCompanies as $companyId => $users) {
            $companiesDirected[$companyId]->setAttribute('users', $users);
        }
    }

    /**
     * @param Company $company
     */
    public function attachCompanyHistoryToCompany(Company $company)
    {
        $company->setAttribute('company_history', array());

        $companyHistories = $this->_em->createQueryBuilder()
            ->select('ch')
            ->from('MetalCompaniesBundle:CompanyHistory', 'ch')
            ->where('ch.company = :company_id OR ch.relatedCompany = :company_id')
            ->setParameter('company_id', $company->getId())
            ->orderBy('ch.createdAt', 'DESC')
            ->getQuery()
            ->getResult();

        $company->setAttribute('company_history', $companyHistories);
    }

    public function loadCompany($id)
    {
        $company = $this->find($id);

        if (!$company || $company->isDeleted()) {
            return null;
        }

        return $company;
    }

    public function getPossibleCompanies(City $city, $companyType, $companyName)
    {
        return $this->_em->createQueryBuilder()
            ->select('c, ci')
            ->from('MetalCompaniesBundle:Company', 'c')
            ->leftJoin('c.city', 'ci')
            ->andWhere('c.title LIKE :companyName')
            ->andWhere('c.city = :city')
            ->andWhere('c.deletedAtTS = 0')
            ->andWhere('c.companyTypeId = :companyTypeId')
            ->setParameter('city', $city)
            ->setParameter('companyTypeId', $companyType)
            ->setParameter('companyName', '%'.$companyName.'%')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param $codeAccess
     *
     * @return array
     */
    public function getCompaniesIdsByCodeAccess($codeAccess)
    {
        $rows = $this->_em->getConnection()->fetchAll(
            'SELECT company.Message_ID AS company_id FROM Message75 AS company WHERE company.code_access = :codeAccess',
            array(
                'codeAccess' => $codeAccess,
            )
        );

        return array_column($rows, 'company_id');
    }

    public function updateCompanyDomain()
    {
        return $this->_em->getConnection()->executeUpdate(
            "UPDATE Message75 c
            JOIN Classificator_Country cc ON c.country_id = cc.Country_ID
            SET c.domain = CONCAT(c.slug, '.', cc.base_host) WHERE c.deleted_at_ts = 0"
        );
    }

    /**
     * @param bool $withReviews
     * @return array
     */
    public function getCompaniesIdsForClientsReviews($withReviews = false)
    {
        $companiesIdsDataQb = $this->_em
            ->getRepository('MetalCompaniesBundle:Company')
            ->createQueryBuilder('c')
            ->select('c.id');

        if ($withReviews) {
            $companiesIdsDataQb
                ->addSelect('cr.id as companyReviewId')
                ->join('MetalCorpsiteBundle:ClientReview', 'cr', 'WITH', 'cr.company = c.id')
                ->andWhere('cr.moderatedAt IS NOT NULL')
                ->andWhere('cr.deletedAt IS NULL');
        } else {
            $companiesIdsDataQb
                ->andWhere("c.codeAccess IN (:premium_companies)")
                ->setParameter("premium_companies", PackageChecker::getPackagesByOption('show_on_corpsite'));
        }

        $companiesIdsData = $companiesIdsDataQb
            ->andWhere("c.logo.name IS NOT NULL AND c.logo.name != ''")
            ->getQuery()
            ->getResult();

        $companiesIds = array();
        foreach ($companiesIdsData as $company) {
            $companiesIds[] = $company['id'];
        }

        shuffle($companiesIds);
        return array_slice($companiesIds, 0, 50);
    }

    public function getCloudflareSlugsForCountry($country)
    {
        $companiesSlugs = $this->_em->createQueryBuilder()
            ->from('MetalCompaniesBundle:Company', 'c', 'c.slug')
            ->select('c.slug')
            ->andWhere('c.codeAccess IN (:https_allowed)')
            ->setParameter('https_allowed', PackageChecker::getPackagesByOption('https_allowed'))
            ->andWhere('c.country = :country')
            ->setParameter('country', $country)
            ->getQuery()
            ->getResult();

        return array_keys($companiesSlugs);
    }

    public function getCloudflareSlugsForCountryByIds($country, array $companiesIds = array())
    {
        if (empty($companiesIds)) {
            return array();
        }

        $companiesSlugs = $this->_em->createQueryBuilder()
            ->from('MetalCompaniesBundle:Company', 'c', 'c.slug')
            ->select('c.slug')
            ->where('c.country = :country')
            ->setParameter('country', $country)
            ->andWhere('c.id IN (:companies_ids)')
            ->setParameter('companies_ids', $companiesIds)
            ->getQuery()
            ->getResult();

        return array_keys($companiesSlugs);
    }

    public function updateCompanyIsAddedToCloudflareStatus($slug, $status)
    {
        // NB! специально кастуем к int. Иначе будет dql вида: UPDATE Company c SET c.isAddedToCloudflare =  WHERE c.slug = :slug
        return $this
            ->createQueryBuilder('c')
            ->update('MetalCompaniesBundle:Company', 'c')
            ->set('c.isAddedToCloudflare', (int)$status)
            ->where('c.slug = :slug')
            ->setParameter('slug', $slug)
            ->getQuery()
            ->execute();
    }

    public function updateCompanyOnlineStatus(int $companyId, \DateTime $lastVisitedAt, \DateTime $updateTimeout): bool
    {
        $affectedRows = $this->_em
            ->getConnection()
            ->executeUpdate(
                'UPDATE Message75 SET last_visit_at = :last_visit_at WHERE Message_ID = :id AND last_visit_at < :update_timeout',
                ['id' => $companyId, 'last_visit_at' => $lastVisitedAt, 'update_timeout' => $updateTimeout],
                ['last_visit_at' => 'datetime', 'update_timeout' => 'datetime']
            );

        return $affectedRows > 0;
    }
}
