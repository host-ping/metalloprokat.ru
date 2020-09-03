<?php

namespace Metal\ProjectBundle\DataFetching\Result;

use Metal\ProjectBundle\DataFetching\Result\Aggregation\AggregationResult;

/**
 * This class represents response from search engine
 */
class SourceResponse implements \IteratorAggregate, \Serializable
{
    private $items;

    private $totalItemsCount;

    /**
     * @var AggregationResult[]
     */
    private $aggregationResults;

    /**
     * @param Item[] $items
     * @param AggregationResult[] $aggregationResults
     */
    public function __construct(array $items, int $totalItemsCount, array $aggregationResults = [])
    {
        $this->items = $items;
        $this->totalItemsCount = $totalItemsCount;
        $this->aggregationResults = $aggregationResults;
    }

    public function getIterator()
    {
        return yield from $this->items;
    }

    /**
     * @return array|Item[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    public function getTotalItemsCount(): int
    {
        return $this->totalItemsCount;
    }

    public function getItemIds(): array
    {
        $ids = [];
        foreach ($this->items as $item) {
            $ids[] = $item->getId();
        }

        return $ids;
    }

    public function hasAggregationResult(string $name): bool
    {
        return isset($this->aggregationResults[$name]);
    }

    public function getAggregationResult(string $name): AggregationResult
    {
        if (!$this->hasAggregationResult($name)) {
            throw new \InvalidArgumentException('Aggregation result not found.');
        }

        return $this->aggregationResults[$name];
    }

    public function serialize()
    {
        return serialize([$this->totalItemsCount, $this->aggregationResults]);
    }

    public function unserialize($serialized)
    {
        list($this->totalItemsCount, $this->aggregationResults) = unserialize($serialized);
        $this->items = [];
    }
}
