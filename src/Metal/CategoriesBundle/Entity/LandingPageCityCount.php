<?php

namespace Metal\CategoriesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use Metal\TerritorialBundle\Entity\City;

/**
 * @ORM\Entity()
 * @ORM\Table(name="landing_page_city_count",
 *  uniqueConstraints={@ORM\UniqueConstraint(name="UNIQ_landing_page_city", columns={"landing_page_id", "city_id"} )}
 * )
 */
class LandingPageCityCount
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="id")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="LandingPage", inversedBy="landingPageCityCounts")
     * @ORM\JoinColumn(name="landing_page_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * @Assert\NotBlank()
     *
     * @var LandingPage
     */
    protected $landingPage;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\TerritorialBundle\Entity\City")
     * @ORM\JoinColumn(name="city_id", referencedColumnName="Region_ID", onDelete="SET NULL")
     *
     * @var City
     */
    protected $city;

    /**
     * Кол-во найденых результатов по поисковому запросу в конкретном городе
     *
     * @ORM\Column(type="integer", name="results_count", nullable=false, options={"default":0})
     */
    protected $resultsCount;

    public function getId()
    {
        return $this->id;
    }

    /**
     * @return LandingPage
     */
    public function getLandingPage()
    {
        return $this->landingPage;
    }

    /**
     * @param LandingPage $landingPage
     */
    public function setLandingPage(LandingPage $landingPage)
    {
        $this->landingPage = $landingPage;
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

    public function getResultsCount()
    {
        return $this->resultsCount;
    }

    public function setResultsCount($resultsCount)
    {
        $this->resultsCount = $resultsCount;
    }
}
