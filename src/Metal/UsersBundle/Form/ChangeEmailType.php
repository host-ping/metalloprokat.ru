<?php

namespace Metal\UsersBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ChangeEmailType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('newEmail', 'email', array(
                'required' => true,
            ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'metal_usersbundle_change_email';
    }
}
