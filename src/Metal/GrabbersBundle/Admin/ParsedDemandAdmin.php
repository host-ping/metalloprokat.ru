<?php

namespace Metal\GrabbersBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class ParsedDemandAdmin extends AbstractAdmin
{
    protected $datagridValues = array(
        '_sort_order' => 'DESC',
        '_sort_by' => 'id',
    );

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection
            ->remove('edit')
            ->remove('show')
            ->remove('create');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add(
                'demand',
                null,
                array(
                    'label' => 'Заявка',
                    'associated_property' => 'id',
                    'admin_code' => 'metal.demands.admin.demand')
            )
            ->add('parsedDemandId', null, array('label' => 'Оригинальная заявка'))
            ->add(
                'url',
                null,
                array(
                    'label' => 'Ссылка',
                    'template' => 'MetalGrabbersBundle:ParsedDemandAdmin:view_url.html.twig'
                )
            )
            ->add('site', null, array('label' => 'Сайт', 'associated_property' => 'title'))
            ->add('demand.moderated', 'boolean', array('label' => 'Промодерирована'))
            ->add('demand.deleted', 'boolean', array('label' => 'Удалена'))
            ->add('createdAt', null, array('label' => 'Дата парсинга'));
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('site', null, array('label' => 'Сайт'), array(), array('property' => 'title'))
            ->add(
                'createdAt',
                'doctrine_orm_date_range',
                array('label' => 'Дата создания'),
                'sonata_type_date_range_picker',
                array(
                    'field_options_start' => array(
                        'format' => 'dd.MM.yyyy',
                        'label' => 'Дата от',
                    ),
                    'field_options_end' => array(
                        'format' => 'dd.MM.yyyy',
                        'label' => 'Дата до',
                    ),
                    'attr' => array(
                        'class' => 'js-sonata-datepicker',
                    ),
                )
            );
    }
}
