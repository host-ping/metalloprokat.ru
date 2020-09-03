<?php

namespace Metal\CompaniesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use Metal\CategoriesBundle\Entity\Category;

/**
 * @ORM\Entity(repositoryClass="Metal\CompaniesBundle\Repository\CompanyCategoryRepository")
 * @ORM\Table(name="Message76",
 *   indexes={
 *      @ORM\Index(name="IDX_is_automatically_added", columns={"is_automatically_added"})
 *   },
 *   uniqueConstraints={@ORM\UniqueConstraint(name="UNIQ_company_category", columns={"company_id", "cat_id"})}
 * )
 */
class CompanyCategory
{
    const DEFAULT_DISPLAY_POSITION = 100;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="Message_ID")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\CompaniesBundle\Entity\Company", inversedBy="companyCategories")
     * @ORM\JoinColumn(name="company_id", referencedColumnName="Message_ID")
     *
     * @var Company
     */
    protected $company;

    /**
     * @ORM\Column(type="integer", name="cat_id")
     */
    protected $categoryId;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\CategoriesBundle\Entity\Category")
     * @ORM\JoinColumn(name="cat_id", referencedColumnName="Message_ID", onDelete="CASCADE")
     *
     * @var Category
     */
    protected $category;

    /** @ORM\Column(type="boolean", name="enabled", options={"default":1}) */
    protected $enabled;

    /**
     * @ORM\Column(type="boolean", name="is_automatically_added")
     */
    protected $isAutomaticallyAdded;

    /**
     * @ORM\Column(type="datetime", name="Created")
     *
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @ORM\Column(type="smallint", name="display_position", nullable=false, options={"default":100})
     * @Assert\GreaterThanOrEqual(1)
     * @Assert\Type(type="integer")
     */
    protected $displayPosition;

    public function __construct()
    {
        $this->isAutomaticallyAdded = false;
        $this->createdAt = new \DateTime();
        $this->enabled = true;
        $this->displayPosition = self::DEFAULT_DISPLAY_POSITION;
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @param Company $company
     */
    public function setCompany(Company $company)
    {
        $this->company = $company;
    }

    /**
     * @return Company
     */
    public function getCompany()
    {
        return $this->company;
    }

    public function getCategoryId()
    {
        return $this->categoryId;
    }

    /**
     * @param Category $category
     */
    public function setCategory(Category $category)
    {
        $this->category = $category;
        $this->categoryId = $category->getId();
    }

    /**
     * @return Category
     */
    public function getCategory()
    {
        return $this->category;
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

    public function setIsAutomaticallyAdded($isAutomaticallyAdded)
    {
        $this->isAutomaticallyAdded = $isAutomaticallyAdded;
    }

    public function getIsAutomaticallyAdded()
    {
        return $this->isAutomaticallyAdded;
    }

    public function getEnabled()
    {
        return $this->enabled;
    }

    public function setEnabled($enabled)
    {
        $this->enabled = (bool)$enabled;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function getDisplayPosition()
    {
        return $this->displayPosition;
    }

    public function setDisplayPosition($displayPosition)
    {
        $this->displayPosition = $displayPosition;
    }
}
