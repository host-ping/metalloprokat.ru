<?php

namespace Metal\ProjectBundle\DataFetching\Elastica;

interface DataFetcherAwareAggregator
{
    public function setDataFetcher(ConcreteDataFetcher $dataFetcher);

    public function getDataFetcher(): ConcreteDataFetcher;
}
