<?php

namespace Metal\TerritorialBundle\Service;

use Doctrine\ORM\EntityManager;

class CityService
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var array
     */
    private $loadedCitiesTitle;

    /**
     * @var array
     */
    private $loadedRegionsTitle;

    /**
     * @var array
     */
    private $loadedCities;

    /**
     * @var array
     */
    private $loadedRegions;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function findTerritory($title, $regionOther = null)
    {
        $wantedCity = $this->em->getRepository('MetalTerritorialBundle:City')->findOneBy(array('title' => $title));
        if ($wantedCity) {
            return $wantedCity;
        }

        if (!$title || strlen($title) <= 4) {
            return null;
        }

        $matches = null;
        $founds = array();
        $citiesTitle = $this->getCitiesTitle();

        foreach ($citiesTitle as $key => $cityTitle) {
            preg_match($this->preparePattern($cityTitle), $title, $matches);

            if ($matches) {
                $founds[$key] = $matches[0];
            }
        }

        if ($founds) {
            return $this->getCityById(key($this->sort($founds)));
        }

        if (!$regionOther || strlen($regionOther) <= 4) {
            return null;
        }

        $regionsTitle = $this->getRegionsTitle();

        $matches = null;
        $founds = array();
        foreach ($regionsTitle as $key => $regionTitle) {
            preg_match($this->preparePattern($regionTitle), $regionOther, $matches);
            if ($matches) {
                $founds[$key] = $matches[0];
            }
        }

        if ($founds) {
            return $this->getRegionById(key($this->sort($founds)))->getAdministrativeCenter();
        }

        return null;
    }

    private function preparePattern(array $titles)
    {
        unset($titles['id']);

        $titles = array_filter($titles);

        $patterns = array();
        foreach ($titles as $title) {
            if (strlen($title) < 4) {
                continue;
            }

            $patterns[] = preg_quote($title);
        }

        return sprintf(
            '/(%s)/ui',
            preg_replace('/[её]/ui', '[её]', implode('|', $patterns))
        );
    }

    private function getCitiesTitle()
    {
        if (!empty($this->loadedCitiesTitle)) {
            return $this->loadedCitiesTitle;
        }

        return $this->loadedCitiesTitle = $this->em->createQueryBuilder()
            ->from('MetalTerritorialBundle:City', 'city', 'city.id')
            ->select('city.title')
            ->addSelect('city.titleAccusative')
            ->addSelect('city.titleGenitive')
            ->addSelect('city.titleLocative')
            ->where('LENGTH(city.title) > 3')
            ->addSelect('city.id')
            ->getQuery()
            ->getArrayResult();
    }

    private function getRegionsTitle()
    {
        if (!empty($this->loadedRegionsTitle)) {
            return $this->loadedRegionsTitle;
        }

        return $this->loadedRegionsTitle = $this->em->createQueryBuilder()
            ->from('MetalTerritorialBundle:Region', 'region', 'region.id')
            ->select('region.title')
            ->addSelect('region.titleAccusative')
            ->addSelect('region.titleGenitive')
            ->addSelect('region.titleLocative')
            ->where('LENGTH(region.title) > 3')
            ->addSelect('region.id')
            ->getQuery()
            ->getArrayResult();
    }

    private function getRegionById($id)
    {
        if (!empty($this->loadedRegions[$id])) {
            return $this->loadedRegions[$id];
        }

        return $this->loadedRegions[$id] = $this->em->getRepository('MetalTerritorialBundle:Region')->find($id);
    }

    private function getCityById($id)
    {
        if (!empty($this->loadedCities[$id])) {
            return $this->loadedCities[$id];
        }

        return $this->loadedCities[$id] = $this->em->getRepository('MetalTerritorialBundle:City')->find($id);
    }

    private function sort(array $array = array())
    {
        uasort($array, function($a, $b) {
                return strlen($b) - strlen($a);
            });

        return $array;
    }
}
