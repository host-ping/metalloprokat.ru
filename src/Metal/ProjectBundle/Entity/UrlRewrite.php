<?php

namespace Metal\ProjectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Metal\CategoriesBundle\Entity\Category;
use Metal\ContentBundle\Entity\Category as ContentCategory;
use Metal\CompaniesBundle\Entity\Company;

/**
 * @ORM\Entity
 * @ORM\Table(name="url_rewrite")
 */
class UrlRewrite
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="id")
     */
    protected $id;

    /** @ORM\Column(name="path_prefix", length=100, unique=true) */
    protected $pathPrefix;

    /**
     * @ORM\OneToOne(targetEntity="Metal\CategoriesBundle\Entity\Category")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="Message_ID", nullable=true, onDelete="CASCADE")
     *
     * @var Category
     */
    protected $category;

    /**
     * @ORM\OneToOne(targetEntity="Metal\ContentBundle\Entity\Category")
     * @ORM\JoinColumn(name="content_category_id", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     *
     * @var ContentCategory
     */
    protected $contentCategory;

    /**
     * @ORM\OneToOne(targetEntity="Metal\CompaniesBundle\Entity\Company")
     * @ORM\JoinColumn(name="company_id", referencedColumnName="Message_ID", nullable=true)
     *
     * @var Company
     */
    protected $company;

    public function getId()
    {
        return $this->id;
    }

    /**
     * @param Category $category
     */
    public function setCategory(Category $category)
    {
        $this->category = $category;
    }

    /**
     * @return Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @return ContentCategory
     */
    public function getContentCategory()
    {
        return $this->contentCategory;
    }

    /**
     * @param ContentCategory $contentCategory
     */
    public function setContentCategory(ContentCategory $contentCategory)
    {
        $this->contentCategory = $contentCategory;
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

    /**
     * @param string $pathPrefix
     */
    public function setPathPrefix($pathPrefix)
    {
        $this->pathPrefix = $pathPrefix;
    }

    /**
     * @return string
     */
    public function getPathPrefix()
    {
        return $this->pathPrefix;
    }
}
