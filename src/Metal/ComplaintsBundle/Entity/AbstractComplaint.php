<?php

namespace Metal\ComplaintsBundle\Entity;

use Doctrine\ORM\Mapping as Orm;
use Metal\CategoriesBundle\Entity\Category;
use Metal\ComplaintsBundle\Entity\ValueObject;
use Metal\TerritorialBundle\Entity\City;
use Metal\UsersBundle\Entity\User;
use Metal\CompaniesBundle\Entity\Company;
use Doctrine\ORM\Mapping\Index;

/**
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="Metal\ComplaintsBundle\Repository\AbstractComplaintRepository")
 * @ORM\Table(name="complaint",indexes={@Index(name="IDX_company_viewer_processed", columns={"company_id", "viewed_by", "processed_at"})})
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="complaint_object_type")
 * @ORM\HasLifeCycleCallbacks
 * @ORM\DiscriminatorMap({
 *   "demand"="Metal\ComplaintsBundle\Entity\DemandComplaint",
 *   "product"="Metal\ComplaintsBundle\Entity\ProductComplaint",
 *   "company"="Metal\ComplaintsBundle\Entity\CompanyComplaint"
 *
 * })
 */
abstract class AbstractComplaint
{
    const PRODUCT_TYPE = 'product';
    const DEMAND_TYPE = 'demand';
    const COMPANY_TYPE = 'company';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="id")
     */
    protected $id;

    /** @ORM\Column(type="text", name="body", nullable=true) */
    protected $body;

    /** @ORM\Column(length=25, name="ip", nullable=true) */
    protected $ip;

    /** @ORM\Column(length=255, name="user_agent", nullable=true) */
    protected $userAgent;

    /** @ORM\Column(length=255, name="referer", nullable=true) */
    protected $referer;

    /**
     * @ORM\Column(type="datetime", name="created_at")
     *
     * @var \DateTime
     */
    protected $createdAt;

    /** @ORM\Column(type="integer", name="complaint_type") */
    protected $complaintTypeId;

    /**
     * @ORM\Column(type="datetime", name="processed_at", nullable=true)
     *
     * @var \DateTime
     */
    protected $processedAt;

    /**
     * @ORM\Column(type="datetime", name="viewed_at", nullable=true)
     *
     * @var \DateTime
     */
    protected $viewedAt;

    /**
     * @var ValueObject\ComplaintType
     */
    protected $complaintType;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\UsersBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="User_ID", nullable=true)
     */
    protected $user;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\CompaniesBundle\Entity\Company")
     * @ORM\JoinColumn(name="company_id", referencedColumnName="Message_ID")
     */
    protected $company;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\UsersBundle\Entity\User")
     * @ORM\JoinColumn(name="viewed_by", referencedColumnName="User_ID", nullable=true)
     */
    protected $viewedBy;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\TerritorialBundle\Entity\City")
     * @ORM\JoinColumn(name="city_id", referencedColumnName="Region_ID", nullable=false)
     *
     * @var City
     */
    protected $city;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\CategoriesBundle\Entity\Category")
     * @ORM\JoinColumn(name="Category_ID", referencedColumnName="Message_ID", nullable=true)
     *
     * @var Category
     */
    protected $category;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    /**
     * @ORM\PostLoad
     */
    public function postLoad()
    {
        $this->complaintType = ValueObject\ComplaintTypeProvider::create($this->complaintTypeId);
    }

    public function getId()
    {
        return $this->id;
    }

    public function setBody($body)
    {
        $this->body = $body;
    }

    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param \Metal\ComplaintsBundle\Entity\ValueObject\ComplaintType $complaintType
     */
    public function setComplaintType(ValueObject\ComplaintType $complaintType)
    {
        $this->complaintType = $complaintType;
        $this->complaintTypeId = $complaintType->getId();
    }

    /**
     * @return \Metal\ComplaintsBundle\Entity\ValueObject\ComplaintType
     */
    public function getComplaintType()
    {
        return $this->complaintType;
    }

    public function setComplaintTypeId($complaintTypeId)
    {
        $this->complaintTypeId = $complaintTypeId;
        $this->postLoad();
    }

    public function getComplaintTypeId()
    {
        return $this->complaintTypeId;
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

    public function setIp($ip)
    {
        $this->ip = $ip;
    }

    public function getIp()
    {
        return $this->ip;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user)
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

    public function setUserAgent($userAgent)
    {
        $this->userAgent = $userAgent;
    }

    public function getUserAgent()
    {
        return $this->userAgent;
    }

    public function setReferer($referer)
    {
        $this->referer = $referer;
    }

    public function getReferer()
    {
        return $this->referer;
    }

    /**
     * @param \DateTime $processedAt
     */
    public function setProcessedAt(\DateTime $processedAt)
    {
        $this->processedAt = $processedAt;
    }

    /**
     * @return \DateTime|null
     */
    public function getProcessedAt()
    {
        return $this->processedAt;
    }

    public function setProcessed($isProcessed = true)
    {
        $this->processedAt = $isProcessed ? new \DateTime() : null;
    }

    public function isProcessed()
    {
        return $this->processedAt !== null;
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
     * @param \DateTime $viewedAt
     */
    public function setViewedAt(\DateTime $viewedAt)
    {
        $this->viewedAt = $viewedAt;
    }

    /**
     * @return \DateTime|null
     */
    public function getViewedAt()
    {
        return $this->viewedAt;
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

    public function getComplaintTypeTitle()
    {
        return $this->complaintType->getTitle();
    }

    /**
     * @return User
     */
    public function getUserName()
    {
        return $this->user ? $this->user->getFullName() : '';
    }

    /**
     * @return User
     */
    public function getAuthor()
    {
        return $this->user;
    }

    /**
     * @return User
     */
    public function getViewedByFullName()
    {
        return $this->viewedBy ? $this->viewedBy->getFullName() : '';
    }


    public static function factory($kind)
    {
        switch ($kind) {
            case self::DEMAND_TYPE:
                return new DemandComplaint();

            case self::PRODUCT_TYPE:
                return new ProductComplaint();

            case self::COMPANY_TYPE:
                return new CompanyComplaint();

            default:
                throw new \InvalidArgumentException();
        }
    }

    public abstract function setObject($object);

    public abstract function getObjectKind();
}
