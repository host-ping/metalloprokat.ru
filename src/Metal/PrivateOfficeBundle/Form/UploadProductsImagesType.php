<?php

namespace Metal\PrivateOfficeBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\NotBlank;

class UploadProductsImagesType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $constraints = array();
        if (!$options['data'] || !$options['data']->getId()) {
            $constraints[] = new NotBlank();
        }
        $constraints[] = new Image();

        $builder
            ->add(
                'uploadedPhoto',
                'file',
                array(
                    'required' => false,
                    'attr' => array(
                        'accept' => 'image/*'
                    ),
                    'constraints' => $constraints
                )
            )
            ->add(
                'description',
                'textarea',
                array(
                    'required' => false,
                )
            )
            ->add(
                'optimized',
                'checkbox',
                array(
                    'label' => 'Оптимизировать изображение',
                    'required' => false
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
                'data_class' => 'Metal\ProductsBundle\Entity\ProductImage'
            )
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'metal_privatebundle_upload_products_images';
    }
}
