<?php

namespace Metal\TerritorialBundle\DataFetching\Spec;

use Metal\TerritorialBundle\Entity\City;
use Metal\TerritorialBundle\Entity\Country;
use Metal\TerritorialBundle\Entity\Region;
use Metal\TerritorialBundle\Entity\TerritoryInterface;

trait TerritorialSpec
{
    public $cityId;

    public $regionId;

    public $countryId;

    public function cityId($cityId)
    {
        $this->cityId = $cityId;

        return $this;
    }

    public function city(City $city = null)
    {
        if ($city) {
            $this->cityId($city->getId());
            $this->country($city->getCountry());
        }

        return $this;
    }

    public function regionId($regionId)
    {
        $this->regionId = $regionId;

        return $this;
    }

    public function region(Region $region = null)
    {
        if ($region) {
            $this->regionId($region->getId());
            $this->country($region->getCountry());
        }

        return $this;
    }

    public function countryId($countryId)
    {
        $this->countryId = $countryId;

        return $this;
    }

    public function country(Country $country)
    {
        $this->countryId($country->getId());

        return $this;
    }

    public function territory(TerritoryInterface $territory)
    {
        if ($territory instanceof City) {
            $this->city($territory);
        } elseif ($territory instanceof Region) {
            $this->region($territory);
        } else {
            $this->country($territory);
        }

        return $this;
    }
}
