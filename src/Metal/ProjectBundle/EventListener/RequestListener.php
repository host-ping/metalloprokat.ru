<?php

namespace Metal\ProjectBundle\EventListener;

use Brouzie\Bundle\HelpersBundle\Helper\HelperFactory;
use Doctrine\ORM\EntityManager;
use Metal\AttributesBundle\Entity\DTO\AttributesCollection;
use Metal\CategoriesBundle\Entity\Category;
use Metal\CategoriesBundle\Helper\DefaultHelper;
use Metal\CompaniesBundle\Entity\Company;
use Metal\CompaniesBundle\Entity\CustomCompanyCategory;
use Metal\ProjectBundle\Helper\UrlHelper;
use Metal\TerritorialBundle\Entity\Country;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class RequestListener
{
    private $em;
    private $hostnamesConfig;
    private $countryId;

    /**
     * @var DefaultHelper
     */
    private $categoryHelper;

    /**
     * @var UrlHelper
     */
    private $urlHelper;

    private $reservedHostnames;

    private $urlGenerator;

    public function __construct(EntityManager $em, UrlGeneratorInterface $urlGenerator, $hostnamesConfig, $countryId, HelperFactory $helperFactory, array $reservedHostnames)
    {
        $this->em = $em;
        $this->urlGenerator  = $urlGenerator;
        $this->hostnamesConfig = $hostnamesConfig;
        $this->countryId = $countryId;
        $this->categoryHelper = $helperFactory->get('MetalCategoriesBundle');
        $this->urlHelper = $helperFactory->get('MetalProjectBundle:Url');
        $this->reservedHostnames = $reservedHostnames;
    }

    public function onKernelRequestBeforeRouting(GetResponseEvent $event)
    {
        // for preventing recursion on 404 pages
        if (!$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();
        //Со страницы вроде http://ekb.metalloprokat.ru/list/shtrips /ozink/1/ (в адресах которых есть пробел) должен быть настроен 301 редирект на адрес без пробела.
        if ($request->isMethodSafe()) {
            $pathInfo = urldecode($request->getPathInfo());
            $newPathInfo = preg_replace('/\s+/ui', '', $pathInfo);
            if ($pathInfo != $newPathInfo) {
                if (null !== $qs = $request->getQueryString()) {
                    $qs = '?'.$qs;
                }

                $redirectTo = $request->getUriForPath($newPathInfo).$qs;
                $event->setResponse(new RedirectResponse($redirectTo, 301));

                return;
            }
        }

        if ($request->isMethodSafe()) {
            $doubleSlashUrl = preg_replace('/\/{2,}/', '/', $request->getPathInfo());
            if ($doubleSlashUrl != $request->getPathInfo()) {
                if (null !== $qs = $request->getQueryString()) {
                    $qs = '?'.$qs;
                }

                $event->setResponse(new RedirectResponse($request->getUriForPath($doubleSlashUrl).$qs, 301));

                return;
            }
        }

        if ($request->isMethodSafe()) {
            $redirects = $this->em->getRepository('MetalProjectBundle:Redirect')->findAll();
            foreach ($redirects as $redirect) {
                $to = preg_replace($redirect->getRedirectFrom(), $redirect->getRedirectTo(), $request->getRequestUri());
                if ($to == $request->getRequestUri()) {
                    continue;
                }

                $event->setResponse(new RedirectResponse($request->getUriForPath($to), 301));

                return;
            }
        }

        $config = $this->getHostConfiguration($request);

        if (!$config) {
            throw new NotFoundHttpException(sprintf('Невозможно получить конфигурацию по текущему base_host для url "%s"! Нужно перепроверить настройки base_host в parameters.yml', $request->getUri()));
        }

        $city = null;
        $matches = array();
        $subdomain = '';

        if (preg_match('#^(?P<subdomain>[-_.0-9a-zA-Z]+)\.'.preg_quote($config['base_host']).'$#', $request->getHost(), $matches)) {
            $subdomain = $matches['subdomain'];
        }

        if ($subdomain === '' && $config['use_www']) {
            $event->setResponse(new RedirectResponse($request->getScheme().'://www.'.$request->getHost().$request->getRequestUri(), 301));
            return;
        }

        if (false !== strpos($subdomain, '.')) {
            list($subdomainPrefix, $subdomain) = explode('.', $subdomain);
            if ($subdomainPrefix === 'www') {
                $event->setResponse(new RedirectResponse($request->getScheme().'://'.$subdomain.'.'.$config['base_host'].$request->getRequestUri(), 301));
                return;
            }
        }

        $loadCityFromQuery = !empty($config['city_query_parameter']);
        if ($loadCityFromQuery) {
            $subdomain = (string)$request->query->get($config['city_query_parameter']);
        }

        $subdomainForRedirect = $request->query->get('subdomain');
        if ($subdomainForRedirect && !$loadCityFromQuery) {
            $uri = preg_replace(sprintf('/[&\?]subdomain=%s/ui', $subdomainForRedirect), '', $request->getRequestUri());
            $url = sprintf('%s://%s.%s%s', $request->getScheme(), $subdomainForRedirect, $config['base_host'], $uri);
            $event->setResponse(new RedirectResponse($url, 301));

            return;
        }

        $country = null;
        $requestAttributes = array();
        $currentHost = $request->getHost();

        if (!in_array($currentHost, $this->reservedHostnames) || $loadCityFromQuery) {
            $tryFindCompany = true;
            if (is_numeric($subdomain)) {
                // ищем область по айди
                $region = $this->em
                    ->getRepository('MetalTerritorialBundle:Region')
                    ->createQueryBuilder('region')
                    ->addSelect('country')
                    ->join('region.country', 'country')
                    ->andWhere('region.id = :slug')
                    ->andWhere('region.country IN (:countryIds)')
                    ->setParameter('slug', $subdomain)
                    ->setParameter('countryIds', Country::getEnabledCountriesIds())
                    ->getQuery()
                    ->getOneOrNullResult();

                if ($region) {
                    $requestAttributes = array('region' => $region, 'territory' => $region);
                    $country = $region->getCountry();
                    $tryFindCompany = false;
                }
            } else {
                // ищем город по слагу, городов меньше, чем компаний, поэтому лезем в них первее
                $cityQb = $this->em
                    ->getRepository('MetalTerritorialBundle:City')
                    ->createQueryBuilder('c')
                    ->join('c.country', 'country')
                    ->addSelect('country')
                    ->andWhere('c.slug = :slug')
                    ->setParameter('slug', $subdomain)
                ;

                if (!$config['allow_foreign_countries']) {
                    $cityQb
                        ->andWhere('c.country IN (:countryIds)')
                        ->setParameter('countryIds', Country::getEnabledCountriesIds())
                    ;
                }

                $city = $cityQb
                    ->getQuery()
                    ->getOneOrNullResult();

                if ($city) {
                    $country = $city->getCountry();
                    $requestAttributes = array('city' => $city, 'territory' => $city);
                    $tryFindCompany = false;
                }
            }

            if ($tryFindCompany) {
                $company = $this->em->getRepository('MetalCompaniesBundle:Company')
                    ->createQueryBuilder('c')
                    ->join('c.country', 'country')
                    ->addSelect('country')
                    ->andWhere('c.slug = :slug')
                    ->andWhere('c.deletedAtTS = 0')
                    ->andWhere('c.minisiteEnabled = true')
                    ->setParameter('slug', $subdomain)
                    ->getQuery()
                    ->getOneOrNullResult();

                if ($company) {
                    $country = $company->getCountry();
                    $requestAttributes = array('company' => $company, 'company_on_subdomain' => true);
                }
            }
        }

        /* @var $country Country */
        if (!$country) {
            $country = $this->em->getRepository('MetalTerritorialBundle:Country')->find($this->countryId);
        }
        $requestAttributes['country'] = $country;
        if (!isset($requestAttributes['territory'])) {
            $requestAttributes['territory'] = $country;
        }

        if ($config['country_redirect']) {
            if ($country && $country->getId() != $this->countryId && $country->getBaseHost() !== $config['base_host']) {
                $subDomain = substr($request->getHost(), 0, strpos($request->getHost(), $config['base_host']));
                $redirectUrl = $request->getScheme().'://'.$subDomain.$country->getBaseHost().$request->getRequestUri();

                $event->setResponse(new RedirectResponse($redirectUrl, 301));
            }
        }

        $request->attributes->add($requestAttributes);
    }

    public function redirectToHttps(GetResponseEvent $event)
    {
        // for preventing recursion on 404 pages
        if (!$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();

        if (!$request->isMethodSafe() || $request->getPathInfo() === '/robots.txt') {
            return;
        }

        $supportsHttps = $this->urlHelper->isClientSupportsHttps();

        if (!$supportsHttps && $request->isSecure()) {
            $url = UrlHelper::replaceScheme($request->getUri(), 'http');
            $event->setResponse(new RedirectResponse($url, 301));

            return;
        }

        $country = $request->attributes->get('country');
        /* @var $country Country */

        $redirectRequired = $country->getSecure() && !$request->isSecure() && $supportsHttps;

        //TODO: проверить/исправить кастомные хосты минисайта, типа metaltop.ru
        if (preg_match('#(metalspros\.ru|8-800-555-56-65\.ru|metaltop\.ru|me1\.ru)#', $request->getHost())) {
            return;
        }

        if (!$redirectRequired) {
            return;
        }

        if ($request->attributes->get('company_on_subdomain', false)) {
            // минисайт
            $company = $request->attributes->get('company');
            /* @var $company Company */

            if ($company->getPackageChecker()->isHttpsAvailable()) {
                $url = UrlHelper::replaceScheme($request->getUri(), 'https');
                $event->setResponse(new RedirectResponse($url, 301));
            }

            return;
        }

        $url = UrlHelper::replaceScheme($request->getUri(), 'https');
        $event->setResponse(new RedirectResponse($url, 301));
    }

    public function onKernelRequestAfterRouting(GetResponseEvent $event)
    {
        // for preventing recursion on 404 pages
        if ($event->getRequestType() !== HttpKernelInterface::MASTER_REQUEST) {
            return;
        }

        $request = $event->getRequest();
        $currentHost = $request->getHost();
        if (!in_array($currentHost, $this->reservedHostnames)) {
            if ($request->attributes->get('subdomain') && !$request->attributes->get('city') && !$request->attributes->get('company') && !$request->attributes->get('region')) {
                $config = $this->getHostConfiguration($request);
                if ($companyOldSlug = $this->em->getRepository('MetalCompaniesBundle:CompanyOldSlug')->findOneBy(array('oldSlug' => $request->attributes->get('subdomain')))) {
                    $redirectUrl = $request->getScheme().'://'.$companyOldSlug->getCompany()->getSlug().'.'.$config['base_host'].$request->getRequestUri();
                    $event->setResponse(new RedirectResponse($redirectUrl, 301));
                } else {
                    throw new NotFoundHttpException('City or company not found.');
                }
            }
        }

        $category = $request->attributes->get('category');
        $parametersSlugs = (array) $request->attributes->get('products_parameters_slugs', array());

        $attributeValueRepository = $this->em->getRepository('MetalAttributesBundle:AttributeValue');
        $attributesCollection = new AttributesCollection();
        if ($category instanceof Category) {
            $attributesCollection = $attributeValueRepository->loadCollectionBySlugs($category, $parametersSlugs);
        } elseif ($category instanceof CustomCompanyCategory) {
            $attributesCollection = $attributeValueRepository->loadCollectionBySlugsOnly($parametersSlugs);
        }
        $request->attributes->set('attributes_collection', $attributesCollection);

        if (!$parametersSlugs || !$category instanceof Category) {
            return;
        }

        if ($request->attributes->get('_controller') === 'Symfony\Bundle\FrameworkBundle\Controller\RedirectController::urlRedirectAction') {
            return;
        }

        // загрузились все атрибуты, но поменяли приоритет параметров в урле
        if (count($attributesCollection->getAttributesValues()) === count($parametersSlugs)) {
            if ($category->getUrl($attributesCollection->getUrl()) !== $request->attributes->get('category_slug')) {
                $event->setResponse($this->getRedirectResponse($request, $category, $attributesCollection));

                return;
            }
        }

        // загрузилось меньше значений атрибутов, чем в УРЛе - в хвост УРЛ-а добавили какие-то несуществуюшие/старые параметры
        if (count($attributesCollection->getAttributesValues()) < count($parametersSlugs)) {
            $oldAttributesCollection = $attributeValueRepository
                ->loadCollectionBySlugs($category, $parametersSlugs, 'oldSlug');

            if (count($oldAttributesCollection->getAttributesValues()) === count($parametersSlugs)) {
                $event->setResponse($this->getRedirectResponse($request, $category, $attributesCollection));

                return;
            }

            foreach ($parametersSlugs as $i => $parametersSlug) {
                $parametersSlugs[$i] = $this->categoryHelper->normalizeSlug($parametersSlug);
            }
            // пробуем загрузить нормализованные
            $attributesCollection = $attributeValueRepository->loadCollectionBySlugs($category, $parametersSlugs);

            if (count($attributesCollection->getAttributesValues()) === count($parametersSlugs)) {
                $event->setResponse($this->getRedirectResponse($request, $category, $attributesCollection));

                return;
            }

            do {
                // пробуем по очереди выкидывать по одному значению атрибута
                array_pop($parametersSlugs);
                $attributesCollection = $attributeValueRepository->loadCollectionBySlugs($category, $parametersSlugs);

                if (count($attributesCollection->getAttributesValues()) === count($parametersSlugs)) {
                    $event->setResponse($this->getRedirectResponse($request, $category, $attributesCollection));

                    return;
                }
            } while (count($parametersSlugs));

            // ничего не помогло
            throw new NotFoundHttpException('Extra parameters added.');
        }
    }

    private function getRedirectResponse(Request $request, Category $category, AttributesCollection $attributesCollection)
    {
        $route = $request->attributes->get('_route');
        $routeParams = $request->attributes->get('_route_params');
        $routeParams['category_slug'] = $category->getUrl($attributesCollection->getUrl());
        $routeParams = array_merge($routeParams, $request->query->all());

        $url = $this->urlGenerator->generate($route, $routeParams);

        return new RedirectResponse($url, 301);
    }

    private function getHostConfiguration(Request $request)
    {
        foreach ($this->hostnamesConfig as $hostname => $config) {
            // пытаемся понять, на каком мы хосте и берем для него нужную конфигурацию
            if (false !== strpos($request->getHost(), $hostname)) {
                return $config;
            }
        }
    }
}
