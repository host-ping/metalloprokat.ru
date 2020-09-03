<?php

namespace Metal\ComplaintsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Metal\DemandsBundle\Entity\AbstractDemand;

/**
 * @ORM\Entity(repositoryClass="Metal\DemandsBundle\Repository\DemandComplaintRepository")
 */
class DemandComplaint extends AbstractComplaint
{
    /**
     * @ORM\ManyToOne(targetEntity="Metal\DemandsBundle\Entity\AbstractDemand")
     * @ORM\JoinColumn(name="demand_id", referencedColumnName="id")
     */
    protected $demand;

    /**
     * @param AbstractDemand $demand
     */
    public function setDemand(AbstractDemand $demand)
    {
        $this->demand = $demand;
    }

    /**
     * @return AbstractDemand
     */
    public function getDemand()
    {
        return $this->demand;
    }

    public function setObject($object)
    {
        $this->setDemand($object);
    }

    public function getObjectKind()
    {
        return 'demand';
    }
}
