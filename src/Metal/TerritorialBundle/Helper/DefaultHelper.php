<?php

namespace Metal\TerritorialBundle\Helper;

use Brouzie\Bundle\HelpersBundle\Helper\HelperAbstract;
use Metal\ProjectBundle\Helper\CounterHelper;
use Metal\TerritorialBundle\Entity\Country;

class DefaultHelper extends HelperAbstract
{
    public function getCountries()
    {
        $em = $this->container->get('doctrine')->getManager();
        /* @var $em \Doctrine\ORM\EntityManager */

        $countryRepo = $em->getRepository('MetalTerritorialBundle:Country');
        $countries = $countryRepo->createQueryBuilder('c')
            ->select('c')
            ->andWhere('c.id IN (:enabled_countries_ids)')
            ->setParameter('enabled_countries_ids', Country::getEnabledCountriesIds())
            ->getQuery()
            ->getResult();

        return $countries;
    }

    public function getHeaderCities($itemsType, $criteria, array $indexRows)
    {
        $allowCitiesIds = array();
        $allowRegionsIds = array();

        $counterHelper = $this->container->get('brouzie.helper_factory')->get('MetalProjectBundle:Counter');
        /* @var $counterHelper CounterHelper */

        $allowCitiesIdsFullCriteria = $counterHelper->getItemsCountPerObject($itemsType, $criteria, $indexRows['city']);
        $allowRegionsIdsFullCriteria = $counterHelper->getItemsCountPerObject($itemsType, $criteria, $indexRows['region']);

        foreach ($allowCitiesIdsFullCriteria as $cityId => $allowCityIdFullCriteria) {
            $allowCitiesIds[$cityId] = array('full' => true);
        }

        foreach ($allowRegionsIdsFullCriteria as $regionId => $allowRegionIdFullCriteria) {
            $allowRegionsIds[$regionId] = array('full' => true);
        }

        if (isset($criteria['categories_ids'])) {
            $allowCitiesIdsModifyCriteria = $counterHelper->getItemsCountPerObject($itemsType, array('categories_ids' => $criteria['categories_ids']), $indexRows['city']);
            $allowRegionsIdsModifyCriteria = $counterHelper->getItemsCountPerObject($itemsType, array('categories_ids' => $criteria['categories_ids']), $indexRows['region']);
            foreach ($allowCitiesIdsModifyCriteria as $cityId => $allowCityIdModifyCriteria) {
                if (!isset($allowCitiesIds[$cityId])) {
                    $allowCitiesIds[$cityId] = array('only_category' => true);
                }
            }

            foreach ($allowRegionsIdsModifyCriteria as $regionId => $allowRegionIdModifyCriteria) {
                if (!isset($allowRegionsIds[$regionId])) {
                    $allowRegionsIds[$regionId] = array('only_category' => true);
                }
            }
        }

        return array($allowCitiesIds, $allowRegionsIds);
    }

    public function getCitiesWithSlug(Country $country)
    {
        $em = $this->container->get('doctrine')->getManager();
        /* @var $em \Doctrine\ORM\EntityManager */

        $citiesIds = array();
        $cityRepo = $em->getRepository('MetalTerritorialBundle:City');
        $cities = $cityRepo->createQueryBuilder('c')
            ->select('c')
            ->andWhere('c.country IN (:enabled_countries_ids)')
            ->andWhere('c.slug IS NOT NULL')
            ->setParameter('enabled_countries_ids', $country->getId())
            ->getQuery()
            ->getResult();
        foreach($cities as $city) {
            $citiesIds[$city->getId()] = 1;
        }

        return $citiesIds;
    }
}
