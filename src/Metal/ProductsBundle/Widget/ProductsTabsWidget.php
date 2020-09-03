<?php

namespace Metal\ProductsBundle\Widget;

use Brouzie\Bundle\WidgetsBundle\Widget\WidgetAbstract;
use Metal\CatalogBundle\DataFetching\Spec\CatalogProductFilteringSpec;
use Metal\ProductsBundle\DataFetching\Elastica\ProductIndex;
use Metal\ProductsBundle\DataFetching\Spec\ProductsFilteringSpec;
use Metal\ProjectBundle\DataFetching\DataFetcher;

class ProductsTabsWidget extends WidgetAbstract
{
    protected function setDefaultOptions()
    {
        parent::setDefaultOptions();

        $this->optionsResolver
            ->setDefaults(
                array(
                    'category' => true,
                    'counts' => array('products_count' => null, 'companies_count' => null),
                    'active_tab' => 'companies',
                    'display_sort' => true,
                    'current_route' => null,
                    'route_parameters' => null,
                )
            );
    }

    public function getParametersToRender()
    {
        $catalogEnabled = $this->container->getParameter('project.catalog_enabled');

        $routeProducts = 'MetalProductsBundle:Products:products_list';
        $routeCompanies = 'MetalProductsBundle:Products:companies_list';

        if ($this->options['category']) {
            $routeProducts = 'MetalProductsBundle:Products:list_category_subdomain';
            $routeCompanies = 'MetalProductsBundle:Products:companies_list_category_subdomain';
        }
        if (!$this->options['display_sort']) {
            $routeProducts = 'MetalProductsBundle:Products:products_list_without_sort';
            $routeCompanies = 'MetalProductsBundle:Products:companies_list_without_sort';
        }

        $tabs = array(
            'products' => array(
                'title' => 'Товары',
                'route' => $routeProducts,
                'counter_name' => 'products_count',
                'priority' => 150,
            ),
            'companies' => array(
                'title' => 'Компании',
                'route' => $routeCompanies,
                'counter_name' => 'companies_count',
                'priority' => 50,
            ),
        );

        // инициализируем те счетчики, которые не были загружены
        $this->getCounts();

        if ($catalogEnabled) {
            $criteria = CatalogProductFilteringSpec::createFromRequest($this->getRequest());

            $additionalCounts = $this->getCatalogCounts($criteria);

            if ($additionalCounts['catalog_products_count']) {
                $catalogRoutes = array(
                    'manufacturers' => 'MetalCatalogBundle:Manufacturers:manufacturers_list_without_sort',
                    'brands' => 'MetalCatalogBundle:Brands:brands_list_without_sort',
                    'catalog_products' => 'MetalCatalogBundle:Products:catalog_products_list_without_sort',
                );

                if ($this->options['category']) {
                    $catalogRoutes = array(
                        'manufacturers' => 'MetalCatalogBundle:Manufacturers:list_category_subdomain',
                        'brands' => 'MetalCatalogBundle:Brands:list_category_subdomain',
                        'catalog_products' => 'MetalCatalogBundle:Products:list_category_subdomain',
                    );
                }

                $this->options['counts'] = array_merge($this->options['counts'], $additionalCounts);

                $tabs = array_merge(
                    $tabs,
                    array(
                        'manufacturers' => array(
                            'title' => 'Производители',
                            'route' => $catalogRoutes['manufacturers'],
                            'counter_name' => 'manufacturers_count',
                            'priority' => 20,
                        ),
                        'brands' => array(
                            'title' => 'Бренды',
                            'route' => $catalogRoutes['brands'],
                            'counter_name' => 'brands_count',
                            'priority' => 15,
                        ),
                        'catalog_products' => array(
                            'title' => 'Продукты',
                            'route' => $catalogRoutes['catalog_products'],
                            'counter_name' => 'catalog_products_count',
                            'priority' => 10,
                        ),
                    )
                );
            }
        }

        if ($this->options['active_tab'] && isset($tabs[$this->options['active_tab']])) {
            $tabs[$this->options['active_tab']]['priority'] += 50;
        }

        uasort($tabs, function($a, $b) {
            return strnatcmp($b['priority'], $a['priority']);
        });

        $emptyTabsEnabled = $this->container->getParameter('project.empty_tabs_enabled');
        foreach ($tabs as $i => $tab) {
            $itemsCount = $this->options['counts'][$tab['counter_name']];

            if ($itemsCount || $emptyTabsEnabled) {
                $tabs[$i]['count'] = $itemsCount;
            } else {
                unset($tabs[$i]);
            }
        }

        return array('tabs' => $tabs);
    }

    public function getCounts()
    {
        $dataFetcher = $this->container
            ->get('metal_products.data_fetcher_factory')
            ->getDataFetcher(ProductIndex::SCOPE);

        if (null === $this->options['counts']['products_count']) {
            $productsSpecification = ProductsFilteringSpec::createFromRequest($this->getRequest());

            $count = $dataFetcher->getItemsCountByCriteria($productsSpecification, DataFetcher::TTL_1DAY);
            $this->options['counts']['products_count'] = $count;
        }

        if (null === $this->options['counts']['companies_count']) {
            $companiesSpecification = ProductsFilteringSpec::createFromRequest($this->getRequest())
                ->allowVirtual(true)
                ->loadCompanies(true);

            $count = $dataFetcher->getItemsCountByCriteria($companiesSpecification, DataFetcher::TTL_1DAY);
            $this->options['counts']['companies_count'] = $count;
        }

        return $this->options['counts'];
    }

    protected function getCatalogCounts(CatalogProductFilteringSpec $criteria)
    {
        $dataFetcher = $this->container->get('metal.catalog.data_fetcher');

        $brandsCriteria = clone $criteria;
        $manufacturersCriteria = clone $criteria;

        $counts = array(
            'catalog_products_count' => $dataFetcher->getItemsCountByCriteria($criteria, DataFetcher::TTL_1DAY),
            'brands_count' => $dataFetcher->getItemsCountByCriteria(
                $brandsCriteria->loadBrands(true),
                DataFetcher::TTL_1DAY
            ),
            'manufacturers_count' => $dataFetcher->getItemsCountByCriteria(
                $manufacturersCriteria->loadManufacturers(true),
                DataFetcher::TTL_1DAY
            ),
        );

        return $counts;
    }
}
