<?php

namespace Metal\GrabbersBundle\Admin;

use Metal\GrabbersBundle\Entity\Site;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class SiteAdmin extends AbstractAdmin
{
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection
            ->remove('create')
            ->remove('delete');
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('title', null, array('label' => 'Название'))
            ->add('host', null, array('label' => 'Хост', 'disabled' => true))
            ->add('login', null, array('label' => 'Логин', 'required' => false))
            ->add('password', null, array('label' => 'Пароль', 'required' => false))
            ->add('code', null, array('label' => 'Код', 'disabled' => true))
            ->add('useProxy', null, array('label' => 'Использовать прокси', 'required' => false))
            ->add('testProxyUri', null, array('label' => 'Тестовый uri для проверки proxy', 'required' => false))
            ->add('manualMode', null, array(
                'label' => 'Ручной режим',
                'help' => 'Сайт не будет обрабатываться в автоматическом режиме.'
            ))
            ->add('isEnabled', null, array('label' => 'Включен'));
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('title', null, array('label' => 'Название'))
            ->add('isEnabled', null, array('label' => 'Включен'))
            ->add('useProxy', null, array('label' => 'Использовать прокси'))
            ->add('createdAt', null, array('label' => 'Дата добавления'));
    }

    public function toString($object)
    {
        return $object instanceof Site ? $object->getTitle() : '';
    }
}
