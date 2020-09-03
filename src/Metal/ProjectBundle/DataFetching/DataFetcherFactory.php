<?php

namespace Metal\ProjectBundle\DataFetching;

interface DataFetcherFactory
{
    /**
     * @param string $scope
     * @return DataFetcher|AdvancedDataFetcher
     */
    public function getDataFetcher(string $scope): DataFetcher;
}
