<?php

namespace Metal\DemandsBundle\Notifications\Model;

class TelegramSubscriber
{
    /**
     * @var string
     */
    private $telegramUserId;

    /**
     * @var int[]
     */
    private $categoryIds;

    /**
     * @var int[]
     */
    private $territorialStructureIds;

    public function __construct(string $telegramUserId, array $categoryIds, array $territorialStructureIds)
    {
        $this->telegramUserId = $telegramUserId;
        $this->categoryIds = $categoryIds;
        $this->territorialStructureIds = $territorialStructureIds;
    }

    public function getTelegramUserId(): string
    {
        return $this->telegramUserId;
    }

    public function satisfiedBy(DemandSubscriptionInfo $demand): bool
    {
        return $this->satisfiedByCategories($demand->getCategoryIds())
            && $this->satisfiedByTerritorialStructure($demand->getTerritorialStructureIds());
    }

    private function satisfiedByCategories(array $categoryIds): bool
    {
        if (!$this->categoryIds) {
            return true;
        }

        return count(array_intersect($this->categoryIds, $categoryIds)) > 0;
    }

    private function satisfiedByTerritorialStructure(array $territorialStructureIds): bool
    {
        if (!$this->territorialStructureIds) {
            return true;
        }

        return count(array_intersect($this->territorialStructureIds, $territorialStructureIds)) > 0;
    }
}
