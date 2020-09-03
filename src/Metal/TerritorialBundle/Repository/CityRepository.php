<?php

namespace Metal\TerritorialBundle\Repository;

use Metal\ProjectBundle\DataFetching\Sphinxy\FacetResultExtractor;
use Metal\ProjectBundle\Doctrine\EntityRepository;
use Metal\TerritorialBundle\Entity\City;
use Metal\TerritorialBundle\Entity\Country;

class CityRepository extends EntityRepository
{
    /**
     * @param int $country
     * @param array $orderBy key - column, value - direction
     *
     * @return City[]
     */
    public function getCitiesWithSlug($country, array $orderBy = array(), $limit = null)
    {
        $citiesQb = $this->_em
            ->createQueryBuilder()
            ->select('c')
            ->from('MetalTerritorialBundle:City', 'c', 'c.id')
            ->andWhere('c.slug IS NOT NULL');

        if ($country) {
            $citiesQb
                ->andWhere('c.country = :country')
                ->setParameter('country', $country);
        }

        foreach ($orderBy as $order => $dir) {
            if ($order === 'rand') {
                if ($dir === true) {
                    $citiesQb
                        ->addSelect('RAND() AS HIDDEN r')
                        ->addOrderBy('r');
                } else {
                    $citiesQb
                        ->addSelect('RAND(:rand) AS HIDDEN r')
                        ->setParameter('rand', $dir)
                        ->addOrderBy('r');
                }

                continue;
            }

            $citiesQb->addOrderBy('c.'.$order, $dir);
        }

        return $citiesQb
            ->getQuery()
            ->setMaxResults($limit)
            ->getResult();
    }

    /**
     * @param $city
     *
     * @return City[]
     */
    public function getLinkedCities($city)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.administrativeCenter = :admin_center')
            ->setParameter('admin_center', $city)
            ->andWhere('c.slug IS NULL')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param City $city
     *
     * @return City[]
     */
    public function getWithLinkedCities(City $city)
    {
        $cities = array();
        if ($city->isAdministrativeCenter()) {
            $cities = $this->getLinkedCities($city);
        }
        $cities[] = $city;

        return $cities;
    }

    /**
     * @param \Metal\ProjectBundle\DataFetching\Sphinxy\FacetResultExtractor $facetResultExtractor
     *
     * @return City[]
     */
    public function loadByFacetResult(FacetResultExtractor $facetResultExtractor)
    {
        return $this->findByIds($facetResultExtractor->getIds(), true);
    }

    public function getSimpleCitiesArray()
    {
        $rows = $this->createQueryBuilder('cities')
            ->select('cities.id AS cityId')
            ->addSelect('cities.title AS cityTitle')
            ->addSelect('country.id AS countryId')
            ->addSelect('country.title AS countryTitle')
            ->join('cities.country', 'country')
            ->andWhere('cities.country IN (:countriesIds)')
            ->setParameter('countriesIds', Country::getEnabledCountriesIds())
            ->getQuery()
            ->getArrayResult()
        ;

        $cities = array();
        foreach ($rows as $row) {
            if (!isset($cities[$row['countryTitle']])) {
                $cities[$row['countryTitle']] = array();
            }

            $cities[$row['countryTitle']][$row['cityId']] = $row['cityTitle'];
        }

        return $cities;
    }

    public function getAdministrativeCentresForCitiesWithoutSlug()
    {
        $cities = $this
            ->createQueryBuilder('c')
            ->select('c.id')
            ->addSelect('IDENTITY(c.administrativeCenter) AS administrative_center_id')
            ->where('c.slug IS NULL')
            ->getQuery()
            ->getResult();

        return array_column($cities, 'administrative_center_id', 'id');
    }

    public function getSlugsForCountry($country)
    {
        $citiesSlugs = $this->_em->createQueryBuilder()
            ->from('MetalTerritorialBundle:City', 'c', 'c.slug')
            ->select('c.slug')
            ->where('c.slug IS NOT NULL')
            ->andWhere('c.displayInCountry = :country')
            ->setParameter('country', $country)
            ->getQuery()
            ->getResult();

        return array_keys($citiesSlugs);
    }
}
