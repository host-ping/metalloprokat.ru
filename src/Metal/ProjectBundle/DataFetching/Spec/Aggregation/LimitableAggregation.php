<?php

namespace Metal\ProjectBundle\DataFetching\Spec\Aggregation;

trait LimitableAggregation
{
    private $limit;

    public function setLimit(int $limit): void
    {
        $this->limit = $limit;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }
}
