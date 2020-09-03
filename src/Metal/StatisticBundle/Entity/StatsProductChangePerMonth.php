<?php

namespace Metal\StatisticBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(readOnly=true)
 * @ORM\Table(name="stats_product_change_per_month",
 *   uniqueConstraints={@ORM\UniqueConstraint(name="UNIQ_dates", columns={"date"})}
 * )
 */
class StatsProductChangePerMonth
{
    /**
     * @ORM\Id
     * @ORM\Column(type="date", name="date", nullable=false)
     *
     * @var \DateTime
     */
    protected $date;

    /** @ORM\Column(type="integer", name="count", nullable=false, options={"default":1}) */
    protected $count;

    public function __construct()
    {
        $this->date = new \DateTime('first day of this month');
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     */
    public function setDate(\DateTime $date)
    {
        $this->date = $date;
    }

    public function getCount()
    {
        return $this->count;
    }

    public function setCount($count)
    {
        $this->count = (int)$count;
    }
}
