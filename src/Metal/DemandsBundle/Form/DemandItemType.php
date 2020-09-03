<?php

namespace Metal\DemandsBundle\Form;

use Metal\ProductsBundle\Entity\ValueObject\ProductMeasureProvider;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DemandItemType extends AbstractType
{
    private $projectFamily;

    public function __construct($projectFamily)
    {
        $this->projectFamily = $projectFamily;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text')
            ->add('volume', 'text')
        ;

        if ($this->projectFamily == 'metalloprokat') {
            $builder
                ->add('size', 'text');
        }

        $builder
            ->add(
                'category',
                'entity_id',
                array(
                    'class' => 'MetalCategoriesBundle:Category'
                )
            )
            ->add(
                'volumeTypeId',
                'choice',
                array(
                    'choices' => ProductMeasureProvider::getAllTypesAsSimpleArray(),
                    'placeholder' => '',
                    'choice_translation_domain' => false,
                )
            );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'Metal\DemandsBundle\Entity\DemandItem'
            )
        );
    }

    public function getName()
    {
        return 'demanditem';
    }
}
