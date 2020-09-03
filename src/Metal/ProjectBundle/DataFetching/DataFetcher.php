<?php

namespace Metal\ProjectBundle\DataFetching;

use Metal\ProjectBundle\DataFetching\Spec\FilteringSpec;
use Metal\ProjectBundle\DataFetching\Spec\OrderingSpec;
use Pagerfanta\Pagerfanta;

interface DataFetcher
{
    public const TTL_INFINITY = 0;
    public const TTL_5MINUTES = 300;
    public const TTL_15MINUTES = 900;
    public const TTL_1HOUR = 3600;
    public const TTL_6HOURS = 21600;
    public const TTL_1DAY = 86400;
    public const TTL_5DAYS = 432000;

    /**
     * @param CacheProfile|int|false|null $cache false for force no cache, int for cache ttl, null for default cache ttl
     */
    public function getPagerfantaByCriteria(
        FilteringSpec $filteringSpec,
        OrderingSpec $orderingSpec = null,
        int $perPage = null,
        int $page = 1,
        $cache = false
    ): Pagerfanta;

    public function getItemsCountByCriteria(FilteringSpec $filteringSpec, $cache = null): int;
}
