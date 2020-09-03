<?php

namespace Metal\ProductsBundle\DataFetching\Elastica\Aggregator;

use Elastica\Aggregation\Filter;
use Elastica\Aggregation\Stats;
use Elastica\Query;
use Elastica\Query\BoolQuery;
use Elastica\ResultSet;
use Metal\ProductsBundle\DataFetching\DataRequest;
use Metal\ProductsBundle\DataFetching\Elastica\ProductIndex;
use Metal\ProductsBundle\DataFetching\Spec\Aggregation\PriceRangeAggregation;
use Metal\ProductsBundle\DataFetching\Spec\ProductsFilteringSpec;
use Metal\ProductsBundle\Entity\Product;
use Metal\ProjectBundle\DataFetching\Elastica\AbstractAggregator;
use Metal\ProjectBundle\DataFetching\Elastica\ConcreteDataFetcher;
use Metal\ProjectBundle\DataFetching\Elastica\DataFetcherAwareAggregator;
use Metal\ProjectBundle\DataFetching\Elastica\DataFetcherAwareAggregatorTrait;
use Metal\ProjectBundle\DataFetching\Result\Aggregation\MinMaxAggregationResult;
use Metal\ProjectBundle\DataFetching\Spec\Aggregation\Aggregation;

class PriceRangeAggregator extends AbstractAggregator implements DataFetcherAwareAggregator
{
    use DataFetcherAwareAggregatorTrait;

    private const PRICE_RANGE_AGG = 'price_range';

    public function buildAggregation(Query $query, DataRequest $dataRequest, Aggregation $aggregation): bool
    {
        if (!$aggregation instanceof PriceRangeAggregation) {
            return false;
        }

        //TODO: do not do boilerplate and add filter aggregation around?
        /** @var ProductsFilteringSpec $commonFilteringSpec */
        $commonFilteringSpec = $dataRequest->getFilteringSpec(ProductsFilteringSpec::class);
        $filteringSpec = $commonFilteringSpec->diff($aggregation->getFilteringSpec());

        $boolQuery = new BoolQuery();
        $boolQuery->addFilter(new Query\Range(ProductIndex::PRICE, ['lt' => Product::PRICE_CONTRACT]));

        $filterAgg = new Filter($this->getAggregationName($aggregation), $boolQuery);
        $query->addAggregation($filterAgg);

        if ($filteringSpec) {
            $this->getDataFetcher()->reflectFilteringSpecToQuery(
                $boolQuery,
                new DataRequest($filteringSpec, null),
                ConcreteDataFetcher::QUERY_MODE_AGGREGATION
            );
        }

        $statsAgg = new Stats(self::PRICE_RANGE_AGG);
        $statsAgg->setField(ProductIndex::PRICE);
        $filterAgg->addAggregation($statsAgg);

        return true;
    }

    public function processAggregationResult(array $aggregations, ResultSet $resultSet): array
    {
        $aggregationResults = [];
        foreach ($aggregations as $aggName => $agg) {
            $agg = $agg[self::PRICE_RANGE_AGG];
            $aggregationResults[$aggName] = new MinMaxAggregationResult($agg['min'], $agg['max']);
        }

        return $aggregationResults;
    }
}
