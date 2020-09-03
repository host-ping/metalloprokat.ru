<?php

namespace Metal\ProductsBundle\DataFetching\Spec\Aggregation;

use Metal\ProductsBundle\DataFetching\Spec\ProductsFilteringSpec;
use Metal\ProjectBundle\DataFetching\Spec\Aggregation\AbstractAggregation;
use Metal\ProjectBundle\DataFetching\Spec\FilteringSpec;
use Metal\ProjectBundle\DataFetching\Spec\Aggregation\FilteringSpecAwareAggregation;
use Metal\ProjectBundle\DataFetching\Spec\Aggregation\FilteringSpecAwareAggregationTrait;

class PriceRangeAggregation extends AbstractAggregation implements FilteringSpecAwareAggregation
{
    use FilteringSpecAwareAggregationTrait;

    public function resetFilteringSpec(FilteringSpec $filteringSpec): void
    {
        /** @var ProductsFilteringSpec $filteringSpec */
        if ($filteringSpec->price) {
            $filteringSpec->clearPrice();
        }
    }
}
