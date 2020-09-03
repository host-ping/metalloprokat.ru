<?php

namespace Metal\ProjectBundle\DataFetching\Result\Aggregation;

class CountAggregationResult implements AggregationResult
{
    private $count;

    public function __construct(int $count)
    {
        $this->count = $count;
    }

    public function getCount(): int
    {
        return $this->count;
    }
}
