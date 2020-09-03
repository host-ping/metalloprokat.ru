<?php

namespace Metal\CatalogBundle\DataFetching;

use Doctrine\ORM\EntityManagerInterface;
use Metal\CatalogBundle\DataFetching\Spec\CatalogProductsLoadingSpec;
use Metal\CatalogBundle\Entity\Product;
use Metal\ProjectBundle\DataFetching\ConcreteEntityLoader;
use Metal\ProjectBundle\DataFetching\Spec\LoadingSpec;
use Metal\ProjectBundle\DataFetching\UnsupportedSpecException;

class CatalogProductsEntityLoader implements ConcreteEntityLoader
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getEntitiesByRows(\Traversable $rows, LoadingSpec $options = null)
    {
        if (null === $options) {
            $options = new CatalogProductsLoadingSpec();
        } elseif (!$options instanceof CatalogProductsLoadingSpec) {
            throw UnsupportedSpecException::create(CatalogProductsLoadingSpec::class, $options);
        }

        $rows = iterator_to_array($rows);
        $productsIds = array_column($rows, 'id');

        $productRepository = $this->em->getRepository('MetalCatalogBundle:Product');
        $products = $productRepository->findByIds($productsIds);
        /* @var $products Product[] */

        if ($options->attachBrands) {
            $productRepository->attachBrandToProducts($products);
        }

        if ($options->attachManufacturers) {
            $productRepository->attachManufacturerToProducts($products);
        }

        if ($options->loadProductsCountPerBrand) {
            $productsCountPerId = array_column($rows, 'products_count', 'id');

            foreach ($products as $product) {
                $product->setAttribute('products_count', $productsCountPerId[$product->getId()]);
            }
        }

        return $products;
    }
}
