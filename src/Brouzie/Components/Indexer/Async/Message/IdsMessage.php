<?php

namespace Brouzie\Components\Indexer\Async\Message;

class IdsMessage implements \JsonSerializable
{
    private const IDS_KEY = 'ids';

    private $ids;

    public function __construct(array $ids)
    {
        $this->ids = $ids;
    }

    public function getIds(): array
    {
        return $this->ids;
    }

    public function jsonSerialize()
    {
        return [self::IDS_KEY => $this->ids];
    }

    public static function jsonDeserialize(string $json)
    {
        $data = json_decode($json, true);

        if (!isset($data[self::IDS_KEY])) {
            throw new \LogicException('The message does not contain "ids" but it is required.');
        }

        return new static($data[self::IDS_KEY]);
    }
}
