<?php

namespace Metal\CatalogBundle\DataFetching\Spec;

use Metal\ProjectBundle\DataFetching\Spec\LoadingSpec;

class CatalogProductsLoadingSpec implements LoadingSpec
{
    public $attachBrands = true;

    public $attachManufacturers = true;

    public $loadProductsCountPerBrand = false;

    public function attachBrands($attach)
    {
        $this->attachBrands = $attach;

        return $this;
    }

    public function attachManufacturers($attach)
    {
        $this->attachManufacturers = $attach;

        return $this;
    }

    public function loadProductsCountPerBrand($load)
    {
        $this->loadProductsCountPerBrand = $load;

        return $this;
    }
}
