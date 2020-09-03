<?php

namespace Metal\ProductsBundle\Widget;

use Brouzie\Bundle\WidgetsBundle\Widget\WidgetAbstract;
use Metal\ProductsBundle\DataFetching\Elastica\ProductIndex;
use Metal\ProductsBundle\DataFetching\Spec\ProductsFilteringSpec;
use Metal\ProductsBundle\DataFetching\Spec\ProductsLoadingSpec;
use Metal\ProductsBundle\DataFetching\Spec\ProductsOrderingSpec;
use Metal\ProductsBundle\Entity\Product;
use Metal\ProjectBundle\DataFetching\ListResultsViewModel;

class ProductTabsWidget extends WidgetAbstract
{
    /**
     * @var ListResultsViewModel
     */
    private $products;

    /**
     * @var ListResultsViewModel
     */
    private $productsByCategory;

    protected function setDefaultOptions()
    {
        parent::setDefaultOptions();

        $this->optionsResolver
            ->setDefaults(
                [
                    'active_tab' => 'similar-products-tab',
                    'product' => null,
                    'per_page' => 20,
                    'page' => 1,
                    'available_tabs' => ['similar-products-tab', 'category-products-tab'],
                    'city' => null,
                    'show_more_route_parameters' => [
                        'similar-products-tab' => [],
                        'category-products-tab' => ['tab' => 'city'],
                    ],
                    'disabled_normalize_price' => false,
                    'product_view_url_mode' => 'standard',
                    'use_pagination' => true,
                ]
            );
    }

    public function getParametersToRender()
    {
        return $this->getProducts();
    }

    public function getProducts()
    {
        if ($this->products != null || $this->productsByCategory != null) {
            return array(
                'products' => $this->products,
                'productsByCategory' => $this->productsByCategory,
            );
        }

        $country = $this->getRequest()->attributes->get('country');

        $product = $this->options['product'];
        /* @var $product Product */

        $loadingOpts = (new ProductsLoadingSpec())
            ->attachFavorite(false)
            ->attachCategories(false);

        $dataFetcher = $this->container->get('metal_products.data_fetcher_factory')
            ->getDataFetcher(ProductIndex::SCOPE);
        $entityLoader = $this->container->get('metal.products.products_entity_loader');

        $productsPagerfanta = new \ArrayIterator();
        if (in_array('similar-products-tab', $this->options['available_tabs'])) {
            $page = 1;
            if ($this->options['active_tab'] === 'similar-products-tab') {
                $page = $this->options['page'];
            }

            //FIXME: для минисайта нужно вызывать ->showOnPortal(null)
            $specification = (new ProductsFilteringSpec())
                ->company($product->getCompany())
                ->category($product->getCategory())
                ->city($this->options['city'])
                ->exceptProductId($product->getId());

            $orderBy = (new ProductsOrderingSpec())
                ->updatedAt()
                ->specialOffer()
                ->random($product->getId());

            $productsPagerfanta = $dataFetcher->getPagerfantaByCriteria(
                $specification,
                $orderBy,
                $this->options['per_page'],
                $page
            );
            $this->products = $entityLoader->getListResultsViewModel($productsPagerfanta, $loadingOpts);
            $productsPagerfanta = $this->products->pagerfanta;
        }

        $productsByCategoryPagerfanta = new \ArrayIterator();
        if (in_array('category-products-tab', $this->options['available_tabs'])) {
            $page = 1;
            if ($this->options['active_tab'] === 'category-products-tab') {
                $page = $this->options['page'];
            }

            $specification = (new ProductsFilteringSpec())
                ->exceptCompany($product->getCompany())
                ->category($product->getCategory())
                ->city($this->options['city'])
                ->exceptProductId($product->getId());

            $orderBy = (new ProductsOrderingSpec())
                ->updatedAt()
                ->random($product->getId());
            //TODO: возможно нужно платников выводить выше

            $productsByCategoryPagerfanta = $dataFetcher->getPagerfantaByCriteria(
                $specification,
                $orderBy,
                $this->options['per_page'],
                $page
            );
            $this->productsByCategory = $entityLoader->getListResultsViewModel(
                $productsByCategoryPagerfanta,
                $loadingOpts
            );
            $productsByCategoryPagerfanta = $this->productsByCategory->pagerfanta;
        }

        $products = array_merge(
            iterator_to_array($productsPagerfanta),
            iterator_to_array($productsByCategoryPagerfanta)
        );

        $user = null;
        if ($this->isGranted('ROLE_USER')) {
            $user = $this->getUser();
        }

        $productRepository = $this->getDoctrine()->getRepository('MetalProductsBundle:Product');
        /* @var $productRepository \Metal\ProductsBundle\Repository\ProductRepository */

        $productRepository->attachFavoriteToProducts($products, $user);
        $productRepository->attachCategoriesToProducts($products);
        $this->getDoctrine()->getRepository('MetalCategoriesBundle:ParameterGroup')
            ->attachAttributesForProducts($products);
        if (!$this->options['disabled_normalize_price']) {
            $productRepository->attachNormalizedPrice($products, $country);
        }

        return array(
            'use_pagination' => $this->options['use_pagination'],
            'products' => $this->products,
            'productsByCategory' => $this->productsByCategory,
        );
    }
}
