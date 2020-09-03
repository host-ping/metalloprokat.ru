<?php

namespace Metal\PrivateOfficeBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Metal\MiniSiteBundle\Entity\ValueObject\BackgroundColorProvider;
use Metal\MiniSiteBundle\Entity\ValueObject\PrimaryColorProvider;
use Metal\MiniSiteBundle\Entity\ValueObject\SecondaryColorProvider;

class MiniSiteColorsType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('backgroundColor', 'choice', array(
                'required'  => true,
                'choices' => array(
                    BackgroundColorProvider::getAllTypesAsSimpleArray()
                ),
                'expanded' => true
            ));
        $builder
            ->add('primaryColor', 'choice', array(
                    'required'  => true,
                    'choices' => array(
                        PrimaryColorProvider::getAllTypesAsSimpleArray()
                    ),
                    'expanded' => true
                ));
        $builder
            ->add('secondaryColor', 'choice', array(
                    'required'  => true,
                    'choices' => array(
                        SecondaryColorProvider::getAllTypesAsSimpleArray()
                    ),
                    'expanded' => true
                ));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
                'data_class' => 'Metal\MiniSiteBundle\Entity\MiniSiteConfig',
            ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'metal_privateofficebundle_minisitecolors';
    }
}
