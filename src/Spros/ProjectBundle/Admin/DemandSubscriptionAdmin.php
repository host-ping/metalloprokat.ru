<?php

namespace Spros\ProjectBundle\Admin;

use Doctrine\ORM\EntityManager;
use Spros\ProjectBundle\Entity\DemandSubscription;
use Metal\TerritorialBundle\Repository\CityRepository;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class DemandSubscriptionAdmin extends AbstractAdmin
{
    protected $datagridValues = array(
        '_sort_order' => 'DESC',
        '_sort_by' => 'createdAt',
    );

    /**
     * @var CityRepository
     */
    private $cityRepository;

    public function __construct($code, $class, $baseControllerName, EntityManager $em)
    {
        parent::__construct($code, $class, $baseControllerName);

        $this->cityRepository = $em->getRepository('MetalTerritorialBundle:City');
    }

    public function getSubject()
    {
        parent::getSubject();

        if ($this->subject && $this->subject->getCity()) {
            $this->subject->cityTitle = $this->subject->getCity()->getTitle();
        }

        return $this->subject;
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('email', null, array(
                'label' => 'Email пользователя',
                'required' => true,
            ))
            ->add(
                'cityTitle',
                'text',
                array(
                    'label' => 'Город',
                    'required' => false,
                    'attr' => array(
                        'typeahead' => '',
                        'typeahead-prefetch-url' => $this->routeGenerator->generate(
                            'MetalTerritorialBundle:Suggest:getCities'
                        ),
                        'typeahead-suggestion-template-url' => "'typeahead-suggestion-with-parent'",
                        'typeahead-model' => 'city',

                    ),
                )
            )
            ->add(
                'city',
                'entity_id',
                array(
                    'class' => 'MetalTerritorialBundle:City',
                    'label' => 'ID города',
                    'hidden' => false,
                    'read_only' => true,
                    'required' => false,
                    'attr' => array(
                        'ng-model' => 'city.id',
                        'initial-value' => '',
                    ),
                )
            )
            ->add(
                'category',
                null,
                array(
                    'label' => 'Категория',
                    'property' => 'title',
                )
            );
    }

    public function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('delete');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('city', null, array('label' => 'Город', 'associated_property' => 'title'))
            ->add(
                'category',
                null,
                array(
                    'label' => 'Раздел',
                    'template' => 'MetalDemandsBundle:DemandAdmin:title.html.twig',
                )
            )
            ->add('ip', null, array('label' => 'Ip'))
            ->add('email', null, array('label' => 'Email пользователя'))
            ->add('createdAt', null, array('label' => 'Дата подачи заявки'))
            ->add('confirmedAt', null, array('label' => 'Дата подтверждения'))
            ->add('unsubscribedAt', null, array('label' => 'Дата отписки'));
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('email', null, array('label' => 'Email'))
            ->add('ip', null, array('label' => 'Ip'))
            ->add(
                'confirmed',
                'doctrine_orm_callback',
                array(
                    'callback' => function ($queryBuilder, $alias, $field, $value) {
                        if (!isset($value['value'])) {
                            return;
                        }

                        if ($value['value'] === 'y') {
                            $queryBuilder->andWhere(sprintf('%s.confirmedAt IS NOT NULL', $alias));
                        } else {
                            $queryBuilder->andWhere(sprintf('%s.confirmedAt IS NULL', $alias));
                        }

                        return true;
                    },
                    'field_type' => 'choice',

                ),
                null,
                array(
                    'choices' => array(
                        'y' => 'Подтвержден',
                        'n' => 'Не подтвержден',
                    ),
                )
            )
            ->add(
                'unsubscribed',
                'doctrine_orm_callback',
                array(
                    'callback' => function ($queryBuilder, $alias, $field, $value) {
                        if (!isset($value['value'])) {
                            return;
                        }

                        if ($value['value'] === 'y') {
                            $queryBuilder->andWhere(sprintf('%s.unsubscribedAt IS NOT NULL', $alias));
                        } else {
                            $queryBuilder->andWhere(sprintf('%s.unsubscribedAt IS NULL', $alias));
                        }

                        return true;
                    },
                    'field_type' => 'choice',

                ),
                null,
                array(
                    'choices' => array(
                        'y' => 'Отписался',
                        'n' => 'Не отписался',
                    ),
                )
            )
            ->add(
                'city',
                'doctrine_orm_choice',
                array(
                    'label' => 'Город',
                    'property' => 'title',
                ),
                'choice',
                array(
                    'choices' => $this->cityRepository->getSimpleCitiesArray(),
                )
            );
    }

    public function toString($object)
    {
        return $object instanceof DemandSubscription ? $object->getEmail() : '';
    }
}
