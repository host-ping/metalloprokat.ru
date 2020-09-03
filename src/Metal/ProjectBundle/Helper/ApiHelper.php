<?php

namespace Metal\ProjectBundle\Helper;

use Brouzie\Bundle\HelpersBundle\Helper\HelperAbstract;
use Doctrine\ORM\EntityManager;
use Metal\CategoriesBundle\Entity\Category;
use Metal\CategoriesBundle\Entity\LandingPage;
use Metal\TerritorialBundle\Entity\City;
use Metal\TerritorialBundle\Entity\Country;
use Metal\TerritorialBundle\Entity\TerritoryInterface;

class ApiHelper extends HelperAbstract
{
    #FIXME: delete-after-merge-facets
    public function getTerritorialStructure(
        array $citiesIds,
        $route,
        Country $country,
        TerritoryInterface $activeTerritory = null,
        Category $category = null,
        array $filterParameters = array(),
        array $allowCitiesIds = array(),
        array $allowRegionsIds = array(),
        $mainPageRoute
    ) {
        $em = $this->container->get('doctrine')->getManager();
        /* @var $em EntityManager */
        $router = $this->container->get('router');

        $citiesData = $em
            ->createQueryBuilder()
            ->select('
                    c.id AS cityId, 
                    c.title AS cityTitle, 
                    c.population, 
                    c.slug AS citySlug, 
                    r.id AS regionId, 
                    r.title AS regionTitle, 
                    IDENTITY(c.administrativeCenter) AS administrativeCenterId,
                    CASE WHEN (c.country <> c.displayInCountry) THEN TRUE ELSE FALSE END AS toOther
                ')
            ->from('MetalTerritorialBundle:City', 'c', 'c.id')
            ->join('c.region', 'r')
            ->where('c.id IN (:cities_ids)')
            ->setParameter('cities_ids', array_keys($citiesIds))
            ->addOrderBy('r.title', 'ASC')
            ->addOrderBy('c.title', 'ASC')
            ->andWhere('c.displayInCountry = :country')
            ->setParameter('country', $country)
            ->getQuery()
            ->getResult()
        ;

        $regions = array();
        $citiesToUrls = array();
        $parametersToRegion = array();
        $parametersToCity = array();
        $parametersToActiveTerritory = $filterParameters;
        $parametersToActiveTerritory['subdomain'] = 'www';

        if ($category) {
            foreach ($filterParameters as $key => $filterParameter) {
                $parametersToRegion[$key] = $filterParameter;
                $parametersToCity[$key] = $filterParameter;
            }
        }

        $regionsToCities = array();
        $regionsUrl = array();
        foreach ($citiesData as $cityId => $cityData) {

            $regionIdFromQb = $cityData['regionId'];
            $regionTitle = $cityData['regionTitle'];
            if ($cityData['toOther']) {
                $regionIdFromQb = 'other';
                $regionTitle = 'Города дальнего зарубежья';
            }

            $parametersToRegion['subdomain'] = $regionIdFromQb;
            $parametersToCity['subdomain'] = $cityData['citySlug'];

            if ($parametersToCity['subdomain']) {
                $citiesToUrls[$cityData['cityId']] = $this->generateUrl(
                    $cityData['cityId'],
                    $route,
                    $parametersToCity,
                    $allowCitiesIds,
                    $mainPageRoute
                );
            }

            if ($allowRegionsIds && isset($parametersToRegion['category_slug'])) {
                $regionsUrl[$regionIdFromQb] = $this->generateUrl(
                    $regionIdFromQb,
                    $route,
                    $parametersToRegion,
                    $allowRegionsIds,
                    $mainPageRoute
                );
            } else {
                $regionsUrl[$regionIdFromQb] = $router->generate($route, $parametersToRegion, true);
            }

            $regions[$regionIdFromQb] = array(
                'id' => $regionIdFromQb,
                'title' => $regionTitle,
                'url' => $regionsUrl[$regionIdFromQb]
            );

            $regionsToCities[$regionIdFromQb][] = $cityId;
        }

        $preferredCities = array();
        $territorialStructure = array();

        $activeTerritoryStructure = null;
        if ($activeTerritory) {
            $activeTerritoryStructure = array(
                'title' => $activeTerritory->getTitle(),
                'kind' => $activeTerritory->getKind(),
            );
        }

        $countryTerritoryStructure = array(
            'title' => 'Все города',
            'kind' => 'country',
            'url' => $router->generate($route, $parametersToActiveTerritory, true),
        );

        $otherCitiesRegion = array();
        foreach ($regions as $regionId => $region) {
            $counter = 0;

            foreach ($regionsToCities[$regionId] as $cityId) {
                if (empty($citiesToUrls[$cityId])) {
                    $region['cities'][] = array(
                        'id' => $cityId,
                        'title' => $citiesData[$cityId]['cityTitle']
                    );
                    $counter++;
                } else {
                    $city = array(
                        'id' => $cityId,
                        'title' => $citiesData[$cityId]['cityTitle'],
                        'url' => $citiesToUrls[$cityId],
                    );

                    if ($citiesData[$cityId]['administrativeCenterId'] == $cityId) {
                        $city['isAdministrativeCenter'] = true;
                    }

                    $region['cities'][] = $city;

                    if (City::checkIsPrimary($country->getId(), $citiesData[$cityId]['population'])) {
                        $preferredCities[] = $city;
                    }
                }
            }

            if (count($region['cities']) == $counter) {
                $region['all_cities_without_url'] = true;
            }

            if ($region['id'] == 'other') {
                $otherCitiesRegion = $region;
            } else {
                $territorialStructure[] = $region;
            }
        }

        if (isset($otherCitiesRegion['cities'])) {
            usort(
                $otherCitiesRegion['cities'],
                function ($a, $b) {
                    return strcmp($a['title'], $b['title']);
                }
            );

            $territorialStructure[] = $otherCitiesRegion;
        }

        return array(
            'regions' => $territorialStructure,
            'preferredCities' => $preferredCities,
            'activeTerritory' => $activeTerritoryStructure,
            'countryTerritory' => $countryTerritoryStructure
        );
    }

    private function generateUrl($id, $route, $parameters, array $allowIds = array(), $mainPageRoute)
    {
        $router = $this->container->get('router');
        if ($allowIds && isset($parameters['category_slug'])) {
            if (isset($allowIds[$id])) {
                if (isset($allowIds[$id]['full'])) {
                    return $router->generate($route, $parameters, true);
                }

                if (isset($allowIds[$id]['only_category'])) {
                    return $router->generate(
                        $route,
                        array('category_slug' => $parameters['category_slug'], 'subdomain' => $parameters['subdomain']),
                        true
                    );
                }
            } else {
                return $router->generate($mainPageRoute, array('subdomain' => $parameters['subdomain']), true);
            }
        }

        return $router->generate($route, $parameters, true);
    }

    #FIXME: delete-after-merge-facets
    /**
     * @param $citiesIds[]
     * @param string $route
     * @param Country $country
     * @param TerritoryInterface $activeTerritory
     * @param Category|LandingPage $object
     * @return string
     */
    public function getTerritorialStructureNew(
        array $citiesIds,
        $route,
        Country $country,
        TerritoryInterface $activeTerritory = null,
        $object = null,
        array $filterParameters = array()
    ) {
        $em = $this->container->get('doctrine')->getManager();
        /* @var $em EntityManager */
        $router = $this->container->get('router');

        $citiesData = $em
            ->createQueryBuilder()
            ->select('
                    c.id AS cityId, 
                    c.title AS cityTitle, 
                    c.population, 
                    c.slug AS citySlug, 
                    r.id AS regionId, 
                    r.title AS regionTitle, 
                    IDENTITY(c.administrativeCenter) AS administrativeCenterId,
                    CASE WHEN (c.country <> c.displayInCountry) THEN TRUE ELSE FALSE END AS toOther
            ')
            ->from('MetalTerritorialBundle:City', 'c', 'c.id')
            ->join('c.region', 'r')
            ->where('c.id IN (:cities_ids)')
            ->setParameter('cities_ids', $citiesIds)
            ->andWhere('c.displayInCountry = :country')
            ->setParameter('country', $country)
            ->orderBy('r.title', 'ASC')
            ->addOrderBy('c.title', 'ASC')
            ->getQuery()
            ->getResult();

        $regions = array();
        $citiesToUrls = array();
        $parametersToRegion = array();
        $parametersToCity = array();
        $parametersToActiveTerritory = $filterParameters;
        $parametersToActiveTerritory['subdomain'] = 'www';

        if ($object) {
            foreach ($filterParameters as $key => $filterParameter) {
                $parametersToRegion[$key] = $filterParameter;
                $parametersToCity[$key] = $filterParameter;
            }
        }

        $regionsToCities = array();
        $regionsUrl = array();
        foreach ($citiesData as $cityId => $cityData) {
            $regionIdFromQb = $cityData['regionId'];
            $regionTitle = $cityData['regionTitle'];
            if ($cityData['toOther']) {
                $regionIdFromQb = 'other';
                $regionTitle = 'Города дальнего зарубежья';
            }

            $parametersToRegion['subdomain'] = $regionIdFromQb;
            $parametersToCity['subdomain'] = $cityData['citySlug'];

            if ($parametersToCity['subdomain']) {
                $citiesToUrls[$cityData['cityId']] = $router->generate($route, $parametersToCity, true);
            }

            $regionsUrl[$regionIdFromQb] = $router->generate($route, $parametersToRegion, true);

            $regions[$regionIdFromQb] = array(
                'id' => $regionIdFromQb,
                'title' => $regionTitle,
                'url' => $regionsUrl[$regionIdFromQb]
            );

            $regionsToCities[$regionIdFromQb][] = $cityId;
        }

        $preferredCities = array();
        $territorialStructure = array();

        $activeTerritoryStructure = null;
        if ($activeTerritory) {
            $activeTerritoryStructure = array(
                'title' => $activeTerritory->getTitle(),
                'kind' => $activeTerritory->getKind(),
            );
        }

        $countryTerritoryStructure = array(
            'title' => 'Все города',
            'kind' => 'country',
            'url' => $router->generate($route, $parametersToActiveTerritory, true),
        );

        $otherCitiesRegion = array();
        foreach ($regions as $regionId => $region) {
            $counter = 0;
            foreach ($regionsToCities[$regionId] as $cityId) {
                if (empty($citiesToUrls[$cityId])) {
                    $region['cities'][] = array(
                        'id' => $cityId,
                        'title' => $citiesData[$cityId]['cityTitle']
                    );
                    $counter++;
                } else {
                    $city = array(
                        'id' => $cityId,
                        'title' => $citiesData[$cityId]['cityTitle'],
                        'url' => $citiesToUrls[$cityId],
                    );

                    if ($citiesData[$cityId]['administrativeCenterId'] == $cityId) {
                        $city['isAdministrativeCenter'] = true;
                    }

                    $region['cities'][] = $city;

                    if (City::checkIsPrimary($country->getId(), $citiesData[$cityId]['population'])) {
                        $preferredCities[] = $city;
                    }
                }
            }

            if (count($region['cities']) == $counter) {
                $region['all_cities_without_url'] = true;
            }

            if ($region['id'] == 'other') {
                $otherCitiesRegion = $region;
            } else {
                $territorialStructure[] = $region;
            }
        }

        if (isset($otherCitiesRegion['cities'])) {
            usort(
                $otherCitiesRegion['cities'],
                function ($a, $b) {
                    return strcmp($a['title'], $b['title']);
                }
            );

            $territorialStructure[] = $otherCitiesRegion;
        }

        return array(
            'regions' => $territorialStructure,
            'preferredCities' => $preferredCities,
            'activeTerritory' => $activeTerritoryStructure,
            'countryTerritory' => $countryTerritoryStructure
        );
    }
}
