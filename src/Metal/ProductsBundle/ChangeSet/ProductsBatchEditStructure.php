<?php

namespace Metal\ProductsBundle\ChangeSet;

class ProductsBatchEditStructure
{
    /**
     * @var array Structure of Product.id: {id: Product.id, categoryId: Product.category.id, companyId: Product.company.id, title: Product.title, size: Product.size}
     */
    public $products = array();

    /**
     * @var array Structure of id Company.id: {cities: []}
     */
    public $companies = array();

    /**
     * @var array Structure of City.id: {countryId: Country.id}
     */
    public $cities = array();

    /**
     * @var array Structure of Category.id: {branchIds: (array)Category.branchIds}
     */
    public $categories = array();
}
