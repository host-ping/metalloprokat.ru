<?php

namespace Metal\TerritorialBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="Classificator_FO")
 */
class FederalDistrict
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="FO_ID")
     */
    protected $id;

    /** @ORM\Column(length=255, name="FO_Name") */
    protected $title;

    /** @ORM\Column(type="boolean", name="Checked") */
    protected $isChecked;

    /**
     * @ORM\ManyToOne(targetEntity="Country")
     * @ORM\JoinColumn(name="country_id", referencedColumnName="Country_ID", nullable=false)
     *
     * @var Country
     */
    protected $country;

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

    public function isChecked()
    {
        return $this->isChecked;
    }

    public function setIsChecked($isChecked)
    {
        $this->isChecked = $isChecked;
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

}
