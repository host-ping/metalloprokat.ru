<?php

namespace Brouzie\Components\Indexer\Elastica;

use Brouzie\Components\Indexer\Operation\ChangeSet;
use Brouzie\Components\Indexer\Operation\Criteria;
use Elastica\Query;

interface OperationMapper
{
    public function mapCriteriaToQuery(Query\BoolQuery $query, Criteria $criteria): void;

    public function getBodyForChangeSet(ChangeSet $changeSet): array;
}
