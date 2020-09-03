<?php

namespace Metal\ProjectBundle\DataFetching\Result\Aggregation;

class CountsAggregationResult implements AggregationResult
{
    /**
     * @var array<mixed, int>
     */
    private $counts;

    public function __construct(array $counts)
    {
        $this->counts = $counts;
    }

    public function getCounts(): array
    {
        return $this->counts;
    }

    public function getIds(): array
    {
        return array_keys($this->counts);
    }
}
