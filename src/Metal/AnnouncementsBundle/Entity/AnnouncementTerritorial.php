<?php

namespace Metal\AnnouncementsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Metal\TerritorialBundle\Entity\City;
use Metal\TerritorialBundle\Entity\Country;
use Metal\TerritorialBundle\Entity\Region;

/**
 * @ORM\Entity()
 * @ORM\Table(
 *      name="announcement_territorial",
 *      uniqueConstraints={
 *          @ORM\UniqueConstraint(name="UNIQ_announcement_county", columns={"country_id", "announcement_id",}),
 *          @ORM\UniqueConstraint(name="UNIQ_announcement_region", columns={"region_id", "announcement_id"}),
 *          @ORM\UniqueConstraint(name="UNIQ_announcement_city", columns={"city_id", "announcement_id"})
 *      },
 *      indexes={
 *          @ORM\Index(name="IDX_announcement_id", columns={"announcement_id"})
 *      }
 * )
 */
class AnnouncementTerritorial
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\AnnouncementsBundle\Entity\Announcement", inversedBy="announcementTerritorial")
     * @ORM\JoinColumn(name="announcement_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     *
     * @var Announcement
     */
    protected $announcement;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\TerritorialBundle\Entity\Country")
     * @ORM\JoinColumn(name="country_id", referencedColumnName="Country_ID", nullable=true, onDelete="CASCADE")
     *
     * @var Country
     */
    protected $country;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\TerritorialBundle\Entity\Region")
     * @ORM\JoinColumn(name="region_id", referencedColumnName="Regions_ID", nullable=true, onDelete="CASCADE")
     *
     * @var Region
     */
    protected $region;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\TerritorialBundle\Entity\City")
     * @ORM\JoinColumn(name="city_id", referencedColumnName="Region_ID", nullable=true, onDelete="CASCADE")
     *
     * @var City
     */
    protected $city;

    /**
     * @return Integer
     */
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
     * @param Announcement $announcement
     */
    public function setAnnouncement(Announcement $announcement)
    {
        $this->announcement = $announcement;
    }

    /**
     * @return Country
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param Country $country
     */
    public function setCountry(Country $country)
    {
        $this->country = $country;
    }

    /**
     * @return Region
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * @param Region $region
     */
    public function setRegion(Region $region)
    {
        $this->region = $region;
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

    /**
     * @return String
     */
    public function getCityTitle()
    {
        return $this->city ? $this->city->getTitle() : '';
    }

    public function setCityTitle($cityTitle)
    {
        // dummy
    }
}