<?php

namespace Brouzie\Components\Indexer;

use Brouzie\Components\Indexer\Operation\ChangeSet;
use Brouzie\Components\Indexer\Operation\Criteria;

interface Indexer
{
    /**
     * Performs full reindex.
     */
    public function reindex(): void;

    /**
     * Performs partial reindex of items by given ids.
     */
    public function reindexIds(array $ids): void;

    public function update(ChangeSet $changeSet, Criteria $criteria): void;

    public function delete(array $ids): void;

    public function clear(): void;
}
