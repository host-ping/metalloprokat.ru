<?php

namespace Brouzie\Components\Indexer\Persister;

use Brouzie\Components\Indexer\Operation\ChangeSet;
use Brouzie\Components\Indexer\Operation\Criteria;
use Brouzie\Components\Indexer\Persister;

class ChainPersister implements Persister
{
    private $persisters;

    /**
     * @param iterable|Persister[] $persisters
     */
    public function __construct(iterable $persisters)
    {
        $this->persisters = $persisters;
    }

    public function persist(array $entries): void
    {
        $this->map(__FUNCTION__, $entries);
    }

    public function update(ChangeSet $changeSet, Criteria $criteria): void
    {
        $this->map(__FUNCTION__, $changeSet, $criteria);
    }

    public function delete(array $ids): void
    {
        $this->map(__FUNCTION__, $ids);
    }

    public function clear(): void
    {
        $this->map(__FUNCTION__);
    }

    private function map($method, ...$args)
    {
        foreach ($this->persisters as $persister) {
            $persister->$method(...$args);
        }
    }
}
