<?php

namespace Metal\ProjectBundle\DataFetching;

use Metal\ProjectBundle\DataFetching\Result\Item;
use Metal\ProjectBundle\DataFetching\Spec\LoadingSpec;

interface ConcreteEntityLoader
{
    /**
     * @throws UnsupportedSpecException
     *
     * @param \Traversable|Item[] $rows
     * @param LoadingSpec|null $options
     *
     * @return object[]
     */
    public function getEntitiesByRows(\Traversable $rows, LoadingSpec $options = null);
}
