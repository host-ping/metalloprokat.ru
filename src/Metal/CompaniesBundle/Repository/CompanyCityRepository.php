<?php

namespace Metal\CompaniesBundle\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Metal\CompaniesBundle\Entity\Company;
use Metal\CompaniesBundle\Entity\CompanyCity;
use Metal\ProductsBundle\Entity\Product;
use Metal\TerritorialBundle\Entity\City;
use Metal\TerritorialBundle\Entity\Country;
use Metal\TerritorialBundle\Entity\Region;
use Metal\TerritorialBundle\Entity\TerritoryInterface;
use Metal\UsersBundle\Entity\User;

class CompanyCityRepository extends EntityRepository
{
    protected $cachedUserCriterias = array();

    public function loadCompanyCitiesCollectionForCompany(Company $company)
    {
        $companyCities = $this->_em->createQueryBuilder()
            ->select('cc')
            ->from('MetalCompaniesBundle:CompanyCity', 'cc')
            ->join('cc.city', 'c')
            ->addSelect('c')
            ->orderBy('c.title')
            ->where('cc.company = :company')
            ->setParameter('company', $company)
            ->getQuery()
            ->getResult()
        ;

        $coll = $company->getCompanyCities();
        foreach ($companyCities as $companyCity) { /* @var $coll \Doctrine\ORM\PersistentCollection */
            $coll->hydrateAdd($companyCity);
        }
        $coll->setInitialized(true);
    }

    public function getCompanyCitiesDataForCompany(Company $company)
    {
        $branches = $this
            ->createQueryBuilder('cc')
            ->andWhere('cc.company = :company')
            ->andWhere('cc.kind = :kind')
            ->andWhere('cc.enabled = true')
            ->setParameter('company', $company)
            ->setParameter('kind', CompanyCity::KIND_BRANCH_OFFICE)
            ->leftJoin('cc.city', 'c')
            ->addSelect('c')
            ->orderBy('cc.isMainOffice', 'DESC')
            ->addOrderBy('c.title', 'ASC')
            ->getQuery()
            ->getResult();

        $branches = array_map(function (CompanyCity $companyCity) {
            return array(
                'id' => $companyCity->getId(),
                'cityId' => $companyCity->getCity()->getId(),
                'title' => $companyCity->getIsMainOffice() ? 'Центральный офис' : $companyCity->getCity()->getTitle()
            );
        }, $branches);

        return $branches;
    }

    public function enableCompanyCitiesByLimit($companyId, $limit)
    {
        return $this->_em->getConnection()->executeUpdate(
            '
                   UPDATE company_delivery_city AS companyCity
                   SET companyCity.enabled = true
                   WHERE companyCity.company_id = :company_id AND companyCity.is_main_office != true
                   ORDER BY companyCity.has_products DESC, companyCity.kind DESC, companyCity.id ASC
                   LIMIT '.$limit,
            array(
                'company_id' => $companyId
            )
        );
    }

    /**
     * @param array $companiesIds
     *
     * @return integer
     */
    public function disableCompanyCities(array $companiesIds = array())
    {
        $parameters = array('status' => Company::VISIBILITY_STATUS_NORMAL);
        $types = array();

        if ($companiesIds) {
            $parameters['companies_ids'] = $companiesIds;
            $types['companies_ids'] = Connection::PARAM_INT_ARRAY;
        }

        return $this->_em->getConnection()
            ->executeUpdate(
                sprintf('UPDATE company_delivery_city AS cdc
                    JOIN Message75 AS company ON company.Message_ID = cdc.company_id
                    SET enabled = 0
                     WHERE is_main_office = FALSE AND company.is_allowed_extra_cities = FALSE
                     AND visibility_status = :status
                     %s', $companiesIds ? 'AND cdc.company_id IN (:companies_ids)' : ''
                ),
                $parameters,
                $types
            );
    }

    public function updateMainOfficeStatus($companyId, $newCityId, $moveProducts)
    {
        $conn = $this->_em->getConnection();

        $conn->beginTransaction();

        $branchOfficeId = (int)$conn->fetchColumn(
            'SELECT cdc.id FROM company_delivery_city cdc WHERE cdc.company_id = :company_id AND cdc.city_id = :city_id',
            array('company_id' => $companyId, 'city_id' => $newCityId)
        );

        if ($branchOfficeId) { // филиал в данном городе уже существовал
            if ($moveProducts) {
                $oldMainOfficeId = (int)$conn->fetchColumn(
                    'SELECT cdc.id FROM company_delivery_city cdc WHERE is_main_office = 1 AND cdc.company_id = :company_id',
                    array('company_id' => $companyId)
                );

                $productsIdsForOldOffice = $conn->fetchAll(
                    'SELECT p.Message_ID AS id FROM Message142 p WHERE branch_office_id = :old_branch_office_id',
                    array(
                        'old_branch_office_id' => $oldMainOfficeId
                    )
                );

                $conn->executeUpdate(
                    'UPDATE Message142 p SET p.branch_office_id = :new_branch_office_id WHERE p.branch_office_id = :old_branch_office_id',
                    array('new_branch_office_id' => $branchOfficeId, 'old_branch_office_id' => $oldMainOfficeId)
                );

                $productsIdsForOldOffice = array_column($productsIdsForOldOffice, 'id');

                $this->processUpdateProductsHash($productsIdsForOldOffice);

            }

            $conn->executeUpdate(
                'UPDATE Message142 p SET p.branch_office_id = :new_branch_office_id WHERE p.Company_ID = :company_id AND p.is_virtual = true',
                array('new_branch_office_id' => $branchOfficeId, 'company_id' => $companyId)
            );

            $conn->executeUpdate(
                'UPDATE company_delivery_city cdc SET cdc.is_main_office = 0 WHERE cdc.company_id = :company_id',
                array('company_id' => $companyId)
            );

            $conn->executeUpdate(
                'UPDATE company_delivery_city cdc SET cdc.is_main_office = 1, cdc.enabled = 1, cdc.kind = :kind WHERE cdc.id = :id',
                array(
                    'id' => $branchOfficeId,
                    'kind' => CompanyCity::KIND_BRANCH_OFFICE
                )
            );
        } else {
            // филиала в данном городе не было - просто меняем город у главного офиса
            $conn->executeUpdate(
                'UPDATE company_delivery_city cdc SET cdc.city_id = :city_id, cdc.kind = :kind WHERE cdc.company_id = :company_id AND cdc.is_main_office = 1',
                array(
                    'company_id' => $companyId,
                    'city_id' => $newCityId,
                    'kind' => CompanyCity::KIND_BRANCH_OFFICE
                )
            );
        }

        $conn->executeUpdate(
            'UPDATE Message75 с SET с.company_city = :city_id WHERE с.Message_ID = :company_id',
            array('company_id' => $companyId, 'city_id' => $newCityId)
        );

        $conn->commit();
    }

    public function processUpdateProductsHash($productsIds)
    {
        if (!$productsIds) {
            return;
        }

        $conn = $this->_em->getConnection();

        $productRepository = $this->_em->getRepository('MetalProductsBundle:Product');

        $productRepository->updateProductsItemHash($productsIds);

        $productsHashes = $conn->fetchAll(
            'SELECT p.item_hash FROM Message142 p WHERE p.Message_ID IN (:productsIds)',
            array(
                'productsIds' => $productsIds
            ),
            array(
                'productsIds' => Connection::PARAM_INT_ARRAY
            )
        );

        $productsHashes = array_column($productsHashes, 'item_hash');


        $productRepository->disableDuplicatedProductsByProductHashes($productsHashes);
    }

    public function updateCompanyCityHasProducts(array $productsIds = array(), $isEnable)
    {
        if (!$productsIds) {
            return;
        }

        if ($isEnable) {
            $this->_em->getConnection()->executeQuery('
            UPDATE company_delivery_city AS cdc
              JOIN (
                SELECT p.branch_office_id, p.Company_ID FROM Message142 p
                WHERE p.Message_ID IN(:products_ids)
                GROUP BY p.Company_ID
                ) AS prod ON cdc.id = prod.branch_office_id
            SET cdc.has_products = 1
        ',  array(
                    'products_ids' => $productsIds
                ),
                array(
                    'products_ids' => Connection::PARAM_INT_ARRAY
                )
            )->execute();
        } else {
           $branchOffices = $this->_em->getConnection()->executeQuery('
                SELECT product.branch_office_id
                FROM Message142 AS product
                  WHERE product.Message_ID IN (:products_ids)
                  GROUP BY product.branch_office_id
            ',  array(
                    'products_ids' => $productsIds
                ),
                array(
                    'products_ids' => Connection::PARAM_INT_ARRAY
                )
            )->fetchAll();

            $branchOfficesIds = array();
            foreach ($branchOffices as $branchOfficeId) {
                $branchOfficesIds[$branchOfficeId['branch_office_id']] = true;
            }

            $this->_em->getConnection()->executeQuery('
                    UPDATE company_delivery_city AS cdc
                    SET cdc.has_products = 0
                    WHERE NOT exists(SELECT
                                       p.Message_ID
                                     FROM Message142 AS p
                                     WHERE p.branch_office_id = cdc.id
                                     AND p.Checked = :status
                    ) AND cdc.id IN (:branch_offices_ids);
            ',  array(
                    'branch_offices_ids' => array_keys($branchOfficesIds),
                    'status' => Product::STATUS_CHECKED
                ),
                array(
                    'branch_offices_ids' => Connection::PARAM_INT_ARRAY
                )
            )->execute();
        }
    }

    /**
     * @param array $companiesIds
     */
    public function refreshBranchOfficeHasProducts(array $companiesIds = array())
    {
        $qb = $this->_em->createQueryBuilder()
            ->update('MetalCompaniesBundle:CompanyCity', 'cc')
            ->set('cc.hasProducts', 0);

        if ($companiesIds) {
            $qb->where('cc.company IN (:companies_ids)')
                ->setParameter('companies_ids', $companiesIds);
        }
        $qb->getQuery()->execute();

        $qb2 = $this->_em->createQueryBuilder();
        $qb2->update('MetalCompaniesBundle:CompanyCity', 'companyCity')
            ->set('companyCity.hasProducts', 1)
            ->where('EXISTS (SELECT product.id FROM MetalProductsBundle:Product product WHERE product.branchOffice = companyCity.id AND product.checked= :status)')
            ->setParameter('status', Product::STATUS_CHECKED);

        if ($companiesIds) {
            $qb2->andWhere('companyCity.company IN (:companies_ids)')
                ->setParameter('companies_ids', $companiesIds);
        }

        $qb2->getQuery()->execute();
    }

    /**
     * @param array $companiesIds
     * @return array|null
     */
    public function getProductsIdsForReindex(array $companiesIds = array())
    {
        if (!$companiesIds) {
            return null;
        }

        return array_keys($this->_em->createQueryBuilder()
                ->select('product.id')
                ->from('MetalProductsBundle:Product', 'product', 'product.id')
            //TODO подумать как сделать что-бы не все продукты брались
                //->join('product.branchOffice', 'branchOffice', 'WITH', 'branchOffice.isMainOffice = 1')
                ->where('product.company IN (:companies_ids)')
                ->setParameter('companies_ids', $companiesIds)
                ->andWhere('product.checked = :status')
                ->setParameter('status', Product::STATUS_CHECKED)
                ->getQuery()
                ->getResult());
    }

    /**
     * @param array $companiesIds
     * @return array|null
     */
    public function getProductsIdsForRemove(array $companiesIds = array())
    {
        if (!$companiesIds) {
            return null;
        }

        return array_keys($this->_em->createQueryBuilder()
            ->select('product.id')
            ->from('MetalProductsBundle:Product', 'product', 'product.id')
            ->where('product.company IN (:companies_ids)')
            ->setParameter('companies_ids', $companiesIds)
            ->getQuery()
            ->getResult());
    }

    /**
     * @param Company $company
     * @param City $city
     *
     * @return CompanyCity
     */
    public function getBranchOfficeInRegionWithoutSlug(Company $company, City $city)
    {
        return $this->_em
            ->createQueryBuilder()
            ->select('companyCity')
            ->from('MetalCompaniesBundle:CompanyCity', 'companyCity')
            ->join('companyCity.city', 'city')
            ->andWhere('companyCity.company = :company')
            ->setParameter('company', $company)
            ->andWhere('city.region = :region')
            ->setParameter('region', $city->getRegion())
            ->andWhere('city.slug IS NULL')
            ->addOrderBy('companyCity.hasProducts', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @see UserRepository::getEmployeesForTerritory must be in sync with it
     *
     * @param User $user
     *
     * @return array
     */
    public function getCitiesCriteriaForUser(User $user)
    {
        if (!$user->requireApplyTerritorialRules()) {
            return array();
        }

        if (isset($this->cachedUserCriterias[$user->getId()])) {
            return $this->cachedUserCriterias[$user->getId()];
        }

        $territoriesForUser = $this->_em->createQueryBuilder()
            ->select('IDENTITY(uc.city) AS cityId, IDENTITY(uc.region) AS regionId, IDENTITY(uc.country) AS countryId, uc.isExcluded')
            ->from('MetalCompaniesBundle:UserCity', 'uc')
            ->where('uc.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();

        $result = array();
        foreach ($territoriesForUser as $territoryForUser) {
            if ($territoryForUser['cityId']) {
                $key = $territoryForUser['isExcluded'] ? 'excludedCities' : 'includedCities';
                $result[$key][] = $territoryForUser['cityId'];
            } elseif ($territoryForUser['regionId']) {
                $key = $territoryForUser['isExcluded'] ? 'excludedRegions' : 'includedRegions';
                $result[$key][] = $territoryForUser['regionId'];
            } elseif ($territoryForUser['countryId']) {
                $key = $territoryForUser['isExcluded'] ? 'excludedCountries' : 'includedCountries';
                $result[$key][] = $territoryForUser['countryId'];
            }
        }

        return $this->cachedUserCriterias[$user->getId()] = $result;
    }

    public function applyFilterByTerritory($alias, QueryBuilder $qb, array $criteria)
    {
        if (!$criteria) {
            return;
        }

        $orX = $qb->expr()->orX();
        if (!empty($criteria['includedCities'])) {
            $orX->add($qb->expr()->in(sprintf('%s.city', $alias), ':includedCities'));
            $qb->setParameter('includedCities', $criteria['includedCities']);
        }

        if (!empty($criteria['includedRegions'])) {
            $orX->add($qb->expr()->in(sprintf('%s.region', $alias), ':includedRegions'));
            $qb->setParameter('includedRegions', $criteria['includedRegions']);
        }

        if (!empty($criteria['includedCountries'])) {
            $orX->add($qb->expr()->in(sprintf('%s.country', $alias), ':includedCountries'));
            $qb->setParameter('includedCountries', $criteria['includedCountries']);
        }

        if (count($orX->getParts())) {
            $qb->andWhere($orX);
        }

        if (!empty($criteria['excludedCities'])) {
            $qb
                ->andWhere(sprintf('%s.city NOT IN (:excl_cities_ids)', $alias))
                ->setParameter('excl_cities_ids', $criteria['excludedCities']);
        }

        if (!empty($criteria['excludedRegions'])) {
            $qb
                ->andWhere(sprintf('%s.region NOT IN (:excl_regions_ids)', $alias))
                ->setParameter('excl_regions_ids', $criteria['excludedRegions']);
        }

        if (!empty($criteria['excludedCountries'])) {
            $qb
                ->andWhere(sprintf('%s.country NOT IN (:excl_countries_ids)', $alias))
                ->setParameter('excl_countries_ids', $criteria['excludedCountries']);
        }
    }

    /**
     * @param array $companiesIds
     *
     * @return array [companyId => [cityId, cityId, ...]]
     */
    public function getCitiesIdsForCompanies(array $companiesIds = array())
    {
        if (!$companiesIds) {
            return array();
        }

        $companyToCities = array_fill_keys($companiesIds, array());

        $companyCities = $this->_em->getRepository('MetalCompaniesBundle:CompanyCity')
            ->createQueryBuilder('companyCity')
            ->select('IDENTITY(companyCity.company) AS companyId')
            ->addSelect('IDENTITY(companyCity.city) AS cityId')
            ->where('companyCity.company IN (:companies)')
            ->setParameter('companies', $companiesIds)
            ->getQuery()
            ->getArrayResult();

        foreach ($companyCities as $companyCity) {
            $companyToCities[$companyCity['companyId']][] = $companyCity['cityId'];
        }

        return $companyToCities;
    }

    /**
     * @param Company[] $companies
     * @param TerritoryInterface $territory
     */
    public function attachCompanyCities(array $companies, TerritoryInterface $territory)
    {
        if (!$companies) {
            return;
        }

        foreach ($companies as $company) {
            $company->setAttribute('company_city', null);
        }

        $cityId = null;
        $regionId = null;
        $countryId = null;
        if ($territory instanceof City) {
            $cityId = $territory->getId();
            $regionId = $territory->getRegion()->getId();
            $countryId = $territory->getCountry()->getId();
        } elseif ($territory instanceof Region) {
            $regionId = $territory->getId();
            $countryId = $territory->getCountry()->getId();
        } else {
            $countryId = $territory->getId();
        }

        $companiesInOtherCountries = array();
        foreach ($companies as $company) {
            if ($company->getCountry()->getId() != $countryId && in_array($company->getCountry()->getId() ,Country::getEnabledCountriesIds())) {
                $companiesInOtherCountries[] = $company;
            }
        }

        if (!$regionId && !$companiesInOtherCountries) {
            return;
        }

        if (!$regionId) {
            $companies = $companiesInOtherCountries;
        }

        $qb = $this
            ->createQueryBuilder('cc')
            ->join('cc.city', 'c')
            ->where('cc.company IN (:companies)')
            ->setParameter('companies', $companies)
            ->andWhere('c.country = :country')
            ->andWhere('cc.enabled = true')
            ->setParameter('country', $countryId)
            ->addOrderBy('c.isCapital', 'DESC')
        ;

        if ($regionId) {
            $qb->andWhere('c.region = :region')
                ->setParameter('region', $regionId);
        }

        $companyCities = $qb
            ->getQuery()
            ->getResult();
        /* @var $companyCities CompanyCity[] */

        $companyToCompanyCities = array();
        foreach ($companyCities as $companyCity) {
            $companyId = $companyCity->getCompany()->getId();

            if (!isset($companyToCompanyCities[$companyId])) {
                $companyToCompanyCities[$companyId] = array();
            }

            if ($cityId && $companyCity->getCity()->getId() == $cityId) {
                array_unshift($companyToCompanyCities[$companyId], $companyCity);
            } else {
                $companyToCompanyCities[$companyId][] = $companyCity;
            }
        }

        foreach ($companies as $company) {
            $companyCity = null;
            if (isset($companyToCompanyCities[$company->getId()])) {
                $companyCity = reset($companyToCompanyCities[$company->getId()]);
            }

            $company->setAttribute('company_city', $companyCity);
        }
    }

    public function getProductsCountByCompanyAndCity($company, $city)
    {
        return $this->_em
            ->createQueryBuilder()
            ->select('COUNT(p.id)')
            ->from('MetalProductsBundle:Product', 'p')
            ->join('p.branchOffice', 'pbo')
            ->where('pbo.city = :city')
            ->andWhere('p.company = :company')
            ->andWhere('p.isVirtual = false')
            ->setParameter('city', $city)
            ->setParameter('company', $company)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getBranchesCountForCompany(Company $company)
    {
        return $this->createQueryBuilder('cc')
            ->select('COUNT(cc)')
            ->where('cc.company = :company')
            ->andWhere('cc.enabled = true')
            ->andWhere('cc.isMainOffice = false')
            ->setParameter('company', $company)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
