<?php

namespace Metal\PrivateOfficeBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AddAnswerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('message', 'textarea', array('required' => true));
    }

    public function getName()
    {
        return 'metal_add_answer';
    }
}
