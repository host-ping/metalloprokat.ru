<?php

namespace Metal\ProjectBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

class RedirectAdmin extends AbstractAdmin
{
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add(
                'redirectFrom',
                null,
                array(
                    'label' => 'С какой страницы',
                )
            )
            ->add(
                'redirectTo',
                null,
                array(
                    'label' => 'На какую страницу',
                )
            )
            ->add(
                'enabled',
                null,
                array(
                    'label' => 'Включен',
                )
            )
            ->add(
                'createdAt',
                null,
                array(
                    'label' => 'Создан',
                )
            )
            ->add(
                'updatedAt',
                null,
                array(
                    'label' => 'Обновлен',
                )
            );
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add(
                'redirectFrom',
                null,
                array(
                    'label' => 'С какой страницы',
                    'help' => '#(.+)?/sort/shpuntlarcena/(.+)?#ui'
                )
            )
            ->add(
                'redirectTo',
                null,
                array(
                    'label' => 'На какую страницу',
                    'help' => '$1/sort/shpunt/shpunt-larsena/$2',
                )
            )
            ->add(
                'exampleUrl',
                null,
                array(
                    'label' => 'Пример адреса страницы',
                    'help' => 'https://www.metalloprokat.ru/sort/shpuntlarcena/',
                )
            )
            ->add(
                'enabled',
                null,
                array(
                    'label' => 'Включен',
                    'required' => false
                )
            );
    }
}
