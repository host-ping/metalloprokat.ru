<?php

namespace Metal\AnnouncementsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Metal\AnnouncementsBundle\Entity\ValueObject\Section;
use Metal\AnnouncementsBundle\Entity\ValueObject\SectionProvider;

use Metal\ProjectBundle\Entity\Behavior\Attributable;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="announcement_zone")
 * @ORM\HasLifecycleCallbacks
 */
class Zone
{
    const STATUS_FREE = 0;
    const STATUS_RESERVED = 1;
    const STATUS_SOLD = 2;

    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\Column(length=100, name="title")
     */
    protected $title;

    /**
     * @ORM\Column(type="integer", name="number", nullable=false)
     */
    protected $number;

    /**
     * @ORM\Column(length=50, name="slug", nullable=true, unique=true)
     */
    protected $slug;

    /**
     * @ORM\Column(type="integer", name="priority", nullable=false)
     */
    protected $priority;

    /**
     * @ORM\Column(type="integer", name="width")
     */
    protected $width;

    /**
     * @ORM\Column(type="integer", name="height")
     */
    protected $height;

    /**
     * @ORM\Column(type="integer", name="cost")
     */
    protected $cost;

    /**
     * @ORM\Column(type="integer", name="section_id")
     * @Assert\NotBlank()
     */
    protected $sectionId;

    /**
     * @var Section
     */
    protected $section;

    /**
     * @ORM\Column(type="boolean", name="always_available", nullable=false, options={"default":0})
     */
    protected $alwaysAvailable;

    /**
     * @ORM\Column(type="boolean", name="is_hidden", nullable=false, options={"default":0})
     * @Assert\NotNull()
     */
    protected $isHidden;

    use Attributable;

    public function __construct()
    {
        $this->isHidden = false;
        $this->alwaysAvailable = false;
        $this->priority = 0;
        $this->number = 0;
    }

    /**
     * @ORM\PostLoad
     */
    public function postLoad()
    {
        $this->section = null;
        if ($this->sectionId) {
            $this->section = SectionProvider::create($this->sectionId);
        }
    }

    public function getTitleAndSize()
    {
        return sprintf('%s (%sx%s)', $this->title, $this->width, $this->height);
    }

    public function getId()
    {
        return $this->id;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getNumber()
    {
        return $this->number;
    }

    public function setNumber($number)
    {
        $this->number = $number;
    }

    public function getSlug()
    {
        return $this->slug;
    }

    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    public function getWidth()
    {
        return $this->width;
    }

    public function setWidth($width)
    {
        $this->width = $width;
    }

    public function getHeight()
    {
        return $this->height;
    }

    public function setHeight($height)
    {
        $this->height = $height;
    }

    public function getCost()
    {
        return $this->cost;
    }

    public function setCost($cost)
    {
        $this->cost = $cost;
    }

    public function getIsHidden()
    {
        return $this->isHidden;
    }

    public function setIsHidden($isHidden)
    {
        $this->isHidden = (bool)$isHidden;
    }

    /**
     * @return Section
     */
    public function getSection()
    {
        return $this->section;
    }

    /**
     * @param Section $section
     */
    public function setSection(Section $section)
    {
        $this->section = $section;
        $this->sectionId = $section->getId();
    }

    public function getSectionTitle()
    {
        if ($this->sectionId) {
            return $this->section->getTitle();
        }

        return '';
    }

    public function getSectionId()
    {
        return $this->sectionId;
    }

    public function setSectionId($sectionId)
    {
        $this->sectionId = $sectionId;
        $this->postLoad();
    }

    public function getAlwaysAvailable()
    {
        return $this->alwaysAvailable;
    }

    public function setAlwaysAvailable($alwaysAvailable)
    {
        $this->alwaysAvailable = $alwaysAvailable;
    }

    public function getPriority()
    {
        return $this->priority;
    }

    public function setPriority($priority)
    {
        $this->priority = $priority;
    }
}
