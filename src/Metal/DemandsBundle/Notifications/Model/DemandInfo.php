<?php

namespace Metal\DemandsBundle\Notifications\Model;

use Metal\DemandsBundle\Entity\ValueObject\ConsumerType;
use Metal\DemandsBundle\Entity\ValueObject\DemandPeriodicity;

class DemandInfo
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $cityTitle;

    /**
     * @var string|null
     */
    private $regionTitle;

    /**
     * @var string
     */
    private $viewUrl;

    /**
     * @var bool
     */
    private $public;

    /**
     * @var DemandPeriodicity
     */
    private $demandPeriodicity;

    /**
     * @var ConsumerType
     */
    private $consumerType;

    /**
     * @var DemandInfoItem[]
     */
    private $items;

    /**
     * @var DemandInfoCategory[]
     */
    private $categories;

    /**
     * @param DemandInfoItem[] $items
     * @param DemandInfoCategory[] $categories
     */
    public function __construct(
        int $id,
        string $cityTitle,
        ?string $regionTitle,
        string $viewUrl,
        bool $public,
        DemandPeriodicity $demandPeriodicity,
        ConsumerType $consumerType,
        array $items,
        array $categories
    ) {
        $this->id = $id;
        $this->cityTitle = $cityTitle;
        $this->regionTitle = $regionTitle;
        $this->viewUrl = $viewUrl;
        $this->public = $public;
        $this->demandPeriodicity = $demandPeriodicity;
        $this->consumerType = $consumerType;
        $this->items = $items;
        $this->categories = $categories;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getCityTitle(): string
    {
        return $this->cityTitle;
    }

    public function getRegionTitle(): ?string
    {
        return $this->regionTitle;
    }

    public function getViewUrl(): string
    {
        return $this->viewUrl;
    }

    public function isPublic(): bool
    {
        return $this->public;
    }

    public function getDemandPeriodicity(): DemandPeriodicity
    {
        return $this->demandPeriodicity;
    }

    public function getConsumerType(): ConsumerType
    {
        return $this->consumerType;
    }

    /**
     * @return DemandInfoItem[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @return DemandInfoCategory[]
     */
    public function getCategories(): array
    {
        return $this->categories;
    }
}
