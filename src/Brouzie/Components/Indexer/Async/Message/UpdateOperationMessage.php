<?php

namespace Brouzie\Components\Indexer\Async\Message;

use Brouzie\Components\Indexer\Operation\ChangeSet;
use Brouzie\Components\Indexer\Operation\Criteria;

class UpdateOperationMessage implements \JsonSerializable
{
    private const CHANGE_SET_KEY = 'changeSet';

    private const CRITERIA_KEY = 'criteria';

    private $changeSet;

    private $criteria;

    public function __construct(ChangeSet $changeSet, Criteria $criteria)
    {
        $this->changeSet = $changeSet;
        $this->criteria = $criteria;
    }

    public function getChangeSet(): ChangeSet
    {
        return $this->changeSet;
    }

    public function getCriteria(): Criteria
    {
        return $this->criteria;
    }

    public function jsonSerialize()
    {
        return [
            self::CHANGE_SET_KEY => serialize($this->changeSet),
            self::CRITERIA_KEY => serialize($this->criteria),
        ];
    }

    public static function jsonDeserialize(string $json)
    {
        $data = json_decode($json, true);
        $diff = array_diff_key(array_fill_keys([self::CHANGE_SET_KEY, self::CRITERIA_KEY], null), $data);

        if ($diff) {
            throw new \LogicException(sprintf('Missing required keys: "%s".', implode('","', array_keys($diff))));
        }

        return new static(unserialize($data[self::CHANGE_SET_KEY]), unserialize($data[self::CRITERIA_KEY]));
    }
}
