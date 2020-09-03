<?php

namespace Metal\TerritorialBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Metal\TerritorialBundle\Repository\TerritorialStructureRepository")
 * @ORM\Table(name="territorial_structure")
 */
class TerritorialStructure
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="id")
     */
    protected $id;

    /** @ORM\Column(length=255, name="title", nullable=true) */
    protected $title;

    /**
     * @ORM\ManyToOne(targetEntity="FederalDistrict")
     * @ORM\JoinColumn(name="federal_district_id", referencedColumnName="FO_ID", nullable=true, unique=true)
     *
     * @var FederalDistrict
     */
    protected $federalDistrict;

    /**
     * @ORM\ManyToOne(targetEntity="Region")
     * @ORM\JoinColumn(name="region_id", referencedColumnName="Regions_ID", nullable=true, unique=true)
     *
     * @var Region
     */
    protected $region;

    /**
     * @ORM\ManyToOne(targetEntity="City")
     * @ORM\JoinColumn(name="city_id", referencedColumnName="Region_ID", nullable=true, unique=true)
     *
     * @var City
     */
    protected $city;

    /**
     * @ORM\ManyToOne(targetEntity="TerritorialStructure")
     * @ORM\JoinColumn(name="parent", referencedColumnName="id", nullable=true)
     *
     * @var TerritorialStructure
     */
    protected $parent;

    /** @ORM\Column(type="integer", name="parent")*/
    protected $parentId;

    /**
     * @ORM\ManyToOne(targetEntity="Country")
     * @ORM\JoinColumn(name="country_id", referencedColumnName="Country_ID", nullable=false)
     *
     * @var Country
     */
    protected $country;

    public function __construct()
    {
        $this->parentId = 0;
    }

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
    public function setCity(City $city)
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

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
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
    public function setCountry($country)
    {
        $this->country = $country;
    }

    /**
     * @return self|null
     */
    public function getParent()
    {
        if (!$this->parentId) {
            return null;
        }

        return $this->parent;
    }

    /**
     * @param TerritorialStructure $parent
     */
    public function setParent(TerritorialStructure $parent = null)
    {
        $this->parent = $parent;
    }

    /**
     * @return integer|null
     */
    public function getParentId()
    {
        return $this->parentId ? $this->parentId : null;
    }
}
