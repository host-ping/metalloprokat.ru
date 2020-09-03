<?php

namespace Metal\CategoriesBundle\Entity;

interface MenuItemInterface
{
    public function getId();

    public function getTitle();

    public function getSlugCombined();

    public function isLabel();

    /**
     * @return MenuItemInterface[]
     */
    public function getLoadedChildren();

    /**
     * @return MenuItemInterface[]
     */
    public function getLoadedSiblings();
}
