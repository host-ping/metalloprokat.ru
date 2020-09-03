<?php

namespace Metal\ProjectBundle\DataFetching\Spec\Aggregation;

abstract class AbstractAggregation implements Aggregation
{
    protected $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
