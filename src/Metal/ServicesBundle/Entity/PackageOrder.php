<?php

namespace Metal\ServicesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Metal\CompaniesBundle\Entity\Company;
use Metal\TerritorialBundle\Entity\City;
use Metal\UsersBundle\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="service_packages")
 * @Assert\Expression(
 *     "this.getStartAt() <= this.getFinishAt()",
 *     message="Дата старта не может быть больше даты окончания"
 * )
 */
class PackageOrder
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="id")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\UsersBundle\Entity\User")
     * @ORM\JoinColumn(name="User_ID", referencedColumnName="User_ID", nullable=true)
     *
     * @var User
     */
    protected $user;

    /**
     * @ORM\ManyToOne(targetEntity="Package")
     * @ORM\JoinColumn(name="package_id", referencedColumnName="Message_ID", nullable=false)
     *
     * @var Package
     */
    protected $package;

    /**
     * @ORM\Column(type="smallint", name="package_period")
     */
    protected $packagePeriod;

    /**
     * @ORM\Column(type="datetime", name="created_at")
     */
    protected $createdAt;

    /**
     * @ORM\Column(type="date", name="start_at")
     */
    protected $startAt;

    /**
     * @ORM\Column(type="date", name="finish_at")
     */
    protected $finishAt;
    /**
     * @ORM\ManyToOne(targetEntity="Metal\CompaniesBundle\Entity\Company")
     * @ORM\JoinColumn(name="company_id", referencedColumnName="Message_ID", nullable=true)
     *
     * @var Company
     */
    protected $company;

    /**
     * @ORM\Column(length=100, name="company_title", nullable=true)
     */
    protected $companyTitle;

    /**
     * @ORM\Column(length=255, name="comment", nullable=true)
     */
    protected $comment;

    /**
     * @ORM\Column(length=255, name="full_name")
     *
     * @Assert\NotBlank(groups={"admin_panel", "corp"})
     */
    protected $fullName;

    /**
     * @ORM\Column(name="email", length=40, nullable=false)
     *
     * @Assert\Email(groups={"corp"}, strict=true)
     * @Assert\NotBlank(groups={"corp"})
     */
    protected $email;

    /**
     * @ORM\Column(length=255, name="phone", nullable=false)
     * @Assert\NotBlank(groups={"corp"})
     */
    protected $phone;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\TerritorialBundle\Entity\City")
     * @ORM\JoinColumn(name="city_id", referencedColumnName="Region_ID", nullable=true)
     *
     * @Assert\NotBlank(message="Нужно выбрать город из списка.", groups={"corp"})
     *
     * @var City
     */
    protected $city;

    /**
     * @ORM\Column(type="boolean", name="is_payed", nullable=true)
     */
    protected $isPayed;

    /**
     * @ORM\Column(type="datetime", name="processed_at", nullable=true)
     *
     * @var \DateTime
     */
    protected $processedAt;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\UsersBundle\Entity\User")
     * @ORM\JoinColumn(name="processed_by", referencedColumnName="User_ID", nullable=true)
     *
     * @var User
     */
    protected $processedBy;

    public $cityTitle;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->startAt = new \DateTime();
        $this->finishAt = clone $this->startAt;
        $this->finishAt = $this->finishAt->modify('+3 month');
    }

    /**
     * @param \DateTime $processedAt
     */
    public function setProcessedAt(\DateTime $processedAt)
    {
        $this->processedAt = $processedAt;
    }

    /**
     * @return \DateTime
     */
    public function getProcessedAt()
    {
        return $this->processedAt;
    }

    /**
     * @param User $processedBy
     */
    public function setProcessedBy(User $processedBy)
    {
        $this->processedBy = $processedBy;
    }

    /**
     * @return User
     */
    public function getProcessedBy()
    {
        return $this->processedBy;
    }

    public function setProcessed($processedAt = true)
    {
        $this->processedAt = $processedAt ? new \DateTime() : null;
    }

    public function isProcessed()
    {
        return $this->processedAt !== null;
    }

    public function getProcessedAtTimestamp()
    {
        return $this->processedAt ? $this->processedAt->getTimestamp() : null;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user = null)
    {
        $this->user = $user;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param City $city
     */
    public function setCity(City $city)
    {
        $this->city = $city;
    }

    /**
     * @return City
     */
    public function getCity()
    {
        return $this->city;
    }

    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    public function getComment()
    {
        return $this->comment;
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

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @param integer $packagePeriod
     */
    public function setPackagePeriod($packagePeriod)
    {
        $this->packagePeriod = $packagePeriod;
    }

    /**
     * @return integer
     */
    public function getPackagePeriod()
    {
        return $this->packagePeriod;
    }

    /**
     * @param Package $package
     */
    public function setPackage(Package $package)
    {
        $this->package = $package;
    }

    public function getPackage()
    {
        return $this->package;
    }

    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    public function getPhone()
    {
        return $this->phone;
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

    /**
     * @param \DateTime $finishAt
     */
    public function setFinishAt(\DateTime $finishAt)
    {
        $this->finishAt = $finishAt;
    }

    /**
     * @return \DateTime
     */
    public function getFinishAt()
    {
        return $this->finishAt;
    }

    public function setFullName($fullName)
    {
        $this->fullName = $fullName;
    }

    public function getFullName()
    {
        return $this->fullName;
    }

    public function setCompanyTitle($companyTitle)
    {
        $this->companyTitle = $companyTitle;
    }

    public function getCompanyTitle()
    {
        return $this->companyTitle;
    }

    public function setIsPayed($isPayed)
    {
        $this->isPayed = $isPayed;
    }

    public function getIsPayed()
    {
        return $this->isPayed;
    }

    public function getPackageName()
    {
        switch ($this->package->getId()) {
            case Package::BASE_PACKAGE:
                return 'Базовый';

            case Package::ADVANCED_PACKAGE:
                return 'Расширенный';

            case Package::FULL_PACKAGE:
                return 'Полный';

            case Package::STANDARD_PACKAGE:
                return 'Стартовый';
        }
    }

    public function getPackagePeriodName()
    {
        switch ($this->packagePeriod) {
            case 1:
                return 'на квартал';

            case 2:
                return 'на полгода';

            case 3:
                return 'на год';

            case 4:
                return 'на месяц';
        }
    }
}
