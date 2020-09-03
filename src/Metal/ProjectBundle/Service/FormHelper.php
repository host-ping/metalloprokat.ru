<?php

namespace Metal\ProjectBundle\Service;

use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormView;

class FormHelper
{
    public function getFormValues(FormView $formView)
    {
        $values = array();

        foreach ($formView->vars['form']->children as $subView) {
            if (count($subView->children)) {
                $values = array_merge($values, $this->getFormValues($subView));
            } else {
                $values[$subView->vars['full_name']] = $subView->vars['data'];

                if (in_array('checkbox', $subView->vars['block_prefixes'])) {
                    $values[$subView->vars['full_name']] = $subView->vars['checked'];
                }
            }
        }

        return $values;
    }

    public function getFormErrorMessages(Form $form, &$parentErrors = array())
    {
        $errors = array();
        foreach ($form->getErrors() as $key => $formError) {
            $errors[$key] = $formError->getMessage();
        }

        if ($form->count()) {
            foreach ($form as $child) {
                /* @var $child Form */
                if (!$child->isValid()) {
                    $this->getFormErrorMessages($child, $parentErrors);
                }
            }
        }

        if ($errors) {
            $key = $form->createView()->vars['full_name'];
            $parentErrors[$key] = $errors;
        }

        return $parentErrors;
    }
}
