<?php

namespace Brouzie\Components\Indexer\Operation;

abstract class AbstractChangeSet implements ChangeSet
{
    protected const ALLOWED_CLASSES = [
        \DateTime::class,
    ];

    private $changes = [];

    public function getChangedFields(): array
    {
        return array_keys($this->changes);
    }

    public function isFieldChanged(string $field): bool
    {
        return array_key_exists($field, $this->changes);
    }

    public function serialize()
    {
        return serialize([$this->changes]);
    }

    public function unserialize($serialized)
    {
        list($this->changes) = unserialize($serialized, ['allowed_classes' => static::ALLOWED_CLASSES]);
    }

    protected function getChange(string $field)
    {
        if (!$this->isFieldChanged($field)) {
            throw new \InvalidArgumentException('Field not changed.');
        }

        return $this->changes[$field];
    }

    protected function setChange(string $field, $value): void
    {
        $this->changes[$field] = $value;
    }
}
