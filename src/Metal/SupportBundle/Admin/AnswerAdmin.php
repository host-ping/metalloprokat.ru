<?php

namespace Metal\SupportBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class AnswerAdmin extends AbstractAdmin
{
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection
            ->remove('create')
            ->remove('list')
            ->remove('delete');
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add(
                'message',
                'textarea',
                array(
                    'label' => 'Сообщение',
                    'attr' => array(
                        'style' => 'height: 150px'
                    )
                )
            );
    }

    public function configure()
    {
        $this->setTemplate('edit', 'MetalSupportBundle:AnswerAdmin:edit_answer.html.twig');
    }

}
