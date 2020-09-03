<?php

namespace Metal\CategoriesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use Metal\TerritorialBundle\Entity\Country;

/**
 * @ORM\Entity()
 * @ORM\Table(name="landing_page_country_count",
 *  uniqueConstraints={@ORM\UniqueConstraint(name="UNIQ_landing_page_country", columns={"landing_page_id", "country_id"} )}
 * )
 */
class LandingPageCountryCount
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="id")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="LandingPage", inversedBy="landingPageCountryCounts")
     * @ORM\JoinColumn(name="landing_page_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * @Assert\NotBlank()
     *
     * @var LandingPage
     */
    protected $landingPage;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\TerritorialBundle\Entity\Country")
     * @ORM\JoinColumn(name="country_id", referencedColumnName="Country_ID", onDelete="SET NULL")
     *
     * @var Country
     */
    protected $country;

    /**
     * Кол-во найденых результатов по поисковому запросу в конкретной стране
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
    public function setLandingPage($landingPage)
    {
        $this->landingPage = $landingPage;
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

    public function getResultsCount()
    {
        return $this->resultsCount;
    }

    public function setResultsCount($resultsCount)
    {
        $this->resultsCount = $resultsCount;
    }
}
