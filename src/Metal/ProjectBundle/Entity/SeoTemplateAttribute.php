<?php

namespace Metal\ProjectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Metal\AttributesBundle\Entity\Attribute;
use Metal\AttributesBundle\Entity\AttributeValue;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @ORM\Table(name="seo_template_attribute")
 */
class SeoTemplateAttribute
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\ProjectBundle\Entity\SeoTemplate", inversedBy="seoTemplateAttributes")
     * @ORM\JoinColumn(name="seo_template_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     *
     * @var SeoTemplate
     */
    private $seoTemplate;

    /**
     * @Assert\NotBlank(message="Указан несуществующий атрибут или некорректное значение атрибута.")
     *
     * @ORM\ManyToOne(targetEntity="Metal\AttributesBundle\Entity\Attribute")
     * @ORM\JoinColumn(name="attribute_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     *
     * @var Attribute
     */
    private $attribute;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\AttributesBundle\Entity\AttributeValue")
     * @ORM\JoinColumn(name="attribute_value_id", referencedColumnName="id", onDelete="CASCADE")
     *
     * @var AttributeValue|null
     */
    private $attributeValue;

    public function getId()
    {
        return $this->id;
    }

    /**
     * @return SeoTemplate
     */
    public function getSeoTemplate()
    {
        return $this->seoTemplate;
    }

    /**
     * @param SeoTemplate $seoTemplate
     */
    public function setSeoTemplate($seoTemplate)
    {
        $this->seoTemplate = $seoTemplate;
    }

    /**
     * @return Attribute
     */
    public function getAttribute()
    {
        return $this->attribute;
    }

    /**
     * @param Attribute $attribute
     */
    public function setAttribute($attribute)
    {
        $this->attribute = $attribute;
    }

    /**
     * @return AttributeValue|null
     */
    public function getAttributeValue()
    {
        return $this->attributeValue;
    }

    /**
     * @param AttributeValue|null $attributeValue
     */
    public function setAttributeValue($attributeValue)
    {
        $this->attributeValue = $attributeValue;
    }
}
