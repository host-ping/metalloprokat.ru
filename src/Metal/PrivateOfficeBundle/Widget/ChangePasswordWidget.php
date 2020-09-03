<?php


namespace Metal\PrivateOfficeBundle\Widget;

use Brouzie\Bundle\WidgetsBundle\Widget\WidgetAbstract;
use Metal\PrivateOfficeBundle\Form\ChangePasswordType;

class ChangePasswordWidget extends WidgetAbstract
{
    public function getParametersToRender()
    {
        $user = null;
        if ($this->isGranted('ROLE_USER')) {
            $user = $this->getUser();
        }

        $form = $this->createForm(new ChangePasswordType(), $user);

        return array(
            'form' => $form->createView()
        );
    }
}
