<?php

namespace Metal\ProjectBundle\Form;

use Metal\ProjectBundle\Entity\Review;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ReviewType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (!$options['is_authenticated']) {
            $builder
                ->add('name')
                ->add('email');
        }
        $builder
            ->add('comment', 'textarea')
            ->add(
                'type',
                'choice',
                array(
                    'expanded' => true,
                    'multiple' => false,
                    'choices' => Review::getTypesAsSimpleArray(),
                    'data' => Review::TYPE_POSITIVE
                )
            );
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setRequired(array('is_authenticated', 'data_class'));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'metal_review';
    }
}
