<?php

namespace Metal\GrabbersBundle\Admin;

use Monolog\Logger;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;

class GrabberLogAdmin extends AbstractAdmin
{
    protected $datagridValues = array(
        '_sort_order' => 'DESC',
        '_sort_by' => 'id',
    );

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection
            ->remove('delete')
            ->remove('edit')
            ->remove('create');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('site', null, array('label' => 'Сайт', 'associated_property' => 'title'))
            ->add(
                'level',
                'choice',
                array('label' => 'Уровень сообщения', 'choices' => array_flip(Logger::getLevels()))
            )
            ->add('message', null, array('label' => 'Сообщение'))
            ->add('createdAt', null, array('label' => 'Дата создания'))
            ->add(
                'actions',
                null,
                array(
                    'label' => 'Действия',
                    'template' => 'MetalGrabbersBundle:GrabberLogAdmin:actions.html.twig'
                )
            );
    }

    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('site', null, array('label' => 'Сайт', 'associated_property' => 'title'))
            ->add(
                'level',
                'choice',
                array('label' => 'Уровень сообщения', 'choices' => array_flip(Logger::getLevels()))
            )
            ->add('message', null, array('label' => 'Сообщение'))
            ->add('context', 'array', array('Дополнительная информация'))
            ->add('createdAt', null, array('label' => 'Дата создания'));;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('site', null, array('label' => 'Сайт'), array(), array('property' => 'title'))
            ->add(
                'level',
                'doctrine_orm_choice',
                array('label' => 'Уровень сообщения'),
                'choice',
                array('choices' => array_flip(Logger::getLevels()))
            );
    }
}
