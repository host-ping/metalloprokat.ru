<?php

namespace Metal\ProjectBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class NormalizedPhoneAdmin extends AbstractAdmin
{
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection
            ->remove('create')
            ->remove('delete')
            ->remove('edit')
            ->add(
                'search_by_phone',
                'search_by_phone',
                array('_controller' => 'MetalProjectBundle:NormalizedPhoneAdmin:searchByPhone')
            );
    }

    public function getDashboardActions()
    {
        $actions = parent::getDashboardActions();

        $actions['search_by_phone'] = array(
            'label' => 'Поиск по номеру телефона',
            'url' => $this->generateUrl('search_by_phone'),
            'icon' => 'search'
        );

        return $actions;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add(
                'phone',
                null,
                array(
                    'label' => 'Номер',
                    'template' => 'MetalProjectBundle:Admin/NormalizedPhoneAdmin:phone_row.html.twig'
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
            );
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add(
                'phone',
                'doctrine_orm_callback',
                array(
                    'label' => 'Номер',
                    'callback' => function ($queryBuilder, $alias, $field, $value) {
                        if (!isset($value['value'])) {
                            return;
                        }

                        $queryBuilder
                            ->andWhere(sprintf('%s.phone LIKE :searchPhone', $alias))
                            ->setParameter('searchPhone', '%'.preg_replace('/\D*/ui', '', $value['value']).'%');
                    }
                )
            );
    }
}
