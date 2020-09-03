<?php

namespace Metal\CatalogBundle\EventListener;

use Doctrine\ORM\EntityManager;
use Metal\AttributesBundle\DataFetching\AttributesFacetResult;
use Metal\AttributesBundle\Entity\AttributeValue;
use Metal\CatalogBundle\DataFetching\Spec\CatalogProductFacetSpec;
use Metal\CatalogBundle\DataFetching\Spec\CatalogProductFilteringSpec;
use Metal\CategoriesBundle\Entity\Category;
use Metal\ProjectBundle\DataFetching\Sphinxy\FacetResultExtractor;
use Metal\ProjectBundle\DataFetching\Sphinxy\SphinxyDataFetcher;
use Metal\ProjectBundle\Repository\SiteRepository;
use Presta\SitemapBundle\Event\SitemapPopulateEvent;
use Presta\SitemapBundle\Service\SitemapListenerInterface;
use Presta\SitemapBundle\Sitemap\Url\UrlConcrete;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;

class SitemapListener implements SitemapListenerInterface
{
    private $router;

    private $em;

    /**
     * @var Request
     */
    private $request;

    private $hostnameMap;

    private $productDataFetcher;

    private $baseHost;

    private $catalogEnabled;

    private $section = '';

    public function __construct(RouterInterface $router, EntityManager $em, $hostnameMap, $baseHost, $catalogEnabled, SphinxyDataFetcher $productDataFetcher)
    {
        $this->router = $router;
        $this->em = $em;
        $this->hostnameMap = $hostnameMap;
        $this->baseHost = $baseHost;
        $this->catalogEnabled = $catalogEnabled;
        $this->productDataFetcher = $productDataFetcher;
    }

    public function setRequest(Request $request = null)
    {
        $this->request = $request;
    }

    public function populateSitemap(SitemapPopulateEvent $event)
    {
        if (!$this->catalogEnabled) {
            return;
        }

        list($subdomain, $domain) = explode('.', $this->request->getHost(), 2);
        preg_match('/^(\w+)\./ui', $this->baseHost, $matches);
        $this->section = $matches[1];

        if (0 !== strpos($domain, $this->section)) {
            return;
        }

        $siteRepository = $this->em->getRepository('MetalProjectBundle:Site');
        /* @var $siteRepository SiteRepository */

        $siteRepository->disableLogging();
        $countryId = null;

        foreach ($this->hostnameMap as $host => $hostInfo) {
            if (false !== strpos($host, $domain)) {
                $countryId = $hostInfo['country_id'];
            }
        }

        $now = new \DateTime();

        if ($subdomain === 'www') {
            $this->buildCategoriesInList($event, $now, $countryId);

            $this->buildProducts($event, $countryId);

            $this->buildBrands($event, $now, $countryId);

            $this->buildManufacturers($event, $now, $countryId);
        }

        $siteRepository->restoreLogging();
    }


    private function buildProducts(SitemapPopulateEvent $event, $countryId)
    {
        $productCityRepository = $this->em->getRepository('MetalCatalogBundle:ProductCity');

        $products = $productCityRepository->createQueryBuilder('pc')
            ->select('p.id, p.updatedAt, c.slugCombined')
            ->join('pc.product', 'p')
            ->join('pc.city', 'city')
            ->join('p.category', 'c')
            ->where('city.country = :country_id')
            ->setParameter('country_id', $countryId)
            ->groupBy('p.id')
            ->getQuery()
            ->iterate();

        $batchSize = 1000;
        $i = 0;
        foreach ($products as $row) {
            $product = current($row);
            $event->getGenerator()->addUrl(
                new UrlConcrete(
                    $this->router->generate('MetalCatalogBundle:Product:view',  array('id' => $product['id'], 'subdomain' => 'www', 'category_slug' => $product['slugCombined']), true),
                    $product['updatedAt'],
                    UrlConcrete::CHANGEFREQ_WEEKLY,
                    0.7
                ),
                $this->section
            );
            if (($i % $batchSize) === 0) {
                $this->em->clear();
            }
            $i++;
        }
        $this->em->clear();

    }

    private function buildBrands(SitemapPopulateEvent $event, $now, $countryId)
    {
        $brandCityRepository = $this->em->getRepository('MetalCatalogBundle:BrandCity');

        $brands = $brandCityRepository->createQueryBuilder('bc')
            ->select('b.slug')
            ->join('bc.brand', 'b')
            ->join('bc.city', 'city')
            ->where('city.country = :country_id')
            ->setParameter('country_id', $countryId)
            ->groupBy('b.id')
            ->getQuery()
            ->iterate();

        $batchSize = 1000;
        $i = 0;
        foreach ($brands as $row) {
            $brand = current($row);
            $event->getGenerator()->addUrl(
                new UrlConcrete(
                    $this->router->generate('MetalCatalogBundle:Brand:view',  array('slug' => $brand['slug']), true),
                    $this->randomizeDate($now, 'hours'),
                    UrlConcrete::CHANGEFREQ_WEEKLY,
                    0.7
                ),
                $this->section
            );
            if (($i % $batchSize) === 0) {
                $this->em->clear();
            }
            $i++;
        }
        $this->em->clear();
    }

    private function buildManufacturers(SitemapPopulateEvent $event, $now, $countryId)
    {
        $manufacturerCityRepository = $this->em->getRepository('MetalCatalogBundle:ManufacturerCity');

        $manufacturers = $manufacturerCityRepository->createQueryBuilder('mc')
            ->select('m.slug')
            ->join('mc.manufacturer', 'm')
            ->join('mc.city', 'city')
            ->where('city.country = :country_id')
            ->setParameter('country_id', $countryId)
            ->groupBy('m.id')
            ->getQuery()
            ->iterate();

        $batchSize = 1000;
        $i = 0;
        foreach ($manufacturers as $row) {
            $manufacturer = current($row);
            $event->getGenerator()->addUrl(
                new UrlConcrete(
                    $this->router->generate('MetalCatalogBundle:Manufacturer:view',  array('slug' => $manufacturer['slug']), true),
                    $this->randomizeDate($now, 'hours'),
                    UrlConcrete::CHANGEFREQ_WEEKLY,
                    0.7
                ),
                $this->section
            );
            if (($i % $batchSize) === 0) {
                $this->em->clear();
            }
            $i++;
        }
        $this->em->clear();

    }

    private function buildCategoriesInList(SitemapPopulateEvent $event, $now, $countryId)
    {
        $specification = (new CatalogProductFilteringSpec())->countryId($countryId);

        $facetSpec = new CatalogProductFacetSpec();
        $facetSpec->facetByCategory($specification);

        $facetsResultSet = $this->productDataFetcher->getFacetedResultSetByCriteria($specification, $facetSpec);
        $facetResultExtractor = new FacetResultExtractor($facetsResultSet, CatalogProductFacetSpec::COLUMN_CATEGORY_ID);

        $categories = $this->em->getRepository('MetalCategoriesBundle:Category')->findBy(array('isEnabled' => true, 'id' => $facetResultExtractor->getIds()));

        $routes = array(
            'MetalCatalogBundle:Products:list_category_subdomain',
            'MetalCatalogBundle:Brands:list_category_subdomain',
            'MetalCatalogBundle:Manufacturers:list_category_subdomain'
        );

        foreach ($categories as $category) {
            /* @var $category Category */

            foreach ($routes as $route) {
                $event->getGenerator()->addUrl(
                    new UrlConcrete(
                        $this->router->generate($route, array('category_slug' => $category->getSlugCombined()), true),
                        $this->randomizeDate($now, 'hours'),
                        UrlConcrete::CHANGEFREQ_DAILY,
                        0.8
                    ),
                    $this->section
                );
            }

            $attributes = $this->em->getRepository('MetalAttributesBundle:AttributeCategory')
                ->getAttributesForCategory($category);

            $facetSpec = new CatalogProductFacetSpec();
            $specification->category($category);
            foreach ($attributes as $attribute) {
                $facetSpec->facetByAttribute($attribute, $specification);
            }

            $facetsResultSet = $this->productDataFetcher->getFacetedResultSetByCriteria($specification, $facetSpec);

            $attributeValueRepository = $this->em->getRepository('MetalAttributesBundle:AttributeValue');
            $attributesCollection = $attributeValueRepository->loadCollectionByFacetResult(
                new AttributesFacetResult($facetsResultSet, $attributes),
                array(AttributeValue::ORDER_OUTPUT_PRIORITY)
            );

            if (!count($attributesCollection)) {
                continue;
            }

            foreach ($attributesCollection->getAttributesValues() as $attributeValue) {
                foreach ($routes as $route) {
                    $event->getGenerator()->addUrl(
                        new UrlConcrete(
                            $this->router->generate($route, array('category_slug' => $category->getUrl($attributeValue->getSlug())), true),
                            $this->randomizeDate($now, 'minutes'),
                            UrlConcrete::CHANGEFREQ_DAILY,
                            0.7
                        ),
                        $this->section
                    );
                }
            }
        }
    }

    private function randomizeDate(\DateTime $date, $dimension)
    {
        $newDate = clone $date;
        $newDate->modify(sprintf('%d %s', mt_rand(-10, 10), $dimension));

        return $newDate;
    }
}
