<?php

namespace Metal\ProductsBundle\DataFetching;

use Metal\ProductsBundle\DataFetching\Elastica\ProductIndex;
use Metal\ProjectBundle\DataFetching\AdvancedDataFetcher;
use Metal\ProjectBundle\DataFetching\DataFetcher;
use Metal\ProjectBundle\DataFetching\DataFetcherFactory;
use Metal\ProjectBundle\DataFetching\Sphinxy\SphinxyDataFetcher;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ProductDataFetcherFactory implements DataFetcherFactory
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @return DataFetcher|AdvancedDataFetcher|SphinxyDataFetcher
     */
    public function getDataFetcher(string $scope): DataFetcher
    {
        if ($scope !== ProductIndex::SCOPE) {
            throw new \InvalidArgumentException('This factory supports only "product" scope.');
        }

//        return $this->container->get('metal.products.data_fetcher');
        return $this->container->get('metal_products.data_fetcher_elastica');
    }
}
