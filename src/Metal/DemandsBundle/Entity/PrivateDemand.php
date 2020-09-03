<?php

namespace Metal\DemandsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Metal\CompaniesBundle\Entity\Company;
use Metal\ProductsBundle\Entity\Product;
use Metal\TerritorialBundle\Entity\City;
use Metal\TerritorialBundle\Entity\Country;
use Metal\UsersBundle\Entity\User;

/**
 * @ORM\Entity(repositoryClass="Metal\DemandsBundle\Repository\PrivateDemandRepository")
 */
class PrivateDemand extends AbstractDemand
{
    /**
     * @ORM\ManyToOne(targetEntity="Metal\ProductsBundle\Entity\Product")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="Message_ID")
     */
    protected $product;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\CompaniesBundle\Entity\Company")
     * @ORM\JoinColumn(name="company_id", referencedColumnName="Message_ID", nullable=true)
     */
    protected $company;

    /** @ORM\Column(type="datetime", name="viewed_at") */
    protected $viewedAt;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\UsersBundle\Entity\User")
     * @ORM\JoinColumn(name="viewed_by", referencedColumnName="User_ID", nullable=true)
     */
    protected $viewedBy;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\TerritorialBundle\Entity\City")
     * @ORM\JoinColumn(name="product_city_id", referencedColumnName="Region_ID", nullable=true)
     *
     * @var City
     */
    protected $productCity;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\TerritorialBundle\Entity\Country")
     * @ORM\JoinColumn(name="product_country_id", referencedColumnName="Country_ID", nullable=true)
     *
     * @var Country
     */
    protected $productCountry;

    /**
     * @param Product $product
     */
    public function setProduct(Product $product)
    {
        $this->product = $product;
        //FIXME: product->category = null
        $this->category = $product->getCategory();
    }

    /**
     * @return Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @param \DateTime $viewedAt
     */
    public function setViewedAt(\DateTime $viewedAt)
    {
        $this->viewedAt = $viewedAt;
    }

    /**
     * @return \DateTime
     */
    public function getViewedAt()
    {
        return $this->viewedAt;
    }

    /**
     * @param User $viewedBy
     */
    public function setViewedBy(User $viewedBy)
    {
        $this->viewedBy = $viewedBy;
    }

    /**
     * @return User
     */
    public function getViewedBy()
    {
        return $this->viewedBy;
    }

    /**
     * @return Company
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @param Company $company
     */
    public function setCompany(Company $company)
    {
        $this->company = $company;
    }

    /**
     * @param City $productCity
     */
    public function setProductCity(City $productCity)
    {
        $this->productCity = $productCity;
    }

    /**
     * @return City
     */
    public function getProductCity()
    {
        return $this->productCity;
    }

    /**
     * @return Country
     */
    public function getProductCountry()
    {
        return $this->productCountry;
    }

    /**
     * @param Country $productCountry
     */
    public function setProductCountry(Country $productCountry)
    {
        $this->productCountry = $productCountry;
    }

    public function isPublic()
    {
        return false;
    }
}
