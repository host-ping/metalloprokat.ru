<?php

namespace Metal\ProductsBundle\DataFetching\Elastica\Aggregator;

use Elastica\Aggregation\Terms;
use Elastica\Query;
use Elastica\ResultSet;
use Metal\ProductsBundle\DataFetching\DataRequest;
use Metal\ProductsBundle\DataFetching\Elastica\ProductIndex;
use Metal\ProductsBundle\DataFetching\Spec\Aggregation\AttributesIdsAggregation;
use Metal\ProjectBundle\DataFetching\Elastica\AbstractAggregator;
use Metal\ProjectBundle\DataFetching\Result\Aggregation\CountsAggregationResult;
use Metal\ProjectBundle\DataFetching\Spec\Aggregation\Aggregation;

class AttributesIdsAggregator extends AbstractAggregator
{
    public function buildAggregation(Query $query, DataRequest $dataRequest, Aggregation $aggregation): bool
    {
        if (!$aggregation instanceof AttributesIdsAggregation) {
            return false;
        }

        $attributesAggregation = new Terms($this->getAggregationName($aggregation));
        $attributesAggregation->setField(ProductIndex::ATTRIBUTES_IDS);
        $attributesAggregation->setSize($aggregation->getLimit());

        $query->addAggregation($attributesAggregation);

        return true;
    }

    public function processAggregationResult(array $aggregations, ResultSet $resultSet): array
    {
        $aggregationResults = [];
        foreach ($aggregations as $aggName => $agg) {
            $buckets = $agg['buckets'];

            $aggregationResult = [];
            foreach ($buckets as $bucket) {
                $aggregationResult[$bucket['key']] = $bucket['doc_count'];
            }

            $aggregationResults[$aggName] = new CountsAggregationResult($aggregationResult);
        }

        return $aggregationResults;
    }
}
