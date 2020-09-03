<?php

namespace Metal\ProjectBundle\DataFetching;

use Metal\ProjectBundle\DataFetching\Result\SourceResponse;
use Metal\ProjectBundle\DataFetching\Spec\Aggregation\Aggregation;
use Metal\ProjectBundle\DataFetching\Spec\FilteringSpec;

interface AdvancedDataFetcher extends DataFetcher
{
    /**
     * @param Aggregation[] $aggregations
     * @param CacheProfile|int|false|null $cache false for force no cache, int for cache ttl, null for default cache ttl
     */
    public function getAggregations(FilteringSpec $filteringSpec, array $aggregations, $cache = null): SourceResponse;
}
