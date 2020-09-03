<?php

namespace Metal\ProductsBundle\Widget;

use Brouzie\Bundle\WidgetsBundle\Widget\WidgetAbstract;

use Metal\ProductsBundle\Form\ProductsImportType;

class AddProductsFromFileWidget extends WidgetAbstract
{
    public function getParametersToRender()
    {
        $form = $this->createForm(new ProductsImportType(), null, array(
                'is_private_office' => true,
                'company_id' => $this->options['company_id'],
                'xls' => true,
                'yml' => true
            ));

        return array(
            'form' => $form->createView(),
        );
    }

    public function setDefaultOptions()
    {
        parent::setDefaultOptions();

        $this->optionsResolver
            ->setRequired(array('company_id'));
    }
}
