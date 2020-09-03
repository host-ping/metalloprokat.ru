<?php

namespace Metal\DemandsBundle\Notifications\Model;

use Metal\ProductsBundle\Entity\ValueObject\ProductMeasure;

class DemandInfoItem
{
    /**
     * @var int
     */
    private $position;

    /**
     * @var string
     */
    private $title;

    /**
     * @var ?float
     */
    private $volume;

    /**
     * @var ?ProductMeasure
     */
    private $unit;

    public function __construct(int $position, string $title, ?float $volume, ?ProductMeasure $unit)
    {
        $this->position = $position;
        $this->title = $title;
        $this->volume = $volume;
        $this->unit = $unit;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getVolume(): ?float
    {
        return $this->volume;
    }

    public function getUnit(): ?ProductMeasure
    {
        return $this->unit;
    }
}
