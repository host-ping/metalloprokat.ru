<?php

namespace Metal\ProjectBundle\Widget;

use Brouzie\WidgetsBundle\Widget\TwigWidget;
use Metal\ProductsBundle\DataFetching\Spec\ProductsFilteringSpec;
use Metal\ProjectBundle\DataFetching\DataFetcher;
use Metal\ProjectBundle\DataFetching\Sphinxy\SphinxyDataFetcher;
use Metal\TerritorialBundle\Entity\TerritoryInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CountingWidget extends TwigWidget
{
    protected $dataFetcher;

    public function __construct(SphinxyDataFetcher $dataFetcher)
    {
        $this->dataFetcher = $dataFetcher;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver
            ->setRequired(array('territory'))
            ->setAllowedTypes('territory', array(TerritoryInterface::class))
        ;
    }

    public function getContext()
    {
        $criteria = new ProductsFilteringSpec();
        $criteria->territory($this->options['territory']);

        $productsCount = $this->dataFetcher->getItemsCountByCriteria($criteria, DataFetcher::TTL_1HOUR);

        $criteria
            ->allowVirtual(true)
            ->loadCompanies(true);

        $companiesCount = $this->dataFetcher->getItemsCountByCriteria($criteria, DataFetcher::TTL_1HOUR);

        return compact('productsCount', 'companiesCount');
    }
}
