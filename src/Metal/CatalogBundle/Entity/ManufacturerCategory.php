<?php

namespace Metal\CatalogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Metal\CategoriesBundle\Entity\Category;
use Metal\ProjectBundle\Entity\Behavior\Updateable;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @ORM\Table(
 *    name="manufacturer_category",
 *    uniqueConstraints={@ORM\UniqueConstraint(name="UNIQ_manufacturer_category", columns={"manufacturer_id", "category_id"})}
 * )
 */
class ManufacturerCategory
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="id")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Manufacturer")
     * @ORM\JoinColumn(name="manufacturer_id", referencedColumnName="id", onDelete="CASCADE")
     *
     * @var Manufacturer
     */
    protected $manufacturer;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\CategoriesBundle\Entity\Category")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="Message_ID", onDelete="CASCADE")
     *
     * @var Category
     */
    protected $category;

    /** @ORM\Column(type="datetime", name="created_at") */
    protected $createdAt;

    use Updateable;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Manufacturer
     */
    public function getManufacturer()
    {
        return $this->manufacturer;
    }

    /**
     * @param Manufacturer $manufacturer
     */
    public function setManufacturer(Manufacturer $manufacturer)
    {
        $this->manufacturer = $manufacturer;
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
    public function setCategory($category)
    {
        $this->category = $category;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
    }

    public function getCategoryTitle()
    {
        if ($this->category) {
            return $this->category->getTitle();
        }

        return null;
    }

    public function setCategoryTitle($categoryTitle)
    {
        // do nothing
    }
}
