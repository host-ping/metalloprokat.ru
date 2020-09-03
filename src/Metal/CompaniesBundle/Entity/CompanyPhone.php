<?php

namespace Metal\CompaniesBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Metal\CompaniesBundle\Repository\CompanyPhoneRepository")
 * @ORM\Table(name="company_phone")
 */
class CompanyPhone
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="id")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\CompaniesBundle\Entity\Company", inversedBy="phones")
     * @ORM\JoinColumn(name="company_id", referencedColumnName="Message_ID")
     *
     * @var Company
     */
    protected $company;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\CompaniesBundle\Entity\CompanyCity", inversedBy="phones")
     * @ORM\JoinColumn(name="branch_office_id", referencedColumnName="id", nullable=true)
     *
     * @var CompanyCity
     */
    protected $branchOffice;

    /**
     * @ORM\Column(length=50, name="phone", options={"default": ""})
     *
     * @Assert\Length(max="50")
     * @Assert\NotBlank(groups={"company_edit"})
     */
    protected $phone;

    /**
     * @ORM\Column(length=50, name="additional_code", nullable=true)
     * @Assert\Length(max="50")
     */
    protected $additionalCode;

    public function __construct()
    {
        $this->phone = '';
    }

    public function getId()
    {
        return $this->id;
    }

    public function setAdditionalCode($additionalCode)
    {
        $this->additionalCode = $additionalCode;
    }

    public function getAdditionalCode()
    {
        return $this->additionalCode;
    }

    /**
     * @param CompanyCity $branchOffice
     */
    public function setBranchOffice(CompanyCity $branchOffice = null)
    {
        $this->branchOffice = $branchOffice;
    }

    /**
     * @return CompanyCity
     */
    public function getBranchOffice()
    {
        return $this->branchOffice;
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

    public function setPhone($phone)
    {
        $this->phone = (string)$phone;
    }

    public function getPhone()
    {
        return $this->phone;
    }
}
