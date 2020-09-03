<?php

namespace Metal\ProjectBundle\DataFetching\Sphinxy;

use Brouzie\Sphinxy\Query\ResultSet;
use Brouzie\Sphinxy\QueryBuilder;
use Metal\ProjectBundle\DataFetching\Spec\FilteringSpec;
use Metal\ProjectBundle\DataFetching\Spec\OrderingSpec;
use Metal\ProjectBundle\DataFetching\UnsupportedSpecException;

interface ConcreteDataFetcher
{
    public function initializeQueryBuilder(QueryBuilder $qb);

    /**
     * @throws UnsupportedSpecException
     *
     * @param QueryBuilder $qb
     * @param FilteringSpec $criteria
     *
     * @return null
     */
    public function applyFilteringSpec(QueryBuilder $qb, FilteringSpec $criteria);

    /**
     * @throws UnsupportedSpecException
     *
     * @param QueryBuilder $qb
     * @param OrderingSpec|null $orderBy
     *
     * @return null
     */
    public function applyOrderingSpec(QueryBuilder $qb, OrderingSpec $orderBy = null);

    public function filterResultSet(ResultSet $resultSet, FilteringSpec $criteria, $offset, $length);
}
