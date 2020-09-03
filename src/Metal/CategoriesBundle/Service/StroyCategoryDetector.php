<?php

namespace Metal\CategoriesBundle\Service;

class StroyCategoryDetector extends ProductCategoryDetector
{
    const DEFAULT_CATEGORY_ID = 1021;

    public function getDefaultCategoryId()
    {
        return self::DEFAULT_CATEGORY_ID;
    }
}
