<?php


namespace Metal\CompaniesBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class CompanyToggleStatusType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('comment', 'textarea', array(
                'label' => 'Комментарии',
                'attr' => array(
                    'width' => '350px'
                ),
                'required' => true
            ))
        ;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'metal_companiesbundle_companytogglestatus';
    }
} 