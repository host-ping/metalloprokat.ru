<?php

namespace Metal\CategoriesBundle\Widget;

use Brouzie\Bundle\WidgetsBundle\Widget\WidgetAbstract;
use Brouzie\WidgetsBundle\Cache\CacheKeyGenerator;
use Brouzie\WidgetsBundle\Cache\CacheProfile;
use Brouzie\WidgetsBundle\Widget\CacheableWidget;
use Doctrine\ORM\EntityManager;
use Metal\CategoriesBundle\Entity\Category;
use Metal\CategoriesBundle\Entity\MenuItem;
use Metal\CategoriesBundle\Helper\MenuHelper;
use Metal\ProjectBundle\DataFetching\DataFetcher;
use Metal\TerritorialBundle\Entity\City;
use Metal\TerritorialBundle\Entity\Country;
use Metal\TerritorialBundle\Entity\Region;

class MenuWidget extends WidgetAbstract implements CacheableWidget
{
    public function setDefaultOptions()
    {
        parent::setDefaultOptions();

        $this->optionsResolver
            ->setRequired(array('country', 'counter_name', 'route', '_template'))
            ->setDefaults(
                array(
                    'city' => null,
                    'region' => null,
                    'category' => null,
                    'route_params' => array(),
                    'display_empty_root_items' => true,
                    'items_count' => 0,
                    'active_tab' => null,
                )
            )
            ->setAllowedValues(
                'counter_name',
                array(
                    'products_count',
                    'companies_count',
                    'demands_count',
                    'catalog_products_count',
                    'manufacturers_count',
                    'brands_count'
                )
            )
            ->setAllowedTypes('category', array(Category::class, 'null'))
            ->setAllowedTypes('city', array(City::class, 'null'))
            ->setAllowedTypes('region', array(Region::class, 'null'))
            ->setAllowedTypes('country', array(Country::class))
        ;
    }

    public function getCacheProfile()
    {
        return new CacheProfile(new CacheKeyGenerator($this), DataFetcher::TTL_1DAY);
    }

    public function getParametersToRender()
    {
        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */
        $menuHelper = $this->container->get('brouzie.helper_factory')->get('MetalCategoriesBundle:Menu');
        /* @var $menuHelper MenuHelper */
        $menuItemRepository = $em->getRepository('MetalCategoriesBundle:MenuItem');

        $currentMenuItem = $menuHelper->getMenuItemForCategory($this->options['category']);
        $rootMenuItems = $menuItemRepository->getChildrenForItems($currentMenuItem);
        $childMenuItems = $menuItemRepository->getChildrenForItems($rootMenuItems);
        $menuItems = array_replace($rootMenuItems, $childMenuItems);
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
            function ($itemId) use (&$rootMenuItems, &$childMenuItems) {
                unset($rootMenuItems[$itemId], $childMenuItems[$itemId]);
            }
        );
        foreach ($menuItems as $itemId => $menuItem) {
            if ($menuItem->getHideIfNotActive()) {
                unset($rootMenuItems[$itemId], $childMenuItems[$itemId]);
            }
        }

        // 2. строим иерархию родитель -> дети
        foreach ($childMenuItems as $childMenuItem) {
            $parentId = $childMenuItem->getParent()->getId();
            if (isset($rootMenuItems[$parentId])) {
                $rootMenuItems[$parentId]->loadedChildren[] = $childMenuItem;
            }
        }

        // 3. прячем родительские элементы, в которых нет ни одного дочернего
        if (!$this->options['display_empty_root_items']) {
            $rootMenuItems = array_filter(
                $rootMenuItems,
                function (MenuItem $menuItem) {
                    return count($menuItem->loadedChildren) > 0;
                }
            );
        }

        return array('rootMenuItems' => $rootMenuItems);
    }
}
