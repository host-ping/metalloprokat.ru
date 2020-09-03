<?php

namespace Metal\ProductsBundle\Widget;

use Brouzie\Bundle\WidgetsBundle\Widget\WidgetAbstract;
use Metal\CompaniesBundle\Entity\Company;
use Metal\ProductsBundle\Entity\Product;
use Metal\ProductsBundle\Form\ProductType;

class ProductFormWidget extends WidgetAbstract
{
    public function setDefaultOptions()
    {
        parent::setDefaultOptions();

        $this->optionsResolver
            ->setRequired(array('company'))
            ->setDefined(array('existing_product_editing'))
            ->setAllowedTypes('company', array(Company::class))
        ;
    }

    public function getParametersToRender()
    {
        $company = $this->options['company'];
        /* @var  $company Company */

        $options = array(
            'existing_product_editing' => $this->options['existing_product_editing'],
            'company_id' => $company->getId()
        );

        $form = $this->createForm(new ProductType(), new Product(), $options);

        return array(
            'form' => $form->createView(),
            'company' => $company
        );
    }
}
