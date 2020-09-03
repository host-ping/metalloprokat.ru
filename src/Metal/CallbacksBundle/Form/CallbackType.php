<?php

namespace Metal\CallbacksBundle\Form;

use Metal\ProductsBundle\Entity\ValueObject\ProductMeasureProvider;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class CallbackType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('phone');

        if ($options['for_product']) {
            $builder
                ->add(
                    'volume',
                    'text',
                    array(
                        'constraints' => array(
                            new NotBlank(),
                        )
                    )
                )
                ->add(
                    'volumeTypeId',
                    'choice',
                    array(
                        'choices' => ProductMeasureProvider::getAllTypesAsSimpleArray(),
                        'placeholder' => '',
                        'choice_translation_domain' => false,
                        'constraints' => array(
                            new NotBlank(),
                        ),
                    )
                );
        }
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'Metal\CallbacksBundle\Entity\Callback',
                'for_product' => false
            )
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'metal_callback';
    }
}
