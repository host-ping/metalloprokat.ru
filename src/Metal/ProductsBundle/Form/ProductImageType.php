<?php

namespace Metal\ProductsBundle\Form;

use Metal\ProjectBundle\Validator\Constraints\Image;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProductImageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'uploadedPhoto',
                'file',
                array(
                    'required' => false,
                    'attr' => array(
                        'accept' => 'image/*'
                    ),
                    //TODO:: удалить, проверить сработает ли constraints в сущности
                    'constraints' => array(
                        new Image()
                    )
                )
            )
        ;

    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'Metal\ProductsBundle\Entity\ProductImage'
            )
        );
    }

    public function getName()
    {
        return 'product_image';
    }
}
