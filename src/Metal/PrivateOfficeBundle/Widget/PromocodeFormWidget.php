<?php

namespace Metal\PrivateOfficeBundle\Widget;

use Brouzie\WidgetsBundle\Widget\ConditionallyRenderedWidget;
use Brouzie\Bundle\WidgetsBundle\Widget\WidgetAbstract;
use Metal\PrivateOfficeBundle\Form\PromocodeType;

class PromocodeFormWidget extends WidgetAbstract implements ConditionallyRenderedWidget
{
    protected function getParametersToRender()
    {
        $form = $this->createForm(new PromocodeType());

        return array(
            'form' => $form->createView(),
        );
    }

    public function shouldBeRendered()
    {
        return $this->container->getParameter('project.promocode_enabled');
    }
}
