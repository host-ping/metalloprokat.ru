<?php

namespace Metal\CatalogBundle\Indexer;

use Brouzie\Sphinxy\Indexer\DoctrineQbIndexer;

class CatalogProductIndexer extends DoctrineQbIndexer
{
    public function processItems(array $items)
    {
        $productsIds = array_column($items, 'product_id');

        $territoriesToProduct = $this->em->getRepository('MetalCatalogBundle:ProductCity')
            ->getTerritoriesIdsForProducts($productsIds);

        $categoriesToProduct = $this->em->getRepository('MetalCatalogBundle:Product')
            ->getCategoriesIdsForProducts($productsIds);

        $productToAttributes = $this->em->getRepository('MetalCatalogBundle:ProductAttributeValue')
            ->getProductsAttributes($productsIds);

        foreach ($items as $i => $productRow) {
            $id = $productRow['product_id'];

            $title = trim($productRow['product_title'].' '.implode(', ', array_column($productToAttributes[$id], 'attribute_value_title')));

            $items[$i] = array(
                'id' => $id,
                'brand_id' => $productRow['brand_id'],
                'manufacturer_id' => $productRow['manufacturer_id'],
                'category_id' => $productRow['category_id'],
                'cities_ids' => isset($territoriesToProduct[$id]['cities_ids']) ? $territoriesToProduct[$id]['cities_ids'] : array(),
                'regions_ids' => isset($territoriesToProduct[$id]['regions_ids']) ? $territoriesToProduct[$id]['regions_ids'] : array(),
                'countries_ids' => isset($territoriesToProduct[$id]['countries_ids']) ? $territoriesToProduct[$id]['countries_ids'] : array(),
                'categories_ids' => $categoriesToProduct[$id],
                'attributes' => json_encode(array_column($productToAttributes[$id], 'attribute_value_id', 'attribute_id')),
                'title' => $title,
                'title_field' => $title,
                'brand_title' => $productRow['brand_title'],
                'brand_title_field' => $productRow['brand_title'],
                'manufacturer_title' => $productRow['manufacturer_title'],
                'manufacturer_title_field' => $productRow['manufacturer_title'],
            );
        }

        return $items;
    }

    protected function getQueryBuilder()
    {
        return $this->em
            ->createQueryBuilder()
            ->from('MetalCatalogBundle:Product', 'e')
            ->select('e.id AS product_id')
            ->join('e.brand', 'b')
            ->join('e.manufacturer', 'm')
            ->addSelect('b.id AS brand_id')
            ->addSelect('m.id AS manufacturer_id')
            ->addSelect('IDENTITY(e.category) AS category_id')
            ->addSelect('e.title AS product_title')
            ->addSelect('b.value AS brand_title')
            ->addSelect('m.value AS manufacturer_title');
    }
}
