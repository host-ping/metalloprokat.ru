<?php

namespace Metal\CategoriesBundle\Admin;

use Metal\CategoriesBundle\Entity\CategoryCityMetadata;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Form\FormMapper;

class CategoryCityMetadataAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        static $i = -1;
        $i++;

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
                        'hidden' => true,
                    ),
                )
            )
            ->add('description', 'textarea', array('label' => 'Описание', 'required' => false))
            ->add(
                '_h1Title',
                'text',
                array('label' => 'H1 Заголовок', 'required' => false, 'property_path' => 'metadata.h1Title')
            )
            ->add(
                '_metadataTitle',
                'textarea',
                array('label' => 'Meta заголовок', 'required' => false, 'property_path' => 'metadata.title')
            )
            ->add(
                '_metadataDescription',
                'textarea',
                array('label' => 'Meta описание', 'required' => false, 'property_path' => 'metadata.description')
            );
    }

    public function toString($object)
    {
        return $object instanceof CategoryCityMetadata ? $object->getCategoryTitle() : '';
    }
}
