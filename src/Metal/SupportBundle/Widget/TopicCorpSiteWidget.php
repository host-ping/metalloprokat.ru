<?php

namespace Metal\SupportBundle\Widget;

use Brouzie\Bundle\WidgetsBundle\Widget\WidgetAbstract;
use Metal\SupportBundle\Form\TopicCorpSiteType;

class TopicCorpSiteWidget extends WidgetAbstract
{
    protected function getParametersToRender()
    {
        $form = $this->createForm(new TopicCorpSiteType());

        return array(
            'form' => $form->createView(),
        );
    }
} 