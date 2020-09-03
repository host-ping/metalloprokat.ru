<?php

namespace Metal\DemandsBundle\Form\Admin;

use Metal\ProductsBundle\Entity\ValueObject\ProductMeasureProvider;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

class VolumeFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('volume', null, ['label' => false])
            ->add(
                'volumeTypeId',
                ChoiceType::class,
                array(
                    'label' => false,
                    'placeholder' => '',
                    'choices' => ProductMeasureProvider::getAllTypesAsSimpleArray(),
                )
            );
    }
}
