<?php

namespace Metal\StatisticBundle\Entity\DTO;

/**
 * DTO
 */
class AnnouncementStatsResult extends StatsResultAbstract
{
    /**
     * @var \DateTime
     */
    public $date;
    public $redirectsCount;
    public $displaysCount;
    public $ctr;

    public function getFields()
    {
        return array(
            'date' => array(
                'cast' => 'datetime',
                'groupFunction' => false,
                'readFrom' => 'd.date',
            ),
            'redirectsCount' => array('cast' => 'int'),
            'displaysCount' => array('cast' => 'int'),
            'ctr' => array(
                'cast' => 'string',
                'readFrom' => 'stats.redirectsCount / stats.displaysCount * 100',
                //FIXME: check ctr counting for grouping
                'groupFunction' => 'AVG',
            ),
        );
    }

    public function getDefaultGrouping()
    {
        return 'day';
    }
}
