<?php

namespace Metal\AttributesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Metal\CategoriesBundle\Entity\Category;

/**
 * @ORM\Entity
 * @ORM\Table(name="attribute_value_category_friend_category", uniqueConstraints=
 *  {@ORM\UniqueConstraint(name="UNIQ_attribute_value_category_category", columns={"attribute_value_category_id", "category_id"})}
 * )
 */
class AttributeValueCategoryFriendCategory
{
    /**
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity="AttributeValueCategory")
     * @ORM\JoinColumn(name="attribute_value_category_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     *
     * @var AttributeValueCategory
     */
    protected $attributeValueCategory;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\CategoriesBundle\Entity\Category")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="Message_ID", nullable=false, onDelete="CASCADE")
     *
     * @var Category
     */
    protected $category;

    /** @ORM\Column(type="smallint", name="flag", nullable=false) */
    protected $flag;

    /**
     * @ORM\Column(type="smallint", name="priority", nullable=false)
     */
    protected $priority;

    /**
     * @return AttributeValueCategory
     */
    public function getAttributeValueCategory()
    {
        return $this->attributeValueCategory;
    }

    /**
     * @param AttributeValueCategory $attributeValueCategory
     */
    public function setAttributeValueCategory(AttributeValueCategory $attributeValueCategory)
    {
        $this->attributeValueCategory = $attributeValueCategory;
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

    public function getFlag()
    {
        return $this->flag;
    }

    public function setFlag($flag)
    {
        $this->flag = $flag;
    }

    public function getPriority()
    {
        return $this->priority;
    }

    public function setPriority($priority)
    {
        $this->priority = $priority;
    }
}
