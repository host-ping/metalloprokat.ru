<?php

namespace Metal\TerritorialBundle\Widget;

use Brouzie\Bundle\WidgetsBundle\Widget\WidgetAbstract;

class CitiesWidget extends WidgetAbstract
{
    public function setDefaultOptions()
    {
        parent::setDefaultOptions();

        $this->optionsResolver
            ->setRequired(array('loading_route'))
            ->setDefaults(
                array(
                    'id' => 'cities',
                    'category' => null,
                    'category_slug' => '',
                    'route_type' => null,
                    'filter_parameters' => array(),
                    'counter_name' => '',
                    'ng_controller' => '',
                    'is_header' => false,
                    'landing_id' => null
                )
            );
    }
}
