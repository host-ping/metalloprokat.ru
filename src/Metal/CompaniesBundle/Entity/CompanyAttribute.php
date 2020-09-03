<?php

namespace Metal\CompaniesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Metal\CompaniesBundle\Entity\ValueObject\CompanyServiceProvider;

/**
 * @ORM\Entity(repositoryClass="Metal\CompaniesBundle\Repository\CompanyAttributeRepository")
 * @ORM\Table(name="company_attribute", uniqueConstraints={
 * @ORM\UniqueConstraint(name="UNIQ_company", columns={"company_id", "type"} )})
 */
class CompanyAttribute
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="id")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\CompaniesBundle\Entity\Company", inversedBy="companyAttributes")
     * @ORM\JoinColumn(name="company_id", referencedColumnName="Message_ID", nullable=false)
     *
     * @var Company
     */
    protected $company;

    /** @ORM\Column(type="smallint", name="type", nullable=false) */
    protected $typeId;

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

    public function setTypeId($typeId)
    {
        $this->typeId = $typeId;
    }

    public function getTypeId()
    {
        return $this->typeId;
    }

    /**
     * @return ValueObject\CompanyService
     */
    public function getType()
    {
        return CompanyServiceProvider::create($this->typeId);
    }
}
