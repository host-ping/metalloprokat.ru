<?php

namespace Brouzie\Bridge\Sphinxy\Indexer;

use Brouzie\Components\Indexer\Entry;
use Brouzie\Components\Indexer\Operation\ChangeSet;
use Brouzie\Components\Indexer\Operation\Criteria;
use Brouzie\Components\Indexer\Persister;
use Brouzie\Sphinxy\Connection;

class SphinxyPersister implements Persister
{
    private $conn;

    private $index;

    private $mapper;

    public function __construct(Connection $conn, string $index, OperationMapper $mapper = null)
    {
        $this->conn = $conn;
        $this->index = $index;
        $this->mapper = $mapper;
    }

    /**
     * @param object[]|Entry[] $entries
     */
    public function persist(array $entries): void
    {
        $escaper = $this->conn->getEscaper();
        $insertQb = $this->conn
            ->createQueryBuilder()
            ->replace($this->getQuotedIndex());

        foreach ($entries as $entry) {
            $data = $entry->getDocumentData();
            $data['id'] = $entry->getId();

            $insertQb->addValues($escaper->quoteSetArr($data));
        }

        $insertQb->execute();
    }

    public function update(ChangeSet $changeSet, Criteria $criteria): void
    {
        if (null === $this->mapper) {
            throw new \RuntimeException('Operation mapper should be set to perform update queries.');
        }

        $qb = $this->conn->createQueryBuilder()
            ->update($this->getQuotedIndex());

        $this->mapper->mapCriteriaToQueryBuilder($criteria, $qb);
        $this->mapper->mapChangeSetToQueryBuilder($changeSet, $qb);

        $qb->execute();
    }

    public function delete(array $ids): void
    {
        $this->conn->createQueryBuilder()
            ->delete($this->getQuotedIndex())
            ->where('id IN :ids')
            ->setParameter('ids', $ids)
            ->execute();
    }

    public function clear(): void
    {
        $this->conn->executeUpdate(sprintf('TRUNCATE RTINDEX %s', $this->getQuotedIndex()));
    }

    private function getQuotedIndex(): string
    {
        return $this->conn->getEscaper()->quoteIdentifier($this->index);
    }
}
