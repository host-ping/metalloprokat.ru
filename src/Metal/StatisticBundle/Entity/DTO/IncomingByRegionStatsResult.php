<?php

namespace Metal\StatisticBundle\Entity\DTO;

/**
 * DTO
 */
class IncomingByRegionStatsResult extends IncomingStatsResultAbstract
{
    public $cityTitle;

    public function getFields()
    {
        return array_merge(
            array(
                'cityTitle' => array(
                    'cast' => 'string',
                    'groupFunction' => false,
                    'readFrom' => 'city.title',
                )
            ),
            parent::getFields()
        );
    }

    public function getDefaultOrderDirection($order)
    {
        return 'cityTitle' === $order ? 'asc' : parent::getDefaultOrderDirection($order);
    }
}
