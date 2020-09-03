<?php

namespace Metal\TerritorialBundle\Admin;

use Metal\TerritorialBundle\Entity\CityCode;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

class CityCodeAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('code', null, array(
                'label' => 'Код города',
                'required' => true,
                ))
            ->add('defaultCityTitle', null, array('label' => 'Ожидаемый город'))
            ->add(
                'cityTitle',
                'text',
                array(
                    'label' => 'Город',
                    'attr' => array(
                        'typeahead' => '',
                        'typeahead-prefetch-url' => $this->routeGenerator->generate('MetalTerritorialBundle:Suggest:getAllCities'),
                        'typeahead-suggestion-template-url' => "'typeahead-suggestion-with-parent'",
                        'typeahead-model' => 'city'
                    ),
                    'required' => false,
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
            );
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('city', null, array('label' => 'Город', 'associated_property' => 'title'))
            ->add('code', null, array('label' => 'Код города'))
            ->add('defaultCityTitle', null, array('label' => 'Ожидаемый город'));
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('code', null, array('label' => 'Код города'))
            ->add('defaultCityTitle', null, array('label' => 'Ожидаемый город'))
            ->add(
                'city',
                'doctrine_orm_callback',
                array(
                    'label' => 'Необработанные города',
                    'callback' => function ($queryBuilder, $alias, $field, $value) {
                        if (!isset($value['value'])) {
                            return;
                        }
                        if ($value['value'] == 'y') {
                            $queryBuilder
                                ->andWhere(sprintf('%s.city IS NULL', $alias));
                        }

                        return true;
                    }
                ),
                'choice',
                array(
                    'choices' => array(
                        'y' => 'Показать необработанные города'
                    )
                )
            );
    }

    public function toString($object)
    {
        return $object instanceof CityCode ? $object->getDefaultCityTitle() : '';
    }
}
