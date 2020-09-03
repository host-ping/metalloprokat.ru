<?php

namespace Metal\TerritorialBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="city_code", uniqueConstraints={
 * @ORM\UniqueConstraint(name="UNIQ_city_code", columns={"city_id", "code"} )})
 */
class CityCode
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="id")
     */
    protected $id;

    /**
     * @ORM\Column(length=50, name="code", nullable=false)
     *
     * @Assert\NotBlank()
     */
    protected $code;

    /**
     * @ORM\ManyToOne(targetEntity="City")
     * @ORM\JoinColumn(name="city_id", referencedColumnName="Region_ID", nullable=true)
     *
     * @var City
     */
    protected $city;

    /**
     * @ORM\Column(length=255, name="default_city_title", nullable=true)
     */
    protected $defaultCityTitle;

    public function getId()
    {
        return $this->id;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function setCode($code)
    {
        $this->code = $code;
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

    public function getDefaultCityTitle()
    {
        return $this->defaultCityTitle;
    }

    public function setDefaultCityTitle($defaultCityTitle)
    {
        $this->defaultCityTitle = $defaultCityTitle;
    }

    public function getCityTitle()
    {
        if ($this->city) {
            return $this->city->getTitle();
        }

        return '';
    }

    public function setCityTitle($title)
    {
        // do nothing
    }
}
