<?php

namespace Metal\CategoriesBundle\EventListener;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Metal\CategoriesBundle\Entity\LandingPage;
use Metal\ProjectBundle\Repository\SiteRepository;
use Metal\TerritorialBundle\Entity\City;
use Metal\TerritorialBundle\Entity\Country;
use Presta\SitemapBundle\Event\SitemapPopulateEvent;
use Presta\SitemapBundle\Service\SitemapListenerInterface;
use Presta\SitemapBundle\Sitemap\Url\UrlConcrete;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;

class LandingPageSitemapListener implements SitemapListenerInterface
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

    private $baseHost;

    private $projectFamily;

    private $section = 'landing';

    private $container;

    public function __construct(RouterInterface $router, EntityManager $em, $hostnameMap, $baseHost, $projectFamily, ContainerInterface $container)
    {
        $this->router = $router;
        $this->em = $em;
        $this->hostnameMap = $hostnameMap;
        $this->baseHost = $baseHost;
        $this->projectFamily = $projectFamily;
        $this->container = $container;
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
        $section = $matches[1];

        if (0 !== strpos($domain, $section)) {
            return;
        }

        $siteRepository = $this->em->getRepository('MetalProjectBundle:Site');
        /* @var $siteRepository SiteRepository */

        $siteRepository->disableLogging();

        $countryId = null;
        foreach ($this->hostnameMap as $host => $hostInfo) {
            if (false !== strpos($host, $domain)) {
                $countryId = $hostInfo['country_id'];
                $country = $this->em->find('MetalTerritorialBundle:Country', $countryId);
                if (!$country) {
                    throw new \InvalidArgumentException('country no found');
                }
            }
        }

        $city = null;
        if ($subdomain !== 'www') {
            $city = $this->em->getRepository('MetalTerritorialBundle:City')->findOneBy(array('slug' => $subdomain));
        }

        $landingPagesQb = $this->em->getRepository('MetalCategoriesBundle:LandingPage')
            ->createQueryBuilder('lp')
            ->where('lp.enabled = true')
        ;

        if ($city) {
            $this->buildCitiesPage($event, $landingPagesQb, $city);
        } else {
            $this->buildCountriesPage($event, $landingPagesQb, $country);
        }
    }

    private function buildCountriesPage(SitemapPopulateEvent $event, QueryBuilder $landingPagesQb, Country $country)
    {
        $landingPagesQbCurrent = clone $landingPagesQb;

        $landingPages = $landingPagesQbCurrent
            ->join('lp.landingPageCountryCounts', 'lpctc')
            ->andWhere('lpctc.country = :country')
            ->andWhere('lpctc.resultsCount >= :count')
            ->setParameter('count', LandingPage::MIN_PRODUCTS_COUNT)
            ->setParameter('country', $country)
            ->getQuery()
            ->getResult();

        // сейчас мы не лезем в сфинкс, потому что расчитываем что команда metal:categories:update-landing-page уже пересчитала все
        foreach ($landingPages as $landingPage) {
            /* @var $landingPage LandingPage */
            $event->getGenerator()->addUrl(
                new UrlConcrete(
                    $this->router->generate(
                        'MetalCategoriesBundle:LandingPage:landing',
                        array('slug' => $landingPage->getSlug(), 'base_host' => $country->getBaseHost()),
                        true
                    ),
                    $landingPage->getResultsCountUpdatedAt(),
                    UrlConcrete::CHANGEFREQ_WEEKLY,
                    0.8
                ),
                $this->section
            );
        }
    }

    private function buildCitiesPage(SitemapPopulateEvent $event, QueryBuilder $landingPagesQb, City $city)
    {
        $landingPagesQbCurrent = clone $landingPagesQb;

        $landingPages = $landingPagesQbCurrent
            ->join('lp.landingPageCityCounts', 'lpcc')
            ->andWhere('lpcc.city = :city_id')
            ->andWhere('lpcc.resultsCount >= :count')
            ->setParameter('count', LandingPage::MIN_PRODUCTS_COUNT)
            ->setParameter('city_id', $city)
            ->getQuery()
            ->getResult();

        // сейчас мы не лезем в сфинкс, потому что расчитываем что команда metal:categories:update-landing-page уже пересчитала все
        foreach ($landingPages as $landingPage) {
            /* @var $landingPage LandingPage */
            $subdomain = $city->getSlugWithFallback();
            $baseHost = $city->getDisplayInCountry()->getBaseHost();

            $event->getGenerator()->addUrl(
                new UrlConcrete(
                    $this->router->generate(
                        'MetalCategoriesBundle:LandingPage:landing',
                        array('slug' => $landingPage->getSlug(), 'subdomain' => $subdomain, 'base_host' => $baseHost),
                        true
                    ),
                    $landingPage->getResultsCountUpdatedAt(),
                    UrlConcrete::CHANGEFREQ_WEEKLY,
                    0.8
                ),
                $this->section
            );
        }
    }
}
