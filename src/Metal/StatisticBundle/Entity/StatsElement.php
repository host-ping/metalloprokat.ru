<?php

namespace Metal\StatisticBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Metal\ProjectBundle\Entity\ValueObject\SourceType;
use Metal\ProjectBundle\Entity\ValueObject\SourceTypeProvider;
use Metal\TerritorialBundle\Entity\City;
use Metal\UsersBundle\Entity\User;

/**
 * @ORM\Entity(repositoryClass="Metal\StatisticBundle\Repository\StatsElementRepository")
 * @ORM\Table(name="stats_element", indexes={
 *   @ORM\Index(name="IDX_date_created_at", columns={"date_created_at"}),
 *   @ORM\Index(name="IDX_company_id", columns={"company_id"}),
 * })
 * @ORM\HasLifecycleCallbacks
 */
class StatsElement
{
    const ACTION_VIEW_PHONE = 1;

    /**
     * Просмотр продукта (конкретного)
     */
    const ACTION_VIEW_PRODUCT = 2;

    const ACTION_GO_TO_WEBSITE = 3;

    /**
     * Показ продукта (в списке)
     */
    const ACTION_SHOW_PRODUCT = 4;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="id")
     */
    protected $id;

    /** @ORM\Column(type="smallint", name="action") */
    protected $action;

    /** @ORM\Column(type="smallint", name="source_type_id") */
    protected $sourceTypeId;

    /**
     * @var SourceType
     */
    protected $sourceType;

    /** @ORM\Column(type="integer", name="company_id") */
    protected $companyId;

    /** @ORM\Column(type="integer", name="product_id", nullable=true) */
    protected $productId;

    /** @ORM\Column(type="integer", name="category_id", nullable=true) */
    protected $categoryId;

    /** @ORM\Column(length=25, name="ip") */
    protected $ip;

    /** @ORM\Column(length=255, name="user_agent") */
    protected $userAgent;

    /** @ORM\Column(length=255, name="referer", nullable=true) */
    protected $referer;

    /** @ORM\Column(length=64, name="session_id", nullable=true) */
    protected $sessionId;

    /**
     * @ORM\Column(type="datetime", name="created_at")
     *
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @ORM\Column(type="date", name="date_created_at")
     *
     * @var \DateTime
     */
    protected $dateCreatedAt;

    /** @ORM\Column(length=40, name="item_hash", nullable=false) */
    protected $itemHash;

    /** @ORM\Column(type="integer", name="city_id", nullable=true) */
    protected $cityId;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\TerritorialBundle\Entity\City")
     * @ORM\JoinColumn(name="city_id", referencedColumnName="Region_ID", nullable=true)
     *
     * @var City
     */
    protected $city;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\UsersBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="User_ID")
     *
     * @var User
     */
    protected $user;

    /** @ORM\Column(name="fake", type="smallint", nullable=false, options={"default":0}) */
    protected $fake;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->dateCreatedAt = new \DateTime();
        $this->fake = 0;
    }

    public function getFake()
    {
        return $this->fake;
    }

    public function setFake($fake)
    {
        $this->fake = $fake;
    }

    /**
     * @ORM\PostLoad
     */
    public function postLoad()
    {
        $this->sourceType = SourceTypeProvider::create($this->sourceTypeId);
    }

    public function getId()
    {
        return $this->id;
    }

    public function setAction($action)
    {
        $this->action = $action;
    }

    public function getAction()
    {
        return $this->action;
    }

    /**
     * @param City $city
     */
    public function setCity(City $city = null)
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

    public function setIp($ip)
    {
        $this->ip = $ip;
    }

    public function getIp()
    {
        return $this->ip;
    }

    public function setCompanyId($companyId)
    {
        $this->companyId = $companyId;
    }

    public function getCompanyId()
    {
        return $this->companyId;
    }

    public function setProductId($productId)
    {
        $this->productId = $productId;
    }

    public function getProductId()
    {
        return $this->productId;
    }

    public function setReferer($referer)
    {
        $this->referer = $referer;
    }

    public function getReferer()
    {
        return $this->referer;
    }

    public function setSessionId($sessionId)
    {
        $this->sessionId = $sessionId;
    }

    public function getSessionId()
    {
        return $this->sessionId;
    }

    /**
     * @param SourceType $sourceType
     */
    public function setSourceType(SourceType $sourceType)
    {
        $this->sourceType = $sourceType;
        $this->sourceTypeId = $sourceType->getId();
    }

    /**
     * @return SourceType
     */
    public function getSourceType()
    {
        return $this->sourceType;
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

    public function setUserAgent($userAgent)
    {
        $this->userAgent = (string)$userAgent;
    }

    public function getUserAgent()
    {
        return $this->userAgent;
    }

    public function setCategoryId($categoryId)
    {
        $this->categoryId = $categoryId;
    }

    public function getCategoryId()
    {
        return $this->categoryId;
    }

    /** @ORM\PrePersist */
    public function prePersist()
    {
        $this->generateHash();
    }

    public function generateHash()
    {
        $hashParts = array(
            $this->companyId,
            $this->dateCreatedAt->format('Y-m-d'),
            $this->action,
            $this->ip,
            $this->userAgent,
        );

        if ($this->action == self::ACTION_VIEW_PRODUCT || $this->action == self::ACTION_SHOW_PRODUCT) {
            $hashParts[] = $this->productId;
        }

        $this->itemHash = sha1(implode('-', $hashParts));
    }

    // зачатки для #MET-1735
    public static function generateSessionHash($action, $ip, $userAgent)
    {
        return sha1(serialize(array($action, $ip, $userAgent)));
    }

    public function toArray()
    {
        $this->generateHash();

        return array(
            'city_id' => $this->city ? $this->city->getId() : null,
            'user_id' => $this->user ? $this->user->getId() : null,
            'action' => $this->action,
            'source_type_id' => $this->sourceTypeId,
            'company_id' => $this->companyId,
            'ip' => $this->ip,
            'user_agent' => $this->userAgent,
            'referer' => $this->referer,
            'session_id' => $this->sessionId,
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
            'date_created_at' => $this->createdAt->format('Y-m-d'),
            'product_id' => $this->productId,
            'category_id' => $this->categoryId,
            'item_hash' => $this->itemHash,
            'fake' => $this->fake,
        );
    }
}
