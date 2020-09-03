<?php

namespace Metal\DemandsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Metal\AttributesBundle\Entity\AttributeValue;

/**
 * @ORM\Entity(repositoryClass="Metal\DemandsBundle\Repository\DemandItemAttributeValueRepository")
 * @ORM\Table(name="demand_item_attribute_value")
 */
class DemandItemAttributeValue
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="id")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="DemandItem", inversedBy="demandItemAttributeValues")
     * @ORM\JoinColumn(name="demand_item_id", referencedColumnName="id", nullable=false)
     *
     * @var DemandItem
     */
    protected $demandItem;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\AttributesBundle\Entity\AttributeValue")
     * @ORM\JoinColumn(name="attribute_value_id", referencedColumnName="id", nullable=false)
     *
     * @var AttributeValue
     */
    protected $attributeValue;

    public function getId()
    {
        return $this->id;
    }

    /**
     * @return DemandItem
     */
    public function getDemandItem()
    {
        return $this->demandItem;
    }

    /**
     * @param DemandItem $demandItem
     */
    public function setDemandItem(DemandItem $demandItem)
    {
        $this->demandItem = $demandItem;
    }

    /**
     * @return AttributeValue
     */
    public function getAttributeValue()
    {
        return $this->attributeValue;
    }

    /**
     * @param AttributeValue $attributeValue
     */
    public function setAttributeValue(AttributeValue $attributeValue)
    {
        $this->attributeValue = $attributeValue;
    }
}
