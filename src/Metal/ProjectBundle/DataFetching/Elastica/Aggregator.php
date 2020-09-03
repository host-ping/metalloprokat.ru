<?php

namespace Metal\ProjectBundle\DataFetching\Elastica;

use Elastica\Query;
use Elastica\ResultSet;
use Metal\ProductsBundle\DataFetching\DataRequest;
use Metal\ProjectBundle\DataFetching\Result\Aggregation\AggregationResult;
use Metal\ProjectBundle\DataFetching\Spec\Aggregation\Aggregation;

interface Aggregator
{
    public function getAggregationsPrefix(): string;

    /**
     * Maps aggregation to query. Returns true if this aggregator can map given aggregation to query, false otherwise.
     */
    public function buildAggregation(Query $query, DataRequest $dataRequest, Aggregation $aggregation): bool;

    /**
     * Extract and process aggregations from result set.
     *
     * @param array $aggregations Indexed array of aggregations by prefix.
     *
     * @return AggregationResult[] An indexed array of aggregation results.
     */
    public function processAggregationResult(array $aggregations, ResultSet $resultSet): array;
}
