<?php

namespace Brouzie\Components\Indexer\Elastica;

use Brouzie\Components\Indexer\Entry;
use Brouzie\Components\Indexer\Operation\ChangeSet;
use Brouzie\Components\Indexer\Operation\Criteria;
use Brouzie\Components\Indexer\Persister;
use Elastica\Document;
use Elastica\Index;
use Elastica\Query;
use Elastica\Query\BoolQuery;
use Elastica\Query\MatchAll;
use Elastica\Query\Terms;
use Elastica\Response;
use Elastica\Script\Script;
use Elasticsearch\Endpoints\UpdateByQuery;

class ElasticaPersister implements Persister
{
    private $index;

    private $options;

    private $mapper;

    public function __construct(Index $index, OperationMapper $mapper = null, array $options = [])
    {
        $this->index = $index;
        $this->mapper = $mapper;
        $this->options = array_replace(
            [
                'routing_field' => null,
                'type' => null,
            ],
            $options
        );

        if (empty($this->options['type'])) {
            throw new \InvalidArgumentException('Type is required.');
        }
    }

    /**
     * @param Entry[] $entries
     */
    public function persist(array $entries): void
    {
        $documents = [];
        foreach ($entries as $entry) {
            $data = $entry->getDocumentData();
            $document = new Document($entry->getId(), $data, $this->options['type']);

            if (null !== $key = $this->options['routing_field']) {
                $document->setRouting($data[$key]);
            }

            $document->setUpsert($data);
            $documents[] = $document;
        }

        $this->index->addDocuments($documents);
    }

    public function update(ChangeSet $changeSet, Criteria $criteria): void
    {
        if (null === $this->mapper) {
            throw new \RuntimeException('Operation mapper should be set to perform update queries.');
        }

        $query = new BoolQuery();
        $this->mapper->mapCriteriaToQuery($query, $criteria);
        $body = $this->mapper->getBodyForChangeSet($changeSet);

        $this->updateByQuery($query, $body, ['scroll_size' => 500, 'conflicts' => 'proceed']);
    }

    public function delete(array $ids): void
    {
        if (null !== $this->options['routing_field']) {
            //TODO: handle response error?
            $this->index->deleteByQuery((new BoolQuery())->addFilter(new Terms('id', $ids)));

            return;
        }

        $documents = [];
        foreach ($ids as $id) {
            $document = new Document($id, [], $this->options['type']);
            $documents[] = $document;
        }

        $this->index->deleteDocuments($documents);
    }

    public function clear(): void
    {
        $this->index->deleteByQuery(new MatchAll());
    }

    /**
     * @param \Elastica\Query|string|array $query
     */
    private function updateByQuery($query, array $updates, array $options = []): Response
    {
        $query = Query::create($query)->getQuery();

        $scriptCodeLines = [];
        foreach ($updates as $field => $value) {
            $scriptCodeLines[] = sprintf('ctx._source.%s = params.%s;', $field, $field);
        }
        $script = new Script(implode("\n", $scriptCodeLines), $updates);

        $endpoint = new UpdateByQuery();
        $endpoint->setBody(
            [
                'query' => is_array($query) ? $query : $query->toArray(),
            ] + $script->toArray()
        );
        $endpoint->setParams($options);

        return $this->index->requestEndpoint($endpoint);
    }
}
