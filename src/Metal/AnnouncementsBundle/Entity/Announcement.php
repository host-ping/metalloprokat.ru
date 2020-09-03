<?php

namespace Metal\AnnouncementsBundle\Entity;

use Behat\Transliterator\Transliterator;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Metal\CompaniesBundle\Entity\Company;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @ORM\Entity(repositoryClass="Metal\AnnouncementsBundle\Repository\AnnouncementRepository")
 * @ORM\Table(name="announcement")
 * @Assert\Expression(
 *   "this.getStartsAt() <= this.getEndsAt()",
 *   message="Дата старта не может быть больше даты окончания"
 * )
 */
class Announcement
{
    const SUBDIR = 'announcements';

    const MAX_ANNOUNCEMENT_COUNT_BY_RANDOM = 5;

    const MAX_ANNOUNCEMENT_COUNT_BY_COMPANY = 5;

    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;

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
     * @Assert\NotBlank(groups={"new_item"})
     * @Assert\File(mimeTypes={"image/*", "text/html", "application/x-shockwave-flash", "application/zip"})
     *
     * @var UploadedFile
     */
    public $photo;

    /**
     * @ORM\Column(name="starts_at", type="date")
     *
     * @var \DateTime
     */
    protected $startsAt;

    /**
     * @ORM\Column(name="ends_at", type="date")
     *
     * @var \DateTime
     */
    protected $endsAt;

    /**
     * @ORM\Column(type="boolean", name="is_payed")
     */
    protected $isPayed;

    /**
     * @Assert\NotBlank()
     * @Assert\Url(protocols={"http", "https"})
     * @ORM\Column
     */
    protected $link;

    /**
     * @ORM\Column(name="mime_type")
     */
    protected $mimeType;

    /**
     * @ORM\Column(name="file_size", type="integer", nullable=true)
     */
    protected $fileSize;

    /**
     * @ORM\Column(name="file_path")
     */
    protected $filePath;

    /**
     * @ORM\Column(name="file_original_name")
     */
    protected $fileOriginalName;

    /**
     * @ORM\Column(name="fallback_file_size", type="integer", nullable=true)
     */
    protected $fallbackFileSize;

    /**
     * @ORM\Column(name="fallback_file_path", nullable=true)
     */
    protected $fallbackFilePath;

    /**
     * @ORM\Column(name="fallback_file_original_name", nullable=true)
     */
    protected $fallbackFileOriginalName;

    /**
     * @ORM\Column(name="fallback_mime_type", nullable=true)
     */
    protected $fallbackMimeType;

    /**
     * @Assert\Image()
     *
     * @var UploadedFile
     */
    public $fallbackPhoto;

    /**
     * @ORM\Column(type="integer", name="daily_views_count")
     */
    protected $dailyViewsCount;

    /**
     * @ORM\ManyToOne(targetEntity="Zone")
     * @ORM\JoinColumn(name="zone_id", referencedColumnName="id", nullable=true)
     * @Assert\NotBlank()
     *
     * @var Zone
     */
    protected $zone;

    /**
     * @ORM\OneToMany(targetEntity="AnnouncementCategory", mappedBy="announcement", cascade={"persist"}, orphanRemoval=true)
     *
     * @var ArrayCollection|AnnouncementCategory[]
     */
    protected $announcementCategories;

    /**
     * @ORM\OneToMany(targetEntity="Metal\AnnouncementsBundle\Entity\AnnouncementTerritorial", mappedBy="announcement", cascade={"persist"}, orphanRemoval=true)
     *
     * @var ArrayCollection|AnnouncementTerritorial[]
     */
    protected $announcementTerritorial;

    /**
     * @ORM\Column(type="smallint", name="version", nullable=false, options={"default":0})
     */
    protected $version;

    /**
     * @ORM\Column(type="boolean", name="resizable", nullable=false, options={"default":0})
     */
    protected $resizable;

    /**
     * @ORM\Column(type="boolean", name="show_everywhere", nullable=false, options={"default":1})
     */
    protected $showEverywhere;

    /**
     * @ORM\Column(type="boolean", name="show_everywhere_territory", nullable=false, options={"default":1})
     */
    protected $showEverywhereTerritory;

    public function __construct()
    {
        $this->startsAt = new \DateTime('+1 day');
        $this->endsAt = new \DateTime('+2 day');
        $this->dailyViewsCount = 0;
        $this->isPayed = false;
        $this->version = 0;
        $this->resizable = true;
        $this->showEverywhere = true;
        $this->showEverywhereTerritory = true;
        $this->announcementCategories = new ArrayCollection();
        $this->announcementTerritorial = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function setCompanyTitle($companyTitle)
    {
        $this->companyTitle = $companyTitle;
    }

    public function getCompanyTitle()
    {
        return $this->companyTitle;
    }

    /**
     * @param \DateTime $endsAt
     */
    public function setEndsAt(\DateTime $endsAt)
    {
        $this->endsAt = $endsAt;
    }

    /**
     * @return \DateTime
     */
    public function getEndsAt()
    {
        return $this->endsAt;
    }

    public function setCompany(Company $company = null)
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

    public function getFileOriginalName()
    {
        return $this->fileOriginalName;
    }

    public function setFileOriginalName($fileOriginalName)
    {
        $this->fileOriginalName = $fileOriginalName;
    }

    public function getFileOriginalNameSanitized()
    {
        return Transliterator::transliterate($this->fileOriginalName);
    }

    public function setFilePath($filePath)
    {
        $this->filePath = $filePath;
    }

    public function getFilePath()
    {
        return $this->filePath;
    }

    public function setFileSize($fileSize)
    {
        $this->fileSize = $fileSize;
    }

    public function getFileSize()
    {
        return $this->fileSize;
    }

    public function setIsPayed($isPayed)
    {
        $this->isPayed = $isPayed;
    }

    public function getIsPayed()
    {
        return $this->isPayed;
    }

    public function setLink($link)
    {
        $this->link = $link;
    }

    public function getLink()
    {
        return $this->link;
    }

    public function setMimeType($mimeType)
    {
        $this->mimeType = $mimeType;
    }

    public function getMimeType()
    {
        return $this->mimeType;
    }

    public function setStartsAt(\DateTime $startsAt)
    {
        $this->startsAt = $startsAt;
    }

    /**
     * @return \DateTime
     */
    public function getStartsAt()
    {
        return $this->startsAt;
    }

    public function setFallbackMimeType($fallbackMimeType)
    {
        $this->fallbackMimeType = $fallbackMimeType;
    }

    public function getFallbackMimeType()
    {
        return $this->fallbackMimeType;
    }

    public function setFallbackFileOriginalName($fallbackFileOriginalName)
    {
        $this->fallbackFileOriginalName = $fallbackFileOriginalName;
    }

    public function getFallbackFileOriginalName()
    {
        return $this->fallbackFileOriginalName;
    }

    public function setFallbackFilePath($fallbackFilePath)
    {
        $this->fallbackFilePath = $fallbackFilePath;
    }

    public function getFallbackFilePath()
    {
        return $this->fallbackFilePath;
    }

    public function setFallbackFileSize($fallbackFileSize)
    {
        $this->fallbackFileSize = $fallbackFileSize;
    }

    public function getFallbackFileSize()
    {
        return $this->fallbackFileSize;
    }

    public function populateFileData()
    {
        if (null !== $this->photo) {
            $this->mimeType = $this->photo->getMimeType();
            $this->fileSize = $this->photo->getSize();
            $this->fileOriginalName = $this->photo->getClientOriginalName();
            $this->filePath = mt_rand(0, 100).'-announcement-'.time().'.'.$this->photo->guessExtension();
        }

        if (null !== $this->fallbackPhoto) {
            $this->fallbackMimeType = $this->fallbackPhoto->getMimeType();
            $this->fallbackFileSize = $this->fallbackPhoto->getSize();
            $this->fallbackFileOriginalName = $this->fallbackPhoto->getClientOriginalName();
            $this->fallbackFilePath = mt_rand(0, 100).'-announcement-'.time().'.'.$this->fallbackPhoto->guessExtension();
        }
    }

    public function getSubDir()
    {
        return self::SUBDIR;
    }

    public function getWebPath()
    {
        return '/'.$this->getSubDir().'/'.$this->getFilePath();
    }

    public function getDevWebPath()
    {
        $fileName = $this->isHtml() ? 'empty.html' : 'one-px.gif';
        return '/'.$this->getSubDir().'/'.$fileName;
    }

    public function getEmbedWebPath()
    {
        return $this->isZip() ? '/'.$this->getSubDir().'/'.$this->getId().'/index.html' : $this->getWebPath();
    }

    public function getFallbackWebPath()
    {
        return '/'.$this->getSubDir().'/'.$this->getFallbackFilePath();
    }

    public function isFlash()
    {
        $mimeType = $this->photo ? $this->photo->getMimeType() : $this->mimeType;

        return $mimeType === 'application/x-shockwave-flash';
    }

    public function isHtml()
    {
        $mimeType = $this->photo ? $this->photo->getMimeType() : $this->mimeType;

        return $mimeType === 'text/html';
    }

    public function isZip()
    {
        $mimeType = $this->photo ? $this->photo->getMimeType() : $this->mimeType;

        return $mimeType === 'application/zip';
    }

    public function getUploadedFile()
    {
        return $this->photo;
    }

    public function getDailyViewsCount()
    {
        return $this->dailyViewsCount;
    }

    public function setDailyViewsCount($dailyViewsCount)
    {
        $this->dailyViewsCount = $dailyViewsCount;
    }

    public function isActive()
    {
        $now = new \DateTime();

        return $this->startsAt <= $now && $now <= $this->endsAt && $this->isPayed;
    }

    /**
     * @return Zone
     */
    public function getZone()
    {
        return $this->zone;
    }

    /**
     * @param Zone $zone
     */
    public function setZone(Zone $zone = null)
    {
        $this->zone = $zone;
    }

    public function getZoneTypeTitle()
    {
        if ($this->zone) {
            return $this->zone->getTitle();
        }

        return '';
    }

    public function getVersion()
    {
        return $this->version;
    }

    public function setVersion($version)
    {
        $this->version = $version;
    }

    public function getResizable()
    {
        return $this->resizable;
    }

    public function setResizable($resizable)
    {
        $this->resizable = $resizable;
    }

    public function isResizable()
    {
        return $this->getResizable();
    }

    public function getShowEverywhere()
    {
        return $this->showEverywhere;
    }

    public function setShowEverywhere($showEverywhere)
    {
        $this->showEverywhere = $showEverywhere;
    }

    public function getShowEverywhereTerritory()
    {
        return $this->showEverywhereTerritory;
    }

    public function setShowEverywhereTerritory($showEverywhereTerritory)
    {
        $this->showEverywhereTerritory = $showEverywhereTerritory;
    }

    public function toArray($bannerId = null)
    {
        //$this->generateHash();
        $fallbackCompanyId = 1; // металопрокат.ру
        return array(
            'id' => $this->id,
            'company_id' => $this->company ? $this->company->getId() : $fallbackCompanyId,
            'starts_at' => $this->startsAt->format('Y-m-d H:i:s'),
            'ends_at' => $this->endsAt->format('Y-m-d H:i:s'),
            'is_payed' => $this->isPayed,
            'link' => $this->link,
            'mime_type' => $this->mimeType,
            'file_size' => $this->fileSize,
            'file_path' => $this->filePath,
            'file_original_name' => $this->fileOriginalName,
            'daily_views_count' => $this->dailyViewsCount,
            'fallback_file_size' => $this->fallbackFileSize,
            'fallback_file_path' => $this->fallbackFilePath,
            'fallback_file_original_name' => $this->fallbackFileOriginalName,
            'fallback_mime_type' => $this->fallbackMimeType,
            'old_search_banner_id' => $bannerId ?: null,
            'company_title' => $this->companyTitle ?: null,
            'show_everywhere' => $this->showEverywhere,
            'show_everywhere_territory' => $this->showEverywhereTerritory,
        );
    }

    public function getAnnouncementCategories()
    {
        return $this->announcementCategories;
    }

    public function addAnnouncementCategory(AnnouncementCategory $announcementCategory)
    {
        $announcementCategory->setAnnouncement($this);
        $this->announcementCategories->add($announcementCategory);
    }

    public function removeAnnouncementCategory(AnnouncementCategory $announcementCategory)
    {
        $this->announcementCategories->removeElement($announcementCategory);
    }

    public function getAnnouncementTerritorial()
    {
        return $this->announcementTerritorial;
    }

    public function addAnnouncementTerritorial(AnnouncementTerritorial $announcementTerritorial)
    {
        $announcementTerritorial->setAnnouncement($this);
        $this->announcementTerritorial->add($announcementTerritorial);
    }

    public function removeAnnouncementTerritorial(AnnouncementTerritorial $announcementTerritorial)
    {
        $this->announcementTerritorial->removeElement($announcementTerritorial);
    }

    /**
     * @Assert\Callback()
     */
    public function validate(ExecutionContextInterface $context)
    {
        if (!$this->getZone()) {
            return;
        }

        $zoneCode = $this->getZone()->getSlug();

        if ($this->isFlash() && 0 === strpos($zoneCode, 'email-')) {
            $context
                ->buildViolation('Нельзя использовать флеш для почтовых рассылок.')
                ->atPath('photo')
                ->addViolation();
        }
        if ($this->isFlash() && 0 === strpos($zoneCode, 'background')) {
            $context
                ->buildViolation('Нельзя использовать флеш для фоновых баннеров')
                ->atPath('photo')
                ->addViolation();
        }
    }
}
