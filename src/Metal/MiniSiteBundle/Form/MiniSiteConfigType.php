<?php

namespace Metal\MiniSiteBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;


class MiniSiteConfigType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'googleAnalyticsId',
                null,
                array(
                    'label' => 'Идентификатор Google Analytics',
                    'required' => false
                )
            )
            ->add(
                'yandexMetrikaId',
                null,
                array(
                    'label' => 'Идентификатор Yandex Metrika',
                    'required' => false
                )
            )
        ;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'metal_add_analytics';
    }
}