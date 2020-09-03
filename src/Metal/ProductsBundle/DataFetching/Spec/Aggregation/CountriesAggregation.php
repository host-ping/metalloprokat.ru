<?php

namespace Metal\ProductsBundle\DataFetching\Spec\Aggregation;

use Metal\ProductsBundle\DataFetching\Spec\ProductsFilteringSpec;
use Metal\ProjectBundle\DataFetching\Spec\Aggregation\AbstractAggregation;
use Metal\ProjectBundle\DataFetching\Spec\Aggregation\LimitableAggregation;
use Metal\ProjectBundle\DataFetching\Spec\FilteringSpec;
use Metal\ProjectBundle\DataFetching\Spec\Aggregation\FilteringSpecAwareAggregation;
use Metal\ProjectBundle\DataFetching\Spec\Aggregation\FilteringSpecAwareAggregationTrait;

class CountriesAggregation extends AbstractAggregation implements FilteringSpecAwareAggregation
{
    use FilteringSpecAwareAggregationTrait;

    use LimitableAggregation;

    public function __construct(string $name, int $limit = 5)
    {
        parent::__construct($name);
        $this->setLimit($limit);
    }

    public function resetFilteringSpec(FilteringSpec $filteringSpec): void
    {
        /** @var ProductsFilteringSpec $filteringSpec */
        $filteringSpec->resetTerritoryFilters(true);
    }
}
