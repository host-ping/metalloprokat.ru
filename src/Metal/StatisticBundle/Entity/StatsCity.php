<?php

namespace Metal\StatisticBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Metal\TerritorialBundle\Entity\City;

/**
 * @ORM\Entity(repositoryClass="Metal\StatisticBundle\Repository\StatsCityRepository", readOnly=true)
 * @ORM\Table(name="stats_city", uniqueConstraints={
 *     @ORM\UniqueConstraint(name="UNIQ_comp_date_city", columns={"company_id", "date", "city_id"})
 * }, indexes={
 *   @ORM\Index(name="IDX_date", columns={"date"}),
 * })
 */
class StatsCity extends ClientStats
{
    /**
     * @ORM\ManyToOne(targetEntity="Metal\TerritorialBundle\Entity\City")
     * @ORM\JoinColumn(name="city_id", referencedColumnName="Region_ID")
     *
     * @var City
     */
    protected $city;

    /**
     * @return City
     */
    public function getCity()
    {
        return $this->city;
    }

}
