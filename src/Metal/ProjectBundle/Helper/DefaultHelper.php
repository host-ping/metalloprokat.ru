<?php

namespace Metal\ProjectBundle\Helper;

use Brouzie\Bundle\HelpersBundle\Helper\HelperAbstract;
use Metal\CompaniesBundle\Entity\Company;
use Metal\ProductsBundle\Entity\Product;
use Metal\ContentBundle\Entity\Category;
use Metal\TerritorialBundle\Entity\City;
use Metal\TerritorialBundle\Entity\Country;
use Metal\TerritorialBundle\Entity\Region;
use Metal\TerritorialBundle\Entity\TerritoryInterface;

class DefaultHelper extends HelperAbstract
{
    public function isWorkingTime()
    {
        return date('H:i') > '09:00' && date('H:i') < '18:00' && date('w') < 6;
    }

    public function isNewYearEve()
    {
        $now = new \DateTime();
        $thisYearStart = \DateTime::createFromFormat('Y-m-d', sprintf('%d-12-10', date('Y')));
        $thisYearFinish = \DateTime::createFromFormat('Y-m-d', sprintf('%d-01-14', date('Y')));

        return $now > $thisYearStart || $now < $thisYearFinish;
    }

    public function getLogoMode()
    {
        if ($this->isNewYearEve()) {
            return 'newyear';
        }

        return 'default';
    }

    public function getLogo()
    {
        $logos = $this->container->getParameter('project.logos');
        $logoMode = $this->getLogoMode();

        return isset($logos[$logoMode]) ? $logos[$logoMode] : $logos['default'];
    }

    public static function isRefererFromSearch($referer)
    {
        if (!$referer) {
            return false;
        }

        $searchSites = array(
            'msn.com',
            'yandex.',
            'google.',
            'yahoo.net',
            'sputnik.ru',
            'mail.ru',
            '1adm.ru',
        );

        $searchSites = array_map('preg_quote', $searchSites);

        return preg_match('#'.implode('|', $searchSites).'#', $referer);
    }

    /**
     * @return Country|null
     */
    public function getCurrentCountry()
    {
        return $this->container->get('request_stack')->getMasterRequest()->attributes->get('country');
    }

    /**
     * @return TerritoryInterface
     */
    public function getCurrentTerritory()
    {
        return $this->container->get('request_stack')->getMasterRequest()->attributes->get('territory');
    }

    /**
     * @return City|null
     */
    public function getCurrentCity()
    {
        return $this->container->get('request_stack')->getMasterRequest()->attributes->get('city');
    }

    /**
     * @return Region|null
     */
    public function getCurrentRegion()
    {
        return $this->container->get('request_stack')->getMasterRequest()->attributes->get('region');
    }

    /**
     * @return Category|null
     */
    public function getCurrentCategory()
    {
        return $this->container->get('request_stack')->getMasterRequest()->attributes->get('category');
    }

    /**
     * @return Company|null
     */
    public function getCurrentCompany()
    {
        return $this->container->get('request_stack')->getMasterRequest()->attributes->get('company');
    }

    /**
     * @return Product|null
     */
    public function getCurrentProduct()
    {
        return $this->container->get('request_stack')->getMasterRequest()->attributes->get('product');
    }
}
