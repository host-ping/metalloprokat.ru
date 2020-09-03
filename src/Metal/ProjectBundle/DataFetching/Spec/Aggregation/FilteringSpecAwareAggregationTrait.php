<?php

namespace Metal\ProjectBundle\DataFetching\Spec\Aggregation;

use Metal\ProjectBundle\DataFetching\Spec\FilteringSpec;

trait FilteringSpecAwareAggregationTrait
{
    private $filteringSpec;

    public function setFilteringSpec(FilteringSpec $filteringSpec): void
    {
        /** @var FilteringSpecAwareAggregation $this */
        $this->resetFilteringSpec($filteringSpec);
        $this->filteringSpec = $filteringSpec;
    }

    public function getFilteringSpec(): FilteringSpec
    {
        return $this->filteringSpec;
    }
}
