<?php

namespace Metal\ProjectBundle\DataFetching\Result\Aggregation;

use Metal\ProjectBundle\DataFetching\Result\Item;

class ItemsAggregationResult implements AggregationResult
{
    private $items;

    /**
     * @param Item[] $items
     */
    public function __construct($items)
    {
        $this->items = $items;
    }

    /**
     * @return Item[]
     */
    public function getItems(): array
    {
        return $this->items;
    }
}
