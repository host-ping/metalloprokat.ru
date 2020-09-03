<?php


namespace Metal\StatisticBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Metal\AnnouncementsBundle\Entity\Announcement;

/**
 * @ORM\Entity(repositoryClass="Metal\StatisticBundle\Repository\StatsAnnouncementRepository", readOnly=true)
 * @ORM\Table(name="stats_announcement", uniqueConstraints={
 * @ORM\UniqueConstraint(name="UNIQ_date_announcement", columns={"date", "announcement_id"})
 * })
 */
class StatsAnnouncementDaily
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="id", type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(name="date", type="date")
     *
     * @var \DateTime
     */
    protected $date;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\AnnouncementsBundle\Entity\Announcement")
     * @ORM\JoinColumn(name="announcement_id", referencedColumnName="id", onDelete="CASCADE")
     *
     * @var Announcement
     */
    protected $announcement;

    /**
     * @ORM\Column(name="redirects_count", type="smallint", nullable=false, options={"default":0})
     */
    protected $redirectsCount;

    /**
     * @ORM\Column(name="displays_count", type="smallint", nullable=false, options={"default":0})
     */
    protected $displaysCount;

    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Announcement
     */
    public function getAnnouncement()
    {
        return $this->announcement;
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    public function getDisplaysCount()
    {
        return $this->displaysCount;
    }


    public function getRedirectsCount()
    {
        return $this->redirectsCount;
    }
}
