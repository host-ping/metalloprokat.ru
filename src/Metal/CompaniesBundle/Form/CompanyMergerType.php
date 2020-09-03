<?php

namespace Metal\CompaniesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class CompanyMergerType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('main_company', 'entity_id', array(
                    'label' => 'ID основной компании',
                    'class' => 'MetalCompaniesBundle:Company',
                    'hidden'  => false,
                    'constraints' => array(new NotBlank())
                ))
            ->add('additional_company', 'entity_id', array(
                    'label' => 'ID дополнительной компании',
                    'class' => 'MetalCompaniesBundle:Company',
                    'hidden'  => false,
                    'constraints' => array(new NotBlank())
                ))
            ->add('comment', 'textarea', array(
                'label' => 'Комментарий',
                'constraints' => array(new NotBlank()),
                'attr' => array(
                    'width' => '350px'
                ),
            ))
        ;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'metal_companiesbundle_companymerger';
    }
}
