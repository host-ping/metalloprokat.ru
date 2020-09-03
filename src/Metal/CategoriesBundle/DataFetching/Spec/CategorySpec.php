<?php

namespace Metal\CategoriesBundle\DataFetching\Spec;

use Metal\CategoriesBundle\Entity\Category;
use Metal\CategoriesBundle\Entity\CategoryAbstract;
use Metal\CompaniesBundle\Entity\CustomCompanyCategory;

trait CategorySpec
{
    public $categoryId;
    public $customCompanyCategoryId;
    public $concreteCategoryId;

    public function categoryId($categoryId)
    {
        $this->categoryId = $categoryId;

        return $this;
    }

    public function category(CategoryAbstract $category = null)
    {
        if ($category instanceof Category) {
            $this->categoryId($category->getRealCategoryId());
        } elseif ($category instanceof CustomCompanyCategory) {
            $this->customCompanyCategoryId = $category->getId();
        }

        return $this;
    }

    public function concreteCategoryId($categoryId)
    {
        $this->concreteCategoryId = $categoryId;

        return $this;
    }

    public function concreteCategory(Category $category = null)
    {
        if ($category) {
            $this->concreteCategoryId($category->getRealCategoryId());
        }

        return $this;
    }
}
