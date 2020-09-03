<?php

namespace Metal\ProjectBundle\DataFetching;

use Metal\ProjectBundle\DataFetching\Pagerfanta\FixedAdapterWithTotalCount;
use Metal\ProjectBundle\DataFetching\Spec\LoadingSpec;
use Pagerfanta\Adapter\FixedAdapter;
use Pagerfanta\Pagerfanta;

class EntityLoader
{
    /**
     * @var ConcreteEntityLoader
     */
    private $entityLoader;

    public function __construct(ConcreteEntityLoader $entityLoader)
    {
        $this->entityLoader = $entityLoader;
    }

    public function getListResultsViewModel(Pagerfanta $pagerfanta, LoadingSpec $options = null, $countOnly = false)
    {
        $viewModel = new ListResultsViewModel();
        if (!$countOnly) {
            $viewModel->pagerfanta = $this->getPagerfantaWithEntities($pagerfanta, $options);
        }

        $viewModel->count = $this->getTotalCountFromPagerfanta($pagerfanta);

        return $viewModel;
    }

    public function getPagerfantaWithEntities(Pagerfanta $pagerfanta, LoadingSpec $options = null)
    {
        $rows = $pagerfanta->getCurrentPageResults();
        $rows = is_array($rows) ? new \ArrayIterator($rows) : $rows;

        $items = $this->entityLoader->getEntitiesByRows($rows, $options);

        $entitiesPagerfanta = new Pagerfanta(new FixedAdapter($pagerfanta->getNbResults(), $items));
        $entitiesPagerfanta->setMaxPerPage($pagerfanta->getMaxPerPage());
        $entitiesPagerfanta->setCurrentPage($pagerfanta->getCurrentPage());

        return $entitiesPagerfanta;
    }

    public function getEntitiesByRows(iterable $rows, LoadingSpec $options = null)
    {
        //TODO: change signature of internal entity loader and use array?
        if (is_array($rows)) {
            $rows = new \ArrayIterator($rows);
        }

        return $this->entityLoader->getEntitiesByRows($rows, $options);
    }

    private function getTotalCountFromPagerfanta(Pagerfanta $pagerfanta)
    {
        $pagerfanta->getCurrentPageResults();

        $adapter = $pagerfanta->getAdapter();
        if (!$adapter instanceof FixedAdapterWithTotalCount) {
            throw new \InvalidArgumentException('Expected Pagerfanta with FixedAdapterWithTotalCount adapter.');
        }

        return $adapter->getTotalCount();
    }
}
