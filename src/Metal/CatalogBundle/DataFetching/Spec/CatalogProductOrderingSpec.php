<?php

namespace Metal\CatalogBundle\DataFetching\Spec;

use Metal\ProjectBundle\DataFetching\Spec\OrderingSpec;

class CatalogProductOrderingSpec extends OrderingSpec
{
    public function iterateByBrand()
    {
        return $this->pushOrder('iterateByBrand');
    }
}
