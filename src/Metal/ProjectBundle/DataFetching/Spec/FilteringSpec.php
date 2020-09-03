<?php

namespace Metal\ProjectBundle\DataFetching\Spec;

abstract class FilteringSpec
{
    public $maxMatches = 5000;

    public function maxMatches($maxMatches)
    {
        $this->maxMatches = $maxMatches;

        return $this;
    }
}
