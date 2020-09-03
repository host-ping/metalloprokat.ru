<?php

namespace Metal\PrivateOfficeBundle\Widget;

use Brouzie\Bundle\WidgetsBundle\Widget\WidgetAbstract;
use Metal\PrivateOfficeBundle\Form\UploadProductsImagesType;
use Metal\ProductsBundle\Entity\ProductImage;

class UploadProductsImagesWidget extends WidgetAbstract
{
    public function getParametersToRender()
    {
        $productImages = new ProductImage();
        $form = $this->createForm(new UploadProductsImagesType(), $productImages);

        return array(
            'form' => $form->createView()
        );
    }
}
