<?php

namespace Metal\DemandsBundle\Widget;

use Brouzie\Bundle\WidgetsBundle\Widget\WidgetAbstract;
use Metal\DemandsBundle\Entity\DemandAnswer;
use Metal\DemandsBundle\Form\DemandAnswerType;

class DemandAnswerFormWidget extends WidgetAbstract
{
    public function getParametersToRender()
    {
        $user = null;
        if ($this->isGranted('ROLE_USER')) {
            $user = $this->getUser();
        }

        $options = array(
            'city_repository' => $this->container->get('doctrine')->getRepository('MetalTerritorialBundle:City'),
            'is_authenticated' => $user !== null,
            'validation_groups' => array(
                $user !== null ? 'authenticated' : 'anonymous',
            ),
        );

        $form = $this->createForm(new DemandAnswerType(), new DemandAnswer(), $options);

        return array(
            'form' => $form->createView()
        );
    }
}
