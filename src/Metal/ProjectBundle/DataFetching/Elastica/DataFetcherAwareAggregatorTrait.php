<?php

namespace Metal\ProjectBundle\DataFetching\Elastica;

trait DataFetcherAwareAggregatorTrait
{
    private $dataFetcher;

    public function setDataFetcher(ConcreteDataFetcher $dataFetcher)
    {
        $this->dataFetcher = $dataFetcher;
    }

    public function getDataFetcher(): ConcreteDataFetcher
    {
        return $this->dataFetcher;
    }
}
