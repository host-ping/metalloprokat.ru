<?php

namespace Metal\DemandsBundle\Widget;

use Brouzie\Bundle\WidgetsBundle\Widget\WidgetAbstract;

class DemandSubscriptionWidget extends WidgetAbstract
{
    protected function setDefaultOptions()
    {
        parent::setDefaultOptions();

        $this->optionsResolver
            ->setRequired(array('popup_id', 'route', 'popup_title', 'load_url', 'load_subscribe_url','search_title'));
    }
}