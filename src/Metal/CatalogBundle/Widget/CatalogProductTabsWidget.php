<?php

namespace Metal\CatalogBundle\Widget;

use Brouzie\Bundle\WidgetsBundle\Widget\WidgetAbstract;
use Metal\CatalogBundle\DataFetching\Spec\CatalogProductFilteringSpec;
use Metal\CatalogBundle\Entity\Product;
use Metal\CatalogBundle\Repository\ProductRepository;
use Metal\TerritorialBundle\Entity\City;

class CatalogProductTabsWidget extends WidgetAbstract
{
    private $productsByBrand;

    private $similarProducts;

    private $productReviews;

    protected function setDefaultOptions()
    {
        parent::setDefaultOptions();

        $this->optionsResolver
            ->setDefaults(array(
                'active_tab' => null,
                'available_tabs' => array('products-review-tab', 'products-brand-tab', 'similar-products-tab'),
                'product' => null,
                'page' => 1,
                'perPage' => 20,
                'city' => null,
                'region' => null,
                'country' => null,
            ))
            ->setAllowedTypes('city', array(City::class, 'null'))
        ;
    }

    protected function getParametersToRender()
    {
        return $this->getProducts();
    }

    public function getProducts()
    {
        $product = $this->options['product'];
        /* @var $product Product */
        $city = $this->options['city'];
        /* @var $city City */
        $availableTabs = $this->options['available_tabs'];

        if ($this->productsByBrand != null || $this->similarProducts != null || $this->productReviews != null) {
            return array(
                'product' => $product,
                'reviews' => $this->productReviews,
                'productsByBrand' => $this->productsByBrand,
                'similarProducts' => $this->similarProducts
            );
        }

        $em = $this->container->get('doctrine.orm.default_entity_manager');

        $productsRepository = $em->getRepository('MetalCatalogBundle:Product');
        /* @var $productsRepository ProductRepository */
        $perPage = $this->options['perPage'];
        $brand = $product->getBrand();

        if (in_array('similar-products-tab', $availableTabs)) {

            $category = $product->getCategory();
            $currentPage = $this->options['page'];
            $region = $this->options['region'];
            $country = $this->options['country'];

            $dataFetcher = $this->container->get('metal.catalog.data_fetcher');
            $entityLoader = $this->container->get('metal.catalog.entity_loader');

            $catalogProductFilteringSpec = (new CatalogProductFilteringSpec())
                ->city($city)
                ->region($region)
                ->country($country)
                ->category($category)
                ->notBrand($brand)
            ;

            $pagerfanta = $dataFetcher->getPagerfantaByCriteria($catalogProductFilteringSpec, null, $perPage, $currentPage);
            $pagerfanta = $entityLoader->getPagerfantaWithEntities($pagerfanta);

            $this->similarProducts = $pagerfanta;
        }

        if (in_array('products-review-tab', $availableTabs)) {
            $reviews = $em->createQueryBuilder()
                ->select('pr')
                ->from('MetalCatalogBundle:ProductReview', 'pr')
                ->where('pr.product = :product')
                ->andWhere('pr.deletedAt IS NULL')
                ->andWhere('pr.moderatedAt IS NOT NULL')
                ->setParameter('product', $product)
                ->orderBy('pr.id', 'DESC')
                ->getQuery()
                ->getResult();

            $this->productReviews = $reviews;
        }

        if (in_array('products-brand-tab', $availableTabs)) {
            $this->productsByBrand = $productsRepository->getProductsByBrand($brand, $product->getId(), $city);
        }

        if (!empty($this->productReviews)) {
            $this->options['active_tab'] = 'products-review-tab';
        } elseif (!empty($this->productsByBrand)) {
            $this->options['active_tab'] = 'products-brand-tab';
        } elseif (count($this->similarProducts)) {
            $this->options['active_tab'] = 'similar-products-tab';
        }

        return array(
            'product' => $product,
            'reviews' => $this->productReviews,
            'productsByBrand' => $this->productsByBrand,
            'similarProducts' => $this->similarProducts
        );
    }
}
