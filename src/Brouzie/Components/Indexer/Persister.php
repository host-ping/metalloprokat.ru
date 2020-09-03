<?php

namespace Brouzie\Components\Indexer;

use Brouzie\Components\Indexer\Operation\ChangeSet;
use Brouzie\Components\Indexer\Operation\Criteria;

interface Persister
{
    /**
     * @param object[]|Entry[] $entries
     */
    public function persist(array $entries): void;

    public function update(ChangeSet $changeSet, Criteria $criteria): void;

    public function delete(array $ids): void;

    public function clear(): void;
}
