<?php

namespace Metal\DemandsBundle\Async\Message;

final class DemandModerated implements \JsonSerializable
{
    private const DEMAND_ID_KEY = 'demand_id';

    private $demandId;

    public function __construct(int $demandId)
    {
        $this->demandId = $demandId;
    }

    public function getDemandId(): int
    {
        return $this->demandId;
    }

    public function jsonSerialize()
    {
        return [self::DEMAND_ID_KEY => $this->demandId];
    }

    public static function jsonDeserialize(string $json): self
    {
        $data = json_decode($json, true);

        if (!isset($data[self::DEMAND_ID_KEY])) {
            throw new \LogicException(
                sprintf('The message does not contain "%s" but it is required.', self::DEMAND_ID_KEY)
            );
        }

        return new self($data[self::DEMAND_ID_KEY]);
    }
}
