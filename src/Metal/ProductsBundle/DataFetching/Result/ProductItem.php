<?php

namespace Metal\ProductsBundle\DataFetching\Result;

use Metal\ProjectBundle\DataFetching\Result\Item;

class ProductItem implements Item
{
    public $id;

    public $companyProductsCount;

    public $companyAccess;

    public $companyCityId;

    public $visibilityStatus;

    public $companyId;

    public function __construct(
        int $id,
        ?int $companyProductsCount,
        int $companyAccess,
        int $companyCityId,
        int $visibilityStatus,
        int $companyId
    ) {
        $this->id = $id;
        $this->companyProductsCount = $companyProductsCount;
        $this->companyAccess = $companyAccess;
        $this->companyCityId = $companyCityId;
        $this->visibilityStatus = $visibilityStatus;
        $this->companyId = $companyId;
    }

    public function getId()
    {
        return $this->id;
    }
}
