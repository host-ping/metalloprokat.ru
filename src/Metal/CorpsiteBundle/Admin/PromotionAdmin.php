<?php

namespace Metal\CorpsiteBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

class PromotionAdmin extends AbstractAdmin
{
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('title', null, array('label' => 'Заголовок'))
            ->add('startsAt', null, array('label' => 'Дата старта'))
            ->add('endsAt', null, array('label' => 'Дата окончания'));
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('title', 'text', array('label' => 'Заголовок'))
            ->add('startsAt', 'sonata_type_date_picker', array('label' => 'Дата старта', 'format' => 'dd.MM.yyyy'))
            ->add('endsAt', 'sonata_type_date_picker', array('label' => 'Дата окончания', 'format' => 'dd.MM.yyyy'))
            ->add('description', 'textarea', array('label' => 'Описание', 'attr' => array('rows' => 30)));
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add(
                'is_active',
                'doctrine_orm_callback',
                array(
                    'label' => 'Время действия',
                    'callback' => function ($queryBuilder, $alias, $field, $value) {
                        if (!isset($value['value'])) {
                            return;
                        }

                        switch ($value['value']) {
                            case 'past': {
                                $queryBuilder->andWhere(sprintf("%s.endsAt < :now", $alias));
                                break;
                            }
                            case 'active': {
                                $queryBuilder->andWhere(
                                    sprintf(":now BETWEEN %s.startsAt AND %s.endsAt", $alias, $alias)
                                );
                                break;
                            }
                            case 'future': {
                                $queryBuilder->andWhere(sprintf("%s.startsAt > :now", $alias));

                                break;
                            }
                        }
                        $queryBuilder->setParameter('now', new \DateTime());

                        return true;
                    },
                ),
                'choice',
                array(
                    'choices' => array(
                        'past' => 'Отработавшие',
                        'active' => 'Действующие',
                        'future' => 'Будущие',
                    ),
                )
            );
    }
}
