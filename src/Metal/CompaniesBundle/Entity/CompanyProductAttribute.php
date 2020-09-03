<?php

namespace Metal\CompaniesBundle\Entity;

use Metal\CategoriesBundle\Entity\ParameterOption;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="company_product_attribute", uniqueConstraints={
 * @ORM\UniqueConstraint(name="UNIQ_company", columns={"company_id", "attribute_value_id"} )}))
 */
class CompanyProductAttribute
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="id")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Company")
     * @ORM\JoinColumn(name="company_id", referencedColumnName="Message_ID", nullable=false)
     *
     * @var Company
     */
    protected $company;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\CategoriesBundle\Entity\ParameterOption")
     * @ORM\JoinColumn(name="attribute_value_id", referencedColumnName="Message_ID")
     *
     * @var ParameterOption
     */
    protected $attributeValue;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
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
     * @return ParameterOption
     */
    public function getAttributeValue()
    {
        return $this->attributeValue;
    }

    /**
     * @param ParameterOption $attributeValue
     */
    public function setAttributeValue(ParameterOption $attributeValue)
    {
        $this->attributeValue = $attributeValue;
    }
}