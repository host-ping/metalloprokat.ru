<?php

namespace Brouzie\Components\Indexer\Persister;

use Brouzie\Components\Indexer\Operation\ChangeSet;
use Brouzie\Components\Indexer\Operation\Criteria;
use Brouzie\Components\Indexer\Persister;
use Brouzie\Components\Indexer\Normalizer;

class NormalizerAwarePersister implements Persister
{
    private $persister;

    private $normalizer;

    public function __construct(Persister $persister, Normalizer $normalizer)
    {
        $this->persister = $persister;
        $this->normalizer = $normalizer;
    }

    public function persist(array $entries): void
    {
        $entries = array_map([$this->normalizer, 'normalize'], $entries);
        $this->persister->persist($entries);
    }

    public function update(ChangeSet $changeSet, Criteria $criteria): void
    {
        $this->persister->update($changeSet, $criteria);
    }

    public function delete(array $ids): void
    {
        $this->persister->delete($ids);
    }

    public function clear(): void
    {
        $this->persister->clear();
    }
}
