<?php

namespace Metal\CorpsiteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="Message177")
 * @ORM\HasLifecycleCallbacks
 */
class Client
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="Message_ID")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\Column(name="Name", nullable=true)
     */
    protected $title;

    /**
     * @ORM\Column(type="datetime", name="Created")
     */
    protected $createdAt;

    /**
     * @ORM\Column(type="datetime", name="LastUpdated")
     */
    protected $updatedAt;

    /**
     * @Assert\File(mimeTypes={"image/*", "application/x-shockwave-flash"})
     *
     * @var UploadedFile
     */
    public $logo;

    /**
     * @ORM\Column(name="Logo", nullable=true)
     */
    protected $logoInfo;

    /**
     * @ORM\Column(name="URL", nullable=true)
     *
     * @Assert\Url(protocols={"http", "https"})
     */
    protected $link;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    /**
     * @ORM\PreFlush()
     */
    public function populateFileData()
    {
        if (null === $this->logo) {
            return;
        }

        $this->logo = $this->logo->getClientOriginalName().':'.$this->logo->getMimeType().':'.$this->logo->getSize();
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getLink()
    {
        return $this->link;
    }

    public function getLogoInfo()
    {
        return $this->logoInfo;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    public function setLogoInfo($logoInfo)
    {
        $this->logoInfo = $logoInfo;
    }

    public function setLink($link)
    {
        $this->link = $link;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }
}