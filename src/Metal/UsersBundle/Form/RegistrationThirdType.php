<?php

namespace Metal\UsersBundle\Form;

use Metal\ServicesBundle\Entity\ValueObject\ServicePeriodTypesProvider;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class RegistrationThirdType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'package',
                'entity',
                array(
                    'class' => 'MetalServicesBundle:Package',
                    'property' => 'titleForPromotions',
                    'expanded' => true,
                    'multiple' => false
                )
            )
            ->add(
                'termPackage',
                'choice',
                array(
                    'choices' => array(
                        ServicePeriodTypesProvider::QUARTER => 'квартал',
                        ServicePeriodTypesProvider::HALF_YEAR => 'полугодие',
                        ServicePeriodTypesProvider::YEAR => 'год'
                    ),
                    'expanded' => true,
                    'multiple' => false
                )
            );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'metal_usersbundle_registration_third';
    }
}
