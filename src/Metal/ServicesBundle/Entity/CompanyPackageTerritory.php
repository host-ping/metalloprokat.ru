<?php

namespace Metal\ServicesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Metal\CompaniesBundle\Entity\Company;
use Metal\CompaniesBundle\Entity\ValueObject\CompanyPackageType;
use Metal\CompaniesBundle\Entity\ValueObject\CompanyPackageTypeProvider;
use Metal\ProjectBundle\Entity\Behavior\Updateable;
use Metal\TerritorialBundle\Entity\TerritorialStructure;

/**
 * @ORM\Entity(repositoryClass="Metal\ServicesBundle\Repository\CompanyPackageTerritoryRepository")
 * @ORM\Table(
 *     name="company_package_territory",
 *     uniqueConstraints={
 *          @ORM\UniqueConstraint(name="company_territory", columns={"company_id", "territory_id"}),
 *     }
 * )
 */
class CompanyPackageTerritory
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="id")
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
     * @ORM\ManyToOne(targetEntity="Metal\TerritorialBundle\Entity\TerritorialStructure")
     * @ORM\JoinColumn(name="territory_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     *
     * @var TerritorialStructure
     */
    protected $territory;

    /**
     * @var CompanyPackageType
     * //TODO: отказаться от VO и вместо этого завязаться на значение в базе?
     */
    protected $companyPackageType;

    /** @ORM\Column(type="smallint", name="package_id") */
    protected $packageId;

    /**
     * @ORM\Column(type="datetime", name="created_at")
     *
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @ORM\Column(type="date", name="spros_ends_at", nullable=true)
     *
     * @var \DateTime
     */
    protected $sprosEndsAt;

    /**
     * @ORM\Column(type="date", name="starts_at", nullable=true)
     *
     * @var \DateTime
     */
    protected $startsAt;

    /**
     * @ORM\Column(type="date", name="ends_at", nullable=true)
     *
     * @var \DateTime
     */
    protected $endsAt;

    use Updateable;

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
     * @return TerritorialStructure
     */
    public function getTerritory()
    {
        return $this->territory;
    }

    /**
     * @param TerritorialStructure $territory
     */
    public function setTerritory(TerritorialStructure $territory)
    {
        $this->territory = $territory;
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

    /**
     * @return \DateTime
     */
    public function getSprosEndsAt()
    {
        return $this->sprosEndsAt;
    }

    /**
     * @param \DateTime $sprosEndsAt
     */
    public function setSprosEndsAt(\DateTime $sprosEndsAt)
    {
        $this->sprosEndsAt = $sprosEndsAt;
    }

    /**
     * @return \DateTime
     */
    public function getStartsAt()
    {
        return $this->startsAt;
    }

    /**
     * @param \DateTime $startsAt
     */
    public function setStartsAt(\DateTime $startsAt)
    {
        $this->startsAt = $startsAt;
    }

    /**
     * @return \DateTime
     */
    public function getEndsAt()
    {
        return $this->endsAt;
    }

    /**
     * @param \DateTime $endsAt
     */
    public function setEndsAt(\DateTime $endsAt)
    {
        $this->endsAt = $endsAt;
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
        $this->companyPackageType = CompanyPackageTypeProvider::create($this->packageId);
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
