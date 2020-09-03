<?php

namespace Metal\ProjectBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class BanRequestAdmin extends AbstractAdmin
{
    protected $datagridValues = array(
        '_sort_order' => 'DESC',
        '_sort_by' => 'createdAt',
    );

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection
            ->remove('delete')
            ->remove('create')
            ->remove('edit');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('intIp', null, array('label' => 'ID'))
            ->add('ip', null, array('label' => 'IP-адрес'))
            ->add(
                'user',
                null,
                array(
                    'label' => 'Пользователь',
                    'template' => 'MetalUsersBundle:AdminPartial:user.html.twig'
                )
            )
            ->add('uri', null, array('label' => 'URI'))
            ->add('method', null, array('label' => 'HTTP Method'))
            ->add('code', null, array('label' => 'HTTP Code'))
            ->add('referer', null, array('label' => 'Referer'))
            ->add('createdAt', null, array('label' => 'Дата создания'));
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('intIp', null, array('label' => 'ID'))
            ->add('ip', null, array('label' => 'IP-адрес'))
            ->add(
                'method',
                'doctrine_orm_choice',
                array(
                    'label' => 'HTTP Method',
                ),
                'choice',
                array(
                    'choices' => array('GET' => 'GET', 'POST' => 'POST', 'DELETE' => 'DELETE')
                )
            )
            ->add('code', null, array('label' => 'HTTP Code'))
            ->add(
                'intCreatedAt',
                'doctrine_orm_datetime_range',
                array('label' => 'Дата создания', 'input_type' => 'timestamp'),
                'sonata_type_datetime_range_picker',
                array(
                    'field_options' => array(
                        'format' => 'dd.MM.yyyy HH:mm:ss',
                        'widget' => 'single_text'
                    ),
                    'attr' => array(
                        'class' => 'js-sonata-datepicker'
                    )
                )
            );
    }
}

