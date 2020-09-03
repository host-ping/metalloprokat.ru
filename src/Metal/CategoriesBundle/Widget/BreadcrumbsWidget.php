<?php

namespace Metal\CategoriesBundle\Widget;

use Brouzie\Bundle\WidgetsBundle\Widget\WidgetAbstract;

use Doctrine\ORM\EntityManager;

use Metal\CategoriesBundle\Entity\Category;
use Metal\CategoriesBundle\Entity\MenuItem;
use Metal\CategoriesBundle\Entity\MenuItemClosure;
use Metal\CategoriesBundle\Entity\PlainMenuItem;
use Metal\CategoriesBundle\Helper\MenuHelper;
use Metal\TerritorialBundle\Entity\City;
use Metal\TerritorialBundle\Entity\Country;
use Metal\TerritorialBundle\Entity\Region;

class BreadcrumbsWidget extends WidgetAbstract
{
    public function setDefaultOptions()
    {
        $this
            ->optionsResolver
            ->setRequired(array('route', 'homepage_route', 'counter_name', 'country'))
            ->setDefined(array('category', 'city', 'region'))
            ->setAllowedTypes('category', array(Category::class, 'null'))
            ->setAllowedTypes('city', array(City::class, 'null'))
            ->setAllowedTypes('region', array(Region::class, 'null'))
            ->setAllowedTypes('country', array(Country::class))
            ->setAllowedValues('counter_name', array('products_count', 'companies_count', 'demands_count', 'catalog_products_count'))
            ->setDefaults(
                array(
                    'route_params' => array(),
                    'homepage_route_params' => array(),
                    'append_items' => array(),
                )
            );
    }

    public function getParametersToRender()
    {
        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */
        $menuItemRepository = $em->getRepository('MetalCategoriesBundle:MenuItem');
        $menuHelper = $this->container->get('brouzie.helper_factory')->get('MetalCategoriesBundle:Menu');
        /* @var $menuHelper MenuHelper */

        $currentMenuItem = $menuHelper->getMenuItemForCategory($this->options['category']);
        if (!$currentMenuItem) {
            return array(
                'branch' => array(),
            );
        }

        $branchClosures = $em->createQueryBuilder()
            ->select('mic')
            ->from('MetalCategoriesBundle:MenuItemClosure', 'mic')
            ->join('mic.ancestor', 'anc')
            ->addSelect('anc')
            ->where('mic.descendant = :menu_item')
            ->setParameter('menu_item', $currentMenuItem)
            ->orderBy('mic.depth', 'DESC')
            ->getQuery()
            ->getResult();
        /* @var $branchClosures MenuItemClosure[] */

        $branch = array();
        $parentToMenuItem = array();
        foreach ($branchClosures as $branchClosure) {
            $menuItem = $branchClosure->getAncestor();
            $branch[$menuItem->getId()] = $menuItem;
            $parentToMenuItem[$menuItem->getParentId()] = $menuItem;
        }

        $siblings = $menuItemRepository->getSiblingsForBranch($branch);
        $rootItems = array_filter($siblings, function (MenuItem $menuItem) {
                return $menuItem->isRoot();
            });
        $childItems = $menuItemRepository->getChildrenForItems($rootItems);
        $menuItems = array_replace($siblings, $childItems);
        /* @var $menuItems MenuItem[] */

        $isDemandsPage = $this->options['counter_name'] === 'demands_count';

        // 1. удаляем элементы, в которых нет данных
        $filterCriteria = array('countries_ids' => $this->options['country']->getId());

        if ($this->options['city']) {
            $filterCriteria = array('cities_ids' => $this->options['city']->getId());
            if ($isDemandsPage) {
                $filterCriteria = array('city_id' => $this->options['city']->getId());
            }
        } elseif ($this->options['region']) {
            $filterCriteria = array('regions_ids' => $this->options['region']->getId());
            if ($isDemandsPage) {
                $filterCriteria = array('region_id' => $this->options['region']->getId());
            }
        }

        if ($this->options['counter_name'] === 'products_count') {
            $filterCriteria['is_virtual'] = false;
        }

        if (in_array($this->options['counter_name'], array('products_count', 'companies_count'))) {
            if ($this->options['city'] && $virtualCityId = City::getVirtualCityIdForCountry($this->options['country']->getId())) {
                $filterCriteria['cities_ids'] = array($virtualCityId, $this->options['city']->getId());
            } elseif ($this->options['region'] && $virtualRegionId = Region::getVirtualRegionIdForCountry($this->options['country']->getId())) {
                $filterCriteria['regions_ids'] = array($virtualRegionId, $this->options['region']->getId());
            }
        }

        $menuHelper->filterMenuItems(
            $menuItems,
            $this->options['counter_name'],
            $filterCriteria,
            function ($itemId) use (&$siblings, &$childItems) {
                unset($siblings[$itemId], $childItems[$itemId]);
            }
        );

        foreach ($menuItems as $itemId => $menuItem) {
            if ($menuItem->getHideIfNotActive() && !isset($branch[$itemId])) {
                unset($siblings[$itemId], $childItems[$itemId]);
            }
        }

        // 2. строим иерархию
        foreach ($siblings as $siblingMenuItem) {
            if (!isset($branch[$siblingMenuItem->getId()])) {
                $parentToMenuItem[$siblingMenuItem->getParentId()]->loadedSiblings[] = $siblingMenuItem;
            }
        }
        foreach ($childItems as $childItem) {
            $rootItems[$childItem->getParentId()]->loadedChildren[] = $childItem;
        }

        //TODO: продумать кеширование виджета

        // 3. удаляем соседей корневого элемента, у которых нет ни одного дочернего элемента
        $rootItem = reset($branch);
        $rootItem->loadedSiblings = array_filter(
            $rootItem->loadedSiblings,
            function (MenuItem $menuItem) {
                return count($menuItem->loadedChildren) > 0;
            }
        );

        foreach ($this->options['append_items'] as $appendItemRaw) {
            $appendItem = new PlainMenuItem();
            $appendItem->setId($appendItemRaw['id']);
            $appendItem->setTitle($appendItemRaw['title']);
            $appendItem->setIsLabel($appendItemRaw['is_label']);
            if (!empty($appendItemRaw['slug_combined'])) {
                $appendItem->setSlugCombined($appendItemRaw['slug_combined']);
            }

            if (!empty($appendItemRaw['siblings'])) {
                foreach ($appendItemRaw['siblings'] as $siblingItemRaw) {
                    $siblingItem = new PlainMenuItem();
                    $siblingItem->setTitle($siblingItemRaw['title']);
                    $siblingItem->setIsLabel($siblingItemRaw['is_label']);
                    if (!empty($siblingItemRaw['slug_combined'])) {
                        $siblingItem->setSlugCombined($siblingItemRaw['slug_combined']);
                    }

                    $appendItem->loadedSiblings[] = $siblingItem;
                }
            }

            $branch[] = $appendItem;
        }

        return array(
            'branch' => $branch,
        );
    }
}
