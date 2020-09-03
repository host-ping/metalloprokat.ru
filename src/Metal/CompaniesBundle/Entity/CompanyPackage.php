<?php

namespace Metal\CompaniesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Metal\CompaniesBundle\Entity\ValueObject\CompanyPackageType;

/**
 * @ORM\Entity
 * @ORM\Table(name="Message106")
 */
class CompanyPackage
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="Message_ID")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\CompaniesBundle\Entity\Company")
     * @ORM\JoinColumn(name="company_id", referencedColumnName="Message_ID", nullable=false)
     *
     * @var Company
     */
    protected $company;

    /**
     * @var CompanyPackageType
     * //TODO: отказаться от VO и вместо этого завязаться на значение в базе?
     */
    protected $companyPackageType;

    /** @ORM\Column(type="datetime", name="Created") */
    protected $createdAt;

    /** @ORM\Column(type="date", name="start_date") */
    protected $startAt;

    /** @ORM\Column(type="date", name="end_date") */
    protected $endAt;

    /** @ORM\Column(type="smallint", name="package_id") */
    protected $packageId;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getTitle()
    {
        if ($this->company->isVisibleInAllCountries()) {
            return 'Полный плюс, Россия и СНГ';
        }

        if ($this->company->isVisibleInOtherCountries()) {
            return 'Полный плюс, СНГ';
        }

        if ($this->company->isVisibleInAllCities()) {
            return 'Полный плюс';
        }

        if ($this->company->getPromocode()) {
            return $this->getCompanyPackageType()->getTitle().' (промокод)';
        }

        return $this->getCompanyPackageType()->getTitle();
    }

    public function getTitleGenitive()
    {
        if ($this->company->isVisibleInAllCountries()) {
            return 'Полного плюс, Россия и СНГ';
        }

        if ($this->company->isVisibleInOtherCountries()) {
            return 'Полного плюс, СНГ';
        }

        if ($this->company->isVisibleInAllCities()) {
            return 'Полного плюс';
        }

        if ($this->company->getPromocode()) {
            return $this->getCompanyPackageType()->getTitle().' (промокод)';
        }

        return $this->getCompanyPackageType()->getTitleGenitive();
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

    /**
     * @param \DateTime $endAt
     */
    public function setEndAt(\DateTime $endAt)
    {
        $this->endAt = $endAt;
    }

    /**
     * @return \DateTime
     */
    public function getEndAt()
    {
        return $this->endAt;
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @return CompanyPackageType
     */
    public function getCompanyPackageType()
    {
        if (null === $this->companyPackageType) {
            $this->initializePackageType();
        }

        return $this->companyPackageType;
    }

    public function initializePackageType()
    {
        $this->companyPackageType = ValueObject\CompanyPackageTypeProvider::create($this->packageId);
    }

    /**
     * @param \DateTime $startAt
     */
    public function setStartAt(\DateTime $startAt)
    {
        $this->startAt = $startAt;
    }

    /**
     * @return \DateTime
     */
    public function getStartAt()
    {
        return $this->startAt;
    }

    public function isActive()
    {
        $date = new \DateTime();

        return $this->endAt > $date && $this->startAt < $date;
    }

    public function getPackageId()
    {
        return $this->packageId;
    }

    public function setPackageId($packageId)
    {
        $this->packageId = $packageId;
        $this->initializePackageType();
    }
}
