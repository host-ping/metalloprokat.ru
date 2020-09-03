<?php

namespace Metal\ProjectBundle\EventListener;

use Brouzie\Bundle\HelpersBundle\Helper\HelperFactory;
use Doctrine\ORM\EntityManager;

use Metal\AttributesBundle\DataFetching\AttributesFacetResult;
use Metal\AttributesBundle\Entity\Attribute;
use Metal\AttributesBundle\Entity\AttributeValue;
use Metal\CategoriesBundle\Entity\Category;
use Metal\DemandsBundle\DataFetching\Spec\DemandFacetSpec;
use Metal\DemandsBundle\DataFetching\Spec\DemandFilteringSpec;
use Metal\DemandsBundle\Entity\Demand;
use Metal\ProductsBundle\DataFetching\Spec\ProductsFacetSpec;
use Metal\ProductsBundle\DataFetching\Spec\ProductsFilteringSpec;
use Metal\ProductsBundle\Entity\Product;
use Metal\ProjectBundle\DataFetching\Sphinxy\FacetResultExtractor;
use Metal\ProjectBundle\DataFetching\Sphinxy\SphinxyDataFetcher;
use Metal\ProjectBundle\Doctrine\Utils;
use Metal\ProjectBundle\Repository\SiteRepository;
use Metal\TerritorialBundle\Entity\City;

use Presta\SitemapBundle\Event\SitemapPopulateEvent;
use Presta\SitemapBundle\Service\SitemapListenerInterface;
use Presta\SitemapBundle\Sitemap\Url\UrlConcrete;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;

class SitemapListener implements SitemapListenerInterface
{
    private $router;

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var Request
     */
    private $request;

    private $hostnameMap;

    /**
     * @var HelperFactory
     */
    private $helperFactory;

    private $baseHost;

    private $productDataFetcher;

    private $demandDataFetcher;

    private $projectFamily;

    private $section = '';

    public function __construct(RouterInterface $router, EntityManager $em, HelperFactory $helperFactory, SphinxyDataFetcher $productDataFetcher, SphinxyDataFetcher $demandDataFetcher, $hostnameMap, $baseHost, $projectFamily)
    {
        $this->router = $router;
        $this->em = $em;
        $this->helperFactory = $helperFactory;
        $this->hostnameMap = $hostnameMap;
        $this->baseHost = $baseHost;
        $this->productDataFetcher = $productDataFetcher;
        $this->demandDataFetcher = $demandDataFetcher;
        $this->projectFamily = $projectFamily;
    }

    public function setRequest(Request $request = null)
    {
        $this->request = $request;
    }

    public function populateSitemap(SitemapPopulateEvent $event)
    {
        if ($this->projectFamily === 'stroy') {
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

        $city = null;
        $cities = array();
        if ($subdomain !== 'www') {
            $cityRepo = $this->em->getRepository('MetalTerritorialBundle:City');
            $city = $cityRepo->findOneBy(array('slug' => $subdomain));
            $cities = $cityRepo->getWithLinkedCities($city);
        }

        $routeParameters = array();
        $now = new \DateTime();

        if ($city) {
            $routeParameters['subdomain'] = $city->getSlug();
        }

        $this->buildMainPage($event, $routeParameters, $now);

        $this->buildCategories($event, $routeParameters, $now, $city, $countryId);

        $this->buildCategoriesInDemands($event, $routeParameters, $now, $city, $countryId);

        if ($city) {
            $this->buildDemands($event, $city, $cities);
            $this->buildProducts($event, $city, $cities);
            $this->buildAllProducts($event, $city);
        }

        $siteRepository->restoreLogging();
    }

    private function buildMainPage(SitemapPopulateEvent $event, $routeParameters, $now)
    {
        $event->getGenerator()->addUrl(
            new UrlConcrete(
                $this->getUrl($routeParameters),
                $now,
                UrlConcrete::CHANGEFREQ_DAILY,
                0.8
            ),
            $this->section
        );
    }

    private function buildProducts(SitemapPopulateEvent $event, City $city, $cities)
    {
        $productRepository = $this->em->getRepository('MetalProductsBundle:Product');

        Utils::checkEmConnection($this->em);
        $products = $productRepository->createQueryBuilder('p')
            ->select('p.id, p.updatedAt')
            ->join('p.branchOffice', 'mainOffice', 'WITH', 'mainOffice.isMainOffice = true')
            ->join('MetalCompaniesBundle:CompanyCity', 'office', 'WITH', 'p.company = office.company')
            ->andWhere('p.checked = :status')
            ->andWhere('p.isVirtual = false')
            ->setParameter('status', Product::STATUS_CHECKED)
            ->andWhere('office.city IN (:cities)')
            ->setParameter('cities', $cities)
            ->getQuery()
            ->iterate();

        $batchSize = 1000;
        $i = 0;
        foreach ($products as $row) {
            $product = current($row);
            $event->getGenerator()->addUrl(
                new UrlConcrete(
                    $this->router->generate('MetalProductsBundle:Product:view_subdomain',  array('id' => $product['id'], 'subdomain' => $city->getSlug()), true),
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

    private function buildAllProducts(SitemapPopulateEvent $event, City $city)
    {
        $companyRepository = $this->em->getRepository('MetalCompaniesBundle:Company');

        Utils::checkEmConnection($this->em);
        $companies = $companyRepository->createQueryBuilder('c')
            ->select('c.slug, c.updatedAt')
            ->join('MetalProductsBundle:Product', 'p', 'WITH', 'c.id = p.company')
            ->andWhere('p.checked = :status')
            ->andWhere('p.isVirtual = false')
            ->setParameter('status', Product::STATUS_CHECKED)
            ->andWhere('c.city = :city')
            ->setParameter('city', $city)
            ->groupBy('c.id')
            ->getQuery()
            ->iterate();

        $batchSize = 1000;
        $i = 0;
        foreach ($companies as $row) {
            $company = current($row);
            $event->getGenerator()->addUrl(
                new UrlConcrete(
                    $this->router->generate('MetalCompaniesBundle:Company:products',  array('company_slug' => $company['slug'], 'subdomain' => $city->getSlug()), true),
                    $company['updatedAt'],
                    UrlConcrete::CHANGEFREQ_WEEKLY,
                    0.5
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

    private function buildCategoriesInDemands(SitemapPopulateEvent $event, $routeParameters, $now, $city, $countryId)
    {
        $specification = (new DemandFilteringSpec())->countryId($countryId);
        if ($city) {
            $specification->cityId($city->getId());
        }

        $facetSpec = new DemandFacetSpec();
        $facetSpec->facetByCategory($specification);

        $facetsResultSet = $this->demandDataFetcher->getFacetedResultSetByCriteria($specification, $facetSpec);

        $facetResultExtractor = new FacetResultExtractor($facetsResultSet, DemandFacetSpec::COLUMN_CATEGORY_ID);

        $categories = $this->em
            ->getRepository('MetalCategoriesBundle:Category')
            ->createQueryBuilder('cat')
            ->andWhere('cat.isEnabled = true')
            ->andWhere('cat.id IN (:categories_ids)')
            ->setParameter('categories_ids', $facetResultExtractor->getIds())
            ->orderBy('cat.title', 'ASC')
            ->getQuery()
            ->getResult()
        ;

        foreach ($categories as $category) {
            /* @var $category Category */
            $event->getGenerator()->addUrl(
                new UrlConcrete(
                    $this->getDemandsUrl($routeParameters + array('category_slug' => $category->getSlugCombined())),
                    $this->randomizeDate($now, 'hours'),
                    UrlConcrete::CHANGEFREQ_WEEKLY,
                    0.6
                ),
                $this->section
            );
        }
    }

    private function buildDemands(SitemapPopulateEvent $event, City $city, $cities)
    {
        Utils::checkEmConnection($this->em);
        $demands = $this->em
            ->getRepository('MetalDemandsBundle:Demand')
            ->createQueryBuilder('d')
            ->andWhere('d.moderatedAt IS NOT NULL')
            ->andWhere('d.deletedAt IS NULL')
            ->andWhere('d.city IN (:cities)')
            ->andWhere('d.category IS NOT NULL')
            ->setParameter('cities', $cities)
            ->getQuery()
            ->iterate();

        $batchSize = 1000;
        $i = 0;

        foreach ($demands as $row) {
            $demand = $row[0];
            /* @var $demand Demand */

            $event->getGenerator()->addUrl(
                new UrlConcrete(
                    $this->router->generate('MetalDemandsBundle:Demand:view', array(
                        'id' => $demand->getId(),
                        'category_slug' => $demand->getCategory()->getSlugCombined(),
                        'subdomain' => $city->getSlug()
                    ), true),
                    $demand->getFakeUpdatedAt() ?: $demand->getUpdatedAt(),
                    UrlConcrete::CHANGEFREQ_WEEKLY,
                    0.6
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

    private function buildCategories(SitemapPopulateEvent $event, $routeParameters, $now, $city, $countryId)
    {
        $specification = (new ProductsFilteringSpec())->countryId($countryId);
        if ($city) {
            $specification->cityId($city->getId());
        }

        $facetSpec = new ProductsFacetSpec();
        $facetSpec->facetByCategory($specification);

        $facetsResultSet = $this->productDataFetcher->getFacetedResultSetByCriteria($specification, $facetSpec);

        $facetResultExtractor = new FacetResultExtractor($facetsResultSet, ProductsFacetSpec::COLUMN_CATEGORY_ID);

        $categories = $this->em
            ->getRepository('MetalCategoriesBundle:Category')
            ->createQueryBuilder('cat')
            ->andWhere('cat.isEnabled = true')
            ->andWhere('cat.id IN (:categories_ids)')
            ->setParameter('categories_ids', $facetResultExtractor->getIds())
            ->orderBy('cat.title', 'ASC')
            ->getQuery()
            ->getResult();

        foreach ($categories as $category) {
            /* @var $category Category */
            $event->getGenerator()->addUrl(
                new UrlConcrete(
                    $this->getUrl($routeParameters + array('category_slug' => $category->getSlugCombined())),
                    $this->randomizeDate($now, 'hours'),
                    UrlConcrete::CHANGEFREQ_DAILY,
                    0.8
                ),
                $this->section
            );

            $attributes = $this->em->getRepository('MetalAttributesBundle:AttributeCategory')
                ->getAttributesForCategory($category);

            $facetSpec = new ProductsFacetSpec();
            $specification->category($category);
            foreach ($attributes as $attribute) {
                $facetSpec->facetByAttribute($attribute, $specification);
            }

            $facetsResultSet = $this->productDataFetcher->getFacetedResultSetByCriteria($specification, $facetSpec);

            $attributeValueRepository = $this->em->getRepository('MetalAttributesBundle:AttributeValue');
            $attributesCollection = $attributeValueRepository->loadCollectionByFacetResult(
                new AttributesFacetResult($facetsResultSet, $attributes),
                array(Attribute::ORDER_OUTPUT_PRIORITY, AttributeValue::ORDER_OUTPUT_PRIORITY)
            );

            if (!count($attributesCollection)) {
                continue;
            }

            foreach ($attributesCollection->getAttributesValues() as $attributeValue) {
                $url = $this->getUrl($routeParameters + array('category_slug' => $category->getUrl($attributeValue->getSlug())));

                $event->getGenerator()->addUrl(
                    new UrlConcrete(
                        $url,
                        $this->randomizeDate($now, 'minutes'),
                        UrlConcrete::CHANGEFREQ_DAILY,
                        0.8
                    ),
                    $this->section
                );
            }
        }
    }

    private function randomizeDate(\DateTime $date, $dimension)
    {
        $newDate = clone $date;
        $newDate->modify(sprintf('%d %s', mt_rand(-10, 10), $dimension));

        return $newDate;
    }

    private function getUrl($parameters)
    {
        if (isset($parameters['category_slug'], $parameters['subdomain'])) {
            return $this->router->generate('MetalProductsBundle:Products:list_category_subdomain', $parameters, true);
        }

        if (isset($parameters['category_slug'])) {
            return $this->router->generate('MetalProductsBundle:Products:list_category', $parameters, true);
        }

        if (isset($parameters['subdomain'])) {
            return $this->router->generate('MetalProjectBundle:Default:index_subdomain', $parameters, true);
        }

        return $this->router->generate('MetalProjectBundle:Default:index', $parameters, true);
    }

    private function getDemandsUrl($parameters)
    {
        if (isset($parameters['category_slug'], $parameters['subdomain'])) {
            return $this->router->generate('MetalDemandsBundle:Demands:list_subdomain_category', $parameters, true);
        }

        if (isset($parameters['category_slug'])) {
            return $this->router->generate('MetalDemandsBundle:Demands:list_category', $parameters, true);
        }

        throw new \InvalidArgumentException('Expected "category_slug" key.');
    }
}
