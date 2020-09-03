<?php

namespace Metal\ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Metal\CategoriesBundle\Entity\Category as CatalogCategory;


/**
 * @ORM\Entity
 * @ORM\Table(name="parser_category_associate")
 */
class ParserCategoryAssociate
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="parser_category_id")
     */
    protected $parserCategoryId;

    /**
     * @ORM\Column(length=255, name="title", nullable=false)
     */
    protected $title;

    /**
     * @ORM\Column(length=255, name="parser_category_url", nullable=true)
     */
    protected $parserCategoryUrl;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\CategoriesBundle\Entity\Category")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="Message_ID", nullable=true, onDelete="SET NULL")
     *
     * @var CatalogCategory
     */
    protected $category;

    /**
     * @ORM\Column(type="integer", name="priority", nullable=false, options={"default":0})
     */
    protected $priority;

    /**
     * @ORM\Column(type="datetime", name="created_at", nullable=true)
     *
     * @var \DateTime
     */
    protected $createdAt;

    public function __construct()
    {
        $this->parserCategoryUrl = '';
        $this->createdAt = new \DateTime();
        $this->priority = 0;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getParserCategoryId()
    {
        return $this->parserCategoryId;
    }

    public function getParserCategoryUrl()
    {
        return $this->parserCategoryUrl;
    }

    public function setParserCategoryUrl($parserCategoryUrl)
    {
        $this->parserCategoryUrl = (string)$parserCategoryUrl;
    }

    public function incrementPriority()
    {
        $this->priority++;
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
     * @return CatalogCategory
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param CatalogCategory $category
     */
    public function setCategory(CatalogCategory $category)
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
}
