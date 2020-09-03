<?php

namespace Metal\DemandsBundle\DataFetching;

use Brouzie\Sphinxy\Query\MultiResultSet;

class FacetIntervalResultExtractor
{
    protected $intervals = array();

    public function __construct(MultiResultSet $facetsResultSet, $column)
    {
        $associatedRows = array(
            4 => 'demands_count_day',
            3 => 'demands_count_week',
            2 => 'demands_count_month',
            1 => 'demands_count_year',
            0 => 'demands_count_total',
        );

        $createdAtToCounts = array();
        if ($facetsResultSet->hasResultSet($column)) {
            foreach ($facetsResultSet->getResultSet($column) as $row) {
                $createdAtToCounts[$row[$column]] = $row['count(*)'];
            }
        }

        $sumIntervalCount = 0;
        foreach ($associatedRows as $key => $intervalName) {
            $this->intervals[$intervalName] = $sumIntervalCount;
            if (isset($createdAtToCounts[$key])) {
                $this->intervals[$intervalName] = $sumIntervalCount += $createdAtToCounts[$key];
            }
        }
    }

    public function appendInterval($intervalName, $resultsCount)
    {
        $this->intervals[$intervalName] = $resultsCount;
    }

    public function getIntervals()
    {
        return $this->intervals;
    }
}
