<?php

namespace Metal\ComplaintsBundle\Widget;

use Brouzie\Bundle\WidgetsBundle\Widget\WidgetAbstract;
use Metal\ComplaintsBundle\Entity\AbstractComplaint;
use Metal\ComplaintsBundle\Form\ComplaintType;

class ComplaintFormWidget extends WidgetAbstract
{
    public function setDefaultOptions()
    {
        parent::setDefaultOptions();

        $this->optionsResolver
            ->setRequired(array('kind'))
        ;
    }

    public function getParametersToRender()
    {
        $demandComplaint = AbstractComplaint::factory($this->options['kind']);
        $form = $this->createForm(new ComplaintType(), $demandComplaint, array('kind' => $this->options['kind']));

        return array(
            'form' => $form->createView()
        );
    }
}
