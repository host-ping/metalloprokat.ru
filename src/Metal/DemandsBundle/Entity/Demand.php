<?php

namespace Metal\DemandsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Metal\CallbacksBundle\Entity\Callback as CallbackEntity;
use Metal\GrabbersBundle\Entity\ParsedDemand;

/**
 * @ORM\Entity(repositoryClass="Metal\DemandsBundle\Repository\DemandRepository")
 */
class Demand extends AbstractDemand
{
    /** @ORM\Column(type="integer", name="old_demand_id", nullable=true) */
    protected $oldDemandId;

    /**
     * @ORM\OneToOne(targetEntity="Metal\DemandsBundle\Entity\PrivateDemand")
     * @ORM\JoinColumn(name="from_demand_id", nullable=true, referencedColumnName="id")
     *
     * @var Demand
     */
    protected $fromDemand;

    /**
     * @ORM\OneToOne(targetEntity="Metal\CallbacksBundle\Entity\Callback")
     * @ORM\JoinColumn(name="from_callback_id", nullable=true, referencedColumnName="id")
     *
     * @var CallbackEntity
     */
    protected $fromCallback;

    /**
     * @ORM\OneToOne(targetEntity="Metal\GrabbersBundle\Entity\ParsedDemand")
     * @ORM\JoinColumn(name="parsed_demand_id", nullable=true, referencedColumnName="id")
     *
     * @var ParsedDemand
     */
    protected $parsedDemand;

    /**
     * @ORM\Column(type="boolean", name="mirror_to_stroy", nullable=false, options={"default":0})
     */
    protected $mirrorToStroy;

    public static function extractDemandNumberFromSearchString($query)
    {
        if (preg_match('/^\s?[#№]?\s?(\d+)\s?$/ui', trim($query), $match)) {
            return array_pop($match);
        }

        return null;
    }

    public function __construct()
    {
        parent::__construct();
        $this->mirrorToStroy = false;
    }

    /**
     * @return ParsedDemand
     */
    public function getParsedDemand()
    {
        return $this->parsedDemand;
    }

    /**
     * @param ParsedDemand $parsedDemand
     */
    public function setParsedDemand(ParsedDemand $parsedDemand)
    {
        $this->parsedDemand = $parsedDemand;
    }

    public function setOldDemandId($oldDemandId)
    {
        $this->oldDemandId = $oldDemandId;
    }

    public function getOldDemandId()
    {
        return $this->oldDemandId;
    }

    /**
     * @return CallbackEntity
     */
    public function getFromCallback()
    {
        return $this->fromCallback;
    }

    public function setFromCallback(CallbackEntity $fromCallback)
    {
        $this->fromCallback = $fromCallback;
    }

    public function getFromDemand()
    {
        return $this->fromDemand;
    }

    public function setFromDemand(PrivateDemand $fromDemand)
    {
        $this->fromDemand = $fromDemand;
    }

    public function getMirrorToStroy()
    {
        return $this->mirrorToStroy;
    }

    public function setMirrorToStroy($mirrorToStroy)
    {
        $this->mirrorToStroy = $mirrorToStroy;
    }

    public function toArray()
    {
        return array(
            'deadline' => $this->deadline,
            'demand_periodicity' => $this->demandPeriodicityId,
            'region' => $this->getRegion()->getId(),
            'company_title' => $this->companyTitle,
            'phone' => $this->phone,
            'person' => $this->person,
            'email' => $this->email,
            'time_to_show' => $this->timeToShow,
            'consumer_type' => $this->consumerTypeId,
            'conditions' => $this->conditions,
            'info' => $this->info,
            'ip' => $this->ip,
            'user_agent' => $this->userAgent,
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt->format('Y-m-d H:i:s'),
            'source_type' => $this->sourceTypeId,
            'referer' => $this->referer,
            'old_demand_id' => $this->oldDemandId,
            'city_id' => $this->city ? $this->city->getId() : null,
            'moderated_at' => $this->moderatedAt ? $this->moderatedAt->format('Y-m-d H:i:s') : null,
            'demand_type' => AbstractDemand::TYPE_PUBLIC,
            'body' => $this->body ?: null
        );
    }

    public function isPublic()
    {
        return true;
    }
}
