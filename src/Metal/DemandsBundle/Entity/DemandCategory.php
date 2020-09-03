<?php

namespace Metal\DemandsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Metal\CategoriesBundle\Entity\Category;

/**
 * @ORM\Entity(repositoryClass="Metal\DemandsBundle\Repository\DemandCategoryRepository")
 * @ORM\Table(name="demand_category", uniqueConstraints={
 *     @ORM\UniqueConstraint(name="UNIQ_demand_category", columns={"demand_id", "category_id"})
 * })
 */
class DemandCategory
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="id")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="AbstractDemand", inversedBy="demandCategories")
     * @ORM\JoinColumn(name="demand_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     *
     * @var AbstractDemand
     */
    protected $demand;

    /**
     * @ORM\Column(name="category_id", type="integer")
     */
    protected $categoryId;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\CategoriesBundle\Entity\Category")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="Message_ID", nullable=false, onDelete="CASCADE")
     *
     * @var Category
     */
    protected $category;

    public function getId()
    {
        return $this->id;
    }

    /**
     * @return AbstractDemand
     */
    public function getDemand()
    {
        return $this->demand;
    }

    /**
     * @param AbstractDemand $demand
     */
    public function setDemand(AbstractDemand $demand)
    {
        $this->demand = $demand;
    }

    public function getCategoryId()
    {
        return $this->categoryId;
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
        $this->categoryId = $category->getId();
    }
}
