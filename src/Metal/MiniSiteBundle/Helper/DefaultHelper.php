<?php

namespace Metal\MiniSiteBundle\Helper;

use Brouzie\Bundle\HelpersBundle\Helper\HelperAbstract;

use Metal\CompaniesBundle\Entity\Company;
use Metal\CompaniesBundle\Entity\CompanyCity;
use Metal\CompaniesBundle\Entity\ContactInfoInterface;
use Metal\TerritorialBundle\Entity\City;

class DefaultHelper extends HelperAbstract
{
    const MINISITE_CITY_COOKIE = 'minisite_city_id';

    private $currentCity;

    private $currentBranchOffice;

    /**
     * @return City|null
     */
    public function getCurrentCity()
    {
        if (null !== $this->currentCity) {
            // false -> null
            return $this->currentCity ?: null;
        }

        $request = $this->getRequest();
        $cityId = $request->query->get('city', $request->cookies->get(self::MINISITE_CITY_COOKIE));

        if (!$cityId) {
            $this->currentCity = false;

            return null;
        }

        return $this->currentCity = $this->container->get('doctrine.orm.default_entity_manager')->find('MetalTerritorialBundle:City', $cityId);
    }

    public function getAnalyticsOptions(Company $company)
    {
        $minisiteConfig = $company->getMinisiteConfig();

        return array('googleAnalyticsId' => $minisiteConfig->getGoogleAnalyticsId(), 'yandexMetrikaId' => $minisiteConfig->getYandexMetrikaId());
    }

    /**
     * @param Company $company
     *
     * @return ContactInfoInterface
     */
    public function getCurrentBranchOffice(Company $company)
    {
        if (null !== $this->currentBranchOffice) {
            return $this->currentBranchOffice;
        }

        $request = $this->getRequest();
        $cityId = $request->query->get('city', $request->cookies->get(self::MINISITE_CITY_COOKIE));

        $companyCity = $this->container->get('doctrine.orm.default_entity_manager')
            ->getRepository('MetalCompaniesBundle:CompanyCity')
            ->findOneBy(array('company' => $company, 'city' => $cityId, 'enabled' => true));
        /* @var $companyCity CompanyCity */

        // если у филиала центрального офиса пробит нет телефона - отображаем телефон из компании
        if ($companyCity && $companyCity->getCity()->getId() === $company->getCity()->getId() && !$companyCity->getPhonesAsString()) {
            return $this->currentBranchOffice = $company;
        }

        if ($companyCity) {
            return $this->currentBranchOffice = $companyCity;
        }

        return $this->currentBranchOffice = $company;
    }

    public function getPhonesForCurrentCity(Company $company)
    {
        $city = $this->getCurrentCity();

        if (!$city || !$company->getHasTerritorialRules()) {
            return $this->getCurrentBranchOffice($company)->getPhonesAsString();
        }

        return $this->container->get('doctrine')
            ->getRepository('MetalCompaniesBundle:CompanyPhone')
            ->getPhonesForCompanyInTerritory($company, $city);
    }
}
