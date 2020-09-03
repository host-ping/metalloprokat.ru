<?php

namespace Metal\CatalogBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Form\FormMapper;

class ProductAttributeValueAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        static $i = -1;
        $i++;

        //FIXME: рыба. нужно по другому строить атрибуты
        $prefetchUrl = $this->routeGenerator->generate('MetalTerritorialBundle:Suggest:getAllCities');
        $formMapper
            ->add(
                'attributeValueTitle',
                'text',
                array(
                    'label' => 'Атрибут',
                    'attr' => array(
                        'typeahead' => '',
                        'typeahead-prefetch-url' => $prefetchUrl,
                        'typeahead-suggestion-template-url' => "'typeahead-suggestion-with-parent'",
                        'typeahead-model' => "cities$i",
                    ),
                )
            )
            ->add(
                'attributeValue',
                'entity_id',
                array(
                    'class' => 'MetalAttributesBundle:AttributeValue',
                    'label' => 'ID атрибута',
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
