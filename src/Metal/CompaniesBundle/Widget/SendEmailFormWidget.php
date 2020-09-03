<?php

namespace Metal\CompaniesBundle\Widget;

use Brouzie\Bundle\WidgetsBundle\Widget\WidgetAbstract;
use Metal\CompaniesBundle\Form\SendEmailToEmployeeType;

class SendEmailFormWidget extends WidgetAbstract
{
    public function getParametersToRender()
    {
        $form = $this->createForm(
            new SendEmailToEmployeeType(),
            null,
            array('is_authenticated' => $this->isGranted('ROLE_USER'))
        );

        return array(
            'form' => $form->createView(),
        );
    }
}
