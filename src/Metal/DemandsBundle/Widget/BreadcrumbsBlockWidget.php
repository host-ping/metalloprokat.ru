<?php

namespace Metal\DemandsBundle\Widget;

use Brouzie\WidgetsBundle\Cache\CacheKeyGenerator;
use Brouzie\WidgetsBundle\Cache\CacheProfile;
use Brouzie\WidgetsBundle\Widget\CacheableWidget;
use Brouzie\WidgetsBundle\Widget\TwigWidget;
use Doctrine\ORM\EntityManager;
use Metal\CategoriesBundle\Entity\Category;
use Metal\ProductsBundle\DataFetching\Spec\ProductsFacetSpec;
use Metal\ProductsBundle\DataFetching\Spec\ProductsFilteringSpec;
use Metal\ProjectBundle\DataFetching\DataFetcher;
use Metal\ProjectBundle\DataFetching\Sphinxy\FacetResultExtractor;
use Metal\TerritorialBundle\Entity\TerritoryInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BreadcrumbsBlockWidget extends TwigWidget implements ContainerAwareInterface, CacheableWidget
{
    use ContainerAwareTrait;

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver
            ->setRequired(array('category', 'route_name', 'territory'))
            ->setAllowedTypes('category', Category::class)
            ->setAllowedTypes('territory', TerritoryInterface::class);
    }

    public function getContext()
    {
        $category = $this->options['category'];
        /* @var $category Category */

        $specification = (new ProductsFilteringSpec())
            ->category($category)
            ->territory($this->options['territory']);

        $facetSpecification = new ProductsFacetSpec();
        $facetSpecification->facetByCategories($specification);

        $dataFetcher = $this->container->get('metal.products.data_fetcher');
        $facetsResultSet = $dataFetcher
            ->getFacetedResultSetByCriteria($specification, $facetSpecification, DataFetcher::TTL_1DAY);

        $facetResultExtractor = new FacetResultExtractor($facetsResultSet, ProductsFacetSpec::COLUMN_CATEGORIES_IDS);
        $currentCategoriesWithCounts = array_intersect($facetResultExtractor->getIds(), $category->getBranchIds());

        $em = $this->container->get('doctrine.orm.default_entity_manager');
        /* @var $em EntityManager */

        $categories = array();
        if ($currentCategoriesWithCounts) {
            $categories = $em->getRepository('MetalCategoriesBundle:Category')
                ->createQueryBuilder('c')
                ->addSelect('MAX(cc.depth) AS HIDDEN _max')
                ->join('MetalCategoriesBundle:CategoryClosure', 'cc', 'WITH', 'cc.descendant = c.id')
                ->where('c.id IN (:categories_ids)')
                ->setParameter('categories_ids', $currentCategoriesWithCounts)
                ->groupBy('cc.descendant')
                ->orderBy('_max', 'DESC')
                ->getQuery()
                ->getResult();
        }

        return compact('categories');
    }

    public function getCacheProfile()
    {
        return new CacheProfile(new CacheKeyGenerator($this), DataFetcher::TTL_1DAY);
    }
}
