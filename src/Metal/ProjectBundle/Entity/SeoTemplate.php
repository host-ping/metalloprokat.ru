<?php

namespace Metal\ProjectBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Metal\AttributesBundle\Entity\Attribute;
use Metal\AttributesBundle\Entity\DTO\AttributesCollection;
use Metal\CategoriesBundle\Entity\Category;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass="Metal\ProjectBundle\Repository\SeoTemplateRepository")
 * @ORM\Table(name="seo_template")
 */
class SeoTemplate
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(max="255")
     *
     * @ORM\Column(length=255)
     */
    private $name;

    /**
     * @Assert\NotBlank()
     *
     * @ORM\ManyToOne(targetEntity="Metal\CategoriesBundle\Entity\Category")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="Message_ID", nullable=false, onDelete="CASCADE")
     *
     * @var Category
     */
    private $category;

    /**
     * @Assert\Valid()
     *
     * @ORM\Embedded(class="Metal\ProjectBundle\Entity\Metadata", columnPrefix=false)
     *
     * @var Metadata
     */
    protected $metadata;

    /**
     * @Assert\Length(max="1000")
     *
     * @ORM\Column(name="text_block", length=1000)
     */
    private $textBlock;

    /**
     * @Assert\Valid()
     *
     * @ORM\OneToMany(targetEntity="Metal\ProjectBundle\Entity\SeoTemplateAttribute", mappedBy="seoTemplate", cascade={"persist"}, orphanRemoval=true)
     *
     * @var Collection|SeoTemplateAttribute[]
     */
    private $seoTemplateAttributes;

    /**
     * @ORM\Column(name="priority", type="integer")
     */
    private $priority;

    public function __construct()
    {
        $this->metadata = new Metadata();
        $this->seoTemplateAttributes = new ArrayCollection();
        $this->priority = 0;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param Category|null $category
     */
    public function setCategory(Category $category = null)
    {
        $this->category = $category;
    }

    /**
     * @return Metadata
     */
    public function getMetadata()
    {
        return $this->metadata;
    }

    /**
     * @param Metadata $metadata
     */
    public function setMetadata(Metadata $metadata)
    {
        $this->metadata = $metadata;
    }

    public function getTextBlock()
    {
        return $this->textBlock;
    }

    public function setTextBlock($textBlock)
    {
        $this->textBlock = $textBlock;
    }

    /**
     * @return Collection|SeoTemplateAttribute[]
     */
    public function getSeoTemplateAttributes()
    {
        return $this->seoTemplateAttributes;
    }

    /**
     * @param SeoTemplateAttribute $seoTemplateAttribute
     */
    public function addSeoTemplateAttribute(SeoTemplateAttribute $seoTemplateAttribute)
    {
        $seoTemplateAttribute->setSeoTemplate($this);

        if ($seoTemplateAttribute->getAttribute()) {
            // обеспечиваем уникальность
            foreach ($this->getSeoTemplateAttributes() as $currentSeoTemplateAttribute) {
                if ($currentSeoTemplateAttribute->getAttribute() === $seoTemplateAttribute->getAttribute()) {
                    $this->removeSeoTemplateAttribute($currentSeoTemplateAttribute);
                    break;
                }
            }
        }

        $this->seoTemplateAttributes->add($seoTemplateAttribute);
    }

    /**
     * @param SeoTemplateAttribute $seoTemplateAttribute
     */
    public function removeSeoTemplateAttribute(SeoTemplateAttribute $seoTemplateAttribute)
    {
        $seoTemplateAttribute->setSeoTemplate(null);
        $this->seoTemplateAttributes->removeElement($seoTemplateAttribute);
    }

    public function getPriority()
    {
        return $this->priority;
    }

    public function setPriority($priority)
    {
        $this->priority = $priority;
    }

    /**
     * @ORM\PreFlush()
     */
    public function refreshPriority()
    {
        $priority = 0;
        foreach ($this->getSeoTemplateAttributes() as $seoTemplateAttribute) {
            $priority += $seoTemplateAttribute->getAttributeValue() ? 10 : 5;
        }

        $this->priority = $priority;
    }

    /**
     * @param Attribute $attribute
     *
     * @return SeoTemplateAttribute|null
     */
    public function getSeoTemplateAttributeByAttribute(Attribute $attribute)
    {
        foreach ($this->getSeoTemplateAttributes() as $seoTemplateAttribute) {
            if ($seoTemplateAttribute->getAttribute() === $attribute) {
                return $seoTemplateAttribute;
            }
        }

        return null;
    }

    public function matches(Category $category, AttributesCollection $attributesCollection)
    {
        if ($category !== $this->category) {
            return false;
        }
//vd($attributesCollection);
        foreach ($this->getSeoTemplateAttributes() as $seoTemplateAttribute) {
            $matches = $attributesCollection->containsAttribute($seoTemplateAttribute->getAttribute());

            $attributeValue = $seoTemplateAttribute->getAttributeValue();
            if ($attributeValue) {
                $matches = $matches && $attributesCollection->containsAttributeValue($attributeValue);
            }

            if (!$matches) {
                return false;
            }
        }

        /** @var Attribute $attribute */
        foreach ($attributesCollection as $attribute => $attributeValues) {
            // выбраны лишние атрибуты, для которых шаблон не предназначен
            if (!$seoTemplateAttribute = $this->getSeoTemplateAttributeByAttribute($attribute)) {
                return false;
            }
//
//            // выбрано больше значений атрибута, чем нужно
            if ($seoTemplateAttribute->getAttributeValue() && count($attributeValues) > 1) {
                return false;
            }
        }

        return true;
    }
}
