<?php

namespace Metal\ProductsBundle\Form;

use Metal\ProductsBundle\Entity\Product;
use Metal\ProductsBundle\Entity\ValueObject\ProductMeasureProvider;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProductsInlineEditType extends AbstractType
{
    public static function getNameForProduct(Product $product)
    {
        return sprintf('metal_productadmin_inline_%d', $product->getId());
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(
                array('data_class' => 'Metal\ProductsBundle\Entity\Product', 'suggest_url' => '', 'iterator' => 0)
            )
            ->setRequired(array('product_volume_title'));
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'title',
                null,
                array(
                    'label' => 'Название',
                )
            )
            ->add(
                'size',
                null,
                array(
                    'label' => mb_convert_case($options['product_volume_title'], MB_CASE_TITLE)
                )
            )
            ->add(
                'price',
                null,
                array(
                    'label' => 'Цена',
                    'required' => false
                )
            )
            ->add(
                'measureId',
                'choice',
                array(
                    'label' => 'Ед. измерения',
                    'choices' => ProductMeasureProvider::getAllTypesAsSimpleArray(),
                    'placeholder' => '',
                )
            )
            ->add(
                'isPriceFrom',
                null,
                array(
                    'label' => 'Цена от',
                    'required' => false
                )
            )
            ->add(
                'imageTitle',
                'text',
                array(
                    'mapped' => false,
                    'label' => 'Изображение',
                    'required' => false,
                    'attr' => array(
                        'typeahead' => '',
                        'typeahead-remote-url' => $options['suggest_url'],
                        'typeahead-suggestion-template-url' => "'typeahead-suggestion-image'",
                        'typeahead-model' => 'image'.$options['iterator']
                    )
                )
            )->add(
                'image',
                'entity_id',
                array(
                    'class' => 'MetalProductsBundle:ProductImage',
                    'label' => 'ID изображения',
                    'hidden' => false,
                    'read_only' => true,
                    'required' => false,
                    'attr' => array(
                        'ng-model' => 'image'.$options['iterator'].'.id',
                        'initial-value' => ''
                    )
                )
            )
            ->add(
                'checked',
                'choice',
                array(
                    'label' => 'Статус',
                    'required' => false,
                    'choices' => Product::getAvailableStatusesForEdit()
                )
            )
            ->add(
                'isSpecialOffer',
                null,
                array(
                    'label' => 'Спец. предложение',
                    'required' => false
                )
            )
            ->add('position', null, array('label' => 'Позиция на минисайте'))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'metal_productadmin_inline';
    }
}
