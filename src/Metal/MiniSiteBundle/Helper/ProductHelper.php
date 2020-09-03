<?php

namespace Metal\MiniSiteBundle\Helper;

use Brouzie\Bundle\HelpersBundle\Helper\HelperAbstract;

class ProductHelper extends HelperAbstract
{
    //TODO: переместить этот метод хелпера в DefaultHelper ProductsBundle или куда-то еще.
    // Как вариант - вообще не создавать отдельный метод а попробовать использовать SeoHelper::implodeParametersGroups
    public function getOptionsByProduct($productsParameters)
    {
        $productOptions = '';
        if ($productsParameters) {
            $productOptions = array();
            foreach ($productsParameters as $option) {
                $productOptions[] = $option['parameterOption']['title'];
            }
            $productOptions = implode(', ', $productOptions);
        }

        return $productOptions;
    }
}
