<?php

namespace Metal\ProductsBundle\Widget;

use Brouzie\Bundle\WidgetsBundle\Widget\WidgetAbstract;
use Brouzie\WidgetsBundle\Cache\CacheProfile;
use Brouzie\WidgetsBundle\Widget\CacheableWidget;
use Doctrine\ORM\EntityManager;
use Metal\AttributesBundle\DataFetching\AttributesFacetResult;
use Metal\AttributesBundle\Entity\DTO\AttributesCollection;
use Metal\CatalogBundle\DataFetching\Spec\CatalogProductFacetSpec;
use Metal\CatalogBundle\DataFetching\Spec\CatalogProductFilteringSpec;
use Metal\CatalogBundle\Entity\Product as CatalogProduct;
use Metal\CategoriesBundle\Entity\Category;
use Metal\CompaniesBundle\Entity\ValueObject\CompanyServiceProvider;
use Metal\ProductsBundle\DataFetching\Elastica\ProductIndex;
use Metal\ProductsBundle\DataFetching\Spec\Aggregation\AttributesAggregation;
use Metal\ProductsBundle\DataFetching\Spec\Aggregation\PriceRangeAggregation;
use Metal\ProductsBundle\DataFetching\Spec\ProductsFacetSpec;
use Metal\ProductsBundle\DataFetching\Spec\ProductsFilteringSpec;
use Metal\ProjectBundle\DataFetching\AdvancedDataFetcher;
use Metal\ProjectBundle\DataFetching\DataFetcher;
use Metal\ProjectBundle\DataFetching\Result\Aggregation\CountsAggregationResult;
use Metal\ProjectBundle\DataFetching\Result\Aggregation\MinMaxAggregationResult;
use Metal\ProjectBundle\DataFetching\Spec\TaggableCacheableSpec;

class FacetFiltersWidget extends WidgetAbstract implements CacheableWidget
{
    private const MAX_RESULTS = 10;

    private $attributesCollection;

    private $priceRange;

    protected function setDefaultOptions()
    {
        parent::setDefaultOptions();

        $this->optionsResolver
            ->setRequired(
                [
                    'category',
                    'current_attributes_collection',
                    'route',
                    'route_parameters',
                    'active_tab',
                    'load_attribute_values_route',
                    'show_company_attrs',
                    'show_price_range',
                ]
            )
            ->setAllowedTypes('category', Category::class)
            ->setAllowedTypes('current_attributes_collection', AttributesCollection::class)
            ->setAllowedValues('active_tab', ['products', 'companies', 'manufacturers', 'brands', 'catalog_products']);
    }

    public function getCacheProfile()
    {
        $criteria = $this->getCriteria();

        $cacheKey = $criteria->getCacheKey();
        if (!$cacheKey) {
            return null;
        }

        return new CacheProfile(
            array(
                'key' => $cacheKey,
                'options' => $this->options,
            ),
            DataFetcher::TTL_1DAY,
            $criteria instanceof TaggableCacheableSpec ? $criteria->getCacheTags() : array()
        );
    }

    public function getContext()
    {
        $tab = $this->options['active_tab'];

        if (in_array($tab, array('products', 'companies'))) {
            $dataFetcher = $this->container
                ->get('metal_products.data_fetcher_factory')
                ->getDataFetcher(ProductIndex::SCOPE);

            if ($dataFetcher instanceof AdvancedDataFetcher) {
                $context = $this->buildContext();
                $context['companyServices'] = CompanyServiceProvider::getAllServices();

                return $context;
            }
        }

        return [
            'companyServices' => CompanyServiceProvider::getAllServices(),
            'attributesCollection' => $this->getAttributesCollection(),
            'priceRange' => $this->getPriceRange(),
        ];
    }

    private function buildContext(): array
    {
        /* @var $category Category */
        $category = $this->options['category'];

        $em = $this->container->get('doctrine.orm.default_entity_manager');
        /* @var $em EntityManager */
        $attributeValueRepository = $em->getRepository('MetalAttributesBundle:AttributeValue');
        $attributeCategoryRepository = $em->getRepository('MetalAttributesBundle:AttributeCategory');

        if (!$category->getAllowProducts()) {
            return [
                'attributesCollection' => new AttributesCollection(),
                'priceRange' => false,
            ];
        }

        $attributes = $attributeCategoryRepository->getAttributesForCategory($category);

        $criteria = $this->getCriteria();

        $dataFetcher = $this->container
            ->get('metal_products.data_fetcher_factory')
            ->getDataFetcher(ProductIndex::SCOPE);

        $aggregations = [];
        $aggregations[] = new PriceRangeAggregation('price_range');
        foreach ($attributes as $attribute) {
            $aggregationName = sprintf('attributes.%d', $attribute->getId());
            $aggregations[] = new AttributesAggregation(
                $aggregationName,
                $attribute->getId(),
                self::MAX_RESULTS + 1
            );
        }

        $sourceResponse = $dataFetcher->getAggregations($criteria, $aggregations);

        //TODO: preserve order of elements like in array of ids
        $attributeValuesIds = [];
        foreach ($attributes as $attribute) {
            $aggregationName = sprintf('attributes.%d', $attribute->getId());
            /** @var CountsAggregationResult $aggregationResult */
            $aggregationResult = $sourceResponse->getAggregationResult($aggregationName);
            $attributeValuesIds[] = $aggregationResult->getIds();
        }

        $attributeValuesIds = $attributeValuesIds ? array_merge(...$attributeValuesIds) : [];
        $attributesCollection = $attributeValueRepository->loadCollectionByAttributesValuesIds($attributeValuesIds);

        return [
            'attributesCollection' => $attributesCollection,
            'priceRange' => $sourceResponse->getAggregationResult('price_range'),
        ];
    }

    private function getAttributesCollection()
    {
        if (null !== $this->attributesCollection) {
            return $this->attributesCollection;
        }

        /* @var $category Category */
        $category = $this->options['category'];

        $em = $this->container->get('doctrine.orm.default_entity_manager');
        /* @var $em EntityManager */
        $attributeValueRepository = $em->getRepository('MetalAttributesBundle:AttributeValue');
        $attributeCategoryRepository = $em->getRepository('MetalAttributesBundle:AttributeCategory');

        $excludedAttributes = array(
            'brands' => array(
                CatalogProduct::ATTR_CODE_BRAND => true,
            ),
            'manufacturers' => array(
                CatalogProduct::ATTR_CODE_BRAND => true,
                CatalogProduct::ATTR_CODE_MANUFACTURER => true,
            ),
        );
        $tab = $this->options['active_tab'];

        $attributes = $attributeCategoryRepository->getAttributesForCategory($category);

        if (!count($attributes)) {
            // если к данной категории не привязаны атрибуты - нет смысла строить фасеты
            return $this->attributesCollection = new AttributesCollection();
        }

        $criteria = $this->getCriteria();

        if (in_array($tab, array('products', 'companies'))) {
            $dataFetcher = $this->container->get('metal.products.data_fetcher');
            $facetSpec = new ProductsFacetSpec();

            if ($category->getAllowProducts()) {
                foreach ($attributes as $attribute) {
                    $facetSpec->facetByAttribute($attribute, $criteria, self::MAX_RESULTS + 1);
                }
            }
        } else {
            $dataFetcher = $this->container->get('metal.catalog.data_fetcher');

            $facetSpec = new CatalogProductFacetSpec();
            foreach ($attributes as $attribute) {
                if (!empty($excludedAttributes[$tab][$attribute->getCode()])) {
                    continue;
                }

                // +1 for null
                $facetSpec->facetByAttribute($attribute, $criteria, self::MAX_RESULTS + 1);
            }
        }

        if (!count($facetSpec->facets)) {
            // если нет ни одного фасета - не делаем запросов
            return $this->attributesCollection = new AttributesCollection();
        }


        $facetsResultSet = $dataFetcher->getFacetedResultSetByCriteria($criteria, $facetSpec);
        $attributesCollection = $attributeValueRepository->loadCollectionByFacetResult(
            new AttributesFacetResult($facetsResultSet, $attributes)
        );

        return $this->attributesCollection = $attributesCollection;
    }

    /**
     * @return ProductsFilteringSpec|CatalogProductFilteringSpec
     */
    private function getCriteria()
    {
        $request = $this->getRequest();
        $category = $this->options['category'];
        /* @var $category Category */

        if (in_array($this->options['active_tab'], array('products', 'companies'))) {
            $criteria = ProductsFilteringSpec::createFromRequest($request);
            if ($category && $category->getAllowProducts()) {
                $criteria->concreteCategory($category);
            }
        } else {
            $criteria = CatalogProductFilteringSpec::createFromRequest($request);
            if ($category) {
                $criteria->concreteCategory($category);
            }
        }

        return $criteria;
    }

    private function getPriceRange()
    {
        if (null !== $this->priceRange) {
            return $this->priceRange;
        }

        if (!$this->options['show_price_range']) {
            return $this->priceRange = false;
        }

        $priceRangeCriteria = $this->getCriteria();
        $priceRangeCriteria
            ->loadPriceRange(true)
            ->loadCompanies(false)
            ->clearPrice();

        $dataFetcher = $this->container->get('metal.products.data_fetcher');
        $row = $dataFetcher
            ->getResultSetByCriteria($priceRangeCriteria, null, 1)
            ->getSingleRow(array('min_price' => 0, 'max_price' => 0));

        return $this->priceRange = new MinMaxAggregationResult($row['min_price'], $row['max_price']);
    }
}
