<?php

namespace Metal\CatalogBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Form\FormMapper;

class ProductCityAdmin extends AbstractAdmin
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
                    ),
                )
            )
        ;
    }

}
