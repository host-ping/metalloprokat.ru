<?php

namespace Metal\ProductsBundle\DataFetching;

use Metal\ProjectBundle\DataFetching\Spec\Aggregation\Aggregation;
use Metal\ProjectBundle\DataFetching\Spec\FilteringSpec;
use Metal\ProjectBundle\DataFetching\Spec\OrderingSpec;
use Metal\ProjectBundle\DataFetching\UnsupportedSpecException;

class DataRequest
{
    private $filteringSpec;

    private $orderingSpec;

    private $aggregations;

    /**
     * @param Aggregation[] $aggregations
     */
    public function __construct(
        FilteringSpec $filteringSpec,
        ?OrderingSpec $orderingSpec,
        array $aggregations = []
    ) {
        $this->filteringSpec = $filteringSpec;
        $this->orderingSpec = $orderingSpec;
        $this->aggregations = [];
        foreach ($aggregations as $aggregation) {
            $this->addAggregation($aggregation);
        }
    }

    public function getFilteringSpec(string $type = null): FilteringSpec
    {
        if (null !== $type) {
            if (!$this->filteringSpec instanceof $type) {
                throw UnsupportedSpecException::create($type, $this->filteringSpec);
            }
        }

        return $this->filteringSpec;
    }

    public function getOrderingSpec(string $type = null, $defaultSpec = null): ?OrderingSpec
    {
        if (null !== $type) {
            if ($this->orderingSpec && !$this->orderingSpec instanceof $type) {
                throw UnsupportedSpecException::create($type, $this->orderingSpec);
            }
        }

        return $this->orderingSpec ?: $defaultSpec;
    }

    /**
     * @return Aggregation[]
     */
    public function getAggregations(): array
    {
        return $this->aggregations;
    }

    public function addAggregation(Aggregation $aggregation)
    {
        $this->aggregations[] = $aggregation;
    }
}
