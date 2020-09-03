<?php

namespace Metal\CompaniesBundle\Widget;

use Brouzie\Bundle\WidgetsBundle\Widget\WidgetAbstract;

use Metal\CompaniesBundle\Entity\ReviewAnswer;
use Metal\CompaniesBundle\Form\ReviewAnswerType;

class ReviewAnswerFormWidget extends WidgetAbstract
{
    public function getParametersToRender()
    {
        $reviewAnswer = new ReviewAnswer();
        $form = $this->createForm(new ReviewAnswerType(), $reviewAnswer);

        return array(
            'form' => $form->createView(),
        );
    }
}
