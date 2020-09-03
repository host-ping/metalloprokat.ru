<?php

namespace Metal\ProjectBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class NormalizedEmailAdmin extends AbstractAdmin
{
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection
            ->remove('create')
            ->remove('delete')
            ->remove('edit')
            ->add(
                'search_by_email',
                'search_by_email',
                array('_controller' => 'MetalProjectBundle:NormalizedEmailAdmin:searchByEmail')
            );
    }

    public function getDashboardActions()
    {
        $actions = parent::getDashboardActions();

        $actions['search_by_email'] = array(
            'label' => 'Поиск по email адресу',
            'url' => $this->generateUrl('search_by_email'),
            'icon' => 'search'
        );

        return $actions;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add(
                'email',
                null,
                array(
                    'label' => 'Email',
                    'template' => 'MetalProjectBundle:Admin/NormalizedEmailAdmin:email_row.html.twig'
                )
            )
            ->add(
                'company',
                null,
                array(
                    'label' => 'Компания',
                    'template' => 'MetalProjectBundle:Admin:company_with_id_list.html.twig'
                )
            )
            ->add(
                'user',
                null,
                array(
                    'label' => 'Пользователь',
                    'template' => 'MetalUsersBundle:AdminPartial:user.html.twig'
                )
            )
            ->add(
                'demand',
                null,
                array(
                    'label' => 'Заявка',
                    'associated_property' => 'id'
                )
            )
            ->add(
                'subscriber',
                null,
                array(
                    'label' => 'Подписчик',
                    'associated_property' => 'id'
                )
            );
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add(
                'email',
                'doctrine_orm_callback',
                array(
                    'label' => 'Email',
                    'callback' => function ($queryBuilder, $alias, $field, $value) {
                        if (!isset($value['value'])) {
                            return;
                        }

                        $queryBuilder
                            ->andWhere(sprintf('%s.email LIKE :searchEmail', $alias))
                            ->setParameter('searchEmail', '%'.$value['value'].'%');
                    }
                )
            );
    }
}
