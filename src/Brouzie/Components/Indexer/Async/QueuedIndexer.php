<?php

namespace Brouzie\Components\Indexer\Async;

use Brouzie\Components\Indexer\Async\Message\IdsMessage;
use Brouzie\Components\Indexer\Async\Message\UpdateOperationMessage;
use Brouzie\Components\Indexer\Indexer;
use Brouzie\Components\Indexer\Operation\ChangeSet;
use Brouzie\Components\Indexer\Operation\Criteria;
use Enqueue\Client\ProducerInterface;

class QueuedIndexer implements Indexer
{
    private $producer;

    private $indexer;

    public function __construct(ProducerInterface $producer, Indexer $indexer = null)
    {
        $this->producer = $producer;
        $this->indexer = $indexer;
    }

    public function reindex(): void
    {
        $this->producer->sendCommand(Commands::REINDEX, null);
    }

    public function reindexIds(array $ids): void
    {
        $this->producer->sendCommand(Commands::REINDEX_IDS, new IdsMessage($ids));
    }

    public function update(ChangeSet $changeSet, Criteria $criteria): void
    {
        $this->producer->sendCommand(Commands::UPDATE, new UpdateOperationMessage($changeSet, $criteria));
    }

    public function delete(array $ids): void
    {
        $this->producer->sendCommand(Commands::DELETE, new IdsMessage($ids));
    }

    public function clear(): void
    {
        $this->producer->sendCommand(Commands::CLEAR, null);
    }

    public function getInnerIndexer(): ?Indexer
    {
        return $this->indexer;
    }
}
