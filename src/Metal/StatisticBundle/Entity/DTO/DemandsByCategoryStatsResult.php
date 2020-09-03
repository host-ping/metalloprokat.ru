<?php

namespace Metal\StatisticBundle\Entity\DTO;

/**
 * DTO
 */
class DemandsByCategoryStatsResult extends DemandsStatsResultAbstract
{
    public $categoryTitle;

    public function getFields()
    {
        return array_merge(
            array(
                'categoryTitle' => array(
                    'cast' => 'string',
                    'groupFunction' => false,
                    'readFrom' => 'category.title',
                )
            ),
            parent::getFields()
        );
    }

    public function getDefaultOrderDirection($order)
    {
        return 'categoryTitle' === $order ? 'asc' : parent::getDefaultOrderDirection($order);
    }
}
