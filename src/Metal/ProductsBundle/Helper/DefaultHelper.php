<?php

namespace Metal\ProductsBundle\Helper;

use Brouzie\Bundle\HelpersBundle\Helper\HelperAbstract;

use Metal\CategoriesBundle\Entity\Category;
use Metal\CompaniesBundle\Entity\Company;
use Metal\CompaniesBundle\Entity\CompanyCity;
use Metal\ProductsBundle\Entity\Product;
use Metal\ProjectBundle\Helper\FormattingHelper;
use Metal\ProjectBundle\Helper\UrlHelper;
use Metal\ServicesBundle\Entity\Package;
use Metal\StatisticBundle\Entity\StatsElement;
use Metal\StatisticBundle\Repository\StatsElementRepository;
use Metal\TerritorialBundle\Entity\City;
use Metal\TerritorialBundle\Entity\Country;
use Metal\TerritorialBundle\Entity\Region;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

class DefaultHelper extends HelperAbstract
{
    /**
     * @var Region
     */
    private $currentRegion;

    /**
     * @var City
     */
    private $currentCity;

    /**
     * @var Country
     */
    private $currentCountry;

    /**
     * {@inheritdoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);

        $request = $this->container->get('request_stack')->getMasterRequest();
        $this->currentCity = $request->attributes->get('city');
        $this->currentRegion = $request->attributes->get('region');
        $this->currentCountry = $request->attributes->get('country');
    }

    public function generateProductsUrl(Company $company, $categorySlug, array $parameters = array(), $absolute = false)
    {
        $parameters['company_slug'] = $company->getSlug();

        $route = 'MetalCompaniesBundle:Company:products';
        if ($categorySlug) {
            $route = 'MetalCompaniesBundle:Company:products_category';
            $parameters['category_slug'] = $categorySlug;
        }
//TODO: проверить поведение для компаний не из России
        if ($company->getVisibilityStatus() != Company::VISIBILITY_STATUS_ALL_COUNTRIES && $company->getCountry()->getId() != $this->currentCountry->getId()) {
            $route = 'MetalCompaniesBundle:Company:products_domain';
            if ($categorySlug) {
                $route = 'MetalCompaniesBundle:Company:products_category_domain';
            }
            $parameters['domain'] = $company->getCountry()->getBaseHost();
        }

        if ($this->currentCity) {
            $parameters['subdomain'] = $this->currentCity->getSlugWithFallback();
        } elseif ($this->currentRegion) {
            $parameters['subdomain'] = $this->currentRegion->getId();
        } else {
            $parameters['subdomain'] = $company->getCity()->getSlugWithFallback();
        }

        return $this->container->get('router')->generate($route, $parameters, $absolute);
    }

    public function generateProductUrl(Product $product, $absolute = false)
    {
        $company = $product->getCompany();
        $parameters = array(
            'id' => $product->getId(),
            'subdomain' => $this->getSubdomain($product),
        );
        //TODO: проверить поведение для компаний не из России
        $route = 'MetalProductsBundle:Product:view_subdomain';
        if ($company->getVisibilityStatus() != Company::VISIBILITY_STATUS_ALL_COUNTRIES && $company->getCountry()->getId() != $this->currentCountry->getId()) {
            $route = 'MetalProductsBundle:Product:view_subdomain_domain';
            $parameters['domain'] = $company->getCountry()->getBaseHost();
        }

        $parameters['_secure'] = $company->getCountry()->getSecure();

        $urlHelper = $this->container->get('brouzie.helper_factory')->get('MetalProjectBundle:Url');
        /* @var $urlHelper UrlHelper */

        return $urlHelper->generateUrl($route, $parameters, $absolute);
    }

    protected function getSubdomain(Product $product)
    {
        $productCity = null;
        if ($product->getBranchOffice()) {
            $productCity = $product->getBranchOffice()->getCity();
        }

        // Нет города и области возращаем поддомен города, где находится продукт
        if (!$this->currentCity && !$this->currentRegion && $productCity) {
            return $productCity->getSlugWithFallback();
        }

        // Компания имеет промо статус, поэтому доступна везде
        if ($product->getCompany()->isPromo()) {
            //TODO: возможно нужно добавить проверку на страну, если компания видна только по своей стране а ее открыли в другой
            if ($this->currentCity) {
                return $this->currentCity->getSlugWithFallback();
            }

            if ($this->currentRegion) {
                return $this->currentRegion->getSlug();
            }

            if ($productCity) {
                return $productCity->getSlugWithFallback();
            }

            return $product->getCompany()->getCity()->getSlugWithFallback();
        }

        // У продукта есть офис и мы просматриваем город или регион
        if ($this->currentCity && $productCity) {
            if ($productCity->getId() == $this->currentCity->getId()) {
                return $this->currentCity->getSlugWithFallback();
            }

            return $product->getBranchOffice()->getCity()->getSlugWithFallback();
        }

        if ($this->currentRegion && $productCity) {
            if ($productCity->getRegion()->getId() == $this->currentRegion->getId()) {
                return $this->currentRegion->getSlug();
            }

            return $productCity->getRegion()->getSlug();
        }

        // У продукта нет офиса и мы просматриваем город или регион
        if ($this->currentCity) {
            return $this->currentCity->getSlugWithFallback();
        }

        if ($this->currentRegion) {
            return $this->currentRegion->getSlug();
        }

        if ($productCity) {
            return $productCity->getSlugWithFallback();
        }

        return $product->getCompany()->getCity()->getSlugWithFallback();
    }

    public function getDeliveryString(Company $company)
    {
        //TODO: обрабатывать ситуацию, когда у нас есть продукт не из главного офиса
        //TODO: обрабатывать ситуацию с компанией, которая отображается в другой стране на www
        $companyCity = $company->getAttribute('company_city');
        /* @var $companyCity CompanyCity */

        $cityTitle = $company->getCity()->getTitle();

        if ($companyCity) {
            if ($companyCity->getIsMainOffice()) {
                $cityTitle = $companyCity->getCity()->getTitle();
            } elseif ($companyCity->isBranchOffice()) {
                $cityTitle = 'филиал в '.$companyCity->getCity()->getTitleLocative();
            } else {
                $cityTitle = 'доставка в '.$companyCity->getCity()->getTitleAccusative();
            }
        } elseif ($company->getCountry()->getId() != $this->currentCountry->getId()) {
                $cityTitle = 'доставка по '.$this->currentCountry->getTitleLocative();
            if ($this->currentCity) {
                $cityTitle = 'доставка в '.$this->currentCity->getTitleAccusative();
            } elseif ($this->currentRegion) {
                $cityTitle = 'доставка в '.$this->currentRegion->getTitleAccusative();
            }
        } elseif ($company->isPromo()) {
            if ($this->currentCity) {
                $cityTitle = 'доставка в '.$this->currentCity->getTitleAccusative();
            } elseif ($this->currentRegion) {
                $cityTitle = 'доставка в '.$this->currentRegion->getTitleAccusative();
            }
        }


        return $cityTitle;
    }

    /**
     * @param Company[] $companies
     * @param string|null $productTitle
     *
     * @return array
     */
    public function productToArrayAsCompany($companies, $productTitle = null)
    {
        $formatHelper = $this->getHelper('MetalProjectBundle:Formatting');
        /* @var $formatHelper FormattingHelper */
        $companiesJson = array();
        $request = $this->getRequest();
        $currentCountry = $request->attributes->get('country');

        $numberHelper = $this->container->get('sonata.intl.templating.helper.number');

        $categorySlug = '';
        $category = $request->attributes->get('category');
        /* @var $category Category */
        if ($category) {
            $attributesCollection = $request->attributes->get('attributes_collection');
            $categorySlug = $category->getUrl($attributesCollection->getUrl());
        }

        $router = $this->container->get('router');
        $translator = $this->container->get('translator');

        $starColors = array(
            Package::BASE_PACKAGE => 'icon-star default',
            Package::ADVANCED_PACKAGE => 'icon-star hovered',
            Package::FULL_PACKAGE => 'icon-star-colored',
            Package::STANDARD_PACKAGE => 'icon-star-colored default'
        );

        $productVolumeTitle = $this->container->getParameter('tokens.product_volume_title');

        foreach ($companies as $company) {
            $product = $company->getAttribute('product');
            /* @var $product Product */

            $contactInfo = $company->getContactInfo();
            if (!$contactInfo->getLatitude() || !$contactInfo->getLongitude()) {
                continue;
            }

            $phonesString = $company->getAttribute('phones_string');
            if (!$phonesString) {
                $phonesString = $contactInfo->getPhonesAsString();
            }

            if (!$phonesString) {
                $phonesString = $company->getPhonesAsString();
            }

            $coordinate = array(round($contactInfo->getLatitude() + 0, 4), round($contactInfo->getLongitude() + 0, 4));

            $minisiteRouteParam = array('domain' => $company->getDomain(), '_secure' => $company->getPackageChecker()->isHttpsAvailable());
            if ($this->currentCity) {
                $minisiteRouteParam['city'] = $this->currentCity->getId();
            }

            $companyProductsRoute = $categorySlug ? 'MetalCompaniesBundle:Company:products_category' : 'MetalCompaniesBundle:Company:products';

            $element = array(
                'id' => $company->getId(),
                'title' => $company->getTitle(),
                'star_color' => $starColors[$company->getCodeAccess()],
                'rating' => $company->getCompanyRating(),
                'has_products' => $product && !$product->getIsVirtual(),
                'updated_at' => $formatHelper->formatDate($company->getAttribute('product_updated_at')),
                'products_count' => $numberHelper->formatDecimal($company->getAttribute('products_count_by_company')),
                'products_count_title' => $translator->transChoice('products_by_count', $company->getAttribute('products_count_by_company'), array(), 'MetalProductsBundle'),
                'coord' => $coordinate,
                'city_title' => $this->getDeliveryString($company),
                'phone' => $phonesString,
                'city_locative' => $company->getCity()->getTitleLocative(),
                'mini_site' => $this->container->get('brouzie.helper_factory')->get('MetalProjectBundle:Url')->generateUrl('MetalMiniSiteBundle:MiniSite:view', $minisiteRouteParam),
                'callback_url' => $router->generate('MetalCallbacksBundle:Callback:save', array(
                    'id' => $company->getId(),
                    'target_object' => 'company',
                    'from' => 'companies-list',
                    'category' => $category ? $category->getId() : null
                    )),
                'show_contact_url' => $router->generate('MetalStatisticBundle:Default:showContact'),
                'category_id' => $category ? $category->getId() : null,
                'company_products_url' => $router->generate($companyProductsRoute, array_filter(array(
                    'subdomain' => $company->getCity()->getSlugWithFallback(),
                    'company_slug' => $company->getSlug(),
                    'category_slug' => $categorySlug
                    )
                )),
                'visible_in_all_cities' => $company->isPromo()
            );

            if ($site = $contactInfo->getSite() ?: $company->getSite()) {
                $element['url'] = $router->generate('MetalStatisticBundle:Default:redirectSite', array(
                        'source' => 'companies-list',
                        'object-id' => $company->getId(),
                        'object-kind' => 'company',
                        'url' => $site
                    )
                );

                $element['url_caption'] = $formatHelper->getDomain($site);
            }

            if ($product) {
                $element['product'] = array(
                    'id' => $company->getId(),
                    'title' => $productTitle ?: $company->getTitle(),
                    'size' => $product->getSize(),
                    'currency' => $product->getCurrency()->getToken(),
                    'currency_symbol_class' => $product->getCurrency()->getSymbolClass(),
                    'measure' => $product->getMeasureId() ? $product->getMeasure()->getTokenPrice() : null,
                    'price' => $numberHelper->formatDecimal($company->getAttribute('company_price')),
                    'is_contract_price' => $this->isContractPrice($company->getAttribute('company_price')),
                    'product_volume_title' => $productVolumeTitle,
                    'normalized_price' => $numberHelper->formatDecimal($product->getAttribute('normalized_price')),
                    'country_symbol_class' => $currentCountry->getCurrency()->getSymbolClass()
                );
            }

            $companiesJson[] = $element;
        }

        return $companiesJson;
    }

    public function trackProductView(Product $product, $viewFrom)
    {
        $request = $this->getRequest();
        $userAgent = $request->headers->get('USER_AGENT');

        $detector = $this->container->get('vipx_bot_detect.detector');
        $botMetadata = $detector->detectFromRequest($request);

        if (null !== $botMetadata) {
            return;
        }

        $city = $request->attributes->get('city');
        if (!$city) {
            $city = $product->getCompany()->getCity();
        }

        $this->createStatsElement($request, $product, $viewFrom, $userAgent, $city);
    }

    public function createStatsElement(Request $request, Product $product, $viewFrom, $userAgent, $city)
    {
        $statsElement = new StatsElement();

        $statsElement->setCompanyId($product->getCompany()->getId());
        $statsElement->setProductId($product->getId());
        $statsElement->setCity($city);

        $statsElement->setAction(StatsElement::ACTION_VIEW_PRODUCT);
        $statsElement->setSourceType($viewFrom);

        $statsElement->setIp($request->getClientIp());
        $statsElement->setUserAgent($userAgent);
        $statsElement->setReferer($request->server->get('HTTP_REFERER'));
        $statsElement->setSessionId($request->getSession()->getId());
        $statsElement->setCategoryId($product->getCategory()->getId());

        $authorizationChecker = $this->container->get('security.authorization_checker');
        $tokenStorage = $this->container->get('security.token_storage');

        if ($authorizationChecker->isGranted('ROLE_USER')) {
            $statsElement->setUser($tokenStorage->getToken()->getUser());
        }

        $statisticHelper = $this->container->get('brouzie.helper_factory')->get('MetalStatisticBundle');
        /* @var $statisticHelper \Metal\StatisticBundle\Helper\DefaultHelper */
        $statsElementRepo = $this->container->get('doctrine')->getManager()->getRepository('MetalStatisticBundle:StatsElement');
        /* @var $statsElementRepo StatsElementRepository */
        $statsElementRepo->insertStatsElement($statsElement, $statisticHelper->canCreateFakeStatsElement());
    }

    public function isContractPrice($price)
    {
        return $price == 0 || $price == Product::PRICE_CONTRACT;
    }

    public function getFilterParametersForCitiesListFromRequest(Request $request)
    {
        $requestBag = $request->request;

        $whiteListParameters = array(
            'price_from',
            'price_to',
            'cattr',
            'attributes_ids'
        );

        $params = array();
        $filterParameters = $requestBag->get('filter_parameters');
        foreach ($whiteListParameters as $param) {
            if (isset($filterParameters[$param])) {
                $params[$param] = $filterParameters[$param];
            }
        }

        if ($requestBag->has('category_slug')) {
            $params['category_slug'] = $requestBag->get('category_slug');
        }

        return $params;
    }
}
