<?php

namespace Metal\CategoriesBundle\Helper;

use Brouzie\Bundle\HelpersBundle\Helper\HelperAbstract;
use Metal\CategoriesBundle\Entity\Parameter;
use Metal\TerritorialBundle\Entity\City;
use Metal\TerritorialBundle\Entity\Country;

class ParameterHelper extends HelperAbstract
{
    const ANCHOR_NAME = 0;
    const ANCHOR_NAME_WITH_REGION = 1;
    const ANCHOR_NAME_WITH_PRICE_WORD = 2;

    const LIMIT_VAL_FOR_4 = 1;
    const LIMIT_VAL_FOR_2 = 3;
    const LIMIT_VAL_FOR_1 = 5;
    const TOTAL_LIMIT = 6;

    public function getTitleForParameterFriend(Parameter $parameter, $mode, City $city = null, Country $country)
    {
        //TODO: можно адаптировать под области и совместить с логикой, которая в шаблоне для ссылок без параметров

        $category = $parameter->getParameterGroup()->getCategory();
        $parameterOption = $parameter->getParameterOption();
        if ($mode == self::ANCHOR_NAME_WITH_PRICE_WORD) {
            $parameterTitle = $parameterOption->getTitleAccusative() ?: $parameterOption->getTitle();
            $title = 'Цена на '.mb_strtolower($category->getTitleAccusative()).' '.$parameterTitle;
        } else {
            $title = $category->getTitle().' '.$parameterOption->getTitle();
            if ($mode == self::ANCHOR_NAME_WITH_REGION) {
                $title .= ' '.($city ? $city->getTitle() : $country->getTitle());
            } elseif ($mode > 2) {
                $title .= ' в '.($city ? $city->getTitleLocative() : $country->getTitleLocative());
            }
        }

        return $title;
    }
}
