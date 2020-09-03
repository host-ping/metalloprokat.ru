<?php

namespace Metal\UsersBundle\Form;

use Metal\UsersBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class PrivateUserType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'firstName',
                'text',
                array(
                    'required' => true,
                    'constraints' => array(
                        new NotBlank(),
                    ),
                )
            )
            ->add('job', 'text')
            ->add(
                'phone',
                null,
                array(
                    'required' => true,
                )
            )
            ->add(
                'additionalCode',
                'text',
                array(
                    'required' => false,
                )
            )
            ->add(
                'secondName',
                'text',
                array(
                    'required' => true,
                    'constraints' => array(
                        new NotBlank(),
                    ),
                )
            )
            ->add(
                'email',
                null,
                array(
                    'required' => false,
                    'disabled' => true,
                )
            )
            ->add(
                'password',
                null,
                array(
                    'required' => false,
                    'mapped' => false,
                    'disabled' => true,
                )
            )
            ->add('skype', 'text')
            ->add('icq', 'text')
            ->add('displayInContacts', 'checkbox')
            ->add('displayPosition', 'integer');
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'Metal\UsersBundle\Entity\User',
                'translation_domain' => false,
            )
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'metal_usersbundle_privateuser';
    }
}
