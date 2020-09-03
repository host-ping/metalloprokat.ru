<?php

namespace Metal\DemandsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="demand_mirror")
 */
class DemandMirror
{
    /**
     * @ORM\Id
     * @ORM\OneToOne(targetEntity="Demand")
     * @ORM\JoinColumn(name="demand_id", referencedColumnName="id", nullable=false)
     *
     * @var Demand
     */
    protected $demand;

    /**
     * @ORM\Column(type="integer", name="original_demand_id", nullable=false)
     */
    protected $originalDemandId;

    /**
     * @ORM\Column(type="datetime", name="mirrored_at", nullable=false)
     *
     * @var \DateTime
     */
    protected $mirroredAt;

    public function __construct()
    {
        $this->mirroredAt = new \DateTime();
    }

    /**
     * @return Demand
     */
    public function getDemand()
    {
        return $this->demand;
    }

    /**
     * @param Demand $demand
     */
    public function setDemand(Demand $demand)
    {
        $this->demand = $demand;
    }

    public function getOriginalDemandId()
    {
        return $this->originalDemandId;
    }

    public function setOriginalDemandId($originalDemandId)
    {
        $this->originalDemandId = $originalDemandId;
    }

    /**
     * @return \DateTime
     */
    public function getMirroredAt()
    {
        return $this->mirroredAt;
    }

    /**
     * @param \DateTime $mirroredAt
     */
    public function setMirroredAt($mirroredAt)
    {
        $this->mirroredAt = $mirroredAt;
    }
}
