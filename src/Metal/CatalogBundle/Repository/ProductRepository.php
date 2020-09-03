<?php

namespace Metal\CatalogBundle\Repository;

use Metal\CatalogBundle\Entity\Product;
use Metal\CatalogBundle\Entity\ProductCity;
use Metal\ProjectBundle\DataFetching\Sphinxy\FacetResultExtractor;
use Metal\ProjectBundle\Doctrine\EntityRepository;
use Metal\TerritorialBundle\Entity\City;
use Metal\UsersBundle\Entity\User;

class ProductRepository extends EntityRepository
{
    /**
     * @param Product[] $products
     */
    public function attachBrandToProducts(array $products)
    {
        $brandsIds = array();
        foreach ($products as $product) {
            if ($product->getBrand()) {
                $brandsIds[$product->getBrand()->getId()] = true;
            }
        }

        if (!$brandsIds) {
            return;
        }

        $brands = $this->_em->createQueryBuilder()
            ->select('brand')
            ->from('MetalCatalogBundle:Brand', 'brand', 'brand.id')
            ->andWhere('brand.id IN (:brands_ids)')
            ->setParameter('brands_ids', array_keys($brandsIds))
            ->getQuery()
            ->getResult();

        foreach ($products as $product) {
            $brandId = $product->getBrand()->getId();
            if (isset($brands[$brandId])) {
                $product->setAttribute('product_brand', $brands[$brandId]);
            }
        }
    }

    /**
     * @param Product[] $products
     */
    public function attachManufacturerToProducts(array $products)
    {
        $manufacturersIds = array();
        foreach ($products as $product) {
            if ($product->getManufacturer()) {
                $manufacturersIds[$product->getManufacturer()->getId()] = true;
            }
        }

        if (!$manufacturersIds) {
            return;
        }

        $manufacturers = $this->_em->createQueryBuilder()
            ->select('m')
            ->from('MetalCatalogBundle:Manufacturer', 'm', 'm.id')
            ->andWhere('m.id IN (:manufacturers_ids)')
            ->setParameter('manufacturers_ids', array_keys($manufacturersIds))
            ->getQuery()
            ->getResult();

        foreach ($products as $product) {
            $manufacturerId = $product->getManufacturer()->getId();
            if (isset($manufacturers[$manufacturerId])) {
                $product->setAttribute('product_manufacturer', $manufacturers[$manufacturerId]);
            }
        }
    }

    /**
     * @param Product[] $products
     */
    public function attachCitiesToProducts(array $products)
    {
        $productIds = array();
        foreach ($products as $product) {
            $product->setAttribute('productCities', array());
            $productIds[] = $product->getId();
        }

        $productCities = $this->_em->createQueryBuilder()
            ->select('productCity')
            ->addSelect('city')
            ->from('MetalCatalogBundle:ProductCity', 'productCity')
            ->where('productCity.product IN (:productIds)')
            ->join('productCity.city', 'city')
            ->setParameter('productIds', $productIds)
            ->getQuery()
            ->getResult();
        /* @var $productCities ProductCity[] */

        $citiesToProducts = array();
        foreach ($productCities as $productCity) {
            $citiesToProducts[$productCity->getProduct()->getId()][] = $productCity->getCity();
        }

        foreach ($products as $product) {
            if (isset($citiesToProducts[$product->getId()])) {
                $product->setAttribute('productCities', $citiesToProducts[$product->getId()]);
            }
        }
    }

    /**
     * @param $brand
     * @param null $excludedProductId
     * @param City|null $city
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getProductsByBrandQb($brand, $excludedProductId = null, City $city = null)
    {
        $productsQb = $this->_em->createQueryBuilder()
            ->select('p')
            ->from('MetalCatalogBundle:Product', 'p')
            ->where('p.brand = :brand')
            ->setParameter('brand', $brand);

        if ($excludedProductId) {
            $productsQb
                ->andWhere('p.id <> :excluded_product_id')
                ->setParameter('excluded_product_id', $excludedProductId);
        }

        if ($city) {
            $productsQb
                ->join('p.productCities', 'pc')
                ->andWhere('pc.city = :city_id')
                ->setParameter('city_id', $city->getId());
        }

        return $productsQb;
    }

    /**
     * @param $brand
     * @param $excludedProductId
     * @param City $city
     *
     * @return Product[]
     */
    public function getProductsByBrand($brand, $excludedProductId = null, City $city = null)
    {
        return $this->getProductsByBrandQb($brand, $excludedProductId, $city)->getQuery()->getResult();
    }

    /**
     * @param array $productsIds
     *
     * @return array [productId => [categoryId, categoryId, ...]]
     */
    public function getCategoriesIdsForProducts(array $productsIds = array())
    {
        if (!$productsIds) {
            return array();
        }

        $productToCategories = array_fill_keys($productsIds, array());

        $productCategories = $this->_em->getRepository('MetalCatalogBundle:Product')
            ->createQueryBuilder('p')
            ->select('p.id as productId')
            ->addSelect('IDENTITY(p.category) AS categoryId')
            ->where('p.id IN (:products_ids)')
            ->setParameter('products_ids', $productsIds)
            ->getQuery()
            ->getArrayResult();

        $categoriesIds = array_column($productCategories, 'categoryId');

        $categories = $this->_em->createQueryBuilder()
            ->from('MetalCategoriesBundle:CategoryClosure', 'cc')
            ->select('IDENTITY(cc.descendant) as descendant')
            ->addSelect('IDENTITY(cc.ancestor) as ancestor')
            ->where('cc.descendant IN (:categories_ids)')
            ->setParameter('categories_ids', $categoriesIds)
            ->getQuery()
            ->getResult();

        $categoriesToDescendant = array();
        foreach ($categories as $category) {
            $categoriesToDescendant[$category['descendant']][] = $category['ancestor'];
        }

        foreach ($productCategories as $productCategory) {
            if (!isset($productToCategories[$productCategory['productId']])) {
                $productToCategories[$productCategory['productId']] = array();
            }
            $productToCategories[$productCategory['productId']] = array_merge($productToCategories[$productCategory['productId']], $categoriesToDescendant[$productCategory['categoryId']]);
        }

        return $productToCategories;
    }

    /**
     * @param Product $product
     */
    public function checkProductUniqueTitle(Product $product)
    {
        $nonUniqueProductsIds = $this->_em->createQueryBuilder()
            ->select('p.id')
            ->from('MetalCatalogBundle:Product', 'p', 'p.id')
            ->where('p.title = :title')
            ->setParameter('title', $product->getTitle())
            ->getQuery()
            ->getArrayResult();

        $isUnique = count($nonUniqueProductsIds) > 1 ? 1 : 0;

        $this->createQueryBuilder('p')
            ->update('MetalCatalogBundle:Product', 'p')
            ->set('p.isTitleNonUnique', $isUnique)
            ->where('p.id IN (:product_ids)')
            ->setParameter('product_ids', array_keys($nonUniqueProductsIds))
            ->getQuery()
            ->execute();

    }

    public function getSimpleAuthors()
    {
        $users = $this->_em->createQueryBuilder()
            ->select('user')
            ->from('MetalUsersBundle:User', 'user')
            ->join('MetalCatalogBundle:Product', 'product', 'WITH', 'product.createdBy = user.id')
            ->groupBy('user.id')
            ->getQuery()
            ->getResult();
        /* @var $users User[] */

        $response = array();
        foreach ($users as $user) {
            $response[$user->getId()] = $user->getFullname();
        }

        return $response;
    }

    /**
     * @param FacetResultExtractor $facetResultExtractor
     *
     * @return Product[]
     */
    public function loadByFacetResult(FacetResultExtractor $facetResultExtractor)
    {
        return $this->findByIds($facetResultExtractor->getIds(), true);
    }
}
