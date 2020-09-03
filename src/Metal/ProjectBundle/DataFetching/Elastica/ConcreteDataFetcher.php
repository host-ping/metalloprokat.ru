<?php

namespace Metal\ProjectBundle\DataFetching\Elastica;

use Elastica\Query;
use Elastica\Query\BoolQuery;
use Elastica\Result;
use Metal\ProductsBundle\DataFetching\DataRequest;
use Metal\ProjectBundle\DataFetching\Result\Item;

interface ConcreteDataFetcher
{
    public const ITEMS_OVERRIDE_AGGREGATION = 'items_override';

    public const TOTAL_ITEMS_COUNT_OVERRIDE_AGGREGATION = 'total_items_count_override';

    public const QUERY_MODE_FULL = 'full';

    public const QUERY_MODE_LIGHT = 'light';

    public const QUERY_MODE_AGGREGATION = 'aggregation';

    public function prepareQuery(Query $query, DataRequest $dataRequest, string $queryMode): void;

    public function reflectFilteringSpecToQuery(
        BoolQuery $boolQuery,
        DataRequest $dataRequest,
        string $queryMode
    ): void;

    public function reflectOrderingSpecToQuery(Query $query, DataRequest $dataRequest, string $queryMode): void;

    /**
     * @param Result[] $results
     *
     * @return Item[]
     */
    public function createItemsFromResults(array $results, DataRequest $dataRequest): array;
}
