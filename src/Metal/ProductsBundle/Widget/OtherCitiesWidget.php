<?php

namespace Metal\ProductsBundle\Widget;

use Brouzie\Bundle\WidgetsBundle\Widget\WidgetAbstract;
use Metal\AttributesBundle\Entity\DTO\AttributesCollection;
use Metal\CatalogBundle\DataFetching\Spec\CatalogProductFacetSpec;
use Metal\CatalogBundle\DataFetching\Spec\CatalogProductFilteringSpec;
use Metal\CategoriesBundle\Entity\Category;
use Metal\ProductsBundle\DataFetching\Elastica\ProductIndex;
use Metal\ProductsBundle\DataFetching\Spec\Aggregation\CitiesAggregation;
use Metal\ProductsBundle\DataFetching\Spec\ProductsFacetSpec;
use Metal\ProductsBundle\DataFetching\Spec\ProductsFilteringSpec;
use Metal\ProjectBundle\DataFetching\AdvancedDataFetcher;
use Metal\ProjectBundle\DataFetching\DataFetcher;
use Metal\ProjectBundle\DataFetching\Result\Aggregation\CountsAggregationResult;
use Metal\ProjectBundle\DataFetching\Sphinxy\FacetResultExtractor;
use Metal\TerritorialBundle\Entity\City;
use Metal\TerritorialBundle\Entity\Country;

class OtherCitiesWidget extends WidgetAbstract
{
    public function setDefaultOptions()
    {
        parent::setDefaultOptions();

        $this->optionsResolver
            ->setDefined(array('category', 'city', 'country', 'attributes_collection'))
            ->setAllowedTypes('category', array(Category::class, 'null'))
            ->setAllowedTypes('city', array(City::class, 'null'))
            ->setAllowedTypes('country', array(Country::class))
            ->setDefaults(
                array(
                    'limit' => 10,
                    'page' => 1,
                    'route_type' => 'frontpage',
                    'counter_name' => 'products_count',
                )
            )
            ->setRequired(array('country'));
    }

    public function getParametersToRender()
    {
        $category = $this->options['category'];
        /* @var $category Category */

        if (!$category->getAllowProducts()) {
            return array(
                'cities' => array(),
            );
        }

        $city = $this->options['city'];
        /* @var $city City */

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
                    'products_list' => 'MetalProductsBundle:Products:list_category_subdomain',
                    'companies_list' => 'MetalProductsBundle:Products:companies_list_category_subdomain',
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

        $dataFetcher = $this->getDataFetcher();
        if ($dataFetcher instanceof AdvancedDataFetcher) {
            $aggregations = [new CitiesAggregation('cities', 50)];

            /** @var CountsAggregationResult $citiesAggResult */
            $citiesAggResult = $dataFetcher
                ->getAggregations($this->getSpecification(), $aggregations, DataFetcher::TTL_1DAY)
                ->getAggregationResult('cities');

            $citiesIds = $citiesAggResult->getIds();
        } else {
            $facetsResultSet = $dataFetcher
                ->getFacetedResultSetByCriteria(
                    $this->getSpecification(),
                    $this->getFacetSpecification(),
                    DataFetcher::TTL_1DAY
                );
            $citiesIds = (new FacetResultExtractor($facetsResultSet, $this->getColumnName()))->getIds();
        }

        $cityRepository = $this->getDoctrine()->getRepository('MetalTerritorialBundle:City');
        $cities = $cityRepository->findByIds($citiesIds, true);

        $currentCityId = $city ? $city->getId() : 0;

        $this->shuffleArray($cities, 1000000 * $currentCityId + $this->options['page']);

        $shuffleCities = array();
        foreach ($cities as $matchCity) {
            //нам нужны только регионы текущей страны, чтоб на украинском домене не выводились города из России
            // города без слагов не участвуют
            if ($matchCity->getCountry()->getId() == $this->options['country']->getId() && $matchCity->getSlug()) {
                $shuffleCities[$matchCity->getId()] = $matchCity;
            }
        }

        if (isset($shuffleCities[City::SEO_TOP_CITY_ID]) && (!$city || $city->getId() != City::SEO_TOP_CITY_ID)) {

            $topCity = $shuffleCities[City::SEO_TOP_CITY_ID];
            unset($shuffleCities[City::SEO_TOP_CITY_ID]);
            array_unshift($shuffleCities, $topCity);
        }

        if ($city) {
            unset($shuffleCities[$city->getId()]);
        }

        $shuffleCities = array_slice($shuffleCities, 0, $this->options['limit']);

        return array(
            'cities' => $shuffleCities,
            'category' => $category,
            'fullCategoryTitle' => $fullCategoryTitle,
            'route' => $route,
        );
    }

    protected function shuffleArray(&$array, $seed)
    {
        $array = array_values($array);
        mt_srand($seed);
        for ($i = count($array) - 1; $i > 0; $i--) {
            $j = mt_rand(0, $i);
            $tmp = $array[$i];
            $array[$i] = $array[$j];
            $array[$j] = $tmp;
        }
    }

    protected function getSpecification()
    {
        switch ($this->options['counter_name']) {
            case 'products_count':
            case 'companies_count':
                $specification = ProductsFilteringSpec::createFromRequest($this->getRequest());
                $specification->resetTerritoryFilters(true);
                $specification->country($this->options['country']);

                if ($this->options['counter_name'] === 'companies_count') {
                    $specification
                        ->loadCompanies(true)
                        ->allowVirtual(true);
                }

                return $specification;

            case 'catalog_products_count':
            case 'brands_count':
            case 'manufacturers_count':
                $specification = CatalogProductFilteringSpec::createFromRequest($this->getRequest());
                $specification->resetTerritoryFilters();
                $specification->country($this->options['country']);

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
                $facetSpec->facetByCities($this->getSpecification());

                return $facetSpec;

            case 'catalog_products_count':
            case 'brands_count':
            case 'manufacturers_count':
                $facetSpec = new CatalogProductFacetSpec();
                $facetSpec->facetByCities($this->getSpecification());

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
                return ProductsFacetSpec::COLUMN_CITIES_IDS;

            case 'catalog_products_count':
            case 'brands_count':
            case 'manufacturers_count':
                return CatalogProductFacetSpec::COLUMN_CITIES_IDS;
        }
    }
}
