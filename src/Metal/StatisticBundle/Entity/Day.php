<?php

namespace Metal\StatisticBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(readOnly=true)
 * @ORM\Table(name="stats_day")
 */
class Day
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="id")
     */
    protected $id;

    /**
     * @ORM\Column(type="date", name="date", unique=true)
     *
     * @var \DateTime
     */
    protected $date;

    /**
     * @ORM\Column(name="year__month", type="smallint", nullable=false, options={"default":0})
     */
    protected $yearMonth;

    /**
     * @ORM\Column(name="year__week", type="smallint", nullable=false, options={"default":0})
     */
    protected $yearWeek;

    final private function __construct()
    {
        //
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }
}
