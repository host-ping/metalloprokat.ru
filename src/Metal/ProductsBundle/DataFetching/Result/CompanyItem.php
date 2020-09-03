<?php

namespace Metal\ProductsBundle\DataFetching\Result;

use Metal\ProjectBundle\DataFetching\Result\Item;

class CompanyItem implements Item
{
    public $id;

    public $productId;

    public $productPrice;

    public $productUpdatedAt;

    public $productsCount;

    public function __construct(
        int $id,
        int $productId,
        float $productPrice,
        string $productUpdatedAt,
        int $productsCount
    ) {
        $this->id = $id;
        $this->productId = $productId;
        $this->productPrice = $productPrice;
        $this->productUpdatedAt = $productUpdatedAt;
        $this->productsCount = $productsCount;
    }

    public function getId()
    {
        return $this->id;
    }
}
