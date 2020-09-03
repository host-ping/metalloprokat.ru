<?php

namespace Metal\DemandsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Metal\TerritorialBundle\Entity\City;
use Metal\TerritorialBundle\Entity\FederalDistrict;
use Metal\TerritorialBundle\Entity\Region;
use Metal\TerritorialBundle\Entity\TerritorialStructure;
use Metal\UsersBundle\Entity\User;

/**
 * @ORM\Entity(repositoryClass="Metal\DemandsBundle\Repository\DemandSubscriptionTerritorialRepository")
 * @ORM\Table(name="demand_subscription_territorial",  uniqueConstraints={
 *     @ORM\UniqueConstraint(name="UNIQ_user_territorial_structure", columns={"user_id", "territorial_structure_id"})
 * })
 */
class DemandSubscriptionTerritorial
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="id")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\UsersBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="User_ID", nullable=false)
     *
     * @var User
     */
    protected $user;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\TerritorialBundle\Entity\FederalDistrict")
     * @ORM\JoinColumn(name="federal_district_id", referencedColumnName="FO_ID", nullable=true)
     *
     * @var FederalDistrict
     */
    protected $federalDistrict;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\TerritorialBundle\Entity\Region")
     * @ORM\JoinColumn(name="region_id", referencedColumnName="Regions_ID", nullable=true)
     *
     * @var Region
     */
    protected $region;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\TerritorialBundle\Entity\City")
     * @ORM\JoinColumn(name="city_id", referencedColumnName="Region_ID", nullable=true)
     *
     * @var City
     */
    protected $city;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\TerritorialBundle\Entity\TerritorialStructure")
     * @ORM\JoinColumn(name="territorial_structure_id", referencedColumnName="id", nullable=true)
     *
     * @var TerritorialStructure
     */
    protected $territorialStructure;

    public function getId()
    {
        return $this->id;
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
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * @return FederalDistrict
     */
    public function getFederalDistrict()
    {
        return $this->federalDistrict;
    }

    /**
     * @param FederalDistrict $federalDistrict
     */
    public function setFederalDistrict(FederalDistrict $federalDistrict)
    {
        $this->federalDistrict = $federalDistrict;
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
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return TerritorialStructure
     */
    public function getTerritorialStructure()
    {
        return $this->territorialStructure;
    }

    /**
     * @param TerritorialStructure $territorialStructure
     */
    public function setTerritorialStructure(TerritorialStructure $territorialStructure)
    {
        $this->territorialStructure = $territorialStructure;
    }

    public function getTerritoryDescription()
    {
        $description = array('title' => $this->territorialStructure->getTitle());
        if ($this->city) {
            $description['cityId'] = $this->getCity()->getId();
        }
        if ($this->region) {
            $description['regionId'] = $this->getRegion()->getId();
        }
        if ($this->federalDistrict) {
             $description['federalDistrictId'] = $this->getFederalDistrict()->getId();
        }

        return $description;
    }
}
