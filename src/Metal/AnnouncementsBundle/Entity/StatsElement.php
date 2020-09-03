<?php

namespace Metal\AnnouncementsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Metal\CategoriesBundle\Entity\Category;
use Metal\ProjectBundle\Entity\ValueObject\SourceType;
use Metal\ProjectBundle\Entity\ValueObject\SourceTypeProvider;
use Metal\TerritorialBundle\Entity\City;
use Metal\UsersBundle\Entity\User;

/**
 * @ORM\Entity
 * @ORM\Table(name="announcement_stats_element", indexes={
 *   @ORM\Index(name="IDX_date_created_at", columns={"date_created_at"})
 * })
 * @ORM\HasLifecycleCallbacks
 */
class StatsElement
{
    const ACTION_VIEW = 1;
    const ACTION_REDIRECT = 2;

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

    /**
     * @ORM\ManyToOne(targetEntity="Metal\AnnouncementsBundle\Entity\Announcement")
     * @ORM\JoinColumn(name="announcement_id", referencedColumnName="id", onDelete="CASCADE")
     *
     * @var Announcement
     */
    protected $announcement;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\CategoriesBundle\Entity\Category")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="Message_ID")
     *
     * @var Category
     */
    protected $category;

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
     * @ORM\PrePersist
     */
    public function generateHash()
    {
        $hashParts = array($this->announcement->getId(), $this->dateCreatedAt->format('Y-m-d'), $this->action, $this->ip, $this->userAgent);

        $this->itemHash = sha1(implode('-', $hashParts));
    }

    /**
     * @ORM\PostLoad
     */
    public function afterLoad()
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

    public function setAnnouncement(Announcement $announcement)
    {
        $this->announcement = $announcement;
    }

    /**
     * @return Announcement
     */
    public function getAnnouncement()
    {
        return $this->announcement;
    }

    public function setUserAgent($userAgent)
    {
        $this->userAgent = $userAgent;
    }

    public function getUserAgent()
    {
        return $this->userAgent;
    }

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

    public function setSessionId($sessionId)
    {
        $this->sessionId = $sessionId;
    }

    public function getSessionId()
    {
        return $this->sessionId;
    }

    public function setReferer($referer)
    {
        $this->referer = $referer;
    }

    public function getReferer()
    {
        return $this->referer;
    }

    public function setItemHash($itemHash)
    {
        $this->itemHash = $itemHash;
    }

    public function getItemHash()
    {
        return $this->itemHash;
    }

    public function setIp($ip)
    {
        $this->ip = $ip;
    }

    public function getIp()
    {
        return $this->ip;
    }

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

    public function setSourceType(SourceType $sourceType)
    {
        $this->sourceType = $sourceType;
        $this->sourceTypeId = $this->sourceType->getId();
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
        $this->sourceType = SourceTypeProvider::create($this->sourceTypeId);
    }

    public function getSourceTypeId()
    {
        return $this->sourceTypeId;
    }

    public function setCategory(Category $category = null)
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

    public function toArray()
    {
        $this->generateHash();

        return array(
            'city_id' => $this->city ? $this->city->getId() : null,
            'user_id' => $this->user ? $this->user->getId() : null,
            'announcement_id' => $this->announcement->getId(),
            'action' => $this->action,
            'source_type_id' => $this->sourceTypeId,
            'ip' => $this->ip,
            'user_agent' => $this->userAgent,
            'referer' => $this->referer,
            'session_id' => $this->sessionId,
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
            'date_created_at' => $this->createdAt->format('Y-m-d'),
            'item_hash' => $this->itemHash,
            'fake' => $this->fake,
            'category_id' => $this->category ? $this->category->getId() : null);
    }
}
