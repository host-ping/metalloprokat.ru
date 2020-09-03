<?php

namespace Metal\AnnouncementsBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class OrderAnnouncementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('email')
            ->add('name')
            ->add('phone')
            ->add('createAnnouncement', 'checkbox', array('label' => 'Нужно изготовление баннера '))
            ->add(
                'startsAt',
                'date',
                array(
                    'widget' => 'single_text',
                    'format' => 'd MMMM yyyy',
                )
            )
            ->add(
                'zone',
                'entity_id',
                array(
                    'class' => 'MetalAnnouncementsBundle:Zone',
                )
            )
            ->add('comment', 'textarea');
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setRequired(array('is_authenticated'));
        $resolver->setDefaults(
            array(
                'data_class' => 'Metal\AnnouncementsBundle\Entity\OrderAnnouncement',
            )
        );
    }

    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['is_authenticated'] = $options['is_authenticated'];
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'metal_announcementbundle_orderannouncementrtype';
    }
}
