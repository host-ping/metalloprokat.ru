<?php

namespace Spros\ProjectBundle\EventListener;

use Doctrine\ORM\EntityManager;
use Metal\AttributesBundle\DataFetching\AttributesFacetResult;
use Metal\AttributesBundle\Entity\Attribute;
use Metal\AttributesBundle\Entity\AttributeValue;
use Metal\CategoriesBundle\Entity\Category;
use Metal\ProductsBundle\DataFetching\Spec\ProductsFacetSpec;
use Metal\ProductsBundle\DataFetching\Spec\ProductsFilteringSpec;
use Metal\ProjectBundle\DataFetching\Sphinxy\SphinxyDataFetcher;
use Metal\ProjectBundle\Repository\SiteRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;

use Presta\SitemapBundle\Service\SitemapListenerInterface;
use Presta\SitemapBundle\Event\SitemapPopulateEvent;
use Presta\SitemapBundle\Sitemap\Url\UrlConcrete;

class SitemapListener implements SitemapListenerInterface
{
    private $router;

    private $em;

    /**
     * @var Request
     */
    private $request;

    private $dataFetcher;

    private $section = 'metalspros';

    public function __construct(RouterInterface $router, EntityManager $em, SphinxyDataFetcher $dataFetcher)
    {
        $this->router = $router;
        $this->em = $em;
        $this->dataFetcher = $dataFetcher;
    }

    public function setRequest(Request $request = null)
    {
        $this->request = $request;
    }

    public function populateSitemap(SitemapPopulateEvent $event)
    {
        list($subdomain, $domain) = explode('.', $this->request->getHost(), 2);
        if (0 !== strpos($domain, $this->section)) {
            return;
        }

        $specification = new ProductsFilteringSpec();

        $siteRepository =  $this->em->getRepository('MetalProjectBundle:Site');
        /* @var $siteRepository SiteRepository */
        $siteRepository->disableLogging();

        $categoryRepository = $this->em->getRepository('MetalCategoriesBundle:Category');

        $categories = $categoryRepository->createQueryBuilder('cat')
            ->andWhere('cat.isEnabledMetalspros = true')
            ->orderBy('cat.title', 'ASC')
            ->getQuery()
            ->getResult();

        $city = null;
        if ($subdomain !== 'www') {
            $city = $this->em->getRepository('MetalTerritorialBundle:City')->findOneBy(array('slug' => $subdomain));
        }

        $routeParameters = array();
        if ($city) {
            $routeParameters['subdomain'] = $city->getSlug();
            $specification->city($city);
        }
        $now = new \DateTime();

        $event->getGenerator()->addUrl(
            new UrlConcrete(
                $this->getUrl($routeParameters),
                $now,
                UrlConcrete::CHANGEFREQ_WEEKLY
            ),
            $this->section
        );

        foreach ($categories as $category) {
            /* @var $category Category */
            $event->getGenerator()->addUrl(
                new UrlConcrete(
                    $this->getUrl($routeParameters + array('category_slug' => $category->getSlugCombined())),
                    $this->randomizeDate($now, 'hours'),
                    UrlConcrete::CHANGEFREQ_WEEKLY
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

            $facetsResultSet = $this->dataFetcher->getFacetedResultSetByCriteria($specification, $facetSpec);

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

        $this->em->createQueryBuilder()
            ->update('MetalProjectBundle:Site', 's')
            ->set('s.sitemapGeneratedAt', ':now')
            ->andWhere('s.hostname = :hostname')
            ->setParameter('now', $now)
            ->setParameter('hostname', sprintf('%s.metalspros.ru', $subdomain))
            ->getQuery()
            ->execute();
        $siteRepository->restoreLogging();
    }

    private function randomizeDate(\DateTime $date, $dimension)
    {
        $newDate = clone $date;
        $newDate->modify(sprintf('%d %s', mt_rand(-10, 10), $dimension));

        return $newDate;
    }

    private function getUrl($parameters)
    {
        if (isset($parameters['category_slug']) && isset($parameters['subdomain'])) {
            return $this->router->generate('SprosProjectBundle:Default:index_subdomain_category', $parameters, true);
        }

        if (isset($parameters['category_slug'])) {
            return $this->router->generate('SprosProjectBundle:Default:index_category', $parameters, true);
        }

        if (isset($parameters['subdomain'])) {
            return $this->router->generate('SprosProjectBundle:Default:index_subdomain', $parameters, true);
        }

        return $this->router->generate('SprosProjectBundle:Default:index', $parameters, true);
    }
}
