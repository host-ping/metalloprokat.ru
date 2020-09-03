<?php

namespace Metal\UsersBundle\Widget;

use Brouzie\Bundle\WidgetsBundle\Widget\WidgetAbstract;
use Metal\UsersBundle\Form\RequestRecoverPasswordType;

class RecoverPasswordWidget extends WidgetAbstract
{
    public function getParametersToRender()
    {
        $form = $this->createForm(new RequestRecoverPasswordType());

        return array(
            'form' => $form->createView(),
        );
    }
}