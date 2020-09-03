<?php

namespace Metal\CategoriesBundle\Helper;

use Brouzie\Bundle\HelpersBundle\Helper\HelperAbstract;

use Doctrine\ORM\EntityManager;
use Metal\CategoriesBundle\Entity\Category;
use Metal\CategoriesBundle\Entity\LandingPage;
use Metal\TerritorialBundle\Entity\Country;

class DefaultHelper extends HelperAbstract
{
    public function implodeParameters($parameters, $exceptParameter)
    {
        $paramsSlugGroups = array();
        if ($parameters) {
            foreach ($parameters as $param) {
                if ($param['id'] != $exceptParameter['id']) {
                    $paramsSlugGroups[$param['parameterOption']['typeId']][] = $param['parameterOption']['slug'];
                }
            }
        }

        $resString = '';
        foreach ($paramsSlugGroups as $group) {
            if ($resString != '') {
                $resString .= '/';
            }
            $resString .= implode('_', $group);
        }

        return $resString;
    }

    public function getBaseHost(LandingPage $landingPage)
    {
        $em = $this->container->get('doctrine')->getManager();
        /* @var $em EntityManager */
        $hosts = array();
        $codes = Country::countriesCodes();
        $countryRepo = $em->getRepository('MetalTerritorialBundle:Country');
        foreach ($codes as $countryId => $code) {
            $getResults = 'getResults'.ucfirst($code);
            if ($landingPage->$getResults()) {
                $country = $countryRepo->find($countryId);
                $hosts[] = $country->getBaseHost();
            }
        }

        return $hosts;
    }

    public function getCategoryWithParamString(Category $category, $parameters, $exceptParameter = null)
    {
        return implode('/', array_filter(array($category->getSlugCombined(), $this->implodeParameters($parameters, $exceptParameter))));
    }

    /**
     * @param string $slug
     *
     * @return string
     */
    public function normalizeSlug($slug)
    {
        $slug = trim($slug);
        $slug = trim($slug, "\xC2\xA0\n");

        $slug = preg_replace('~,+~', '.', $slug);
        $slug = preg_replace('~[^-.a-zA-Z0-9]~', '-', $slug);

        // удаляем точки и дефисы по бокам строки
        $slug = preg_replace('~^[-.]+|[-.]+$~', '', $slug);

        // убираем дубли
        $slug = preg_replace('~\.{2,}~', '.', $slug);
        $slug = preg_replace('~-{2,}~', '-', $slug);

        return $slug;
    }
}
