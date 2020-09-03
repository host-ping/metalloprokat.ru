<?php

namespace Metal\StatisticBundle\Repository;

use Doctrine\ORM\QueryBuilder;

class StatsCityRepository extends ClientStatsRepository
{
    protected function processStatsResultQuery(QueryBuilder $qb, array $criteria)
    {
        parent::processStatsResultQuery($qb, $criteria);

        $qb
            ->join('stats.city', 'city')
            ->groupBy('stats.city');
    }
}
