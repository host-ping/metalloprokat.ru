<?php

namespace Metal\AttributesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Metal\CategoriesBundle\Entity\Category;

/**
 * @ORM\Entity(repositoryClass="Metal\AttributesBundle\Repository\AttributeCategoryRepository")
 * @ORM\Table(name="attribute_category", uniqueConstraints=
 *  {@ORM\UniqueConstraint(name="UNIQ_attribute_category", columns={"attribute_id", "category_id"})}
 * )
 */
class AttributeCategory
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="id")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Attribute")
     * @ORM\JoinColumn(name="attribute_id", referencedColumnName="id", nullable=false)
     *
     * @var Attribute
     */
    protected $attribute;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\CategoriesBundle\Entity\Category")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="Message_ID", nullable=false)
     *
     * @var Category
     */
    protected $category;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
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
    public function setAttribute(Attribute $attribute)
    {
        $this->attribute = $attribute;
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
}
