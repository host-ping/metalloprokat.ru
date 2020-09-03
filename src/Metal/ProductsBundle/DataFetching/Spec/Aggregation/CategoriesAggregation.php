<?php

namespace Metal\ProductsBundle\DataFetching\Spec\Aggregation;

use Metal\ProjectBundle\DataFetching\Spec\Aggregation\AbstractAggregation;
use Metal\ProjectBundle\DataFetching\Spec\Aggregation\LimitableAggregation;

class CategoriesAggregation extends AbstractAggregation
{
    use LimitableAggregation;

    public function __construct(string $name, int $limit)
    {
        parent::__construct($name);
        $this->setLimit($limit);
    }
}
