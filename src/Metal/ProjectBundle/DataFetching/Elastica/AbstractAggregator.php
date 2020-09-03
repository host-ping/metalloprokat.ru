<?php

namespace Metal\ProjectBundle\DataFetching\Elastica;

use Metal\ProjectBundle\DataFetching\Spec\Aggregation\Aggregation;

abstract class AbstractAggregator implements Aggregator
{
    private $aggregationPrefix;

    public function getAggregationsPrefix(): string
    {
        if (null !== $this->aggregationPrefix) {
            return $this->aggregationPrefix;
        }

        $fqcn = get_class($this);
        $sqcn = substr($fqcn, strrpos($fqcn, '\\') + 1);

        return $this->aggregationPrefix = sprintf('%s_%s:', strtolower($sqcn), substr(sha1($fqcn), 0, 8));
    }

    protected function getAggregationName(Aggregation $aggregation, string $suffix = ''): string
    {
        return $this->prefixString($aggregation->getName(), $suffix);
    }

    protected function prefixString(string $name, string $suffix = ''): string
    {
        return $this->getAggregationsPrefix().$name.$suffix;
    }
}
