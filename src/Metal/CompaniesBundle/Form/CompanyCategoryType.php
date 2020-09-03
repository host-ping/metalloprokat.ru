<?php

namespace Metal\CompaniesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CompanyCategoryType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'category',
                'entity_id',
                array(
                    'class' => 'MetalCategoriesBundle:Category',
                )
            )
            ->add('categoryTitle', 'text');
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'Metal\CompaniesBundle\Entity\CompanyCategory',
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'metal_companiesbundle_companycategory';
    }
}
