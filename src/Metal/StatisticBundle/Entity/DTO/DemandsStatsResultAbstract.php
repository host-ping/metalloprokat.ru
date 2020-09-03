<?php

namespace Metal\StatisticBundle\Entity\DTO;

class DemandsStatsResultAbstract extends StatsResultAbstract
{
    public $demandsViewsCount;
    public $demandsToFavoriteCount;
    public $demandsAnswersCount;

    public function getFields()
    {
        return array(
            'demandsViewsCount' => array('cast' => 'int'),
            'demandsToFavoriteCount' => array('cast' => 'int'),
            'demandsAnswersCount' => array('cast' => 'int')
        );
    }
}
