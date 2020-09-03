<?php

namespace Metal\AttributesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Metal\CategoriesBundle\Entity\Category;

/**
 * @ORM\Entity(repositoryClass="Metal\AttributesBundle\Repository\AttributeValueCategoryRepository")
 * @ORM\Table(name="attribute_value_category", uniqueConstraints=
 *  {@ORM\UniqueConstraint(name="UNIQ_attribute_value_category", columns={"attribute_value_id", "category_id"})}
 * )
 */
class AttributeValueCategory
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="id")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="AttributeValue")
     * @ORM\JoinColumn(name="attribute_value_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     *
     * @var AttributeValue
     */
    protected $attributeValue;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\CategoriesBundle\Entity\Category")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="Message_ID", nullable=false, onDelete="CASCADE")
     *
     * @var Category
     */
    protected $category;

    /** @ORM\Column(length=1000, name="regex_match", nullable=true) */
    protected $regexMatch;

    /** @ORM\Column(length=1000, name="regex_exclude", nullable=true) */
    protected $regexExclude;

    /** @ORM\Column(type="smallint", name="matching_priority", nullable=false) */
    protected $matchingPriority;

    public function __construct()
    {
        $this->matchingPriority = 0;
    }

    public function getId()
    {
        return $this->id;
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

    /**
     * @return Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param Category $category
     */
    public function setCategory(Category $category)
    {
        $this->category = $category;
    }

    public function getRegexExclude()
    {
        return $this->regexExclude;
    }

    public function setRegexExclude($regexExclude)
    {
        $this->regexExclude = $regexExclude;
    }

    public function getRegexMatch()
    {
        return $this->regexMatch;
    }

    public function setRegexMatch($regexMatch)
    {
        $this->regexMatch = $regexMatch;
    }

    public function getMatchingPriority()
    {
        return $this->matchingPriority;
    }

    public function setMatchingPriority($matchingPriority)
    {
        $this->matchingPriority = $matchingPriority;
    }
}
