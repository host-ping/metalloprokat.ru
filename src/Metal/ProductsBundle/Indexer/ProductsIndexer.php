<?php

namespace Metal\ProductsBundle\Indexer;

use Brouzie\Sphinxy\Indexer\DoctrineQbIndexer;
use Metal\AttributesBundle\Entity\AttributeValue;
use Metal\CompaniesBundle\Entity\Company;
use Metal\CompaniesBundle\Entity\PackageChecker;
use Metal\ProductsBundle\Entity\Product;
use Metal\TerritorialBundle\Entity\City;
use Metal\TerritorialBundle\Entity\Country;
use Metal\TerritorialBundle\Entity\Region;

/**
 * @deprecated
 */
class ProductsIndexer extends DoctrineQbIndexer
{
    private $citiesToAdministrativeCenter;

    public function processItems(array $items)
    {
        $companyToCities = array();
        $companyToAttributes = array();
        $productToParameters = array();
        $branchOfficeNoProducts = array();
        $companyCountries = array();
        $companyToVirtualProducts = array();
        $companiesToTerritorialPackages = array();

        foreach ($items as $productRow) {
            $companyToCities[$productRow['companyId']] = array();
            $companyToAttributes[$productRow['companyId']] = array();
            $productToParameters[$productRow['product']['id']] = array();

            $companyCountries[$productRow['companyId']] = array();

            if ($productRow['product']['isVirtual']) {
                $companyToVirtualProducts[$productRow['companyId']] = $productRow['product']['id'];
            }

            if ($productRow['codeAccessByTerritory']) {
                $companiesToTerritorialPackages[$productRow['companyId']] = $productRow['codeAccessByTerritory'];
            }
        }

        $virtualProductsToCategories = array_fill_keys($companyToVirtualProducts, array());
        $companiesToCategories = $companyCategories = $this->em->getRepository('MetalCompaniesBundle:CompanyCategory')
            ->getCategoriesIdsForCompanies(array_keys($companyToVirtualProducts));

        foreach ($companiesToCategories as $companyId => $companyToCategories) {
            $virtualProductsToCategories[$companyToVirtualProducts[$companyId]] = $companyToCategories;
        }

        $companyDeliveries = $this->em->createQueryBuilder()
            ->select('
                IDENTITY(d.company) AS companyId,
                IDENTITY(d.city) AS cityId,
                IDENTITY(c.country) AS countryId,
                IDENTITY(c.region) AS regionId,
                d.isMainOffice,
                d.hasProducts
                ')
            ->addSelect('d.isAssociatedWithCityCode')
            ->from('MetalCompaniesBundle:CompanyCity', 'd')
            ->leftJoin('d.city', 'c')
            ->where('d.company IN (:ids)')
            ->andWhere('c.country IS NOT NULL')
            ->andWhere('d.enabled = true')
            ->setParameter('ids', array_keys($companyToCities))
            ->getQuery()
            ->getResult()
        ;

        $virtualProductsBranchOffices = array();
        $phoneCitiesIds = array();
        foreach ($companyDeliveries as $companyDelivery) {
            $companyId = $companyDelivery['companyId'];
            if (isset($companyToVirtualProducts[$companyId])) {
                $virtualProductsBranchOffices[$companyToVirtualProducts[$companyId]]['cities_ids'][] = $companyDelivery['cityId'];
                $virtualProductsBranchOffices[$companyToVirtualProducts[$companyId]]['regions_ids'][] = $companyDelivery['regionId'];
            }

            if (!$companyDelivery['hasProducts']) {
                $branchOfficeNoProducts[$companyId]['cities_ids'][$companyDelivery['cityId']] = true;
                $branchOfficeNoProducts[$companyId]['regions_ids'][$companyDelivery['regionId']] = true;
            }

            if ($companyDelivery['isAssociatedWithCityCode']) {
                $phoneCitiesIds[$companyId][] = $companyDelivery['cityId'];
            }

            $companyCountries[$companyId][$companyDelivery['countryId']] = true;
        }

        $companyAttributes = $this->em->createQueryBuilder()
            ->select('IDENTITY(ca.company) AS companyId, ca.typeId')
            ->from('MetalCompaniesBundle:CompanyAttribute', 'ca')
            ->where('ca.company IN (:ids)')
            ->setParameter('ids', array_keys($companyToAttributes))
            ->getQuery()
            ->getResult();

        foreach ($companyAttributes as $companyAttribute) {
            $companyToAttributes[$companyAttribute['companyId']][] = $companyAttribute['typeId'];
        }

        $productToAttributes = $this->em->getRepository('MetalProductsBundle:ProductParameterValue')
            ->getParameterOptions(array_keys($items));

        foreach ($productToAttributes as $productId => $productToAttribute) {
            foreach ($productToAttribute as $attribute) {
                $productToParameters[$productId][] = $attribute;
            }
        }

        $priorityShowPackages = PackageChecker::getOptionValuesPerPackage('priority_show');

        $companyPackageTerritoryRepository = $this->em->getRepository('MetalServicesBundle:CompanyPackageTerritory');
        $companiesTerritorialPackages = $companyPackageTerritoryRepository->replaceCodeAccessToPriorityByPackage($companiesToTerritorialPackages, $priorityShowPackages);

        foreach ($items as $i => $productRow) {
            $product = $productRow['product'];
            $productId = $product['id'];
            $companyId = $productRow['companyId'];
            $price = $product['normalizedPrice'] == 0 ? Product::PRICE_CONTRACT : $product['normalizedPrice'];

            $productTitle = $product['isVirtual'] ? $product['title'] : AttributeValue::normalizeTitle($product['title'].' '.$product['size'].' '.$productRow['categoryTitle']);
            $companyCityId = $productRow['citySlug'] ? $productRow['companyCityId'] : $productRow['companyAdministrativeCenterId'];

            $items[$i] = array(
                'id' => $productId,
                'title' => $productTitle,
                'product_title' => $productTitle,
                'company_id' => $companyId,
                'price' => $price,
                'attributes_ids' => $productToParameters[$productId],
                'category_id' => $productRow['categoryId'] ?: 0,
                'categories_ids' => $product['isVirtual'] ? $virtualProductsToCategories[$productId] : array_merge(array_filter(explode(',', $productRow['branchIds'])), array_filter(explode(',', $productRow['virtualParentsIds']))),
                'custom_categories_ids' => $product['isVirtual'] ? array() : array_filter(explode(',', $productRow['customBranchIds'])),
                'company_city_id' => $companyCityId,
                'product_city_id' => $productRow['productOfficeCityId'],
                'countries_ids' => $this->prepareCountriesIds($productRow['companyMainCountryId'], array_keys($companyCountries[$companyId]), $productRow['companyVisibilityStatus']),
                'phone_cities_ids' => isset($phoneCitiesIds[$companyId]) ? array_merge($phoneCitiesIds[$companyId], array($companyCityId)) : array($companyCityId),
                // округляем до целой даты, что б новые бесплатные ни в коем случае не попадали выше старых платных
                // возможно, округлять стоит только для бесплатных
                'created_at' => $product['createdAt']->modify('midnight')->getTimestamp(),
                'day_updated_at' => $product['updatedAt']->modify('midnight')->format('Ymd'),
                'company_access' => $productRow['codeAccess'],
                'company_rating' => $productRow['companyRating'],
                'company_attributes_ids' => $companyToAttributes[$companyId],
                //TODO: переименовать: поля для поиска с суффиксом field, атрибуты для фильтрации без суффикса
                'company_title' => $productRow['companyNormalizedTitle'].' ',
                'company_title_field' => (string)$productRow['companyNormalizedTitle'].' ',
                'day_company_created_at' => $productRow['companyCreatedAt']->modify('midnight')->format('Ymd'),
                'city_title' => $productRow['cityTitle'],
                'company_last_visited_at' => $productRow['companyLastVisitAt']->getTimestamp(),
                'is_special_offer' => $product['isSpecialOffer'],
                'visibility_status' => $productRow['companyVisibilityStatus'],
                'is_virtual' => $product['isVirtual'],
                'show_on_portal' => $productRow['showProductOnPortal'],
                'priority_show' => $productRow['codeAccessByTerritory'] ? 0 : $priorityShowPackages[$productRow['codeAccess']],
                'attributes' => json_encode($productToAttributes[$productId]),
                'product_position' => $product['position'],
                'priority_show_territorial' => $productRow['codeAccessByTerritory'] ? json_encode($companiesTerritorialPackages[$companyId]) : json_encode(array()),
                'is_hot_offer' => $product['isHotOffer'],
                'hot_offer_position' => $product['hotOfferPosition'],
            );

            if (!$product['isVirtual'] && $productRow['isMainOffice'] && !empty($branchOfficeNoProducts[$companyId])) {
                $items[$i]['cities_ids'] = array_unique(
                    array_merge(
                        !empty($productRow['productOfficeCityId']) ? array($productRow['productOfficeCityId']) : array(),
                        array_keys($branchOfficeNoProducts[$companyId]['cities_ids'])
                    )
                );

                $items[$i]['regions_ids'] = array_unique(
                    array_merge(
                        !empty($productRow['productOfficeRegionId']) ? array($productRow['productOfficeRegionId']) : array(),
                        array_keys($branchOfficeNoProducts[$companyId]['regions_ids'])
                    )
                );
            } elseif ($product['isVirtual']) {
                $items[$i]['cities_ids'] = !empty($virtualProductsBranchOffices[$productId]['cities_ids']) ? $virtualProductsBranchOffices[$productId]['cities_ids'] : array();
                $items[$i]['regions_ids'] = !empty($virtualProductsBranchOffices[$productId]['regions_ids']) ? $virtualProductsBranchOffices[$productId]['regions_ids'] : array();
            } else {
                $items[$i]['cities_ids'] = !empty($productRow['productOfficeCityId']) ? array($productRow['productOfficeCityId']) : array();
                $items[$i]['regions_ids'] = !empty($productRow['productOfficeRegionId']) ? array($productRow['productOfficeRegionId']) : array();
            }

            $items[$i]['cities_ids'] = $this->prepareCitiesIds($productRow['companyMainCountryId'], $items[$i]['cities_ids'], $productRow['companyVisibilityStatus']);
            $items[$i]['regions_ids'] = $this->prepareRegionsIds($productRow['companyMainCountryId'], $items[$i]['regions_ids'], $productRow['companyVisibilityStatus']);
            if ($productRow['codeAccessByTerritory']) {
                if (isset($productRow['codeAccessByTerritory']['cities'])) {
                    $items[$i]['cities_ids'] = array_unique(array_merge($items[$i]['cities_ids'], array_keys($productRow['codeAccessByTerritory']['cities'])));
                }
                if (isset($productRow['codeAccessByTerritory']['regions'])) {
                    $items[$i]['regions_ids'] = array_unique(array_merge($items[$i]['regions_ids'], array_keys($productRow['codeAccessByTerritory']['regions'])));
                }
            }
        }

        return $items;
    }

    protected function getQueryBuilder()
    {
        return $this->em
            ->createQueryBuilder()
            ->select('e AS product')
            ->addSelect('IDENTITY(e.company) AS companyId')
            ->addSelect('IDENTITY(e.category) AS categoryId')
            ->addSelect('city.id AS companyCityId')
            ->addSelect('city.slug AS citySlug')
            ->addSelect('city.title AS cityTitle')
            ->addSelect('IDENTITY(city.administrativeCenter) AS companyAdministrativeCenterId')
            ->addSelect('comp.companyRating')
            ->addSelect('comp.codeAccess')
            ->addSelect('cat.title AS categoryTitle')
            ->addSelect('cat.branchIds')
            ->addSelect('custom_cat.branchIds AS customBranchIds')
            ->addSelect('cat.virtualParentsIds')
            ->addSelect('comp.title AS companyTitle')
            ->addSelect('comp.createdAt as companyCreatedAt')
            ->addSelect('comp.lastVisitAt AS companyLastVisitAt')
            ->addSelect('comp.codeAccessTerritory AS codeAccessByTerritory')
            ->addSelect('comp.normalizedTitle as companyNormalizedTitle')
            ->addSelect('IDENTITY(comp.country) as companyMainCountryId')
            ->addSelect('off.id AS branchOfficeId')
            ->addSelect('comp.visibilityStatus AS companyVisibilityStatus')
            ->addSelect('off.isMainOffice AS isMainOffice')
            ->addSelect('productOfficeCity.id AS productOfficeCityId')
            ->addSelect('IDENTITY(productOfficeCity.region) AS productOfficeRegionId')
            ->addSelect('e.showOnPortal AS showProductOnPortal')
            ->from('MetalProductsBundle:Product', 'e', 'e.id')
            ->join('e.company', 'comp')
            ->leftJoin('e.category', 'cat')
            ->leftJoin('e.customCategory', 'custom_cat')
            ->join('comp.city', 'city')
            ->join('e.branchOffice', 'off')
            ->join('off.city', 'productOfficeCity')
            ->andWhere('e.checked = :status')
            ->setParameter('status', Product::STATUS_CHECKED)
            ->andWhere('comp.deletedAtTS = 0')
            ->andWhere('comp.title != :empty_string')
            ->setParameter('empty_string', '')
            ->andWhere('off.enabled = true')
//            ->andWhere('comp.virtualProduct IS NOT NULL')
            ->andWhere('(e.category IS NOT NULL OR e.isVirtual = true)')
        ;
    }

    private function prepareCitiesIds($mainCountryId, array $citiesIds, $visibilityStatus)
    {
        if (null === $this->citiesToAdministrativeCenter) {
            $this->citiesToAdministrativeCenter = $this->em
                ->getRepository('MetalTerritorialBundle:City')
                ->getAdministrativeCentresForCitiesWithoutSlug();
        }

        // дописываем административный центр области для городов, которые не имеют своего слага
        foreach ($citiesIds as $cityId) {
            if (isset($this->citiesToAdministrativeCenter[$cityId])) {
                $citiesIds[] = $this->citiesToAdministrativeCenter[$cityId];
            }
        }

        switch ($visibilityStatus) {
            case Company::VISIBILITY_STATUS_ALL_CITIES:
                if (!$virtualCityId = City::getVirtualCityIdForCountry($mainCountryId)) {
                    // для стран из дальнего зарубежья домерживаем Россию
                    $virtualCityId = City::VIRTUAL_CITY_ID_ALL_RUSSIA;
                }

                $citiesIds[] = $virtualCityId;
                break;

            case Company::VISIBILITY_STATUS_OTHER_COUNTRIES:
                // если компания в России - домерживаем все включенные страны, кроме России
                $citiesIds = array_merge($citiesIds, array_diff(City::getVirtualCitiesIds(), array(City::getVirtualCityIdForCountry($mainCountryId))));
                break;

            case Company::VISIBILITY_STATUS_ALL_COUNTRIES:
                $citiesIds = array_merge($citiesIds, City::getVirtualCitiesIds());
                break;
        }

        return array_values(array_filter(array_unique($citiesIds)));
    }

    private function prepareRegionsIds($mainCountryId, array $regionsIds, $visibilityStatus)
    {
        switch ($visibilityStatus) {
            case Company::VISIBILITY_STATUS_ALL_CITIES:
                if (!$virtualRegionId = Region::getVirtualRegionIdForCountry($mainCountryId)) {
                    // для стран из дальнего зарубежья домерживаем Россию
                    $virtualRegionId = Region::VIRTUAL_REGION_ID_ALL_RUSSIA;
                }

                $regionsIds[] = $virtualRegionId;
                break;

            case Company::VISIBILITY_STATUS_OTHER_COUNTRIES:
                // если компания в России - домерживаем все включенные страны, кроме России
                $regionsIds = array_merge($regionsIds, array_diff(Region::getVirtualRegionsIds(), array(Region::getVirtualRegionIdForCountry($mainCountryId))));
                break;

            case Company::VISIBILITY_STATUS_ALL_COUNTRIES:
                $regionsIds = array_merge($regionsIds, Region::getVirtualRegionsIds());
                break;
        }

        return array_values(array_filter(array_unique($regionsIds)));
    }

    private function prepareCountriesIds($mainCountryId, array $countriesIds, $visibilityStatus)
    {
        // для стран из дальнего зарубежья добавляем Россию, что б их товары показывались бы на www
        if (!in_array($mainCountryId, Country::getEnabledCountriesIds())) {
            $countriesIds[] = Country::COUNTRY_ID_RUSSIA;
        }

        if (in_array($visibilityStatus, array(Company::VISIBILITY_STATUS_ALL_COUNTRIES, Company::VISIBILITY_STATUS_OTHER_COUNTRIES))) {
            $countriesIds = array_merge($countriesIds, Country::getEnabledCountriesIds());
        }

        return array_values(array_filter(array_unique($countriesIds)));
    }
}
