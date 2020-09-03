<?php

namespace Metal\ProjectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Metal\ProjectBundle\Entity\ValueObject\SiteSourceType;
use Metal\ProjectBundle\Entity\ValueObject\SiteSourceTypeProvider;
use Metal\TerritorialBundle\Entity\City;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="Metal\ProjectBundle\Repository\SiteRepository")
 * @ORM\Table(name="site")
 * @UniqueEntity("hostname")
 * @ORM\HasLifecycleCallbacks
 */
class Site
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="id")
     */
    protected $id;

    /** @ORM\Column(length=255, name="hostname", unique=true) */
    protected $hostname;

    /** @ORM\Column(length=255, name="yandex_code", nullable=true) */
    protected $yandexCode;

    /** @ORM\Column(length=255, name="yandex_site_id", nullable=true) */
    protected $yandexSiteId;

    /**
     * @ORM\Column(type="datetime", name="sitemap_generated_at", nullable=true)
     *
     * @var \DateTime
     */
    protected $sitemapGeneratedAt;

    /** @ORM\Column(length=255, name="google_code", nullable=true) */
    protected $googleCode;

    /** @ORM\Column(length=255, name="google_site_id", nullable=true) */
    protected $googleSiteId;

    /** @ORM\Column(type="integer", name="source_type") */
    protected $sourceTypeId;

    /**
     * @var SiteSourceType
     */
    protected $sourceType;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\TerritorialBundle\Entity\City")
     * @ORM\JoinColumn(name="city_id", referencedColumnName="Region_ID", nullable=true)
     *
     * @var City
     */
    protected $city;

    /**
     * @ORM\PostLoad
     */
    public function postLoad()
    {
        $this->sourceType = SiteSourceTypeProvider::create($this->sourceTypeId);
    }

    public function getId()
    {
        return $this->id;
    }

    public function setHostname($hostname)
    {
        $this->hostname = $hostname;
    }

    public function getHostname()
    {
        return $this->hostname;
    }

    public function setYandexCode($yandexCode)
    {
        $this->yandexCode = $yandexCode;
    }

    public function getYandexCode()
    {
        return $this->yandexCode;
    }

    public function setYandexSiteId($yandexSiteId)
    {
        $this->yandexSiteId = $yandexSiteId;
    }

    public function getYandexSiteId()
    {
        return $this->yandexSiteId;
    }

    /**
     * @param \DateTime $sitemapGeneratedAt
     */
    public function setSitemapGeneratedAt(\DateTime $sitemapGeneratedAt)
    {
        $this->sitemapGeneratedAt = $sitemapGeneratedAt;
    }

    /**
     * @return \DateTime
     */
    public function getSitemapGeneratedAt()
    {
        return $this->sitemapGeneratedAt;
    }

    public function setGoogleCode($googleCode)
    {
        $this->googleCode = $googleCode;
    }

    public function getGoogleCode()
    {
        return $this->googleCode;
    }

    public function setGoogleSiteId($googleSiteId)
    {
        $this->googleSiteId = $googleSiteId;
    }

    public function getGoogleSiteId()
    {
        return $this->googleSiteId;
    }

    /**
     * @return SiteSourceType
     */
    public function getSourceType()
    {
        return $this->sourceType;
    }

    /**
     * @param SiteSourceType $sourceType
     */
    public function setSourceType(SiteSourceType $sourceType)
    {
        $this->sourceType = $sourceType;
        $this->sourceTypeId = $sourceType->getId();
    }

    public function getSourceTypeId()
    {
        return $this->sourceTypeId;
    }

    public function setSourceTypeId($sourceTypeId)
    {
        $this->sourceTypeId = $sourceTypeId;
        $this->postLoad();
    }

    public function getSourceTypeTitle()
    {
        return $this->sourceType->getTitle();
    }

    /**
     * @return City
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param City $city
     */
    public function setCity(City $city)
    {
        $this->city = $city;
    }
}
