<?php

namespace Metal\ComplaintsBundle\Form;

use Metal\ComplaintsBundle\Entity\ValueObject\ComplaintTypeProvider;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
use Metal\ComplaintsBundle\Entity\AbstractComplaint;

class ComplaintType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('body')
            ->add(
                'complaintTypeId',
                'choice',
                array(
                    'choices' => ComplaintTypeProvider::getAllTypesAsSimpleArray($options['kind']),
                )
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired(array('kind'))
            ->setAllowedValues('kind', array('demand', 'product', 'company'))
            ->setDefault('data_class', AbstractComplaint::class);
    }

    public function getName()
    {
        return 'metal_complaint_type';
    }
}
