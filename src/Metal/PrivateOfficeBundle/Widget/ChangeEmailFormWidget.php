<?php

namespace Metal\PrivateOfficeBundle\Widget;

use Brouzie\Bundle\WidgetsBundle\Widget\WidgetAbstract;
use Metal\UsersBundle\Form\ChangeEmailType;

class ChangeEmailFormWidget extends WidgetAbstract
{
    public function getParametersToRender()
    {
        $form = $this->createForm(new ChangeEmailType());

        return array(
            'form' => $form->createView(),
        );
    }
}
