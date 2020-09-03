<?php

namespace Metal\GrabbersBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Metal\DemandsBundle\Entity\Demand;

/**
 * @ORM\Entity
 * @ORM\Table(name="grabber_parsed_demand")
 */
class ParsedDemand
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="id")
     */
    protected $id;

    /**
     * @ORM\OneToOne(targetEntity="Metal\DemandsBundle\Entity\Demand")
     * @ORM\JoinColumn(name="demand_id", referencedColumnName="id", onDelete="CASCADE")
     *
     * @var Demand
     */
    protected $demand;

    /**
     * @ORM\Column(name="parsed_demand_id", length=255)
     */
    protected $parsedDemandId;

    /**
     * @ORM\Column(name="url", length=255)
     */
    protected $url;

    /**
     * @ORM\Column(name="hash", length=32)
     */
    protected $hash;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\GrabbersBundle\Entity\Site")
     * @ORM\JoinColumn(name="site_id", referencedColumnName="id", nullable=true)
     *
     * @var Site
     */
    protected $site;

    /**
     * @ORM\Column(type="datetime", name="created_at")
     */
    protected $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
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
        $this->demand->setParsedDemand($this);
    }

    public function getParsedDemandId()
    {
        return $this->parsedDemandId;
    }

    public function setParsedDemandId($parsedDemandId)
    {
        $this->parsedDemandId = $parsedDemandId;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function setUrl($url)
    {
        $this->url = $url;
    }

    public function getHash()
    {
        return $this->hash;
    }

    public function setHash($hash)
    {
        $this->hash = $hash;
    }

    /**
     * @return Site
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * @param Site $site
     */
    public function setSite(Site $site)
    {
        $this->site = $site;
    }

    public function isLockedForDeleting()
    {
        return $this->demand->isModerated();
    }
}
