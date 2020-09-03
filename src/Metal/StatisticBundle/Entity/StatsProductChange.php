<?php

namespace Metal\StatisticBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Metal\CompaniesBundle\Entity\Company;
use Metal\ProductsBundle\Entity\Product;

/**
 * @ORM\Entity(repositoryClass="Metal\StatisticBundle\Repository\StatsProductChangeRepository", readOnly=true)
 * @ORM\Table(name="stats_product_change")
 */
class StatsProductChange
{
    /**
     * @ORM\Id
     * @ORM\Column(type="date", name="date_created_at")
     *
     * @var \DateTime
     */
    protected $dateCreatedAt;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Metal\ProductsBundle\Entity\Product")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="Message_ID", nullable=false)
     *
     * @var Product
     */
    protected $product;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\CompaniesBundle\Entity\Company")
     * @ORM\JoinColumn(name="company_id", referencedColumnName="Message_ID", nullable=false)
     *
     * @var Company
     */
    protected $company;

    /** @ORM\Column(type="boolean", name="is_added") */
    protected $isAdded;

    public function __construct()
    {
        $this->dateCreatedAt = new \DateTime();
        $this->isAdded = false;
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
     * @param \DateTime $dateCreatedAt
     */
    public function setDateCreatedAt(\DateTime $dateCreatedAt)
    {
        $this->dateCreatedAt = $dateCreatedAt;
    }

    /**
     * @return \DateTime
     */
    public function getDateCreatedAt()
    {
        return $this->dateCreatedAt;
    }

    public function setIsAdded($isAdded)
    {
        $this->isAdded = $isAdded;
    }

    public function getIsAdded()
    {
        return $this->isAdded;
    }

    /**
     * @param Product $product
     */
    public function setProduct(Product $product)
    {
        $this->product = $product;
    }

    /**
     * @return Product
     */
    public function getProduct()
    {
        return $this->product;
    }
}
