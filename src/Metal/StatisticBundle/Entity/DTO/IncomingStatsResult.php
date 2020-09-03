<?php

namespace Metal\StatisticBundle\Entity\DTO;

/**
 * DTO
 */
class IncomingStatsResult extends IncomingStatsResultAbstract
{
    /**
     * @var \DateTime
     */
    public $date;

    public function getFields()
    {
        return array_merge(
            array(
                'date' => array(
                    'cast' => 'datetime',
                    'groupFunction' => 'MIN',
                    'readFrom' => 'd.date'
                )
            ),
            parent::getFields()
        );
    }

    public function getDefaultGrouping()
    {
        return 'day';
    }
}
