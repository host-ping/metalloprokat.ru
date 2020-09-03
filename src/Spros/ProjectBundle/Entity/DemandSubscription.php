<?php


namespace Spros\ProjectBundle\Entity;

use Metal\ProjectBundle\Util\RandomGenerator;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

use Metal\CategoriesBundle\Entity\Category;
use Metal\TerritorialBundle\Entity\City;

/**
 * @ORM\Entity(repositoryClass="Spros\ProjectBundle\Repository\DemandSubscriptionRepository")
 * @ORM\Table(name="metalspros_demand_subscription")
 */
class DemandSubscription
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="id")
     */
    protected $id;

    /**
     * @ORM\Column(length=255, name="email")
     * @Assert\Email(strict=true)
     * @Assert\NotBlank()
     */
    protected $email;

    /** @ORM\Column(type="datetime", name="created_at") */
    protected $createdAt;

    /** @ORM\Column(length=255, name="referer", nullable=true) */
    protected $referer;

    /** @ORM\Column(length=25, name="ip", nullable=true) */
    protected $ip;

    /** @ORM\Column(length=255, name="user_agent", nullable=true) */
    protected $userAgent;

    /** @ORM\Column(length=30, name="confirmation_code", nullable=false, unique=true) */
    protected $confirmationCode;

    /** @ORM\Column(type="datetime", name="confirmed_at", nullable=true) */
    protected $confirmedAt;

    /** @ORM\Column(type="datetime", name="unsubscribed_at", nullable=true) */
    protected $unsubscribedAt;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\CategoriesBundle\Entity\Category")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="Message_ID", nullable=true)
     *
     * @var Category
     */
    protected $category;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\TerritorialBundle\Entity\City")
     * @ORM\JoinColumn(name="city_id", referencedColumnName="Region_ID", nullable=true)
     *
     * @var City
     */
    protected $city;

    public $cityTitle;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->confirmationCode = RandomGenerator::generateRandomCode();
    }

    public function getId()
    {
        return $this->id;
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

    /**
     * @param Category|null $category
     */
    public function setCategory(Category $category = null)
    {
        $this->category = $category;
    }

    /**
     * @return Category|null
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param City|null $city
     */
    public function setCity(City $city = null)
    {
        $this->city = $city;
    }

    /**
     * @return City|null
     */
    public function getCity()
    {
        return $this->city;
    }

    public function setConfirmationCode($confirmationCode)
    {
        $this->confirmationCode = $confirmationCode;
    }

    public function getConfirmationCode()
    {
        return $this->confirmationCode;
    }

    public function setConfirmedAt($confirmedAt)
    {
        $this->confirmedAt = $confirmedAt;
    }

    public function getConfirmedAt()
    {
        return $this->confirmedAt;
    }

    public function setReferer($referer)
    {
        $this->referer = $referer;
    }

    public function getReferer()
    {
        return $this->referer;
    }

    public function setUserAgent($userAgent)
    {
        $this->userAgent = $userAgent;
    }

    public function getUserAgent()
    {
        return $this->userAgent;
    }

    public function setIp($ip)
    {
        $this->ip = $ip;
    }

    public function getIp()
    {
        return $this->ip;
    }

    public function setConfirmed($isConfirmed = true)
    {
        $this->confirmedAt = $isConfirmed ? new \DateTime() : null;
    }

    public function isConfirmed()
    {
        return $this->confirmedAt !== null;
    }

    public function setUnsubscribedAt($unsubscribedAt)
    {
        $this->unsubscribedAt = $unsubscribedAt;
    }

    public function getUnsubscribedAt()
    {
        return $this->unsubscribedAt;
    }

    public function setUnsubscribed($isUnsubscribed = true)
    {
        $this->unsubscribedAt = $isUnsubscribed ? new \DateTime() : null;
    }

    public function isUnsubscribed()
    {
        return $this->unsubscribedAt !== null;
    }
}
