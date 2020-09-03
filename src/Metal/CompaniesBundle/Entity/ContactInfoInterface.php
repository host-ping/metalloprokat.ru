<?php

namespace Metal\CompaniesBundle\Entity;

use Metal\TerritorialBundle\Entity\City;

interface ContactInfoInterface
{
    /**
     * @return City
     */
    public function getCity();

    public function getAddress();

    public function getPhonesAsString();

    public function getSite();

    public function getLatitude();

    public function getLongitude();

    public function isContactsShouldBeVisible();
}
