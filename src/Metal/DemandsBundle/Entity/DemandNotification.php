<?php

namespace Metal\DemandsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="demand_notification")
 */
class DemandNotification
{
    public const SERVICE_TELEGRAM = 1;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\DemandsBundle\Entity\Demand")
     * @ORM\JoinColumn(name="demand_id", onDelete="CASCADE")
     */
    private $demand;

    /**
     * @ORM\Column(type="smallint")
     */
    private $service;

    /**
     * @ORM\Column(name="notified_at", type="datetime")
     */
    private $notifiedAt;

    public function __construct(AbstractDemand $demand, int $service)
    {
        $this->demand = $demand;
        $this->service = $service;
        $this->notifiedAt = new \DateTime();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getDemand(): AbstractDemand
    {
        return $this->demand;
    }

    public function getService(): int
    {
        return $this->service;
    }

    public function getNotifiedAt(): \DateTime
    {
        return $this->notifiedAt;
    }
}
