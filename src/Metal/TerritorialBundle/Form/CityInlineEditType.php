<?php

namespace Metal\TerritorialBundle\Form;

use Metal\TerritorialBundle\Entity\City;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CityInlineEditType extends AbstractType
{
    public static function getNameForCity(City $city)
    {
        return sprintf('metal_cityadmin_inline_%d', $city->getId());
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(array('data_class' => 'Metal\TerritorialBundle\Entity\City'));
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', null, array('label' => 'Название'))
            ->add('titleLocative', null, array('label' => 'Местный падеж'))
            ->add('titleGenitive', null, array('label' => 'Родительный падеж'))
            ->add('titleAccusative', null, array('label' => 'Винительный падеж'));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'metal_cityadmin_inline';
    }
}
