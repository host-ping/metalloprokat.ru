<?php

namespace Metal\UsersBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class RequestRecoverPasswordType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', 'email', array(
                'required' => true,
                'constraints' => array(
                    new NotBlank(),
                    new Email(),
                )
            ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'metal_usersbundle_before_recover_password';
    }
}
