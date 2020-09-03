<?php

namespace Metal\CategoriesBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Metal\CategoriesBundle\Entity\LandingPage;

class LandingPageAttributeValueRepository extends EntityRepository
{
    /**
     * @param LandingPage[] $landingPages
     */
    public function attachAttributesForLandingPages($landingPages)
    {
        foreach ($landingPages as $landingPage) {
            $landingPage->setAttribute('landing_page_attributes', array());
        }

        $attributes = $this->loadAttributesForLandingPages($landingPages);

        foreach ($landingPages as $landingPage) {
            $landingPage->setAttribute(
                'landing_page_attributes',
                isset($attributes[$landingPage->getId()]) ? $attributes[$landingPage->getId()] : array()
            );
        }
    }

    /**
     * @param LandingPage[] $landingPages
     * @return array
     */
    public function loadAttributesForLandingPages(array $landingPages)
    {
        $attributesPerLandingPage = array();
        foreach ($landingPages as $landingPage) {
            $attributesPerLandingPage[$landingPage->getId()] = array();
        }

        //TODO: возможно нужно производителя и бренда как то выделять, как в \Metal\CatalogBundle\Repository\ProductAttributeValueRepository::loadAttributesForProducts

        $attributes = $this->_em
            ->createQueryBuilder()
            ->from('MetalCategoriesBundle:LandingPageAttributeValue', 'lpav')
            ->join('lpav.attributeValue', 'av')
            ->join('av.attribute', 'a')
            ->addSelect('IDENTITY(lpav.landingPage) AS landingPageId')
            ->addSelect('av.value AS attributeValueTitle')
            ->addSelect('a.title AS attributeTitle')
            ->addSelect('a.code AS code')
            ->addOrderBy('a.outputPriority')
            ->addOrderBy('av.outputPriority')
            ->andWhere('lpav.landingPage IN (:landing_pages)')
            ->setParameter('landing_pages', $landingPages)
            ->getQuery()
            ->getArrayResult();


        foreach ($attributes as $attribute) {
            $attributesPerLandingPage[$attribute['landingPageId']][] = $attribute;
        }

        return $attributesPerLandingPage;
    }
}
