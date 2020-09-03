<?php

namespace Metal\CatalogBundle\Repository;

use Metal\CatalogBundle\Entity\Brand;
use Metal\CatalogBundle\Entity\Product;
use Metal\ProjectBundle\DataFetching\Sphinxy\FacetResultExtractor;
use Metal\ProjectBundle\Doctrine\EntityRepository;

class BrandRepository extends EntityRepository
{
    public function loadBrandBySlug($slug)
    {
        $brand = $this
            ->createQueryBuilder('b')
            ->andWhere('b.slug = :slug')
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getOneOrNullResult();

        return $brand;
    }

    /**
     * @param Brand[] $brands
     * @param array $productsIds
     */
    public function attachProductsToBrands($brands, array $productsIds)
    {
        if (!count($brands)) {
            return;
        }

        $products = $this->_em
            ->getRepository('MetalCatalogBundle:Product')
            ->findBy(array('id' => $productsIds));
        /* @var $products Product[] */

        $productsPerBrand = array();
        foreach ($products as $product) {
            $productsPerBrand[$product->getBrand()->getId()][] = $product;
        }

        foreach ($brands as $brand) {
            $id = $brand->getId();
            $brand->setAttribute(
                'brand_products',
                isset($productsPerBrand[$id]) ? $productsPerBrand[$id] : array()
            );
        }
    }

    /**
     * @param $manufacturer
     *
     * @return Brand[]
     */
    public function getBrandsByManufacturer($manufacturer)
    {
        $brandsRepository = $this->_em->getRepository('MetalCatalogBundle:Brand');

        $brandsIds = $this->_em->createQueryBuilder()
            ->select('IDENTITY(p.brand) AS brandId')
            ->from('MetalCatalogBundle:Product', 'p')
            ->where('p.manufacturer = :manufacturer')
            ->setParameter('manufacturer', $manufacturer)
            ->groupBy('p.brand')
            ->getQuery()
            ->getResult();

        $brandsIds = array_column($brandsIds, 'brandId');

        $brands = $brandsRepository->findBy(array('id' => $brandsIds));

        $this->attachCategoriesTitlesToBrands($brands);

        return $brands;
    }

    /**
     * @param Brand[] $brands
     */
    public function attachCategoriesTitlesToBrands($brands)
    {
        $brandsCategoriesTitles = $this->_em->createQueryBuilder()
            ->select('IDENTITY(bc.brand) AS brandId, c.title AS categoryTitle')
            ->from('MetalCatalogBundle:BrandCategory', 'bc')
            ->join('bc.category', 'c')
            ->where('bc.brand IN (:brands)')
            ->setParameter('brands', $brands)
            ->getQuery()
            ->getResult();

        $categoriesTitlesPerBrands = array();
        foreach ($brandsCategoriesTitles as $brandCategoryTitle) {
            $categoriesTitlesPerBrands[$brandCategoryTitle['brandId']][] = $brandCategoryTitle['categoryTitle'];
        }

        foreach ($brands as $brand) {
            $brandId = $brand->getId();
            $brand->setAttribute(
                'brand_categories',
                isset($categoriesTitlesPerBrands[$brandId]) ? $categoriesTitlesPerBrands[$brandId] : array()
            );
        }
    }

    /**
     * @param FacetResultExtractor $facetResultExtractor
     *
     * @return Brand[]
     */
    public function loadByFacetResult(FacetResultExtractor $facetResultExtractor)
    {
        return $this->findByIds($facetResultExtractor->getIds(), true);
    }
}
