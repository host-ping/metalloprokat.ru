<?php

namespace Metal\DemandsBundle\Widget;

use Metal\DemandsBundle\DataFetching\Spec\DemandFilteringSpec;
use Metal\ProjectBundle\DataFetching\DataFetcher;
use Metal\ProjectBundle\Widget\CountingWidget as ProjectCountingWidget;

class CountingWidget extends ProjectCountingWidget
{
    public function getContext()
    {
        $criteria = new DemandFilteringSpec();
        $criteria->territory($this->options['territory']);

        $demandsCount = $this->dataFetcher->getItemsCountByCriteria($criteria, DataFetcher::TTL_1HOUR);

        return compact('demandsCount');
    }
}
