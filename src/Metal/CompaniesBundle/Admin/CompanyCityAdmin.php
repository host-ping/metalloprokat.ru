<?php

namespace Metal\CompaniesBundle\Admin;

use Metal\CompaniesBundle\Entity\CompanyCity;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

class CompanyCityAdmin extends AbstractAdmin
{
    protected $datagridValues = array(
        '_sort_order' => 'ASC',
        '_sort_by' => 'kind'
    );

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('company', null, array('label' => 'Компания', 'associated_property' => 'title'))
            ->add('city', null, array('label' => 'Город', 'associated_property' => 'title'))
            ->add('address', null, array('label' => 'Адрес'))
            ->add('email', null, array('label' => 'Email'))
            ->add('displayPosition', null, array('label' => 'Порядок вывода на витрине'));
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        static $i = -1;
        $i++;

        //TODO: добавить поддержку телефонов когда https://github.com/sonata-project/SonataAdminBundle/pull/2985 будет реализовано

        $prefetchUrl = $this->routeGenerator->generate('MetalTerritorialBundle:Suggest:getAllCities');
        $formMapper
            ->add(
                'cityTitle',
                'text',
                array(
                    'label' => 'Город',
                    'attr' => array(
                        'typeahead' => '',
                        'typeahead-prefetch-url' => $prefetchUrl,
                        'typeahead-suggestion-template-url' => "'typeahead-suggestion-with-parent'",
                        'typeahead-model' => "cities$i",
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
                        'ng-model' => "cities$i.id",
                        'initial-value' => '',
                    ),
                )
            )
            ->add('site', 'url', array('label' => 'Сайт', 'required' => false))
            ->add('address', null, array('label' => 'Адрес', 'required' => false))
            ->add('latitude', null, array('label' => 'Широта', 'precision' => 6))
            ->add('longitude', null, array('label' => 'Долгота', 'precision' => 6))
            ->add('description', 'textarea', array('label' => 'Описание', 'required' => false, 'attr' => array('style' => 'width: 550px;', 'rows' => '5')))
            ->add(
                'isAssociatedWithCityCode',
                'checkbox',
                array(
                    'label' => 'Проасоциирован с кодом города',
                    'read_only' => true,
                    'disabled' => true
                )
            )
            ->add(
                'displayPosition',
                null,
                array(
                    'label' => 'Порядок вывода',
                    'attr' => array('style' => 'width: 100px;')
                )
            )
            ->add(
                'enabled',
                'checkbox',
                array(
                    'label' => 'Включен',
                )
            )
           ;
    }

    public function toString($object)
    {
        return $object instanceof CompanyCity ? $object->getCityTitle() : '';
    }
}
