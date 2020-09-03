<?php

namespace Metal\StatisticBundle\Repository;

use Doctrine\ORM\QueryBuilder;

class StatsCategoryRepository extends ClientStatsRepository
{
    protected function processStatsResultQuery(QueryBuilder $qb, array $criteria)
    {
        parent::processStatsResultQuery($qb, $criteria);

        $qb
            ->join('stats.category', 'category')
            ->groupBy('stats.category');
    }
}
