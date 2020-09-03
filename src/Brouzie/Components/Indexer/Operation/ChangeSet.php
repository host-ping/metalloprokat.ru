<?php

namespace Brouzie\Components\Indexer\Operation;

interface ChangeSet extends \Serializable
{
    public function getChangedFields(): array;

    public function isFieldChanged(string $field): bool;
}
