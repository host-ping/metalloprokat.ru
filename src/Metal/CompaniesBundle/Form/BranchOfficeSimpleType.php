<?php

namespace Metal\CompaniesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints as Assert;

class BranchOfficeSimpleType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'companyCities',
                'collection',
                array(
                    'type' => new CompanyCityType(),
                    'by_reference' => false,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'constraints' => array(
//                        new Assert\Valid(),
                    ),
                )
            );
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'Metal\CompaniesBundle\Entity\Company',
                'cascade_validation' => true,
                'validation_groups' => 'batch_branch_office',
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'metal_companies_bundle_branch_office_simple_type';
    }
}
