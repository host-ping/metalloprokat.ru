<?php

namespace Metal\UsersBundle\Form;

use Metal\CompaniesBundle\Form\CompanyCategoryType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class RegistrationSecondType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('uploadedPrice', 'file')
            ->add(
                'ourExperts',
                'checkbox',
                array(
                    'label' => 'при помощи наших специалистов',
                    'mapped' => false,
                    'attr' => array(
                        'checked' => 'checked'
                    )
                )
            )
            ->add('companyCategories', 'collection', array(
                'type' => new CompanyCategoryType(),
                'by_reference' => false,
                'allow_add' => true,
                'allow_delete' => true,
            ))
            ->add(
                'updater',
                'choice',
                array(
                    'choices' => array(
                        3 => 'раз в две недели',
                        2 => 'раз в месяц'
                    ),
                    'expanded' => true,
                    'multiple' => false,
                    'data' => 2
                )
            );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'metal_usersbundle_registration_second';
    }
}
