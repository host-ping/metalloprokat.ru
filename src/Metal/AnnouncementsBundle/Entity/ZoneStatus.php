<?php

namespace Metal\AnnouncementsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Metal\CompaniesBundle\Entity\Company;
use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\ORM\Mapping\Index;
/**
 * @ORM\Entity(repositoryClass="Metal\AnnouncementsBundle\Repository\ZoneStatusRepository")
 * @ORM\Table(name="announcement_zone_status", indexes={@Index(name="IDX_deleted", columns={"deleted"})})
 */
class ZoneStatus
{
    const STATUS_PURCHASED = 1;
    const STATUS_RESERVED = 2;
    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\Column(length=255, name="comment", nullable=true)
     */
    protected $comment;

    /**
     * @ORM\Column(type="smallint", name="status", nullable=false)
     */
    protected $status;

    /** @ORM\Column(type="date", name="starts_at", nullable=false) */
    protected $startsAt;

    /** @ORM\Column(type="date", name="ends_at", nullable=false) */
    protected $endsAt;

    /**
     * @ORM\ManyToOne(targetEntity="Zone")
     * @ORM\JoinColumn(name="zone_id", referencedColumnName="id", nullable=false)
     * @Assert\NotBlank()
     *
     * @var Zone
     */
    protected $zone;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\CompaniesBundle\Entity\Company")
     * @ORM\JoinColumn(name="company_id", referencedColumnName="Message_ID", nullable=true)
     * @Assert\NotBlank()
     *
     * @var Company
     */
    protected $company;

    /**
     * @ORM\OneToOne(targetEntity="Announcement")
     * @ORM\JoinColumn(name="announcement_id", referencedColumnName="id", nullable=true)
     *
     * @var Announcement
     */
    protected $announcement;

    /**
     * @ORM\Column(type="boolean", name="deleted", nullable=false, options={"default":0})
     */
    protected $deleted;

    public function __construct()
    {
        $this->status = self::STATUS_PURCHASED;
        $this->startsAt = new \DateTime('+1 day');
        $this->endsAt = new \DateTime('+1 month + 1 day');
        $this->deleted = false;
    }

    public static function getStatuses()
    {
        return array(self::STATUS_PURCHASED => 'Выкуплен', self::STATUS_RESERVED => 'Зарезервирован');
    }

    public function getId()
    {
        return $this->id;
    }

    public function getComment()
    {
        return $this->comment;
    }

    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    /**
     * @return \DateTime
     */
    public function getEndsAt()
    {
        return $this->endsAt;
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
    public function getStartsAt()
    {
        return $this->startsAt;
    }

    /**
     * @param \DateTime $startsAt
     */
    public function setStartsAt(\DateTime $startsAt)
    {
        $this->startsAt = $startsAt;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status)
    {
        $this->status = $status;
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
    public function setZone(Zone $zone)
    {
        $this->zone = $zone;
    }

    /**
     * @return Company
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @param Company $company
     */
    public function setCompany(Company $company)
    {
        $this->company = $company;
    }

    /**
     * @return Announcement
     */
    public function getAnnouncement()
    {
        return $this->announcement;
    }

    /**
     * @param Announcement $announcement
     */
    public function setAnnouncement(Announcement $announcement = null)
    {
        $this->announcement = $announcement;
    }

    public function getDeleted()
    {
        return $this->deleted;
    }

    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;
    }
}
