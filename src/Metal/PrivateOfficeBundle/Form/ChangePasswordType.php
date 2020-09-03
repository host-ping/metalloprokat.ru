<?php

namespace Metal\PrivateOfficeBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;

class ChangePasswordType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
            $builder->add('oldPassword', 'password', array(
                    'required' => true,
                    'constraints' => new UserPassword(array(
                            'groups' => array(
                                'change_password'
                            )
                        )),
                )
            );
            $builder->add('newPassword', 'repeated', array(
                    'type' => 'password',
                    'invalid_message' => 'Пароли должны совпадать',
                    'required' => true,
                ));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
                'data_class' => 'Metal\UsersBundle\Entity\User'
            ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'metal_privateofficebundle_changepassword';
    }
}
