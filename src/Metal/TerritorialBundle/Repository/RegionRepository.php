<?php

namespace Metal\TerritorialBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Metal\TerritorialBundle\Entity\City;

class RegionRepository extends EntityRepository
{
    public function getRegionsWithCities($country = null)
    {
        $cities = $this->getCitiesByCountry($country);

        return $this->getRegionsByCities($cities);
    }

    public function getCitiesByCountry($country = null)
    {
        $citiesQb = $this->_em
            ->getRepository('MetalTerritorialBundle:City')
            ->createQueryBuilder('c')
            ->join('c.region', 'cr')
            ->addSelect('cr')
            ->andWhere('c.slug IS NOT NULL')
            ->addOrderBy('cr.title', 'ASC')
            ->addOrderBy('c.title', 'ASC');

        if ($country) {
            $citiesQb
                ->andWhere('cr.country = :country_id')
                ->setParameter('country_id', $country);
        } else {
            $citiesQb->andWhere('cr.country IS NOT NULL');
        }

        return $citiesQb
            ->getQuery()
            ->getResult();
    }

    /**
     * @return array region_id => (cities_ids)
     */
    public function getCitiesByRegions()
    {
        $citiesWithRegions = $this->getCitiesByCountry();
        /* @var $citiesWithRegions City[] */
        $regionsData = array();
        foreach ($citiesWithRegions as $city) {
            if ($city->getRegion()->getCountry()) {
                $regionsData[$city->getRegion()->getId()][] = $city->getId();
            }
        }

        return $regionsData;
    }

    /**
     * @param City[] $cities
     *
     * @return array
     */
    public function getRegionsByCities($cities)
    {
        $regionsData = array();
        foreach ($cities as $_city) {
            if ($_city->getRegion()->getCountry()) {
                $regionId = $_city->getRegion()->getId();
                $countryName = $_city->getRegion()->getCountry()->getId();
                if (!isset($regionsData[$countryName])) {
                    $regionsData[$countryName] = array();
                }

                if (!isset($regionsData[$countryName][$regionId])) {
                    $regionsData[$countryName][$regionId] = array(
                        'region' => $_city->getRegion(),
                        'cities' => array(),
                    );
                }
                $regionsData[$countryName][$regionId]['cities'][] = $_city;
            }
        }

        return $regionsData;
    }

    public function getSlugsForCountry($country)
    {
        $regoinsSlugs = $this->_em->createQueryBuilder()
            ->from('MetalTerritorialBundle:Region', 'r', 'r.id')
            ->select('r.id')
            ->andWhere('r.country = :country')
            ->setParameter('country', $country)
            ->getQuery()
            ->getResult();

        return array_keys($regoinsSlugs);
    }
}
