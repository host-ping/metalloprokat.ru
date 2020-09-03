<?php

namespace Metal\PrivateOfficeBundle\Widget;

use Brouzie\WidgetsBundle\Widget\TwigWidget;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductCategoryWidget extends TwigWidget
{
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver
            ->setRequired(array('_template'))
            ->setDefined(array('categories', 'widget_id', 'ng_click'));
    }
}
