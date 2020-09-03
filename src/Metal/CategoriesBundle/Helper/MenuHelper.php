<?php

namespace Metal\CategoriesBundle\Helper;

use Brouzie\Bundle\HelpersBundle\Helper\HelperAbstract;
use Doctrine\ORM\EntityManager;
use Metal\CategoriesBundle\Entity\Category;
use Metal\CategoriesBundle\Entity\MenuItem;
use Metal\ProjectBundle\Helper\CounterHelper;

class MenuHelper extends HelperAbstract
{
    protected $cache = array();

    protected $countersCache = array();

    /**
     * @param Category $category
     *
     * @return MenuItem|null
     */
    public function getMenuItemForCategory(Category $category = null)
    {
        $cacheKey = $category ? $category->getId() : 0;
        if (array_key_exists($cacheKey, $this->cache)) {
            return $this->cache[$cacheKey];
        }

        $em = $this->container->get('doctrine')->getManager();
        /* @var $em EntityManager */
        $menuItemRepository = $em->getRepository('MetalCategoriesBundle:MenuItem');
        if ($category) {
            return $this->cache[$cacheKey] = $menuItemRepository->findOneBy(
                array('category' => $category, 'mode' => array(MenuItem::MODE_REFERENCE, MenuItem::MODE_VIRTUAL_REFERENCE))
            );
        }

        return $this->cache[$cacheKey] = null;
    }

    /**
     * @param MenuItem[] $menuItems Ключем должен быть MenuItem.id
     * @param string $counterName
     * @param array $counterConfig
     * @param callable $removeCallback
     */
    public function filterMenuItems($menuItems, $counterName, array $counterConfig, $removeCallback)
    {
        $this->attachCategoryCounterToMenuItem($menuItems, $counterName, $counterConfig);

        foreach ($menuItems as $menuItemId => $menuItem) {
            $shouldBeRemoved = true;
            if ($menuItem->isLabel() || $menuItem->isVirtualReference()) {
                $shouldBeRemoved = count($dependentMenuItemIds = $menuItem->getDependsFromMenuItemsIds()) > 0;

                foreach ($dependentMenuItemIds as $dependentMenuItemId) {
                    if ($menuItems[$dependentMenuItemId]->getAttribute($counterName)) {
                        $shouldBeRemoved = false;
                        break;
                    }
                }
            } elseif ($menuItem->getAttribute($counterName)) {
                $shouldBeRemoved = false;
            }

            if ($shouldBeRemoved) {
                $removeCallback($menuItemId);
            }
        }
    }

    /**
     * @param MenuItem[] $menuItems
     * @param $counterName
     * @param array $counterConfig
     */
    public function attachCategoryCounterToMenuItem($menuItems, $counterName, array $counterConfig)
    {
        if (!count($menuItems)) {
            return;
        }

        //TODO: меню нужно выгребать полностью за 1 запрос сразу с хлебными крошками и за 1 запрос фильтровать

        $cacheKey = sha1(serialize(array($counterName, $counterConfig)));

        if (!isset($this->countersCache[$cacheKey])) {
            $counterHelper = $this->getHelper('MetalProjectBundle:Counter');
            /* @var $counterHelper CounterHelper */
            $this->countersCache[$cacheKey] = $counterHelper->getItemsCountPerObject(
                $counterName,
                $counterConfig,
                'categories_ids'
            );
        }

        $categoriesToCounts = $this->countersCache[$cacheKey];

        foreach ($menuItems as $menuItem) {
            if ($category = $menuItem->getCategory()) {
                $categoryId = $category->getId();
                $itemsCount = isset($categoriesToCounts[$categoryId]) ? $categoriesToCounts[$categoryId] : 0;
                $menuItem->setAttribute($counterName, $itemsCount);
            }
        }
    }
}
