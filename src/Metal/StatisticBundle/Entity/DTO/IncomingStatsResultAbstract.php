<?php

namespace Metal\StatisticBundle\Entity\DTO;

class IncomingStatsResultAbstract extends StatsResultAbstract
{
    public $demandsCount;
    public $demandsProcessedCount;
    public $callbacksCount;
    public $callbacksProcessedCount;
    public $reviewsCount;
    public $complaintsCount;
    public $reviewsPhonesCount;
    public $websiteVisitsCount;
    public $reviewsProductsCount;
    public $complaintsProcessedCount;
    public $showProductsCount;

    public function getFields()
    {
        return array(
            'demandsCount' => array('cast' => 'int'),
            'demandsProcessedCount' => array('cast' => 'int'),
            'callbacksCount' => array('cast' => 'int'),
            'callbacksProcessedCount' => array('cast' => 'int'),
            'reviewsCount' => array('cast' => 'int'),
            'complaintsCount' => array('cast' => 'int'),
            'reviewsPhonesCount' => array('cast' => 'int'),
            'websiteVisitsCount' => array('cast' => 'int'),
            'reviewsProductsCount' => array('cast' => 'int'),
            'complaintsProcessedCount' => array('cast' => 'int'),
            'showProductsCount' => array('cast' => 'int'),
        );
    }
}
