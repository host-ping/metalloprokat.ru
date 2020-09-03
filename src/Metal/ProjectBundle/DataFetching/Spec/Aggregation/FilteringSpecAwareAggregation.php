<?php

namespace Metal\ProjectBundle\DataFetching\Spec\Aggregation;

use Metal\ProjectBundle\DataFetching\Spec\FilteringSpec;

interface FilteringSpecAwareAggregation
{
    /**
     * Resets filters which prevents building this aggregation.
     */
    public function resetFilteringSpec(FilteringSpec $filteringSpec): void;

    /**
     * Stores copy of actual filtering spec for reflecting this filters to query later on aggregation build phase.
     */
    public function setFilteringSpec(FilteringSpec $filteringSpec): void;

    /**
     * Returns actual filtering spec.
     */
    public function getFilteringSpec(): FilteringSpec;
}
