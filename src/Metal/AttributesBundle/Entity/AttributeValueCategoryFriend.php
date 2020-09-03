<?php

namespace Metal\AttributesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="attribute_value_category_friend", uniqueConstraints=
 *  {@ORM\UniqueConstraint(name="UNIQ_attribute_value_category_friend", columns={"attribute_value_category_id", "attribute_value_category_friend_id"})}
 * )
 */
class AttributeValueCategoryFriend
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
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity="AttributeValueCategory")
     * @ORM\JoinColumn(name="attribute_value_category_friend_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     *
     * @var AttributeValueCategory
     */
    protected $attributeValueCategoryFriend;

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
     * @return AttributeValueCategory
     */
    public function getAttributeValueCategoryFriend()
    {
        return $this->attributeValueCategoryFriend;
    }

    /**
     * @param AttributeValueCategory $attributeValueCategoryFriend
     */
    public function setAttributeValueCategoryFriend(AttributeValueCategory $attributeValueCategoryFriend)
    {
        $this->attributeValueCategoryFriend = $attributeValueCategoryFriend;
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
