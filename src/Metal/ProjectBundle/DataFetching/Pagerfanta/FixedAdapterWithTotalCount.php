<?php

namespace Metal\ProjectBundle\DataFetching\Pagerfanta;

use Pagerfanta\Adapter\FixedAdapter;

class FixedAdapterWithTotalCount extends FixedAdapter
{
    private $totalCount;

    public function __construct(int $nbResults, iterable $results, int $totalCount)
    {
        $this->totalCount = $totalCount;
        parent::__construct($nbResults, $results);
    }

    public function getTotalCount(): int
    {
        return $this->totalCount;
    }
}
