<?php

namespace Metal\DemandsBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Metal\CategoriesBundle\Entity\Category;
use Metal\DemandsBundle\Entity\ValueObject\ConsumerType;
use Metal\DemandsBundle\Entity\ValueObject\DemandPeriodicity;
use Metal\ProjectBundle\Entity\Behavior\Attributable;
use Metal\ProjectBundle\Entity\Behavior\SoftDeleteable;
use Metal\ProjectBundle\Entity\Behavior\Updateable;
use Metal\ProjectBundle\Entity\ValueObject\AdminSourceType;
use Metal\ProjectBundle\Entity\ValueObject\AdminSourceTypeProvider;
use Metal\ProjectBundle\Entity\ValueObject\SiteSourceType;
use Metal\ProjectBundle\Entity\ValueObject\SiteSourceTypeProvider;
use Metal\TerritorialBundle\Entity\City;
use Metal\TerritorialBundle\Entity\Country;
use Metal\TerritorialBundle\Entity\Region;
use Metal\UsersBundle\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="Metal\DemandsBundle\Repository\AbstractDemandRepository")
 * @ORM\Table(name="demand", indexes={
 *     @ORM\Index(name="IDX_moderated_deleted", columns={"moderated_at", "deleted_at"}),
 *     @ORM\Index(name="IDX_deleted_at", columns={"deleted_at"}),
 *     @ORM\Index(name="IDX_demand_type", columns={"demand_type"})
 *  })
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="demand_type", type="integer")
 * @ORM\HasLifecycleCallbacks
 *
 * @ORM\DiscriminatorMap({
 *   1="Metal\DemandsBundle\Entity\Demand",
 *   2="Metal\DemandsBundle\Entity\PrivateDemand"
 * })
 */
abstract class AbstractDemand
{
    const TYPE_PUBLIC = 1;
    const TYPE_PRIVATE = 2;

    const EXPORT_LIMIT = 10000;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="id")
     */
    protected $id;

    /** @ORM\Column(type="text", name="body", nullable=true) */
    protected $body;

    /** @ORM\Column(type="integer", name="deadline") */
    protected $deadline;

    /**
     * @ORM\Column(type="integer", name="demand_periodicity")
     * в металспросе это колонка type
     */
    protected $demandPeriodicityId;

    /**
     * @var DemandPeriodicity
     */
    protected $demandPeriodicity;

    /** @ORM\Column(length=255, name="company_title", nullable=true) */
    protected $companyTitle;

    /**
     * @ORM\Column(length=255, name="phone")
     * @Assert\Regex(
     *   pattern="/\d+/",
     *   message="Неправильный номер телефона",
     *   groups={"anonymous", "anonymous_prokat", "authenticated_prokat", "admin_panel"}
     * )
     * @Assert\Length(
     *   min=6,
     *   max=255,
     *   groups={"anonymous", "anonymous_prokat", "authenticated_prokat", "admin_panel"}
     * )
     */
    protected $phone;

    /**
     * @ORM\Column(length=255, name="person", nullable=true)
     * @Assert\NotBlank(groups={"anonymous", "anonymous_prokat"})
     * @Assert\Length(min=2, groups={"anonymous", "anonymous_prokat"})
     */
    protected $person;

    /**
     * @ORM\Column(length=255, name="email", nullable=true)
     * @Assert\Email(groups={"anonymous", "anonymous_prokat", "authenticated_prokat", "admin_panel"}, strict=true)
     */
    protected $email;

    /** @ORM\Column(type="integer", name="time_to_show", nullable=true) */
    protected $timeToShow;

    /** @ORM\Column(type="integer", name="consumer_type") */
    protected $consumerTypeId;

    /**
     * @var ConsumerType
     */
    protected $consumerType;

    /** @ORM\Column(type="text", name="conditions") */
    protected $conditions;

    /** @ORM\Column(type="text", name="info", nullable=true) */
    protected $info;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\UsersBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="User_ID", nullable=true)
     *
     * @var User
     */
    protected $user;

    /**
     * @ORM\OneToMany(targetEntity="DemandItem", mappedBy="demand", cascade={"persist"}, orphanRemoval=true)
     * @Assert\Valid()
     *
     * @var ArrayCollection|DemandItem[]
     */
    protected $demandItems;

    /**
     * @ORM\OneToMany(targetEntity="DemandCategory", mappedBy="demand", indexBy="categoryId", cascade={"persist"}, orphanRemoval=true)
     *
     * @var ArrayCollection|DemandCategory[]
     */
    protected $demandCategories;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\CategoriesBundle\Entity\Category")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="Message_ID", nullable=true, onDelete="SET NULL")
     *
     * @var Category|null
     */
    protected $category;

    /** @ORM\Column(length=25, name="ip", nullable=true) */
    protected $ip;

    /** @ORM\Column(length=255, name="user_agent", nullable=true) */
    protected $userAgent;

    /**
     * @ORM\Column(type="datetime", name="created_at")
     *
     * @Assert\NotBlank(
     *   groups={"admin_panel"},
     *   message="Нужно указать дату создания"
     * )
     */
    protected $createdAt;

    /**
     * @ORM\Column(type="datetime", name="fake_updated_at", nullable=true)
     *
     * @var \DateTime
     */
    protected $fakeUpdatedAt;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\UsersBundle\Entity\User")
     * @ORM\JoinColumn(name="updated_by", referencedColumnName="User_ID", nullable=true)
     *
     * @var User
     */
    protected $updatedBy;

    /** @ORM\Column(length=25, name="last_ip", nullable=true) */
    protected $lastIp;

    /** @ORM\Column(length=255, name="last_user_agent", nullable=true) */
    protected $lastUserAgent;

    /** @ORM\Column(type="text", name="address", nullable=true) */
    protected $address;

    /** @ORM\Column(length=255, name="referer", nullable=true) */
    protected $referer;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\TerritorialBundle\Entity\City")
     * @ORM\JoinColumn(name="city_id", referencedColumnName="Region_ID", nullable=true)
     *
     * @Assert\NotBlank(
     *   groups={"anonymous", "anonymous_prokat", "authenticated_prokat", "admin_panel"},
     *   message="Нужно выбрать город из списка."
     * )
     *
     * @var City|null
     */
    protected $city;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\TerritorialBundle\Entity\Region")
     * @ORM\JoinColumn(name="region_id", referencedColumnName="Regions_ID", nullable=true)
     *
     * @var Region
     */
    protected $region;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\TerritorialBundle\Entity\Country")
     * @ORM\JoinColumn(name="country_id", referencedColumnName="Country_ID", nullable=true)
     *
     * @var Country
     */
    protected $country;

    /** @ORM\Column(type="boolean", name="is_wholesale") */
    protected $wholesale;

    /** @ORM\Column(type="integer", name="views_count", nullable=false) */
    protected $viewsCount;

    /** @ORM\Column(type="integer", name="answers_count", nullable=false) */
    protected $answersCount;

    /** @ORM\Column(type="integer", name="source_type") */
    protected $sourceTypeId;

    /**
     * @var SiteSourceType
     */
    protected $sourceType;

    /** @ORM\Column(type="integer", name="admin_source_type") */
    protected $adminSourceTypeId;

    /**
     * @var AdminSourceType
     */
    protected $adminSourceType;

    public $cityTitle;

    /**
     * @ORM\OneToMany(targetEntity="DemandFile", mappedBy="demand", cascade={"persist"}, orphanRemoval=true)
     * @Assert\Valid()
     *
     * @var ArrayCollection|DemandFile[]
     */
    protected $demandFiles;

    /**
     * @ORM\Column(length=255, name="email_file_path", nullable=true)
     */
    protected $emailFilePath;

    /**
     * @ORM\Column(type="datetime", name="moderated_at", nullable=true)
     *
     * @var \DateTime
     */
    protected $moderatedAt;

    use SoftDeleteable;

    /** @ORM\Column(length=255, name="confirmation_code", nullable=true) */
    protected $confirmationCode;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\UsersBundle\Entity\User")
     * @ORM\JoinColumn(name="possible_user_id", referencedColumnName="User_ID", nullable=true)
     *
     * @var User
     */
    protected $possibleUser;

    /**
     * @ORM\Column(type="boolean", name="display_file_on_site")
     */
    protected $displayFileOnSite;

    /**
     * @ORM\Column(type="datetime", name="public_until", nullable=true)
     *
     * @var \DateTime
     */
    protected $publicUntil;

    use Updateable;

    use Attributable;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
        $this->fakeUpdatedAt = new \DateTime();
        $this->demandCategories = new ArrayCollection();
        $this->demandItems = new ArrayCollection();
        $this->demandFiles = new ArrayCollection();
        $this->deadline = '14';
        $this->demandPeriodicityId = 1;
        $this->consumerTypeId = 3;
        $this->conditions = '';
        $this->wholesale = false;
        $this->timeToShow = 14;
        $this->viewsCount = 1;
        $this->answersCount = 0;
        $this->setSourceTypeId(SiteSourceTypeProvider::SOURCE_PORTAL);
        $this->adminSourceTypeId = 1;
        $this->displayFileOnSite = false;
    }

    /**
     * @ORM\PostLoad
     */
    public function postLoad()
    {
        $this->demandPeriodicity = ValueObject\DemandPeriodicityProvider::create($this->demandPeriodicityId);
        $this->consumerType = ValueObject\ConsumerTypeProvider::create($this->consumerTypeId);
        $this->sourceType = SiteSourceTypeProvider::create($this->sourceTypeId);

        $this->adminSourceType = null;
        if ($this->adminSourceTypeId) {
            $this->adminSourceType = AdminSourceTypeProvider::create($this->adminSourceTypeId);
        }
    }

    public function getId()
    {
        return $this->id;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function setBody($body)
    {
        $this->body = $body;
    }

    public function appendBody($body)
    {
        if ($this->body) {
            $this->body .= "\n".$body;
        } else {
            $this->body = $body;
        }
    }

    public function getDeadline()
    {
        return $this->deadline;
    }

    public function setDeadline($deadline)
    {
        $this->deadline = $deadline;
    }

    public function getCompanyTitle()
    {
        return $this->companyTitle;
    }

    public function setCompanyTitle($companyTitle)
    {
        $this->companyTitle = $companyTitle;
    }

    public function getPhone()
    {
        return $this->phone;
    }

    public function setPhone($phone)
    {
        $this->phone = (string)$phone;
    }

    public function getPerson()
    {
        return $this->person;
    }

    public function setPerson($person)
    {
        $this->person = $person;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getConditions()
    {
        return $this->conditions;
    }

    public function setConditions($conditions)
    {
        $this->conditions = $conditions;
    }

    public function getInfo()
    {
        return $this->info;
    }

    public function setInfo($info)
    {
        $this->info = $info;
    }

    public function getTimeToShow()
    {
        return $this->timeToShow;
    }

    public function setTimeToShow($timeToShow)
    {
        $this->timeToShow = $timeToShow;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setUser($user)
    {
        $this->user = $user;
    }

    public function getDemandItems()
    {
        return $this->demandItems;
    }

    public function getDemandItemsIds($notLockedAttributeValues = false)
    {
        $demandItemsIds = array();
        foreach ($this->demandItems as $demandItem) {
            if (!$notLockedAttributeValues || !$demandItem->getIsLockedAttributeValues()) {
                $demandItemsIds[] = $demandItem->getId();
            }
        }

        return $demandItemsIds;
    }

    public function addDemandItem(DemandItem $demandItem)
    {
        $demandItem->setDemand($this);
        $this->demandItems->add($demandItem);
        if (!$this->category) {
            $this->category = $demandItem->getCategory();
        }
    }

    public function removeDemandItem(DemandItem $demandItem)
    {
        $this->demandItems->removeElement($demandItem);
    }

    /**
     * @return ArrayCollection|DemandFile[]
     */
    public function getDemandFiles()
    {
        return $this->demandFiles;
    }

    public function addDemandFile(DemandFile $demandFile)
    {
        $demandFile->setDemand($this);
        $this->demandFiles->add($demandFile);
    }

    public function addDemandFiles(DemandFile $demandFile)
    {
        $this->addDemandFile($demandFile);
    }

    public function removeDemandFile(DemandFile $demandFile)
    {
        $this->demandFiles->removeElement($demandFile);
    }

    /**
     * @ORM\PrePersist()
     */
    public function updateCategories()
    {
        foreach ($this->demandItems as $key => $demandItem) {
            if ($demandItem->isEmpty()) {
                $this->demandItems->remove($key);
            }
        }

        $currentCategoriesIds = $this->demandCategories->getKeys();
        if ($this->category) {
            array_push($currentCategoriesIds, $this->getCategoryId());
        }
        $newCategoriesIds = array();
        //TODO: try to simplify this using getCategories method
        foreach ($this->demandItems as $demandItem) {
            if (!$demandItem->getCategory()) {
                continue;
            }

            $categoryId = $demandItem->getCategory()->getId();
            $newCategoriesIds[] = $categoryId;
            if (!$this->demandCategories->get($categoryId)) {
                $demandCategory = new DemandCategory();
                $demandCategory->setCategory($demandItem->getCategory());
                $this->addDemandCategory($demandCategory);
            }
        }

        $categoriesIdsRemoved = array_diff($currentCategoriesIds, $newCategoriesIds);
        foreach ($categoriesIdsRemoved as $categoryIdRemoved) {
            $this->demandCategories->remove($categoryIdRemoved);
        }

        if (!$this->category || in_array($this->category->getId(), $categoriesIdsRemoved)) {
            $this->category = null;

            $demandCategory = $this->demandCategories->first();
            if ($demandCategory) {
                $this->category = $demandCategory->getCategory();
            }
        }
    }

    public function getDemandPeriodicityId()
    {
        return $this->demandPeriodicityId;
    }

    public function getDemandPeriodicity()
    {
        return $this->demandPeriodicity;
    }

    public function getDemandPeriodicityTitle()
    {
        return $this->demandPeriodicity->getTitle();
    }

    public function setDemandPeriodicity(ValueObject\DemandPeriodicity $demandPeriodicity)
    {
        $this->demandPeriodicity = $demandPeriodicity;
        $this->demandPeriodicityId = $demandPeriodicity->getId();
    }

    public function getConsumerTypeId()
    {
        return $this->consumerTypeId;
    }

    public function getConsumerType()
    {
        return $this->consumerType;
    }

    public function getConsumerTypeTitle()
    {
        return $this->consumerType->getTitle();
    }

    public function setConsumerType(ValueObject\ConsumerType $consumerType)
    {
        $this->consumerType = $consumerType;
        $this->consumerTypeId = $consumerType->getId();
    }

    public function getIp()
    {
        return $this->ip;
    }

    public function getUserAgent()
    {
        return $this->userAgent;
    }

    public function getLastIp()
    {
        return $this->lastIp;
    }

    public function getLastUserAgent()
    {
        return $this->lastUserAgent;
    }

    public function getAddress()
    {
        return $this->address;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return User
     */
    public function getUpdatedBy()
    {
        return $this->updatedBy;
    }

    /**
     * @return \DateTime
     */
    public function getFakeUpdatedAt()
    {
        return $this->fakeUpdatedAt;
    }

    /**
     * @param \DateTime $fakeUpdatedAt
     */
    public function setFakeUpdatedAt(\DateTime $fakeUpdatedAt)
    {
        $this->fakeUpdatedAt = $fakeUpdatedAt;
    }

    public function setDemandPeriodicityId($demandPeriodicityId)
    {
        $this->demandPeriodicityId = $demandPeriodicityId;
        $this->postLoad();
    }

    public function setConsumerTypeId($consumerTypeId)
    {
        $this->consumerTypeId = $consumerTypeId;
        $this->postLoad();
    }

    public function setIp($ip)
    {
        $this->ip = $ip;
        $this->lastIp = $ip;
    }

    public function setUserAgent($userAgent)
    {
        $this->userAgent = $userAgent;
        $this->lastUserAgent = $userAgent;
    }

    public function setCreatedAt(\DateTime $createdAt = null)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @param User $updatedBy
     */
    public function setUpdatedBy(User $updatedBy)
    {
        $this->updatedBy = $updatedBy;
    }

    public function setLastIp($lastIp)
    {
        $this->lastIp = $lastIp;
    }

    public function setLastUserAgent($lastUserAgent)
    {
        $this->lastUserAgent = $lastUserAgent;
    }

    public function setAddress($address)
    {
        $this->address = $address;
    }

    public function getCity(): ?City
    {
        return $this->city;
    }

    /**
     * @return City
     */
    public function getLinkedCity()
    {
        return $this->city->getSlug() ? $this->city : $this->city->getAdministrativeCenter();
    }

    /**
     * @param City $city
     */
    public function setCity(City $city)
    {
        $this->city = $city;
        $this->region = $city->getRegion();
        $this->country = $city->getCountry();
    }

    /**
     * @return Region
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * @param Region $region
     */
    public function setRegion(Region $region)
    {
        $this->region = $region;
        $this->country = $region->getCountry();
    }

    /**
     * @return Country
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param Country $country
     */
    public function setCountry(Country $country)
    {
        $this->country = $country;
    }

    public function getWholesale()
    {
        return $this->wholesale;
    }

    public function setWholesale($wholesale)
    {
        $this->wholesale = $wholesale;
    }

    public function getDemandCategories()
    {
        return $this->demandCategories;
    }

    public function addDemandCategory(DemandCategory $demandCategory)
    {
        $demandCategory->setDemand($this);
        if (null === $demandCategory->getCategoryId()) {
            throw new \LogicException('Category Id should be setted first');
        }
        $this->demandCategories->set($demandCategory->getCategoryId(), $demandCategory);
    }

    /**
     * @return Category[]
     */
    public function getCategoriesFromDemandItems()
    {
        $categories = array();
        foreach ($this->demandItems as $demandItem) {
            $category = $demandItem->getCategory();
            if (!$category) {
                continue;
            }
            $categories[$category->getId()] = $category;
        }

        return $categories;
    }

    /**
     * @return Category[] Structure of id => Category
     */
    public function getCategories()
    {
        $categories = array();
        foreach ($this->demandCategories as $demandCategory) {
            $category = $demandCategory->getCategory();
            $categories[$category->getId()] = $category;
        }

        return $categories;
    }

    public function getCategoriesIds()
    {
        return array_keys($this->getCategories());
    }

    public function getCategoriesIdsWithAllParents()
    {
        $parts = [[]];
        foreach ($this->getCategories() as $category) {
            $parts[] = $category->getBranchIds();
        }

        return array_values(array_unique(array_merge(...$parts)));
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function getCategoryId()
    {
        if ($this->category) {
            return $this->category->getId();
        }

        return null;
    }

    public function setCategory(Category $category = null)
    {
        $this->category = $category;
    }

    public function setViewsCount($viewsCount)
    {
        $this->viewsCount = $viewsCount;
    }

    public function getViewsCount()
    {
        return $this->viewsCount;
    }

    public function setAnswersCount($answersCount)
    {
        $this->answersCount = $answersCount;
    }

    public function getAnswersCount()
    {
        return $this->answersCount;
    }

    /**
     * @param SiteSourceType $sourceType
     */
    public function setSourceType(SiteSourceType $sourceType)
    {
        $this->sourceType = $sourceType;
        $this->sourceTypeId = $sourceType->getId();
    }

    /**
     * @return SiteSourceType
     */
    public function getSourceType()
    {
        return $this->sourceType;
    }

    /**
     * @param \Metal\ProjectBundle\Entity\ValueObject\AdminSourceType $adminSourceType
     */
    public function setAdminSourceType(AdminSourceType $adminSourceType)
    {
        $this->adminSourceType = $adminSourceType;
        $this->adminSourceTypeId = $adminSourceType->getId();
    }

    /**
     * @return \Metal\ProjectBundle\Entity\ValueObject\AdminSourceType
     */
    public function getAdminSourceType()
    {
        return $this->adminSourceType;
    }

    public function setAdminSourceTypeId($adminSourceTypeId)
    {
        if (is_integer($adminSourceTypeId)) {
            $this->adminSourceTypeId = (int)$adminSourceTypeId;
            $this->postLoad();
        }
    }

    public function getAdminSourceTypeId()
    {
        return $this->adminSourceTypeId;
    }

    public function getSourceTypeTitle()
    {
        return $this->sourceType->getTitle();
    }

    public function getAdminSourceTypeTitle()
    {
        return $this->adminSourceType ? $this->adminSourceType->getTitle() : '';
    }

    public function setSourceTypeId($sourceTypeId)
    {
        $this->sourceTypeId = $sourceTypeId;
        $this->postLoad();
    }

    public function getSourceTypeId()
    {
        return $this->sourceTypeId;
    }

    public function setReferer($referer)
    {
        $this->referer = $referer;
    }

    public function getReferer()
    {
        return $this->referer;
    }

    public function getEmailFilePath()
    {
        return $this->emailFilePath;
    }

    public function setEmailFilePath($emailFilePath)
    {
        $this->emailFilePath = $emailFilePath;
    }

    public function getEmailSubDir()
    {
        return 'emails';
    }

    public function getEmailWebPath()
    {
        return '/'.$this->getEmailSubDir().'/'.$this->getEmailFilePath();
    }

    /**
     * @param \DateTime $moderatedAt
     */
    public function setModeratedAt(\DateTime $moderatedAt)
    {
        $this->moderatedAt = $moderatedAt;
    }

    /**
     * @return \DateTime
     */
    public function getModeratedAt()
    {
        return $this->moderatedAt;
    }

    public function setModerated($moderatedAt = true)
    {
        $this->moderatedAt = $moderatedAt ? new \DateTime() : null;
    }

    public function isModerated()
    {
        return $this->moderatedAt !== null;
    }

    public function getModeratedAtTimestamp()
    {
        return $this->moderatedAt ? $this->moderatedAt->getTimestamp() : null;
    }

    public function isFromTrader()
    {
        return $this->consumerTypeId == ValueObject\ConsumerTypeProvider::TRADER;
    }

    public function isFromBuyer()
    {
        return $this->consumerTypeId == ValueObject\ConsumerTypeProvider::CONSUMER;
    }

    public function setConfirmationCode($confirmationCode)
    {
        $this->confirmationCode = $confirmationCode;
    }

    public function getConfirmationCode()
    {
        return $this->confirmationCode;
    }

    /**
     * @param User $possibleUser
     */
    public function setPossibleUser(User $possibleUser = null)
    {
        $this->possibleUser = $possibleUser;
    }

    /**
     * @return User
     */
    public function getPossibleUser()
    {
        return $this->possibleUser;
    }

    public function getDisplayFileOnSite()
    {
        return $this->displayFileOnSite;
    }

    public function setDisplayFileOnSite($displayFileOnSite)
    {
        $this->displayFileOnSite = $displayFileOnSite;
    }

    /**
     * @return \DateTime
     */
    public function getPublicUntil()
    {
        return $this->publicUntil;
    }

    /**
     * @param \DateTime|null $publicUntil
     */
    public function setPublicUntil(\DateTime $publicUntil = null)
    {
        $this->publicUntil = $publicUntil;
    }

    public function populateDataFromRequest(Request $request, $setSourceType = true, $setUpdated = false)
    {
        $this->setIp($request->getClientIp());
        $this->setUserAgent($request->headers->get('USER_AGENT'));
        $this->setReferer($request->headers->get('REFERER'));
        if ($setSourceType) {
            $this->setSourceType(SiteSourceTypeProvider::getSourceByHost($request->getHost()));
        }

        if ($setUpdated) {
            $this->setLastIp($request->getClientIp());
            $this->setLastUserAgent($request->headers->get('USER_AGENT'));
        }
    }

    public function getFixedCompanyTitle()
    {
        if ($this->companyTitle) {
            return $this->companyTitle;
        }

        if ($this->user && $this->sourceTypeId != SiteSourceTypeProvider::SOURCE_ADMIN) {
            if ($this->user->getCompanyTitle()) {
                return $this->user->getCompanyTitle();
            }

            if ($company = $this->user->getCompany()) {
                return $company->getTitle();
            }
        }

        return null;
    }

    public function getFixedEmail()
    {
        if ($this->email) {
            return $this->email;
        }

        if ($this->user && $this->sourceTypeId != SiteSourceTypeProvider::SOURCE_ADMIN) {
            $company = $this->user->getCompany();
            if ($company && $company->getCompanyLog()->getCreatedBy()->getEmail()) {
                return trim($company->getCompanyLog()->getCreatedBy()->getEmail());
            }

            return trim($this->user->getEmail());
        }

        return null;
    }

    public static function getFixedEmailForExport($user, $demandEmail, $sourceTypeId)
    {
        if ($demandEmail) {
            return $demandEmail;
        }

        if ($user && $sourceTypeId != SiteSourceTypeProvider::SOURCE_ADMIN) {
            if ($user['companyEmail']) {
                return $user['companyEmail'];
            }

            return $user['userEmail'];
        }

        return '';
    }

    /**
     * @see self::getFixedUserTitleForExport
     *
     * @return null|string
     */
    public function getFixedUserTitle()
    {
        if ($this->person) {
            return $this->person;
        }

        if ($this->user && $this->sourceTypeId != SiteSourceTypeProvider::SOURCE_ADMIN) {
            return $this->user->getFullName();
        }

        return null;
    }

    public static function getFixedUserTitleForExport($person, $sourceTypeId, array $user = array())
    {
        if ($person) {
            return $person;
        }

        if ($user && $sourceTypeId != SiteSourceTypeProvider::SOURCE_ADMIN) {
            return implode(' ', array_filter(array($user['firstName'], $user['secondName'])));
        }

        return null;
    }

    public function getFixedCityTitle()
    {
        return $this->cityTitle ?: $this->getCity()->getTitle();
    }

    public function isOpened()
    {
        return $this->publicUntil ? $this->publicUntil > new \DateTime() : false;
    }

    public function getDisplayTime()
    {
        return max($this->createdAt, $this->moderatedAt);
    }

    public abstract function isPublic();
}
