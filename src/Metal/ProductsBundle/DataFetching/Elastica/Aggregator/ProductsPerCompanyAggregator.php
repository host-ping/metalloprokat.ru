<?php

namespace Metal\ProductsBundle\DataFetching\Elastica\Aggregator;

use Elastica\Aggregation\Filter;
use Elastica\Aggregation\Max;
use Elastica\Aggregation\Min;
use Elastica\Aggregation\Terms;
use Elastica\Aggregation\TopHits;
use Elastica\Query;
use Elastica\ResultSet;
use Metal\ProductsBundle\DataFetching\DataRequest;
use Metal\ProductsBundle\DataFetching\Elastica\ProductIndex;
use Metal\ProductsBundle\DataFetching\Result\ProductItem;
use Metal\ProductsBundle\DataFetching\Spec\Aggregation\ProductsPerCompanyAggregation;
use Metal\ProjectBundle\DataFetching\Elastica\AbstractAggregator;
use Metal\ProjectBundle\DataFetching\Result\Aggregation\ItemsAggregationResult;
use Metal\ProjectBundle\DataFetching\Spec\Aggregation\Aggregation;

class ProductsPerCompanyAggregator extends AbstractAggregator
{
    private const ES_SOURCE = [
        'includes' => [
            ProductIndex::TITLE,
            ProductIndex::IS_SPECIAL_OFFER,
            ProductIndex::COMPANY_ACCESS,
            ProductIndex::COMPANY_CITY_ID,
            ProductIndex::VISIBILITY_STATUS,
        ],
    ];

    private const COMPANY_PRODUCTS_AGG = 'company_products';

    private const TOP_PRODUCTS_AGG = 'top_products';

    public function buildAggregation(Query $query, DataRequest $dataRequest, Aggregation $aggregation): bool
    {
        if (!$aggregation instanceof ProductsPerCompanyAggregation) {
            return false;
        }

        $filterPayedClientsAgg = new Filter(
            $this->getAggregationName($aggregation),
            new Query\Range(ProductIndex::PRIORITY_SHOW, ['gt' => 0])
        );

//        $filterAggregation = new Aggregation\Filter(
//            'exclude_virtual_products',
//            new Query\Term(['is_virtual' => ['value' => false]])
//        );
//        $filterPayedClientsAgg->addAggregation($filterAggregation);

        $query->addAggregation($filterPayedClientsAgg);

        $companyProductsAgg = new Terms(self::COMPANY_PRODUCTS_AGG);
        $companyProductsAgg->setField(ProductIndex::COMPANY_ID);
        $companyProductsAgg->setSize(250);

//        $companyProductsAgg->setParam('collect_mode', 'breadth_first');
//        $companyProductsAgg->setParam('collect_mode', 'depth_first');
//        $companyProductsAgg->setParam('execution_hint', 'map');
        $filterPayedClientsAgg->addAggregation($companyProductsAgg);
//        $query->addAggregation($companyProductsAgg);

        $topProductsAgg = new TopHits(self::TOP_PRODUCTS_AGG);
        $topProductsAgg->setSource(self::ES_SOURCE);
        $topProductsAgg->setSize($aggregation->getLimitProductsPerCompany());
        $companyProductsAgg->addAggregation($topProductsAgg);

        if ($aggregation->getOrderMode() === ProductsPerCompanyAggregation::ORDER_MODE_NONE) {
            return true;
        }

        $lastVisitedOrderAggName = $this->prefixString('last_visited_order');
        $lastVisitedOrderAgg = new Max($lastVisitedOrderAggName);
        $lastVisitedOrderAgg->setField(ProductIndex::COMPANY_LAST_VISITED_AT);
        $companyProductsAgg->addAggregation($lastVisitedOrderAgg);

        switch ($aggregation->getOrderMode()) {
            case ProductsPerCompanyAggregation::ORDER_MODE_RATING:
                $ratingOrderAggName = $this->prefixString('rating_order');

                $ratingOrderAgg = new Max($ratingOrderAggName);
                $ratingOrderAgg->setField(ProductIndex::COMPANY_RATING);
                $companyProductsAgg->addAggregation($ratingOrderAgg);

                $companyProductsAgg->setParam(
                    'order',
                    [
                        [$ratingOrderAggName => 'desc'],
                        [$lastVisitedOrderAggName => 'desc'],
                    ]
                );

                $topProductsAgg->setSort(
                    [
                        [ProductIndex::IS_SPECIAL_OFFER => ['order' => 'desc']],
                    ]
                );
                break;

            case ProductsPerCompanyAggregation::ORDER_MODE_PRICE:
                $priceOrderAggName = $this->prefixString('price_order');

                $priceOrderAgg = new Min($priceOrderAggName);
                $priceOrderAgg->setField(ProductIndex::PRICE);
                $companyProductsAgg->addAggregation($priceOrderAgg);

                $companyProductsAgg->setParam(
                    'order',
                    [
                        [$priceOrderAggName => 'asc'],
                        [$lastVisitedOrderAggName => 'desc'],
                    ]
                );

                $topProductsAgg->setSort(
                    [
                        [ProductIndex::PRICE => ['order' => 'asc']],
                        [ProductIndex::IS_SPECIAL_OFFER => ['order' => 'desc']],
                    ]
                );
                break;

            case ProductsPerCompanyAggregation::ORDER_MODE_DATE:
                $dayUpdatedAtOrderAggName = $this->prefixString('day_updated_at_order');

                $dayUpdatedAtOrderAgg = new Max($dayUpdatedAtOrderAggName);
                $dayUpdatedAtOrderAgg->setField(ProductIndex::DAY_UPDATED_AT);
                $companyProductsAgg->addAggregation($dayUpdatedAtOrderAgg);

                $companyProductsAgg->setParam(
                    'order',
                    [
                        [$dayUpdatedAtOrderAggName => 'desc'],
                        [$lastVisitedOrderAggName => 'desc'],
                    ]
                );

                $topProductsAgg->setSort(
                    [
                        [ProductIndex::DAY_UPDATED_AT => ['order' => 'desc']],
                        [ProductIndex::IS_SPECIAL_OFFER => ['order' => 'desc']],
                    ]
                );
                break;

            default:
                throw new \InvalidArgumentException('Unsupported order mode.');
        }

        return true;
    }

    public function processAggregationResult(array $aggregations, ResultSet $resultSet): array
    {
        $aggregationResults = [];
        foreach ($aggregations as $aggName => $agg) {
            $buckets = $agg[self::COMPANY_PRODUCTS_AGG]['buckets'];
            $i = 0;
            $items = [];
            do {
                $hasProducts = false;
                foreach ($buckets as $bucket) {
                    $companyProducts = $bucket[self::TOP_PRODUCTS_AGG]['hits']['hits'];
                    if (isset($companyProducts[$i])) {
                        $hasProducts = true;
                        $source = $companyProducts[$i]['_source'];
                        $items[] = new ProductItem(
                            $companyProducts[$i]['_id'],
                            $bucket['doc_count'],
                            $source[ProductIndex::COMPANY_ACCESS],
                            $source[ProductIndex::COMPANY_CITY_ID],
                            $source[ProductIndex::VISIBILITY_STATUS],
                            $bucket['key']
                        );
                    }
                }
                $i++;
            } while ($hasProducts);

            $aggregationResults[$aggName] = new ItemsAggregationResult($items);
        }

        return $aggregationResults;
    }
}
