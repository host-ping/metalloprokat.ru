<?php

namespace Metal\MiniSiteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Metal\CompaniesBundle\Entity\Company;
use Metal\ProjectBundle\Entity\Behavior\Updateable;

/**
 * @ORM\Entity()
 * @ORM\Table(name="company_minisite")
 */
class MiniSiteConfig
{
    const DEFAULT_BACKGROUND_COLOR = "#d3e7f5";
    const DEFAULT_PRIMARY_COLOR = "#18a3d1";
    const DEFAULT_SECONDARY_COLOR = "#c7902c";

    /**
     * @ORM\Id
     * @ORM\OneToOne(targetEntity="Metal\CompaniesBundle\Entity\Company")
     * @ORM\JoinColumn(name="company_id", referencedColumnName="Message_ID", nullable=false)
     *
     * @var Company
     */
    protected $company;

    /**
     * @ORM\Column(name="background_color", length = 7)
     */
    protected $backgroundColor;

    /**
     * @ORM\Column(name="primary_color", length = 7)
     */
    protected $primaryColor;

    /**
     * @ORM\Column(name="secondary_color", length = 7)
     */
    protected $secondaryColor;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\MiniSiteBundle\Entity\MiniSiteCover")
     * @ORM\JoinColumn(name="cover_id", referencedColumnName="id", nullable=true)
     */
    protected $cover;

    /**
     * @ORM\Column(name="google_analytics_id", length=255, nullable=true)
     */
    protected $googleAnalyticsId;

    /**
     * @ORM\Column(name="yandex_metrika_id", length=255, nullable=true)
     */
    protected $yandexMetrikaId;

    /**
     * @ORM\Column(name="has_custom_category", type="boolean", nullable=true)
     */
    protected $hasCustomCategory;

    use Updateable;

    public function __construct()
    {
        $this->backgroundColor = self::DEFAULT_BACKGROUND_COLOR;
        $this->primaryColor = self::DEFAULT_PRIMARY_COLOR;
        $this->secondaryColor = self::DEFAULT_SECONDARY_COLOR;
        $this->updatedAt = new \DateTime();
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

    public function setBackgroundColor($backgroundColor)
    {
        $this->backgroundColor = $backgroundColor;
    }

    public function getBackgroundColor()
    {
        return $this->backgroundColor;
    }

    public function setPrimaryColor($primaryColor)
    {
        $this->primaryColor = $primaryColor;
    }

    public function getPrimaryColor()
    {
        return $this->primaryColor;
    }

    public function setSecondaryColor($secondaryColor)
    {
        $this->secondaryColor = $secondaryColor;
    }

    public function getSecondaryColor()
    {
        return $this->secondaryColor;
    }

    /**
     * @param MiniSiteCover $cover
     */
    public function setCover(MiniSiteCover $cover)
    {
        $this->cover = $cover;
    }

    /**
     * @return MiniSiteCover
     */
    public function getCover()
    {
        return $this->cover;
    }

    public function getGoogleAnalyticsId()
    {
        return $this->googleAnalyticsId;
    }

    public function setGoogleAnalyticsId($googleAnalyticsId)
    {
        $this->googleAnalyticsId = $googleAnalyticsId;
    }

    public function getYandexMetrikaId()
    {
        return $this->yandexMetrikaId;
    }

    public function setYandexMetrikaId($yandexMetrikaId)
    {
        $this->yandexMetrikaId = $yandexMetrikaId;
    }

    public function getHasCustomCategory()
    {
        return $this->hasCustomCategory;
    }

    public function setHasCustomCategory($hasCustomCategory)
    {
        $this->hasCustomCategory = $hasCustomCategory;
    }
}
