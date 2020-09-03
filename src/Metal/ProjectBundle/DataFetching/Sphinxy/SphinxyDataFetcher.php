<?php

namespace Metal\ProjectBundle\DataFetching\Sphinxy;

use Brouzie\Sphinxy\Connection;
use Brouzie\Sphinxy\Pagerfanta\Adapter\SphinxyQbAdapter;
use Brouzie\Sphinxy\Query\MultiResultSet;
use Brouzie\Sphinxy\Query\ResultSet;
use Brouzie\Sphinxy\QueryBuilder;
use Metal\ProjectBundle\DataFetching\CacheProfile;
use Metal\ProjectBundle\DataFetching\DataFetcher;
use Metal\ProjectBundle\DataFetching\Pagerfanta\FixedAdapterWithTotalCount;
use Metal\ProjectBundle\DataFetching\Spec\FacetSpec;
use Metal\ProjectBundle\DataFetching\Spec\FilteringSpec;
use Metal\ProjectBundle\DataFetching\Spec\OrderingSpec;
use Metal\ProjectBundle\DataFetching\Util\CacheUtil;
use Pagerfanta\Pagerfanta;
use Symfony\Component\Cache\Adapter\TagAwareAdapterInterface;

class SphinxyDataFetcher implements DataFetcher
{
    protected const CACHE_PATTERN_FACET = 'df_facet_%s';

    protected const CACHE_PATTERN_COUNT = 'df_count_%s';

    protected const CACHE_PATTERN_RESULT = 'df_result_%s';

    /**
     * @var Connection
     */
    private $sphinxy;

    /**
     * @var ConcreteDataFetcher
     */
    private $dataFetcher;

    /**
     * @var TagAwareAdapterInterface
     */
    private $cache;

    public function __construct(Connection $sphinxy, ConcreteDataFetcher $dataFetcher, TagAwareAdapterInterface $cache = null)
    {
        $this->sphinxy = $sphinxy;
        $this->dataFetcher = $dataFetcher;
        $this->cache = $cache;
    }

    public function getPagerfantaByCriteria(
        FilteringSpec $filteringSpec,
        OrderingSpec $orderingSpec = null,
        int $perPage = null,
        int $page = 1,
        $cache = false
    ): Pagerfanta {
        $resultSet = $this->getResultSetByCriteria($filteringSpec, $orderingSpec, $perPage, $page, $cache);

        $items = [];
        $hasItems = false;
        foreach ($resultSet as $row) {
            if (isset($row['_item'])) {
                $items[] = $row['_item'];
                $hasItems = true;
            }
        }

        if (!$hasItems) {
            $items = $resultSet->getIterator();
        }

        $pagerfanta = new Pagerfanta(
            new FixedAdapterWithTotalCount(
                $resultSet->getAllowedCount(),
                $items,
                $resultSet->getTotalCount()
            )
        );
        $pagerfanta->setMaxPerPage($perPage);
        $pagerfanta->setCurrentPage($page);

        return $pagerfanta;
    }

    public function getResultSetByCriteria(FilteringSpec $criteria, OrderingSpec $orderBy = null, $perPage = null, $page = 1, $cache = false): ResultSet
    {
        $callable = function() use ($criteria, $orderBy, $perPage, $page) {
            $qb = $this->buildSphinxQuery($criteria, $orderBy);

            $adapter = new SphinxyQbAdapter($qb);
            $pagerfanta = new Pagerfanta($adapter);
            $pagerfanta->setMaxPerPage($perPage);
            $pagerfanta->setCurrentPage($page);

            $pagerfanta->getCurrentPageResults();
            $resultSet = $adapter->getPreviousResultSet();

            // inlined calculateOffsetForCurrentPageResults
            $offset = ($pagerfanta->getCurrentPage() - 1) * $pagerfanta->getMaxPerPage();

            $this->dataFetcher->filterResultSet($resultSet, $criteria, $offset, $pagerfanta->getMaxPerPage());

            return $resultSet;
        };

        $keyParts = array(
            'per_page' => $perPage,
            'page' => $page,
        );

        $cacheProfile = CacheUtil::getCacheProfile($cache, [$criteria, $orderBy], self::CACHE_PATTERN_RESULT, $keyParts);

        return $this->cacheCallable($callable, $cacheProfile);
    }

    public function getItemsCountByCriteria(FilteringSpec $filteringSpec, $cache = null): int
    {
        $callable = function() use ($filteringSpec) {
            return $this
                ->getResultSetByCriteria($filteringSpec, null, 1)
                ->getTotalCount();
        };

        $cacheProfile = CacheUtil::getCacheProfile($cache, [$filteringSpec], self::CACHE_PATTERN_COUNT);

        return $this->cacheCallable($callable, $cacheProfile);
    }

    public function getFacetedResultSetByCriteria(FilteringSpec $criteria, FacetSpec $facetSpec, $cache = null): MultiResultSet
    {
        $callable = function() use ($criteria, $facetSpec) {
            $qb = $this->buildSphinxQuery($criteria);
            $qb->setMaxResults(1);

            $qbs = array();
            foreach ($facetSpec->facets as $facet) {
                $subQb = $qb;
                if (isset($facet['criteria'])) {
                    $subQb = $this->buildSphinxQuery($facet['criteria']);
                    $subQb->setMaxResults(1);
                    $qbs[] = $subQb;
                }
                $subQb
                    ->facet($facet['column'], null, $facet['order'], $facet['direction'], $facet['limit'], $facet['skip'])
                    ->nameResultSet($facet['name'] ?: $facet['column']);
            }

            $result = $qb->getMultiResult();
            foreach ($qbs as $qb) {
                /* @var $qb QueryBuilder */
                $subResult = $qb->getMultiResult();
                $result->merge($subResult);
            }

            return $result;
        };

        $cacheProfile = CacheUtil::getCacheProfile($cache, [$criteria, $facetSpec], self::CACHE_PATTERN_FACET);

        return $this->cacheCallable($callable, $cacheProfile);
    }

    private function cacheCallable(callable $callable, CacheProfile $cacheProfile = null)
    {
        if (!$this->cache || !$cacheProfile) {
            return $callable();
        }

        $item = $this->cache->getItem($cacheProfile->getKey());

        if ($item->isHit()) {
            return $item->get();
        }

        $result = $callable();

        $cacheProfile->configureCacheItem($item);
        $this->cache->save($item->set($result));

        return $result;
    }

    private function buildSphinxQuery(FilteringSpec $criteria, OrderingSpec $orderBy = null)
    {
        $qb = $this->sphinxy->createQueryBuilder();

        $this->dataFetcher->initializeQueryBuilder($qb);
        $this->dataFetcher->applyFilteringSpec($qb, $criteria);
        $this->dataFetcher->applyOrderingSpec($qb, $orderBy);

        $qb->setMaxResults($criteria->maxMatches);
        $qb->setOption('max_matches', $criteria->maxMatches);

        if (false) {
            $trace = array();
            foreach (debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS) as $line) {
                $traceItem = '';
                if (isset($line['class'])) {
                    $traceItem .= $line['class'].'::';
                }
                if (isset($line['function'])) {
                    $traceItem .= $line['function'];
                }
                $trace[] = $traceItem;
            }
            $trace = implode(';', $trace);

            $qb
                ->setOption('comment', ':query_comment')
                ->setParameter('query_comment', $trace);
        }

        return $qb;
    }
}
