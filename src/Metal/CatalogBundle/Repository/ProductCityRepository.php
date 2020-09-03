<?php

namespace Metal\CatalogBundle\Repository;

use Doctrine\ORM\EntityRepository;

class ProductCityRepository extends EntityRepository
{
    /**
     * @param array $productsIds
     *
     * @return array [productId => [[cities_ids => [cityId, cityId, ...]], [regions_ids => [cityId, cityId, ...]], [countries_ids => [cityId, cityId, ...]]]]
     */
    public function getTerritoriesIdsForProducts(array $productsIds = array())
    {
        if (!$productsIds) {
            return array();
        }

        $productToTerritories = array_fill_keys($productsIds, array());

        $productTerritories = $this->_em->getRepository('MetalCatalogBundle:ProductCity')
            ->createQueryBuilder('pc')
            ->select('IDENTITY(pc.product) AS productId')
            ->join('pc.city', 'city')
            ->addSelect('city.id AS cityId')
            ->addSelect('IDENTITY(city.region) AS regionId')
            ->addSelect('IDENTITY(city.country) AS countryId')
            ->where('pc.product IN (:products_ids)')
            ->setParameter('products_ids', $productsIds)
            ->getQuery()
            ->getArrayResult();

        foreach ($productTerritories as $productTerritory) {
            $productToTerritories[$productTerritory['productId']]['cities_ids'][] = $productTerritory['cityId'];
            $productToTerritories[$productTerritory['productId']]['regions_ids'][] = $productTerritory['regionId'];
            $productToTerritories[$productTerritory['productId']]['countries_ids'][] = $productTerritory['countryId'];
        }

        return $productToTerritories;
    }
}