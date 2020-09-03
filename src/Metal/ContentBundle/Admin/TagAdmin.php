<?php

namespace Metal\ContentBundle\Admin;

use Metal\ContentBundle\Entity\Tag;
use Metal\ContentBundle\Entity\ValueObject\StatusTypeProvider;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class TagAdmin extends AbstractAdmin
{
    public function toString($object)
    {
        return $object instanceof Tag ? $object->getTitle() : '';
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection
            ->remove('delete');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('title', null, array('label' => 'Заголовок'))
            ->add('createdAt', null, array('label' => 'Дата добавления'))
            ->add('updatedAt', null, array('label' => 'Дата обновления'))
            ->add(
                'statusTypeId',
                'choice',
                array('label' => 'Статус модерации', 'choices' => StatusTypeProvider::getAllTypesAsSimpleArray())
            );
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('title', null, array('label' => 'Заголовок'))
            ->add(
                'statusTypeId',
                'choice',
                array('label' => 'Статус модерации', 'choices' => StatusTypeProvider::getAllTypesAsSimpleArray())
            );
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id', null, array('label' => 'ID'))
            ->add('title', null, array('label' => 'Заголовок'))
            ->add(
                'statusTypeId',
                'doctrine_orm_choice',
                array('label' => 'Статус модерации'),
                'choice',
                array(
                    'choices' => StatusTypeProvider::getAllTypesAsSimpleArray(),
                )
            );
    }
}
