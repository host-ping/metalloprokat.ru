<?php

namespace Metal\CatalogBundle\Widget;

use Brouzie\Bundle\WidgetsBundle\Widget\WidgetAbstract;
use Metal\CatalogBundle\Entity\Product;
use Metal\CatalogBundle\Entity\ProductReview;
use Metal\ProjectBundle\Form\ReviewType;

class ProductReviewFormWidget extends WidgetAbstract
{
    public function setDefaultOptions()
    {
        parent::setDefaultOptions();

        $this->optionsResolver
            ->setDefined(array('product'))
            ->setAllowedTypes('product', array(Product::class, 'null'))
        ;
    }

    public function getParametersToRender()
    {
        $form = $this->createForm(
            new ReviewType(),
            new ProductReview(),
            array(
                'is_authenticated' => $this->isGranted('ROLE_USER'),
                'validation_groups' => array(
                    $this->isGranted('ROLE_USER') ? 'authenticated' : 'anonymous',
                ),
                'data_class' => ProductReview::class,
            )
        );

        return array(
            'form' => $form->createView(),
            'product' => $this->options['product'],
        );
    }
}
