<?php

namespace Metal\PrivateOfficeBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MiniSiteSaveCoverType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'uploadedFile',
                'file',
                array(
                    'required' => true,
                    'attr' => array(
                        'accept' => 'image/*'
                    ),
                )
            );
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
                'data_class' => 'Metal\MiniSiteBundle\Entity\MiniSiteCover',
            ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'metal_privateofficebundle_minisitesavecover';
    }
}
