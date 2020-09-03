<?php

namespace Metal\PrivateOfficeBundle\Form;

use Metal\CompaniesBundle\Entity\CompanyCounter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UpdateTimeType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'scheduledActualizationTime',
                'choice',
                array(
                    'choices' => CompanyCounter::getScheduledActualizationTimeValues(),
                    'placeholder' => '',
                    'required' => false,
                )
            );
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'Metal\CompaniesBundle\Entity\CompanyCounter'
            )
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'metal_privateofficebundle_updatetime';
    }
}
