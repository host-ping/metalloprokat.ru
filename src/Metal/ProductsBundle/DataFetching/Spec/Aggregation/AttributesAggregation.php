<?php

namespace Metal\ProductsBundle\DataFetching\Spec\Aggregation;

use Metal\ProductsBundle\DataFetching\Spec\ProductsFilteringSpec;
use Metal\ProjectBundle\DataFetching\Spec\Aggregation\AbstractAggregation;
use Metal\ProjectBundle\DataFetching\Spec\Aggregation\FilteringSpecAwareAggregationTrait;
use Metal\ProjectBundle\DataFetching\Spec\Aggregation\LimitableAggregation;
use Metal\ProjectBundle\DataFetching\Spec\FilteringSpec;
use Metal\ProjectBundle\DataFetching\Spec\Aggregation\FilteringSpecAwareAggregation;

class AttributesAggregation extends AbstractAggregation implements FilteringSpecAwareAggregation
{
    use FilteringSpecAwareAggregationTrait;

    use LimitableAggregation;

    private $attributeId;

    public function __construct(string $name, int $attributeId, int $limit)
    {
        parent::__construct($name);
        $this->setLimit($limit);
        $this->attributeId = $attributeId;
    }

    public function getAttributeId(): int
    {
        return $this->attributeId;
    }

    public function resetFilteringSpec(FilteringSpec $filteringSpec): void
    {
        /** @var ProductsFilteringSpec $filteringSpec */
        if (isset($filteringSpec->productAttrsByGroup[$this->attributeId])) {
            unset($filteringSpec->productAttrsByGroup[$this->attributeId]);
        }
    }
}
