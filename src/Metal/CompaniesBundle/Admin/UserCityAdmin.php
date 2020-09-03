<?php

namespace Metal\CompaniesBundle\Admin;

use Doctrine\ORM\EntityRepository;
use Metal\CompaniesBundle\Entity\UserCity;
use Metal\TerritorialBundle\Entity\Country;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

class UserCityAdmin extends AbstractAdmin
{
    public function toString($object)
    {
        return $object instanceof UserCity ? $object->getCityTitle() : '';
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('user', null, array('label' => 'Компания', 'associated_property' => 'title'))
            ->add('city', null, array('label' => 'Город', 'associated_property' => 'title'))
            ->add('country', null, array('label' => 'Город', 'associated_property' => 'title'))
            ->add('isExcluded', null, array('label' => 'Исключен'));
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        static $i = -1;
        $i++;

        $regionsUrl = $this->routeGenerator->generate('MetalTerritorialBundle:Suggest:getRegions');
        $citiesUrl = $this->routeGenerator->generate('MetalTerritorialBundle:Suggest:getCities');

        $formMapper
            ->add(
                'cityTitle',
                'text',
                array(
                    'label' => 'Город',
                    'attr' => array(
                        'typeahead' => '',
                        'typeahead-prefetch-url' => $citiesUrl,
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
            ->add(
                'regionTitle',
                'text',
                array(
                    'label' => 'Область',
                    'attr' => array(
                        'typeahead' => '',
                        'typeahead-prefetch-url' => $regionsUrl,
                        'typeahead-suggestion-template-url' => "'typeahead-suggestion-with-parent'",
                        'typeahead-model' => "regions$i",
                    ),
                )
            )
            ->add(
                'region',
                'entity_id',
                array(
                    'class' => 'MetalTerritorialBundle:Region',
                    'label' => 'ID области',
                    'hidden' => false,
                    'read_only' => true,
                    'required' => false,
                    'attr' => array(
                        'ng-model' => "regions$i.id",
                        'initial-value' => '',
                    ),
                )
            )
            ->add(
                'phone',
                'text',
                array(
                    'label' => 'Телефон',
                    'required' => false,
                )
            )
            ->add(
                'country',
                null,
                array(
                    'property' => 'title',
                    'label' => 'Страна',
                    'query_builder' => function (EntityRepository $repository) {
                        return $repository
                            ->createQueryBuilder('c')
                            ->andWhere('c.id IN (:ids)')
                            ->setParameter('ids', Country::getEnabledCountriesIds());
                    },
                )
            )
            ->add('isExcluded', null, array('label' => 'Исключен'));
    }
}
