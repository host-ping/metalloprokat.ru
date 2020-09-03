<?php

namespace Metal\CatalogBundle\Helper;

use Brouzie\Bundle\HelpersBundle\Helper\HelperAbstract;
use Metal\CatalogBundle\Entity\Product;

class BreadcrumbsHelper extends HelperAbstract
{
    public function getBreadcrumbsForProduct(Product $product)
    {
        $productBreadcrumb = array(
            'id' => $product->getId(),
            'title' => $product->getTitle(),
            'is_label' => true,
        );

        return array($productBreadcrumb);
    }
}