<?php

namespace Metal\TerritorialBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

trait Coordinate
{
    /** @ORM\Column(type="decimal", name="latitude", nullable=true, scale=8, precision=12) */
    protected $latitude;

    /** @ORM\Column(type="decimal", name="longitude", nullable=true, scale=8, precision=12) */
    protected $longitude;

    public function getLongitude()
    {
        return $this->longitude;
    }

    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;
    }

    public function getLatitude()
    {
        return $this->latitude;
    }

    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;
    }

    public function hasCoordinate()
    {
        return null !== $this->latitude && null !== $this->longitude;
    }
}
