<?php

namespace Metal\ProjectBundle\Widget;

use Brouzie\Bundle\WidgetsBundle\Widget\WidgetAbstract;
use Brouzie\WidgetsBundle\Widget\ConditionallyRenderedWidget;
use Metal\TerritorialBundle\Entity\City;
use Metal\CategoriesBundle\Entity\Category;
use Metal\ProductsBundle\Entity\Product;

class OneSignalWidget extends WidgetAbstract implements ConditionallyRenderedWidget
{
    public function setDefaultOptions()
    {
        parent::setDefaultOptions();

        $this->optionsResolver
            ->setDefined(['currentCity', 'category', 'product'])
            ->setAllowedTypes('currentCity', City::class);
    }

    public function shouldBeRendered()
    {
        /** @var City $currentCity */
        $currentCity = $this->options['currentCity'];

        return $currentCity->getOnesignalCode();
    }

    public function getParametersToRender()
    {
        $product = $this->options['product'];
        /* @var $product Product */

        $category = $product ? $product->getCategory() : $this->options['category'];
        /* @var $category Category */

        $currentCity = $this->options['currentCity'];
        /* @var $currentCity City */

        return array(
            'currentCategory' => $category,
            'currentCity' => $currentCity
        );
    }
}