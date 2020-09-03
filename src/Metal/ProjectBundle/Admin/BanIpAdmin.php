<?php

namespace Metal\ProjectBundle\Admin;

use Metal\ProjectBundle\Entity\BanIP;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

class BanIpAdmin extends AbstractAdmin
{
    protected $datagridValues = array(
        '_sort_order' => 'DESC',
        '_sort_by' => 'createdAt',
    );

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('ip', 'text', array('label' => 'IP'))
            ->add('status', 'choice', array('label' => 'Статус', 'choices' => BanIP::getIpStatusAsSimpleArray()))
            ->add('note', null, array('label' => 'Примечание'))
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('intIp', null, array('label' => 'ID'))
            ->add('ip', null, array('label' => 'IP-адрес'))
            ->add('hostname', null, array('label' => 'Имя хоста'))
            ->add(
                'status',
                'choice',
                array(
                    'label' => 'Статус',
                    'choices' => BanIP::getIpStatusAsSimpleArray()
                )
            )
            ->add('createdAt', null, array('label' => 'Дата добавления'))
            ->add('note', null, array('label' => 'Примечание'))
            ->add(
                'actions',
                null,
                array(
                    'label' => 'Действие',
                    'template' => 'MetalProjectBundle:Admin:requestActions.html.twig'
                )
            );
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('ip', null, array('label' => 'IP-адрес'))
            ->add('hostname', null, array('label' => 'Имя хоста'))
            ->add(
                'status',
                'doctrine_orm_choice',
                array(
                    'label' => 'Статус'
                ),
                'choice',
                array(
                    'choices' => BanIP::getIpStatusAsSimpleArray()
                )
            );
    }

    public function toString($object)
    {
        return $object instanceof BanIP ? $object->getIp() : '';
    }
}
