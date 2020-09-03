<?php


namespace Metal\PrivateOfficeBundle\Widget;

use Brouzie\Bundle\WidgetsBundle\Widget\WidgetAbstract;
use Metal\PrivateOfficeBundle\Form\AddTopicType;


class AddTopicFormWidget extends WidgetAbstract
{
    public function getParametersToRender()
    {
        $form = $this->createForm(new AddTopicType());

        return array(
            'form' => $form->createView()
        );
    }

} 