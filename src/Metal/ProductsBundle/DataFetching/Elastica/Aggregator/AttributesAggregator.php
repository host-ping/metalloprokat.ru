<?php

namespace Metal\ProductsBundle\DataFetching\Elastica\Aggregator;

use Elastica\Aggregation\Filter;
use Elastica\Aggregation\Terms;
use Elastica\Query;
use Elastica\Query\BoolQuery;
use Elastica\ResultSet;
use Metal\ProductsBundle\DataFetching\DataRequest;
use Metal\ProductsBundle\DataFetching\Elastica\ProductIndex;
use Metal\ProductsBundle\DataFetching\Spec\Aggregation\AttributesAggregation;
use Metal\ProductsBundle\DataFetching\Spec\ProductsFilteringSpec;
use Metal\ProjectBundle\DataFetching\Elastica\AbstractAggregator;
use Metal\ProjectBundle\DataFetching\Elastica\DataFetcherAwareAggregator;
use Metal\ProjectBundle\DataFetching\Elastica\DataFetcherAwareAggregatorTrait;
use Metal\ProjectBundle\DataFetching\Result\Aggregation\CountsAggregationResult;
use Metal\ProjectBundle\DataFetching\Elastica\ConcreteDataFetcher;
use Metal\ProjectBundle\DataFetching\Spec\Aggregation\Aggregation;

class AttributesAggregator extends AbstractAggregator implements DataFetcherAwareAggregator
{
    use DataFetcherAwareAggregatorTrait;

    public function buildAggregation(Query $query, DataRequest $dataRequest, Aggregation $aggregation): bool
    {
        if (!$aggregation instanceof AttributesAggregation) {
            return false;
        }

        /** @var ProductsFilteringSpec $commonFilteringSpec */
        $commonFilteringSpec = $dataRequest->getFilteringSpec(ProductsFilteringSpec::class);
        $filteringSpec = $commonFilteringSpec->diff($aggregation->getFilteringSpec());

        $filterAgg = null;
        if ($filteringSpec) {
            $boolQuery = new BoolQuery();
            $filterAgg = new Filter($this->getAggregationName($aggregation, ':filtered'), $boolQuery);

            $this->getDataFetcher()->reflectFilteringSpecToQuery(
                $boolQuery,
                new DataRequest($filteringSpec, null),
                ConcreteDataFetcher::QUERY_MODE_AGGREGATION
            );
        }

        $attributesAggregation = new Terms($this->getAggregationName($aggregation));
        $attributesAggregation->setField(sprintf('%s.%d', ProductIndex::ATTRIBUTES, $aggregation->getAttributeId()));
        $attributesAggregation->setSize($aggregation->getLimit());
        //TODO: support sort
//        $attributesAggregation->setOrder('min_products_price', 'asc');

        if ($filterAgg) {
            $filterAgg->addAggregation($attributesAggregation);
            $query->addAggregation($filterAgg);
        } else {
            $query->addAggregation($attributesAggregation);
        }

        return true;
    }

    public function processAggregationResult(array $aggregations, ResultSet $resultSet): array
    {
        $aggregationResults = [];
        foreach ($aggregations as $aggName => $agg) {
            $aggName = preg_replace('#:filtered$#', '', $aggName, 1, $filtered);
            $buckets = $filtered ? $agg[$this->prefixString($aggName)]['buckets'] : $agg['buckets'];

            $aggregationResult = [];
            foreach ($buckets as $bucket) {
                $aggregationResult[$bucket['key']] = $bucket['doc_count'];
            }

            $aggregationResults[$aggName] = new CountsAggregationResult($aggregationResult);
        }

        return $aggregationResults;
    }
}
