<?php

namespace Metal\ContentBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ContentEntryTagType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('tag', 'entity_id', array(
                'class' => 'MetalContentBundle:Tag',
            ))
            ->add('tagTitle', 'text');
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Metal\ContentBundle\Entity\ContentEntryTag'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'metal_contentbundle_contententrytag';
    }
}