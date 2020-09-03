<?php

namespace Metal\ProjectBundle\Exception;

use Symfony\Component\Form\Form;

class FormValidationException extends \RuntimeException
{
    private $form;

    public function  __construct(Form $form)
    {
        $this->form = $form;
    }

    /**
     * @return Form
     */
    public function getForm()
    {
        return $this->form;
    }
}
