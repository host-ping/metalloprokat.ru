<?php

namespace Metal\TerritorialBundle\Helper;

use Brouzie\Bundle\HelpersBundle\Helper\HelperAbstract;

class MapHelper extends HelperAbstract
{
    public function getYandexUrl($longitude, $latitude, $text = '', $zoom = 13)
    {

        return '//maps.yandex.ru?'. http_build_query(array('ll' => $latitude.','.$longitude, 'text' => $text, 'z' => $zoom, 'l' => 'map'));
    }
}
