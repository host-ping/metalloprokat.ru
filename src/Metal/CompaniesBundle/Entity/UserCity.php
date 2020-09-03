<?php

namespace Metal\CompaniesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Metal\TerritorialBundle\Entity\City;
use Metal\TerritorialBundle\Entity\Country;
use Metal\TerritorialBundle\Entity\Region;
use Metal\UsersBundle\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @ORM\Entity()
 * @ORM\Table(
 *     name="user_city",
 *     uniqueConstraints={
 *          @ORM\UniqueConstraint(name="UNIQ_user_city", columns={"user_id", "city_id"}),
 *          @ORM\UniqueConstraint(name="UNIQ_user_region", columns={"user_id", "region_id"}),
 *          @ORM\UniqueConstraint(name="UNIQ_user_country", columns={"user_id", "country_id"})
 *     }
 * )
 */
class UserCity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="id")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\UsersBundle\Entity\User", inversedBy="userCities")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="User_ID", nullable=false, onDelete="CASCADE")
     *
     * @var User
     */
    protected $user;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\TerritorialBundle\Entity\City")
     * @ORM\JoinColumn(name="city_id", referencedColumnName="Region_ID", nullable=true, onDelete="CASCADE")
     *
     * @var City
     */
    protected $city;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\TerritorialBundle\Entity\Region")
     * @ORM\JoinColumn(name="region_id", referencedColumnName="Regions_ID", nullable=true, onDelete="CASCADE")
     *
     * @var Region
     */
    protected $region;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\TerritorialBundle\Entity\Country")
     * @ORM\JoinColumn(name="country_id", referencedColumnName="Country_ID", nullable=true, onDelete="CASCADE")
     *
     * @var Country
     */
    protected $country;

    /**
     * @ORM\Column(length=255, name="phone", nullable=false, options={"default": ""})
     */
    protected $phone;

    /** @ORM\Column(type="boolean", name="is_excluded", nullable=false, options={"default":0}) */
    protected $isExcluded;

    public function __construct()
    {
        $this->isExcluded = false;
        $this->phone = '';
    }

    public function getId()
    {
        return $this->id;
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
     * @return City
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param City $city
     */
    public function setCity(City $city = null)
    {
        $this->city = $city;
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
    public function setRegion(Region $region = null)
    {
        $this->region = $region;
    }

    public function getIsExcluded()
    {
        return $this->isExcluded;
    }

    public function setIsExcluded($isExcluded)
    {
        $this->isExcluded = $isExcluded;
    }

    public function getCityTitle()
    {
        if ($this->city) {
            return $this->city->getTitle();
        }

        return '';
    }

    public function setCityTitle($cityTitle)
    {
        // do nothing. Readonly
    }

    public function getRegionTitle()
    {
        if ($this->region) {
            return $this->region->getTitle();
        }

        return '';
    }

    public function setRegionTitle($cityTitle)
    {
        // do nothing. Readonly
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

    public function getPhone()
    {
        return $this->phone;
    }

    public function setPhone($phone)
    {
        $this->phone = (string)$phone;
    }

    /**
     * @Assert\Callback()
     */
    public function validate(ExecutionContextInterface $context)
    {
        if ($this->country && $this->city) {
            $context
                ->buildViolation('Нельзя одновременно указывать и страну и город.')
                ->atPath('country')
                ->addViolation();
        }

        if ($this->country && $this->region) {
            $context
                ->buildViolation('Нельзя одновременно указывать и страну и область.')
                ->atPath('country')
                ->addViolation();
        }

        if ($this->region && $this->city) {
            $context
                ->buildViolation('Нельзя одновременно указывать и область и город.')
                ->atPath('region')
                ->addViolation();
        }
    }
}
