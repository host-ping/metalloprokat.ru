<?php

namespace Metal\ProductsBundle\Widget;

use Brouzie\WidgetsBundle\Widget\TwigWidget;
use Metal\ProductsBundle\DataFetching\Elastica\ProductIndex;
use Metal\ProductsBundle\DataFetching\Result\ProductItem;
use Metal\ProductsBundle\DataFetching\Spec\Aggregation\ProductsPerCompanyAggregation;
use Metal\ProductsBundle\DataFetching\Spec\ProductsFilteringSpec;
use Metal\ProductsBundle\DataFetching\Spec\ProductsLoadingSpec;
use Metal\ProjectBundle\DataFetching\DataFetcher;
use Metal\ProjectBundle\DataFetching\DataFetcherFactory;
use Metal\ProjectBundle\DataFetching\EntityLoader;
use Metal\ProjectBundle\DataFetching\Result\Aggregation\ItemsAggregationResult;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PremiumProductsWidget extends TwigWidget
{
    private $dataFetcherFactory;

    private $entityLoader;

    private const AGG_NAME = 'company_products';

    public function __construct(DataFetcherFactory $dataFetcherFactory, EntityLoader $entityLoader)
    {
        $this->dataFetcherFactory = $dataFetcherFactory;
        $this->entityLoader = $entityLoader;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver
            ->setDefaults(
                [
                    'category' => null,
                    'city' => null,
                    'country' => null,
                    'products_count' => 3,
                    'show_category_mode' => 'none',
                    'context' => 'portal',
                ]
            )
            ->setAllowedValues('show_category_mode', ['category', 'other_companies', 'none']);
    }

    public function getContext()
    {
        $specification = (new ProductsFilteringSpec())
            ->onlyPriorityShowCompanies();

        if ($this->options['category']) {
            $specification->category($this->options['category']);
        }

        if ($this->options['country']) {
            $specification->country($this->options['country']);
        }
        if ($this->options['city']) {
            $specification->city($this->options['city']);
        }

        $productItems = $this->getRandomProductItems($specification);

        $loadingOpts = (new ProductsLoadingSpec())
            ->attachFavorite(false);

        $products = $this->entityLoader->getEntitiesByRows($productItems, $loadingOpts);

        return ['products' => $products];
    }

    private function getRandomProductItems(ProductsFilteringSpec $specification): array
    {
        $productItems = $this->getProductItemsSlice($specification, $this->options['products_count']);
        $extraItemsCount = $this->options['products_count'] - count($productItems);

        if ($specification->hasTerritoryFilters() && $extraItemsCount > 0) {
            $specification->resetTerritoryFilters();
            $productItems = array_merge($productItems, $this->getProductItemsSlice($specification, $extraItemsCount));
        }

        return $productItems;
    }

    /**
     * @return ProductItem[]
     */
    private function getProductItemsSlice(ProductsFilteringSpec $specification, int $count): array
    {
        $aggregations = [new ProductsPerCompanyAggregation(self::AGG_NAME)];

        $sourceResponse = $this->dataFetcherFactory
            ->getDataFetcher(ProductIndex::SCOPE)
            ->getAggregations($specification, $aggregations, DataFetcher::TTL_1DAY);

        /** @var ItemsAggregationResult $aggregationResult */
        $aggregationResult = $sourceResponse->getAggregationResult(self::AGG_NAME);
        $items = $aggregationResult->getItems();
        shuffle($items);

        return array_slice($items, 0, $count);
    }
}
