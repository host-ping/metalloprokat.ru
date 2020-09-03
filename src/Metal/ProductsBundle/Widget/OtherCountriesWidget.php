<?php

namespace Metal\ProductsBundle\Widget;

use Brouzie\WidgetsBundle\Cache\CacheProfile;
use Brouzie\WidgetsBundle\Widget\CacheableWidget;
use Brouzie\Bundle\WidgetsBundle\Widget\WidgetAbstract;
use Metal\AttributesBundle\Entity\DTO\AttributesCollection;
use Metal\CatalogBundle\DataFetching\Spec\CatalogProductFacetSpec;
use Metal\CatalogBundle\DataFetching\Spec\CatalogProductFilteringSpec;
use Metal\CategoriesBundle\Entity\Category;
use Metal\ProductsBundle\DataFetching\Elastica\ProductIndex;
use Metal\ProductsBundle\DataFetching\Spec\Aggregation\CountriesAggregation;
use Metal\ProductsBundle\DataFetching\Spec\ProductsFacetSpec;
use Metal\ProductsBundle\DataFetching\Spec\ProductsFilteringSpec;
use Metal\ProjectBundle\DataFetching\AdvancedDataFetcher;
use Metal\ProjectBundle\DataFetching\CacheProfile as DataFetchingCacheProfile;
use Metal\ProjectBundle\DataFetching\DataFetcher;
use Metal\ProjectBundle\DataFetching\Result\Aggregation\CountsAggregationResult;
use Metal\ProjectBundle\DataFetching\Sphinxy\FacetResultExtractor;
use Metal\TerritorialBundle\Entity\Country;

class OtherCountriesWidget extends WidgetAbstract implements CacheableWidget
{
    public function setDefaultOptions()
    {
        parent::setDefaultOptions();

        $this->optionsResolver
            ->setDefined(array('category', 'city', 'country', 'attributes_collection'))
            ->setAllowedTypes('category', array(Category::class, 'null'))
            ->setAllowedTypes('country', array(Country::class))
            ->setDefaults(
                array(
                    'limit' => 3,
                    'route_type' => 'frontpage',
                    'counter_name' => 'products_count',
                )
            )
            ->setRequired(array('country'));
    }

    public function getCacheProfile()
    {
        $specifications = array($this->getSpecification(), $this->getFacetSpecification());
        if (!$cacheKey = DataFetchingCacheProfile::getKeyFromSpecifications($specifications)) {
            return;
        }

        return new CacheProfile(
            array(
                'key' => $cacheKey,
                'route_type' => $this->options['route_type'],
                'counter' => $this->options['counter_name'],
            ),
            DataFetcher::TTL_5DAYS
        );
    }

    protected function getParametersToRender()
    {
        $category = $this->options['category'];
        /* @var $category Category */

        if (!$category->getAllowProducts()) {
            return array(
                'countries' => array(),
            );
        }

        $country = $this->options['country'];
        /* @var $country Country */

        $routes = array(
            'products_list' => 'MetalProductsBundle:Products:products_list',
            'companies_list' => 'MetalProductsBundle:Products:companies_list',
            'last_products_list' => 'MetalProductsBundle:Products:products_list_without_sort',
            'last_companies_list' => 'MetalProductsBundle:Products:companies_list_without_sort',
            'frontpage' => 'MetalProjectBundle:Default:index_subdomain',
            'search' => 'MetalProductsBundle:Products:search',
        );

        $currentRoute = $this->options['route_type'];
        $route = isset($routes[$currentRoute]) ? $routes[$currentRoute] : $routes['frontpage'];

        if ($category) {
            $routes = array_merge(
                $routes,
                array(
                    'manufacturers_list' => 'MetalCatalogBundle:Manufacturers:list_category_subdomain',
                    'brands_list' => 'MetalCatalogBundle:Brands:list_category_subdomain',
                    'catalog_products_list' => 'MetalCatalogBundle:Products:list_category_subdomain',
                    'products_list' => 'MetalProductsBundle:Products:list_category_country',
                    'companies_list' => 'MetalProductsBundle:Products:companies_list_category_country',
                )
            );
            $route = isset($routes[$currentRoute]) ? $routes[$currentRoute] : $routes['products_list'];
        }

        $fullCategoryTitle = ucfirst($this->container->getParameter('tokens.product_title.nominative')).' ';

        if ($category) {
            $attributesCollection = $this->options['attributes_collection'];
            /* @var $attributesCollection AttributesCollection */

            $fullCategoryTitle = $category->getTitle().' ';
            if (count($attributesCollection)) {
                $fullCategoryTitle .= $attributesCollection->toString(' ', ' ').' ';
            }
        }

        // кеш запроса не нужен, потому что весь виджет попадет в него
        $dataFetcher = $this->getDataFetcher();
        if ($dataFetcher instanceof AdvancedDataFetcher) {
            $aggregations = [new CountriesAggregation('countries')];

            /** @var CountsAggregationResult $countriesAggResult */
            $countriesAggResult = $dataFetcher
                ->getAggregations($this->getSpecification(), $aggregations, false)
                ->getAggregationResult('countries');

            $countriesIds = $countriesAggResult->getIds();
        } else {
            $facetsResultSet = $dataFetcher
                ->getFacetedResultSetByCriteria($this->getSpecification(), $this->getFacetSpecification(), false);

            $countriesIds = (new FacetResultExtractor($facetsResultSet, $this->getColumnName()))->getIds();
        }

        $countryRepository = $this->getDoctrine()->getRepository('MetalTerritorialBundle:Country');
        $countries = $countryRepository->findByIds($countriesIds, true);

        unset($countries[$country->getId()]);
        $countries = array_slice($countries, 0, $this->options['limit']);

        return array(
            'countries' => $countries,
            'category' => $category,
            'fullCategoryTitle' => $fullCategoryTitle,
            'route' => $route,
        );
    }

    protected function getSpecification()
    {
        switch ($this->options['counter_name']) {
            case 'products_count':
            case 'companies_count':
                $specification = ProductsFilteringSpec::createFromRequest($this->getRequest());
                $specification->resetTerritoryFilters(true);

                if ($this->options['counter_name'] === 'companies_count') {
                    $specification->allowVirtual(true);
                }

                return $specification;

            case 'catalog_products_count':
            case 'brands_count':
            case 'manufacturers_count':
                $specification = CatalogProductFilteringSpec::createFromRequest($this->getRequest());
                $specification->resetTerritoryFilters();

                if ($this->options['counter_name'] === 'brands_count') {
                    $specification->loadBrands(true);
                } elseif ($this->options['counter_name'] === 'manufacturers_count') {
                    $specification->loadManufacturers(true);
                }

                return $specification;
        }
    }

    protected function getFacetSpecification()
    {
        switch ($this->options['counter_name']) {
            case 'products_count':
            case 'companies_count':
                $facetSpec = new ProductsFacetSpec();
                $facetSpec->facetByCountries($this->getSpecification());

                return $facetSpec;

            case 'catalog_products_count':
            case 'brands_count':
            case 'manufacturers_count':
                $facetSpec = new CatalogProductFacetSpec();
                $facetSpec->facetByCountries($this->getSpecification());

                return $facetSpec;
        }
    }

    protected function getDataFetcher()
    {
        switch ($this->options['counter_name']) {
            case 'products_count':
                return $this->container->get('metal_products.data_fetcher_factory')
                    ->getDataFetcher(ProductIndex::SCOPE);

            case 'companies_count':
                return $this->container->get('metal.products.data_fetcher');

            case 'catalog_products_count':
            case 'brands_count':
            case 'manufacturers_count':
                return $this->container->get('metal.catalog.data_fetcher');
        }
    }

    protected function getColumnName()
    {
        switch ($this->options['counter_name']) {
            case 'products_count':
            case 'companies_count':
                return ProductsFacetSpec::COLUMN_COUNTRIES_IDS;

            case 'catalog_products_count':
            case 'brands_count':
            case 'manufacturers_count':
                return CatalogProductFacetSpec::COLUMN_COUNTRIES_IDS;
        }
    }
}
