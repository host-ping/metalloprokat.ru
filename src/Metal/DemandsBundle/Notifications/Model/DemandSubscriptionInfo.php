<?php

namespace Metal\DemandsBundle\Notifications\Model;

class DemandSubscriptionInfo
{
    /**
     * @var int[]
     */
    private $categoryIds;

    /**
     * @var int[]
     */
    private $territorialStructureIds;

    public function __construct(array $categoryIds, array $territorialStructureIds)
    {
        $this->categoryIds = $categoryIds;
        $this->territorialStructureIds = $territorialStructureIds;
    }

    /**
     * @return int[]
     */
    public function getCategoryIds(): array
    {
        return $this->categoryIds;
    }

    /**
     * @return int[]
     */
    public function getTerritorialStructureIds(): array
    {
        return $this->territorialStructureIds;
    }
}
