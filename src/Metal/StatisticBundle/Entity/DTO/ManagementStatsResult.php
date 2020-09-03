<?php

namespace Metal\StatisticBundle\Entity\DTO;

/**
 * DTO
 */
class ManagementStatsResult extends StatsResultAbstract
{
    /**
     * @var \DateTime
     */
    public $date;
    public $addedProductsCount;
    public $updatedProductsCount;
    public $usersOnSiteCount;

    public function getFields()
    {
        return array(
            'date' => array(
                'cast' => 'datetime',
                'groupFunction' => 'MIN',
                'readFrom' => 'd.date'
            ),
            'addedProductsCount' => array('cast' => 'int'),
            'updatedProductsCount' => array('cast' => 'int'),
            'usersOnSiteCount' => array('cast' => 'int'),
        );
    }

    public function getDefaultGrouping()
    {
        return 'day';
    }
}
