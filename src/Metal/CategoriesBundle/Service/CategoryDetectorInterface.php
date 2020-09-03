<?php

namespace Metal\CategoriesBundle\Service;

use Metal\CategoriesBundle\Entity\Category;

interface CategoryDetectorInterface
{
    /**
     * @param string $title
     *
     * @return Category|null
     */
    public function getCategoryByTitle($title);

    /**
     * @param string $text
     *
     * @return Category[]
     */
    public function getCategoriesByText($text);

    /**
     * @return integer
     */
    public function getDefaultCategoryId();
}
