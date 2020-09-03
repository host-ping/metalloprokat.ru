<?php

namespace Metal\ProjectBundle\Widget;

use Brouzie\Bundle\WidgetsBundle\Widget\WidgetAbstract;

class ShowTextWidget extends WidgetAbstract
{
    public function setDefaultOptions()
    {
        parent::setDefaultOptions();

        $this->optionsResolver
            ->setRequired(
                array(
                    'popup_name',
                    'popup_text'
                )
            );
    }
}
