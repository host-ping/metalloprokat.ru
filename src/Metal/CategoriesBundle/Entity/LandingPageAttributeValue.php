<?php

namespace Metal\CategoriesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Metal\AttributesBundle\Entity\AttributeValue;

/**
 * @ORM\Entity(repositoryClass="Metal\CategoriesBundle\Repository\LandingPageAttributeValueRepository")
 * @ORM\Table(
 *      name="landing_page_attribute_value",
 *      uniqueConstraints={@ORM\UniqueConstraint(name="UNIQ_landing_page_attribute_value", columns={"landing_page_id", "attribute_value_id"} )}
 * )
 */
class LandingPageAttributeValue
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="id")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="LandingPage", inversedBy="landingPageAttributesValues")
     * @ORM\JoinColumn(name="landing_page_id", referencedColumnName="id", nullable=false)
     *
     * @var LandingPage
     */
    protected $landingPage;

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
     * @return LandingPage
     */
    public function getLandingPage()
    {
        return $this->landingPage;
    }

    /**
     * @param LandingPage $landingPage
     */
    public function setLandingPage(LandingPage $landingPage)
    {
        $this->landingPage = $landingPage;
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

    public function getAttributeValueTitle()
    {
        if ($this->attributeValue) {
            return $this->attributeValue->getValue();
        }

        return '';
    }

    public function setAttributeValueTitle($cityTitle)
    {
        // do nothing. Readonly
    }
}
