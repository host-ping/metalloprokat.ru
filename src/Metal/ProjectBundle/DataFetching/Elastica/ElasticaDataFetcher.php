<?php

namespace Metal\ProjectBundle\DataFetching\Elastica;

use Elastica\Query;
use Elastica\ResultSet;
use FOS\ElasticaBundle\Elastica\Index;
use Metal\ProductsBundle\DataFetching\DataRequest;
use Metal\ProjectBundle\DataFetching\AdvancedDataFetcher;
use Metal\ProjectBundle\DataFetching\CacheProfile;
use Metal\ProjectBundle\DataFetching\Pagerfanta\FixedAdapterWithTotalCount;
use Metal\ProjectBundle\DataFetching\Result\Aggregation\CountAggregationResult;
use Metal\ProjectBundle\DataFetching\Result\Aggregation\ItemsAggregationResult;
use Metal\ProjectBundle\DataFetching\Result\Item;
use Metal\ProjectBundle\DataFetching\Result\SourceResponse;
use Metal\ProjectBundle\DataFetching\Spec\Aggregation\Aggregation;
use Metal\ProjectBundle\DataFetching\Spec\FilteringSpec;
use Metal\ProjectBundle\DataFetching\Spec\OrderingSpec;
use Metal\ProjectBundle\DataFetching\Spec\Aggregation\FilteringSpecAwareAggregation;
use Metal\ProjectBundle\DataFetching\Util\CacheUtil;
use Metal\ProjectBundle\DataFetching\Util\ElasticaUtil;
use Pagerfanta\Pagerfanta;
use Symfony\Component\Cache\Adapter\TagAwareAdapterInterface;

class ElasticaDataFetcher implements AdvancedDataFetcher
{
    protected const CACHE_PATTERN_AGGREGATION = 'df_es_agg_%s';

    protected const CACHE_PATTERN_RESULT = 'df_es_result_%s';

    protected const CACHE_PATTERN_COUNT = 'df_es_count_%s';

    private $index;

    private $dataFetcher;

    private $aggregators;

    private $cache;

    /**
     * @param Aggregator[] $aggregators
     */
    public function __construct(
        Index $index,
        ConcreteDataFetcher $dataFetcher,
        iterable $aggregators = [],
        TagAwareAdapterInterface $cache = null
    ) {
        $this->index = $index;
        $this->dataFetcher = $dataFetcher;
        $this->aggregators = $aggregators;
        $this->cache = $cache;
    }

    public function getPagerfantaByCriteria(
        FilteringSpec $filteringSpec,
        OrderingSpec $orderingSpec = null,
        int $perPage = null,
        int $page = 1,
        $cache = false
    ): Pagerfanta {
        $cacheProfile = CacheUtil::getCacheProfile($cache, [$filteringSpec, $orderingSpec], self::CACHE_PATTERN_RESULT);
        $dataRequest = new DataRequest($filteringSpec, $orderingSpec);

        if (!$sourceResponse = $this->getSourceResponseFromCache($cacheProfile)) {
            $query = $this->buildQuery($dataRequest, ConcreteDataFetcher::QUERY_MODE_FULL);

            $offset = self::calculateOffset($page, $perPage);
            $resultSet = $this->index->search($query, ['size' => $perPage, 'from' => $offset]);

            $sourceResponse = $this->createSourceResponseFromResultSet($resultSet, $dataRequest);
            $this->saveSourceResponseInCache($sourceResponse, $cacheProfile);
        }

        $pagerfanta = new Pagerfanta(
            new FixedAdapterWithTotalCount(
                $sourceResponse->getTotalItemsCount(),
                $this->paginateSourceResponse($sourceResponse, $dataRequest, $page, $perPage),
                $sourceResponse->getTotalItemsCount()
            )
        );
        $pagerfanta->setMaxPerPage($perPage);
        $pagerfanta->setCurrentPage($page);

        return $pagerfanta;
    }

    public function getItemsCountByCriteria(FilteringSpec $filteringSpec, $cache = null): int
    {
        $cacheProfile = CacheUtil::getCacheProfile($cache, [$filteringSpec], self::CACHE_PATTERN_COUNT);

        if (!$sourceResponse = $this->getSourceResponseFromCache($cacheProfile)) {
            $dataRequest = new DataRequest($filteringSpec, null);

            //TODO: use query mode light or agg or create count query mode?
            $query = $this->buildQuery($dataRequest, ConcreteDataFetcher::QUERY_MODE_LIGHT);
            $resultSet = $this->index->search($query, ['size' => 0]);
            $sourceResponse = $this->createSourceResponseFromResultSet($resultSet, $dataRequest);

            $this->saveSourceResponseInCache($sourceResponse, $cacheProfile);
        }

        return $sourceResponse->getTotalItemsCount();
    }

    public function getAggregations(FilteringSpec $filteringSpec, array $aggregations, $cache = null): SourceResponse
    {
        $cacheProfile = CacheUtil::getCacheProfile(
            $cache,
            [$filteringSpec],
            self::CACHE_PATTERN_AGGREGATION,
            $aggregations
        );

        if (!$sourceResponse = $this->getSourceResponseFromCache($cacheProfile)) {
            $dataRequest = new DataRequest($filteringSpec, null, $aggregations);
            $query = $this->buildAggregationQuery($dataRequest);
            $resultSet = $this->index->search($query, ['size' => 0]);
            $sourceResponse = $this->createSourceResponseFromResultSet($resultSet, $dataRequest);

            $this->saveSourceResponseInCache($sourceResponse, $cacheProfile);
        }

        return $sourceResponse;
    }

    private function getSourceResponseFromCache(?CacheProfile $cacheProfile): ?SourceResponse
    {
        if (!$this->cache || !$cacheProfile) {
            return null;
        }

        $item = $this->cache->getItem($cacheProfile->getKey());

        return $item->isHit() ? $item->get() : null;
    }

    private function saveSourceResponseInCache(SourceResponse $sourceResponse, ?CacheProfile $cacheProfile): void
    {
        if (!$this->cache || !$cacheProfile) {
            return;
        }

        $item = $this->cache->getItem($cacheProfile->getKey());

        $cacheProfile->configureCacheItem($item);
        $this->cache->save($item->set($sourceResponse));
    }

    /**
     * @return Item[]
     */
    private function paginateSourceResponse(
        SourceResponse $sourceResponse,
        DataRequest $dataRequest,
        int $page,
        int $perPage
    ): array {
        $itemsSlice = [];

        if ($sourceResponse->hasAggregationResult(ConcreteDataFetcher::ITEMS_OVERRIDE_AGGREGATION)) {
            /** @var ItemsAggregationResult $itemsOverrideAgg */
            $itemsOverrideAgg = $sourceResponse->getAggregationResult(ConcreteDataFetcher::ITEMS_OVERRIDE_AGGREGATION);
            $itemsOverride = $itemsOverrideAgg->getItems();
            $itemsSlice = self::sliceItems($itemsOverride, $page, $perPage);
        }

        $itemsCount = count($itemsSlice);
        $extraItemsCount = $perPage - $itemsCount;
        if ($extraItemsCount > 0) {
            $itemsSlice = array_merge($itemsSlice, array_slice($sourceResponse->getItems(), $itemsCount));
        }

        $itemsCount = count($itemsSlice);
        $extraItemsCount = $perPage - $itemsCount;
        if ($extraItemsCount > 0) {
            // create light query for extra results
            $offset = self::calculateOffset($page, $perPage) + $itemsCount;
            // out of range
            if ($offset > $sourceResponse->getTotalItemsCount()) {
                return $itemsSlice;
            }

            $query = $this->buildQuery($dataRequest, ConcreteDataFetcher::QUERY_MODE_LIGHT);

            $resultSet = $this->index
                ->search($query, ['size' => $extraItemsCount, 'from' => $offset]);

            $sourceResponseExtraItems = $this->createSourceResponseFromResultSet($resultSet, $dataRequest);

            $itemsSlice = array_merge($itemsSlice, $sourceResponseExtraItems->getItems());
        }

        return $itemsSlice;
    }

    /**
     * @param Item[] $items
     *
     * @return Item[]
     */
    private static function sliceItems(array $items, int $page, int $perPage): array
    {
        $offset = self::calculateOffset($page, $perPage);
        $length = $perPage;

        return count($items) ? array_slice($items, $offset, $length) : [];
    }

    private static function calculateOffset(int $page, int $perPage): int
    {
        return ($page - 1) * $perPage;
    }

    private function buildQuery(DataRequest $dataRequest, string $queryMode): Query
    {
        $query = new Query();
        $boolQuery = new Query\BoolQuery();
        $query->setQuery($boolQuery);

        $this->dataFetcher->prepareQuery($query, $dataRequest, $queryMode);
        $this->dataFetcher->reflectFilteringSpecToQuery($boolQuery, $dataRequest, $queryMode);
        $this->dataFetcher->reflectOrderingSpecToQuery($query, $dataRequest, $queryMode);

        foreach ($dataRequest->getAggregations() as $aggregation) {
            $this->buildAggregation($query, $dataRequest, $aggregation);
        }

        return $query;
    }

    private function buildAggregationQuery(DataRequest $dataRequest): Query
    {
        $actualFilteringSpec = clone $dataRequest->getFilteringSpec();
        $commonFilteringSpec = $dataRequest->getFilteringSpec();
        foreach ($dataRequest->getAggregations() as $aggregation) {
            if ($aggregation instanceof FilteringSpecAwareAggregation) {
                $aggregation->resetFilteringSpec($commonFilteringSpec);
                $aggregation->setFilteringSpec(clone $actualFilteringSpec);
            }
        }

        $query = new Query();
        $boolQuery = new Query\BoolQuery();
        $query->setQuery($boolQuery);

        $queryMode = ConcreteDataFetcher::QUERY_MODE_AGGREGATION;
        $this->dataFetcher->prepareQuery($query, $dataRequest, $queryMode);
        $this->dataFetcher->reflectFilteringSpecToQuery($boolQuery, $dataRequest, $queryMode);

        foreach ($dataRequest->getAggregations() as $aggregation) {
            $this->buildAggregation($query, $dataRequest, $aggregation);
        }

        return $query;
    }

    private function buildAggregation(Query $query, DataRequest $dataRequest, Aggregation $aggregation): void
    {
        $aggregationBuilt = false;
        foreach ($this->aggregators as $aggregator) {
            if ($aggregator instanceof DataFetcherAwareAggregator) {
                $aggregator->setDataFetcher($this->dataFetcher);
            }

            if ($aggregator->buildAggregation($query, $dataRequest, $aggregation)) {
                $aggregationBuilt = true;
                break;
            }
        }

        if (!$aggregationBuilt) {
            throw new \InvalidArgumentException(
                sprintf('Could find aggregator for building "%s" aggregation.', get_class($aggregation))
            );
        }
    }

    private function createSourceResponseFromResultSet(ResultSet $resultSet, DataRequest $dataRequest): SourceResponse
    {
        $items = $this->dataFetcher->createItemsFromResults($resultSet->getResults(), $dataRequest);

        $aggregationResults = [[]];
        foreach ($this->aggregators as $aggregator) {
            $aggregations = ElasticaUtil::getAggregationsByPrefix($aggregator->getAggregationsPrefix(), $resultSet);
            $aggregationResults[] = $aggregator->processAggregationResult($aggregations, $resultSet);
        }
        $aggregationResults = array_merge(...$aggregationResults);

        $totalItemsCount = $resultSet->getTotalHits();
        if (!empty($aggregationResults[ConcreteDataFetcher::TOTAL_ITEMS_COUNT_OVERRIDE_AGGREGATION])) {
            /** @var CountAggregationResult $totalItemsCountAgg */
            $totalItemsCountAgg = $aggregationResults[ConcreteDataFetcher::TOTAL_ITEMS_COUNT_OVERRIDE_AGGREGATION];
            $totalItemsCount = $totalItemsCountAgg->getCount();
        }

        return new SourceResponse($items, $totalItemsCount, $aggregationResults);
    }
}
