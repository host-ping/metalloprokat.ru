<?php

namespace Metal\ProductsBundle\Widget;

use Brouzie\WidgetsBundle\Widget\TwigWidget;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductSidebarWidget extends TwigWidget
{
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver
            ->setRequired(['current_route', 'route_parameters'])
            ->setDefaults(['seo_text' => '']);
    }
}
