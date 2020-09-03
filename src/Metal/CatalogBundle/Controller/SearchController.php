<?php

namespace Metal\CatalogBundle\Controller;

use Metal\CatalogBundle\DataFetching\Spec\CatalogProductFilteringSpec;
use Metal\ProductsBundle\DataFetching\Spec\ProductsFilteringSpec;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class SearchController extends Controller
{
    public function searchAction(Request $request)
    {
        $q = $request->query->get('q');

        $specification = CatalogProductFilteringSpec::createFromRequest($request)
            ->loadProductsCountPerBrand(true);

        $catalogDataFetcher = $this->get('metal.catalog.data_fetcher');

        $brandsSpecification = clone $specification;
        $manufacturersSpecification = clone $specification;
        $counts = array(
            'MetalCatalogBundle:Products:catalog_products_list_without_sort' => (int)$catalogDataFetcher->getItemsCountByCriteria($specification),
            'MetalCatalogBundle:Brands:brands_list_without_sort' => (int)$catalogDataFetcher->getItemsCountByCriteria($brandsSpecification->loadBrands(true)),
            'MetalCatalogBundle:Manufacturers:manufacturers_list_without_sort' => (int)$catalogDataFetcher->getItemsCountByCriteria($manufacturersSpecification->loadManufacturers(true)),
        );

        $additionalCounts = array();
        if (!max($counts)) {
            $productDataFetcher = $this->get('metal.products.data_fetcher');
            $productsSpecification = ProductsFilteringSpec::createFromRequest($request);

            $productsCount = (int)$productDataFetcher->getItemsCountByCriteria($productsSpecification);

            $companiesSpecification = ProductsFilteringSpec::createFromRequest($request)
                ->allowVirtual(true)
                ->loadCompanies(true);

            $companiesCount = (int)$productDataFetcher->getItemsCountByCriteria($companiesSpecification);
            $additionalCounts = array(
                'MetalProductsBundle:Products:products_list' => $productsCount,
                'MetalProductsBundle:Products:companies_list' => $companiesCount
            );
        }

        if ($additionalCounts && max($additionalCounts)) {
            $counts = array_merge($counts, $additionalCounts);
        }

        return $this->redirect($this->generateUrl(array_search(max($counts), $counts), array('q' => $q, 'subdomain' => 'www')));
    }
}
