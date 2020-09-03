<?php

namespace Metal\ProjectBundle\DataFetching\Util;

use Elastica\ResultSet;

class ElasticaUtil
{
    public static function hasAggregation(string $name, ResultSet $resultSet): bool
    {
        foreach ($resultSet->getAggregations() as $aggName => $agg) {
            if ($name === $aggName) {
                return true;
            }
        }

        return false;
    }

    public static function getAggregationsByPrefix(string $prefix, ResultSet $resultSet, bool $trimPrefix = true): array
    {
        $aggregations = [];
        foreach ($resultSet->getAggregations() as $aggName => $agg) {
            if (0 === strpos($aggName, $prefix)) {
                if ($trimPrefix) {
                    $aggName = substr($aggName, strlen($prefix));
                }

                $aggregations[$aggName] = $agg;
            }
        }

        return $aggregations;
    }
}
