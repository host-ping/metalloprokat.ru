<?php

namespace Metal\ProductsBundle\ChangeSet;

class ProductsBatchEditChangeSet
{
    /**
     * @var array Products ids
     */
    public $productsToEnable = array();

    /**
     * @var array Products ids
     */
    public $productsToDisable = array();

    /**
     * @var array Structure of product_id: {old: old_category_id, new: new_category_id}
     */
    public $productsToChangeCategory = array();
}
