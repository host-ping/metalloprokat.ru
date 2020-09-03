<?php

namespace Metal\ProjectBundle\DataFetching\Result\Aggregation;

class MinMaxAggregationResult implements AggregationResult
{
    private $min;

    private $max;

    public function __construct($min, $max)
    {
        $this->min = $min;
        $this->max = $max;
    }

    public function getMin()
    {
        return $this->min;
    }

    public function getMax()
    {
        return $this->max;
    }
}
