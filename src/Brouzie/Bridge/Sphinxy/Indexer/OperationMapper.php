<?php

namespace Brouzie\Bridge\Sphinxy\Indexer;

use Brouzie\Components\Indexer\Operation\ChangeSet;
use Brouzie\Components\Indexer\Operation\Criteria;
use Brouzie\Sphinxy\QueryBuilder;

interface OperationMapper
{
    public function mapCriteriaToQueryBuilder(Criteria $criteria, QueryBuilder $qb): void;

    public function mapChangeSetToQueryBuilder(ChangeSet $changeSet, QueryBuilder $qb): void;
}
