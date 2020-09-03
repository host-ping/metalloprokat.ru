<?php

namespace Metal\CompaniesBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\DoctrineORMAdminBundle\Datagrid\ProxyQuery;

class PromocodeAdmin extends AbstractAdmin
{
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection
            ->remove('delete')
            ->remove('create')
            ->remove('show')
            ->remove('edit');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('company', null, array('label' => 'Компания', 'template' => 'MetalProjectBundle:Admin:company_with_id_list.html.twig'))
            ->add('code', null, array('label' => 'Промокод'))
            ->add('activatedAt', null, array('label' => 'Дата активации'))
            ->add('startsAt', null, array('label' => 'С какой даты можно активировать (включительно)'))
            ->add('endsAt', null, array('label' => 'До какой даты можно активирован (включительно)'))
            ->add('createdAt', null, array('label' => 'Дата создания'))
        ;
    }

    public function getExportFormats()
    {
        return array('txt');
    }

    protected function configureDatagridFilters(DatagridMapper $filter)
    {
        $filter
            ->add(
                'activatedAt',
                'doctrine_orm_callback',
                array(
                    'label' => 'Использованные',
                    'callback' => function ($queryBuilder, $alias, $field, $value) {

                        if (!isset($value['value'])) {
                            return;
                        }

                        if ($value['value'] === 'y') {
                            $queryBuilder->andWhere(sprintf('%s.activatedAt IS NOT NULL', $alias));
                        } else {
                            $queryBuilder->andWhere(sprintf('%s.activatedAt IS NULL', $alias));
                        }

                        return true;
                    }
                ),
                'choice',
                array(
                    'choices' => array(
                        'y' => 'Да',
                        'n' => 'Нет'
                    )
                )
            )
            ->add('company.id', null, array('label' => 'Компания'))
            ->add(
                'startsAt',
                'doctrine_orm_callback',
                array(
                    'label' => 'Время действия',
                    'callback' => function (ProxyQuery $queryBuilder, $alias, $field, $value) {
                        if (!isset($value['value'])) {
                            return;
                        }

                        switch ($value['value']) {
                            case 'past': {
                                $queryBuilder->andWhere(sprintf("%s.endsAt < :now", $alias));
                                break;
                            }
                            case 'active': {
                                $queryBuilder->andWhere(sprintf(":now BETWEEN %s.startsAt AND %s.endsAt", $alias, $alias));
                                break;
                            }
                            case 'future': {
                                $queryBuilder->andWhere(sprintf("%s.startsAt > :now", $alias));

                                break;
                            }
                        }
                        $queryBuilder->setParameter('now', new \DateTime());

                        return true;
                    }
                ),
                'choice',
                array(
                    'choices' => array(
                        'past' => 'Отработвашие',
                        'active' => 'Действующие',
                        'future' => 'Будущие'
                    )
                )
            )
        ;
    }


}
