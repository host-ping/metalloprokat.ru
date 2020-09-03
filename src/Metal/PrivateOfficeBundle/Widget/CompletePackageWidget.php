<?php

namespace Metal\PrivateOfficeBundle\Widget;

use Brouzie\Bundle\WidgetsBundle\Widget\WidgetAbstract;

class CompletePackageWidget extends WidgetAbstract
{
    public function setDefaultOptions()
    {
        parent::setDefaultOptions();

        $this->optionsResolver
            ->setDefaults(array('text' => '', 'visible_close_button' => false, 'visible_save_button' => true, 'widget_id' => 'complete-package', 'popup_non_closable' => false));
    }
}
