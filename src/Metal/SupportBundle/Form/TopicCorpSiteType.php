<?php

namespace Metal\SupportBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TopicCorpSiteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('email')
            ->add('userName')
            ->add('description', 'textarea');
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
                'validation_groups' => array('corp_site', 'Default'),
            ));
    }

    public function getName()
    {
        return 'metal_topic_corp_site';
    }
} 