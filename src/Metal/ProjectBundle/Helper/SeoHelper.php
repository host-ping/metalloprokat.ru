<?php

namespace Metal\ProjectBundle\Helper;

use Brouzie\Bundle\HelpersBundle\Helper\HelperAbstract;
use Doctrine\ORM\EntityManager;
use Metal\AttributesBundle\Entity\DTO\AttributesCollection;
use Metal\AttributesBundle\Entity\DTO\AttributesCollectionTwigSerializer;
use Metal\CategoriesBundle\Entity\Category;
use Metal\CategoriesBundle\Entity\CategoryCityMetadata;
use Metal\CategoriesBundle\Entity\LandingPage;
use Metal\CategoriesBundle\Entity\ParameterGroup;
use Metal\CategoriesBundle\Helper\DefaultHelper as CategoryDefaultHelper;
use Metal\CompaniesBundle\Entity\Company;
use Metal\DemandsBundle\Entity\Demand;
use Metal\DemandsBundle\Entity\DemandItem;
use Metal\ProductsBundle\Entity\Product;
use Metal\TerritorialBundle\Entity\City;
use Metal\TerritorialBundle\Entity\Country;
use Metal\TerritorialBundle\Entity\Region;
use Metal\TerritorialBundle\Entity\TerritoryInterface;
use Pagerfanta\Pagerfanta;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class SeoHelper extends HelperAbstract
{
    const META_TAG_NOINDEX_NOFOLLOW = '<meta name="robots" content="noindex, nofollow"/>';
    const META_TAG_NOINDEX_FOLLOW = '<meta name="robots" content="noindex, follow"/>';
    const META_TAG_NOINDEX_NOFOLLOW_YANDEX = '<meta name="yandex" content="noindex, follow" />';

    /**
     * @var Region|null
     */
    protected $currentRegion;

    /**
     * @var City|null
     */
    protected $currentCity;

    /**
     * @var Country
     */
    protected $currentCountry;

    /**
     * @var Category|null
     */
    protected $currentCategory;

    /**
     * @var CategoryCityMetadata
     */
    protected $categoryCityMetadata;

    /**
     * @var AttributesCollection
     */
    protected $attributes;

    protected $page;

    protected $projectFamily;

    protected $usedUrls = array();

    protected $minTitleLength = 65;

    protected $maxTitleLengthForDemandPage = 100;

    protected $loadedBranchOffices = array();

    protected $attributeValuesCombinations;

    public static function limitText($string, $length = 120)
    {
        if (mb_strlen($string) <= $length) {
            return $string;
        }

        $substringLimited = mb_substr($string, 0, $length);
        $cropString = mb_substr($substringLimited, 0,  mb_strrpos($substringLimited, ' '));

        if (!$cropString) {
            return self::cropBracket($string);
        }

        return self::cropBracket($cropString);
    }

    public static function limitCompanyText($string, $length = 300)
    {
        if (mb_strlen($string) <= $length) {
            return $string;
        }

        $substringLimited = mb_substr($string, 0, $length);

        if (mb_strrpos($substringLimited, '.')) {
            $strEnd = mb_strrpos($substringLimited, '.');
        }
        else {
            $strEnd = mb_strrpos($substringLimited, ' ');
        }

        return mb_substr($substringLimited, 0, $strEnd);

    }

    public static function cropBracket($string)
    {
        $lastClosedBracket = mb_strrpos($string, ')');
        $lastOpenBracket = mb_strrpos($string, '(');

        if ($lastClosedBracket === false && $lastOpenBracket === false) {
            return trim($string, ' \t\n\r\0\x0B,');
        }

        if ($lastClosedBracket === false || $lastClosedBracket < $lastOpenBracket) {
            return trim(mb_substr($string, 0, $lastOpenBracket), ' \t\n\r\0\x0B,');
        }

        return trim($string, ' \t\n\r\0\x0B,');
    }

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
        $this->currentCategory = $request->attributes->get('category');
        $this->attributes = $request->attributes->get('attributes_collection');
        $this->projectFamily = $this->container->getParameter('project.family');
        $this->page = $request->get('page');

        if ($this->projectFamily === 'product') {
            $this->maxTitleLengthForDemandPage = 60;
        }
    }

    public function setUrlWasDisplayed($url)
    {
        $this->usedUrls[$url] = true;
    }

    public function isUrlWasDisplayed($url)
    {
        return isset($this->usedUrls[$url]);
    }

    public function getMetaTitleForFrontpage()
    {
        $locationSlug = 'www';
        if ($this->currentCity) {
            $locationSlug = $this->currentCity->getSlug();
        } elseif ($this->currentRegion) {
            $locationSlug = $this->currentRegion->getSlug();
        }

        return 'Металлопрокат купить в '.$this->getLocationTitle().' оптом и в розницу — цены в каталоге металлоторгующих компаний | '.$locationSlug.'.'.$this->currentCountry->getBaseHost();
    }

    public function getCanonicalUrlForFrontpage()
    {
        $request = $this->getRequest();

        return $this->generateAbsoluteUrl(
            $request->attributes->get('_route'),
            $request->attributes->get('_route_params')
        );
    }

    public function getMetaDescriptionForFrontpage()
    {
        return 'Купить выгодно металлопрокат оптом и в розницу в '.$this->getLocationTitle().' ➤➤➤ MetalloProkat: ✔Сравнивайте цены. ✔Большая база поставщиков. ✔Прямые контакты компаний. Выберите поставщика самостоятельно!';
    }

    public function getHeadTitleForFrontpage()
    {
        if ($this->currentCity || $this->currentRegion) {
            return $this->currentCountry->getDomainTitle().' в '.$this->getLocationTitle();
        }

        return $this->currentCountry->getDomainTitle();
    }

    public function getMetaTitleForDemandFrontPage()
    {
        return 'Заявки, потребности и тендеры в '.$this->getLocationTitle().' — '.$this->currentCountry->getDomainTitle();
    }

    public function getHeadTitleForDemandFrontPage()
    {
        return 'Заявки в '.$this->getLocationTitle();
    }

    public function getMetaDescriptionForDemandFrontPage()
    {
        return 'Заявки, тендеры и потребности покупателей в '.$this->getLocationTitle().' — каталог на '.$this->currentCountry->getDomainTitle().'. Оставить заявку на поставку '.$this->container->getParameter('tokens.market_title').'.';
    }

    public function getMetaTitleForDemandPage(Demand $demand)
    {
        $page = $this->getRequest()->query->get('page');
        $pageSuffix = $page ? ' — Страница '.$page : '';

        $demandItems = $demand->getAttribute('demandItems');
        /* @var $demandItems DemandItem[] */
        $firstDemandItem = current($demandItems);
        /* @var $firstDemandItem DemandItem */

        if ($this->projectFamily === 'product' && mb_strlen($firstDemandItem->getTitle()) < $this->maxTitleLengthForDemandPage) {
            return $firstDemandItem->getTitle().' — куплю в '.$this->getLocationTitle().' - '.$this->currentCountry->getDomainTitle().$pageSuffix;
        }

        // конкатенируем позиции, ограничивая длину 100 символами, обрезая до полного наименования позиции
        if (count($demandItems) > 1) {
            $title = 'Заявка: ';
            foreach ($demandItems as $i => $demandItem) {
                if ($i && mb_strlen($title.', '.$demandItem->getTitle()) > $this->maxTitleLengthForDemandPage) {
                    break;
                }

                if ($i) {
                    $title .= ', ';
                }

                $title .= $demandItem->getTitle();
            }

            return $title.$pageSuffix;
        }

        return 'Заявка: '.self::limitText($firstDemandItem->getTitle(), $this->maxTitleLengthForDemandPage).' — заявки в '.$this->getLocationTitle().' — '.$this->currentCountry->getDomainTitle().$pageSuffix;
    }

    public function getMetaDescriptionForDemandPage(Demand $demand)
    {
        $demandItems = $demand->getAttribute('demandItems');
        /* @var $demandItems DemandItem[] */

        if (count($demandItems) > 1) {
            return;
        }

        $firstDemandItem = current($demandItems);
        /* @var $firstDemandItem DemandItem */

        return $firstDemandItem->getTitle().' в '.$this->getLocationTitle().' - принять участие в заявке и продать прямо сейчас.';
    }

    protected function getMetadataProperty($property)
    {
        if (null === $this->currentCategory) {
            return null;
        }

        if (null === $this->categoryCityMetadata) {
            $em = $this->container->get('doctrine')->getManager();
            /* @var $em EntityManager */

            $this->categoryCityMetadata = false;
            if ($this->currentCity) {
                $categoryCityMetadata = $em->getRepository('MetalCategoriesBundle:CategoryCityMetadata')
                    ->findOneBy(array('city' => $this->currentCity, 'category' => $this->currentCategory));

                if ($categoryCityMetadata) {
                    $this->categoryCityMetadata = $categoryCityMetadata;
                }
            }
        }

        $accessor = PropertyAccess::createPropertyAccessor();
        $metadataText = $accessor->getValue($this->currentCategory, $property);
        if ($this->categoryCityMetadata) {
            $metadataText = $accessor->getValue($this->categoryCityMetadata, $property) ?: $metadataText;
        }

        return $metadataText;
    }

    public function getCategoryWithAttributes(Category $category = null)
    {
        if (!$category) {
            return 'Предложения';
        }

        $categoryTitle = $this->getTitleForCategory($category);

        if ($parametersTitlesPerGroup = $this->getParametersTitlesPerGroup()) {
            if ($this->projectFamily === 'metalloprokat') {
                $this->normalizeGostParameter($parametersTitlesPerGroup);
                $parametersTitles = $this->implodeParametersGroups($parametersTitlesPerGroup, ' ', ' ');
            } else {
                $parametersTitles = $this->implodeParametersGroups($parametersTitlesPerGroup);
            }

            return $categoryTitle.' '.$parametersTitles;
        }

        return $categoryTitle;
    }

    public function getTitleForCategory(Category $category)
    {
        $categoryTitle = $category->getTitle();
        if ($this->projectFamily === 'metalloprokat' && $this->getParametersTitlesPerGroup() && Category::CATEGORY_ID_TRUBA_ST === $category->getId()) {
            $categoryTitleParts = explode(' ', $category->getTitle(), 2);
            list($categoryTitle) = $categoryTitleParts;
        }

        return $categoryTitle;
    }

    public function getSeoTitleAccusativeForEmbed(Category $category)
    {
        $categoryTitle = $category->getTitleAccusativeForEmbed();
        if ($this->projectFamily === 'metalloprokat' && $this->getParametersTitlesPerGroup() && Category::CATEGORY_ID_TRUBA_ST === $category->getId()) {
            $categoryTitleParts = explode(' ', $category->getTitleAccusativeForEmbed(), 2);
            list($categoryTitle) = $categoryTitleParts;
        }

        return $categoryTitle;
    }

    public function getHeadTitleForHumansForLandingPage(LandingPage $landingPage)
    {
        if ($h1Title = $landingPage->getMetadata()->getH1Title()) {
            return $h1Title;
        }

        return $landingPage->getTitle();
    }

    public function getMetaTitleForAllProductsPage()
    {
        $company = $this->getRequest()->attributes->get('company');
        /* @var $company Company */

        $page = $this->getRequest()->query->get('page');

        if ($parametersTitlesPerGroup = $this->getParametersTitlesPerGroup()) {
            $parametersTitles = $this->implodeParametersGroups($parametersTitlesPerGroup);

            $result = $this->currentCategory->getTitle().' '.$parametersTitles.' от компании '.$company->getTitle();
        } elseif ($this->currentCategory) {
            $result = $this->currentCategory->getTitle().' от компании '.$company->getTitle();
        } else {
            $result = 'Товары и услуги от компании '.$company->getTitle().' — '.$this->currentCountry->getDomainTitle();
        }

        if ($page) {
            $result .= ' — Страница '.$page;
        }

        return $result;
    }

    public function getHeadTitleForAllProductsPage()
    {
        $company = $this->getRequest()->attributes->get('company');
        /* @var $company Company */

        if ($parametersTitlesPerGroup = $this->getParametersTitlesPerGroup()) {
            $parametersTitles = $this->implodeParametersGroups($parametersTitlesPerGroup, ', ', ', ');
            return $this->currentCategory->getTitle().' '.$parametersTitles.' от компании '.$company->getTitle();
        }

        if ($this->currentCategory) {
            return $this->currentCategory->getTitle().' от компании '.$company->getTitle();
        }

        return 'Товары от компании '.$company->getTitle();
    }

    public function getMetaDescriptionForAllProductsPage()
    {
        $page = $this->getRequest()->query->get('page');
        if ($page) {
            return null;
        }

        $company = $this->getRequest()->attributes->get('company');
        /* @var $company Company */

        if ($parametersTitlesPerGroup = $this->getParametersTitlesPerGroup()) {
            $parametersTitles = $this->implodeParametersGroups($parametersTitlesPerGroup);
            return $this->currentCategory->getTitle().' '.$parametersTitles.' от компании '.$company->getTitle().' — каталог товаров, услуг и прайс-листов от компании на сайте. '.$this->currentCategory->getTitle().' '.$parametersTitles.' — актуальные цены на товары компании '.$company->getTitle();
        }

        if ($this->currentCategory) {
            return $this->currentCategory->getTitle().' от компании '.$company->getTitle().' — каталог товаров, услуг и прайс-листов от компании на сайте. '.$this->currentCategory->getTitle().' — актуальные цены на товары компании '.$company->getTitle();
        }

        return $company->getTitle().' — каталог предложений товаров, услуг и прайс-листов от компании на сайте. Актуальные цены на товары компании '.$company->getTitle().'. Одни из лучших предложений в '.$company->getCity()->getTitleLocative();
    }

    public function getMetaTitleForMiniSiteFrontPage()
    {
        $company = $this->getRequest()->attributes->get('company');
        /* @var $company Company */
        $page = $this->getRequest()->query->get('page');

        return $company->getTitle().' в '.$company->getCity()->getTitleLocative().' — прайс-листы, контакты, информация о компании'.($page ? ' — Страница '.$page : '');
    }

    public function getMetaDescriptionsForMiniSiteFrontPage()
    {
        $company = $this->getRequest()->attributes->get('company');
        /* @var $company Company */

        return 'Информация о компании '.$company->getTitle().': г. '.$company->getCity()->getTitle().', '.$company->getPhonesAsString().'. Прай-листы компании '.$company->getTitle().', цены на товары и услуги, отзывы на '.$this->currentCountry->getDomainTitle();
    }

    public function getMetaTitleForMiniSiteProductsPage()
    {
        $request = $this->getRequest();
        $company = $request->attributes->get('company');
        /* @var $company Company */

        $page = $request->query->get('page');
        $city = $company->getCity();
        if ($cityId = $request->query->get('city')) {
            if ($filialCity = $this->container->get('doctrine.orm.entity_manager')->getRepository('MetalTerritorialBundle:City')->find($cityId)) {
                $city = $filialCity;
            }
        }

        return $this->currentCategory->getTitle().' в '.$city->getTitleLocative().' от компании '.$company->getTitle().' — каталог товаров, цены на '.$city->getCountry()->getDomainTitle().' '.($page ? ' — Страница '.$page : '');
    }

    public function getHeadTitleForMiniSiteProductsPage()
    {
        return $this->currentCategory->getTitle();
    }

    public function getMetaDescriptionsForMiniSiteProductsPage()
    {
        $page = $this->getRequest()->query->get('page');
        if ($page) {
            return null;
        }

        $request = $this->getRequest();
        $company = $request->attributes->get('company');
        /* @var $company Company */

        $city = $company->getCity();
        if ($cityId = $request->query->get('city')) {
            if ($filialCity = $this->container->get('doctrine.orm.entity_manager')->getRepository('MetalTerritorialBundle:City')->find($cityId)) {
                $city = $filialCity;
            }
        }

        return $this->currentCategory->getTitle().' — купить в '.$city->getTitleLocative().' от компании '.$company->getTitle().': каталог товаров, цены. Заказать '.$this->currentCategory->getTitleAccusativeForEmbed().' можно по телефону: '.$company->getPhonesAsString();
    }

    public function getMetaTitleForMiniSiteProductPage(Product $product)
    {
        $company = $this->getRequest()->attributes->get('company');
        /* @var $company Company */
        $page = $this->getRequest()->query->get('page');

        $city = $product->getBranchOffice()->getCity();

        $productTitle = $product->getTitle();
        if ($product->getIsTitleNonUnique()) {
            $productTitle = $product->getTitle().', '.$this->container->getParameter('tokens.product_volume_title').' '.$product->getSize();
        }
        $title = $productTitle.' — купить в '.$city->getTitleLocative().' от компании '.$company->getTitle();

        $isTitleTooShort = mb_strlen($title) < $this->minTitleLength;
        if ($isTitleTooShort) {
            if ($product->isContractPrice()) {
                $title = $productTitle.' купить по договорной цене в '.$city->getTitleLocative().' от компании '.$company->getTitle();
            } else {
                $title = $productTitle.' '.$this->container->getParameter('tokens.buy_from').' за '.$product->getPrice().' '.$product->getCurrency()->getFallbackToken().' в '.$city->getTitleLocative().' от компании '.$company->getTitle();
            }
        }

        if ($page) {
            $title .= ' — Страница '.$page;
        }

        return $title;
    }

    public function getHeadTitleForMiniSiteProductPage(Product $product)
    {
        $company = $this->getRequest()->attributes->get('company');
        /* @var $company Company */

        $headTitle = sprintf(
            '%s в %s от %s',
            $product->getTitle(),
            $product->getBranchOffice()->getCity()->getTitleLocative(),
            $company->getTitle());

        if ($product->getIsTitleNonUnique()) {
            $headTitle = sprintf(
                '%s %s в %s от %s',
                $product->getTitle(),
                $product->getSize(),
                $product->getBranchOffice()->getCity()->getTitleLocative(),
                $company->getTitle());
        }

        return $headTitle;
    }

    public function getMetaDescriptionForMiniSiteProductPage(Product $product)
    {
        $company = $this->getRequest()->attributes->get('company');
        /* @var $company Company */

        $productTitle = $product->getTitle();
        if ($product->getIsTitleNonUnique()) {
            $productTitle = $product->getTitle().', '.$this->container->getParameter('tokens.product_volume_title').' '.$product->getSize();
        }

        return $productTitle.' — купить в '.$product->getBranchOffice()->getCity()->getTitleLocative().' от компании '.$company->getTitle().': каталог товаров, цены. Заказать '.$product->getCategory()->getTitleAccusativeForEmbed().' можно по телефону: '.$company->getPhonesAsString();
    }

    public function getCanonicalUrlForMinisite()
    {
        $request = $this->getRequest();

        if ($city = $request->query->get('city')) {
            $queryBag = $request->query->all();
            unset($queryBag['city']);

            $routeParams = array_merge($request->attributes->get('_route_params'), $queryBag);

            return $this->generateAbsoluteUrl($request->attributes->get('_route'), $routeParams);
        }

        return null;
    }

    public function getCanonicalUrlForCompanyProducts(Company $company)
    {
        if ($this->currentCity) {
            $currentBranchOffice = $this->getBranchOfficeForCurrentCity($company, $this->currentCity);

            $canonicalRequired = false;
            if (!$currentBranchOffice) {
                if ($this->currentCity->isAdministrativeCenter()) { // смотрим административный центр и в этой области у компании есть филиал без домена
                    $companyCityRepository = $this->container
                        ->get('doctrine.orm.default_entity_manager')
                        ->getRepository('MetalCompaniesBundle:CompanyCity');

                    $branchOffice = $companyCityRepository->getBranchOfficeInRegionWithoutSlug($company,
                        $this->currentCity
                    );
                    $canonicalRequired = !$branchOffice || !$branchOffice->getHasProducts();
                }
            } elseif (!$currentBranchOffice->getHasProducts()) { // в филиале нет товаров, значит мы смотрим дубли из головного офиса
                $canonicalRequired = true;
            }

            if ($canonicalRequired) {
                $request = $this->getRequest();
                $routeParameters = $request->attributes->get('_route_params');
                $routeParameters['subdomain'] = $company->getCity()->getSlugWithFallback();

                return $this->generateAbsoluteUrl($request->attributes->get('_route'), $routeParameters);
            }
        }

        return $this->getCanonicalUrlForSearchResults();
    }

    public function getCanonicalUrlForSearchResults()
    {
        $request = $this->getRequest();
        $routeParameters = $request->attributes->get('_route_params');
        $routeParameters['page'] = $this->page;
        $route = $request->attributes->get('_route');

        if ($this->getParametersTitlesPerGroup()) {
            if ($canonicalRouteWithAttributes = $this->getCanonicalRouteWithAttributes($request, $route, $routeParameters)) {
                return $canonicalRouteWithAttributes;
            }
        }

        return $this->generateAbsoluteUrl($route, $routeParameters);
    }

    public function getCanonicalRouteWithAttributes(Request $request, $route, $routeParameters)
    {
        $categoryHelper = $this->getHelper('MetalCategoriesBundle');
        /* @var $categoryHelper CategoryDefaultHelper */
        $parameters = $request->attributes->get('products_parameters');

        $parametersTitlesPerGroup = $this->getParametersTitlesPerGroup();
        if (!$parametersTitlesPerGroup) {
            return null;
        }

        $isCanonicalRequired = false;
        foreach ($parametersTitlesPerGroup as $group) {
            if (count($group) >= 2) {
                $isCanonicalRequired = true;
                break;
            }
        }

        if (!$isCanonicalRequired) {
            return null;
        }

        $categorySlug = $categoryHelper->getCategoryWithParamString($this->currentCategory, array(array_shift($parameters)));
        $routeParameters['category_slug'] = $categorySlug;

        return $this->generateAbsoluteUrl($route, $routeParameters);
    }

    /**
     * @param array|\Countable $items
     *
     * @return null|string
     */
    public function getAdditionalMetaTagsForSearchResults($items = array())
    {
        foreach ($this->getParametersTitlesPerGroup() as $group) {
            // выбрано больше 1 фильтра в группе
            if (count($group) > 1) {
                return self::META_TAG_NOINDEX_NOFOLLOW;
            }
        }

        // выбрано 3 и более групп фильтров
        if (count($this->getParametersTitlesPerGroup()) > 2) {
            return self::META_TAG_NOINDEX_NOFOLLOW;
        }

        if ($this->currentCategory && ($this->currentCategory->getNoindex() || !count($items))) {
            return self::META_TAG_NOINDEX_NOFOLLOW;
        }

        if ($this->getRequest()->query->get('order')) {
            return self::META_TAG_NOINDEX_NOFOLLOW;
        }

        if ($this->page > 50){
            return self::META_TAG_NOINDEX_FOLLOW;
        }

        $company = $this->getRequest()->attributes->get('company');
        /* @var $company Company */

        if ($company && $this->page > 1) {
            return self::META_TAG_NOINDEX_NOFOLLOW;
        }

        /*if ($this->page > 1) {
            return self::META_TAG_NOINDEX_NOFOLLOW_YANDEX;
        }*/
    }

    /**
     * @param array|\Countable $items
     *
     * @return null|string
     */
    public function getAdditionalMetaTagsForSearchResultsCatalog($items = array())
    {
        foreach ($this->getParametersTitlesPerGroup() as $group) {
            // выбрано больше 1 фильтра в группе
            if (count($group) > 1) {
                return self::META_TAG_NOINDEX_NOFOLLOW;
            }
        }

        // выбрано 2 и более групп фильтров
        if (count($this->getParametersTitlesPerGroup()) > 1) {
            return self::META_TAG_NOINDEX_NOFOLLOW;
        }

        if ($this->page > 50){
            return self::META_TAG_NOINDEX_FOLLOW;
        }

        /*if ($this->page > 1) {
            return self::META_TAG_NOINDEX_NOFOLLOW_YANDEX;
        }*/
    }

    public function getAdditionalMetaTagsForDemand(Demand $demand)
    {
        $request = $this->getRequest();
        $page = $request->get('page');

        if ($this->currentCategory->getId() != $demand->getCategory()->getId() || !$this->currentCity || $page > 1) {
            return self::META_TAG_NOINDEX_NOFOLLOW;
        }
        return self::META_TAG_NOINDEX_NOFOLLOW;
    }

    public function getAdditionalMetaTagsDemandsForSearchResults()
    {
        if ($this->currentCategory && $this->currentCategory->getNoindex()) {
            return self::META_TAG_NOINDEX_NOFOLLOW;
        }

        $request = $this->getRequest();
        $sortAttributes = $request->query->get('order');
        if ($sortAttributes) {
            return self::META_TAG_NOINDEX_NOFOLLOW;
        }

        // выбрано 2 и более групп фильтров
        if (count($this->getParametersTitlesPerGroup()) > 1) {
            return self::META_TAG_NOINDEX_NOFOLLOW;
        }

        foreach ($this->getParametersTitlesPerGroup() as $group) {
            // выбрано больше 1 фильтра в группе
            if (count($group) > 1) {
                return self::META_TAG_NOINDEX_NOFOLLOW;
            }
        }

        if ($this->page > 50){
            return self::META_TAG_NOINDEX_FOLLOW;
        }

        if ($this->page > 1) {
            return self::META_TAG_NOINDEX_NOFOLLOW_YANDEX;
        }

        return self::META_TAG_NOINDEX_NOFOLLOW;
    }

    public function getAdditionalMetaTagsForMinisiteProducts()
    {
        $request = $this->getRequest();
        $sortAttributes = $request->query->get('order');
        if ($sortAttributes) {
            return self::META_TAG_NOINDEX_NOFOLLOW;
        }

        return count($this->attributes) ? self::META_TAG_NOINDEX_NOFOLLOW : '';
    }

    public function getCanonicalUrlForDemand(Demand $demand)
    {
        if ($this->currentCategory->getId() == $demand->getCategory()->getId() && $this->currentCity) {
            return;
        }

        $routeParameters = array(
            'id' => $demand->getId(),
            'subdomain' => $demand->getCity()->getSlugWithFallback(),
            'category_slug' => $demand->getCategory()->getSlugCombined()
        );

        return $this->generateAbsoluteUrl('MetalDemandsBundle:Demand:view', $routeParameters);
    }

    public function getCanonicalUrlForMiniSiteProduct(Product $product)
    {
        $cityToRedirect = $product->getBranchOffice() ? $product->getBranchOffice()->getCity() : $product->getCompany()->getCity();
        $routeParameters['subdomain'] = $cityToRedirect->getSlugWithFallback();
        $routeParameters['id'] = $product->getId();

        return $this->generateAbsoluteUrl('MetalProductsBundle:Product:view_subdomain', $routeParameters);
    }

    /**
     * Этот метод похож на self::getCanonicalUrlForProduct, но есть небольшое отличие - когда в филиале нет продуктов - редирект не нужен
     *
     * @param Product $product
     * @return City|null
     */
    public function getCityForRedirectForProduct(Product $product)
    {
        if (!$product->getCompany()) {
            return;
        }

        //FIXME: тут должна быть какая-то своя логика, но пока нет времени ее реализовывать
//        if ($currentRegion) {
//            return;
//        }

        $redirectRequired = true; // на www всегда нужен
        if ($this->currentCity) {
            $city = $this->currentCity;
            $productCity = $product->getBranchOffice() ? $product->getBranchOffice()->getCity() : null;
            //Если у продукта есть филиал и текущий город отличается от города филиала но находятся в одной области используем город филиала
            if ($productCity && (($this->currentCity->getId() !== $productCity->getId()) && ($this->currentCity->getCityWithFallback()->getId() === $productCity->getCityWithFallback()->getId()))) {
                $city = $productCity;
            }

            $currentBranchOffice = $this->getBranchOfficeForCurrentCity($product->getCompany(), $city);

            $redirectRequired = false;
            if (!$currentBranchOffice) { // открыли товар в городе, где вообще нет филиала
                $redirectRequired = true;
            } elseif (!$product->getBranchOffice()) { // продукт не привязан к филиалу
                //FIXME: проверить на зацикливание
                $redirectRequired = true;
            } elseif ($product->getBranchOffice()->getIsMainOffice() && !$currentBranchOffice->getHasProducts()) { // в филиале нет продуктов, значит там можно показывать этот товар
                $redirectRequired = false;
            } elseif ($product->getBranchOffice()->getCity()->getId() != $this->currentCity->getId()) {
                // если открыли товар в административном центре в той же области и у филиала нет слага, то редирект не нужен
                $redirectRequired = !(
                    $this->currentCity->getRegion()->getId() == $product->getBranchOffice()->getCity()->getRegion()->getId()
                    && $this->currentCity->isAdministrativeCenter()
                    && !$product->getBranchOffice()->getCity()->getSlug()
                );
            }
        }

        if ($redirectRequired) {
            return $product->getBranchOffice() ? $product->getBranchOffice()->getCity() : $product->getCompany()->getCity();
        }
    }

    public function implodeParametersForGroup($parametersInGroup, $implodeParamSymbol = ' и ')
    {
        return implode($implodeParamSymbol, $parametersInGroup);
    }

    public function implodeParametersGroups($parametersTitlesPerGroup, $implodeParamSymbol = ' и ', $implodeGroupSymbol = ', ')
    {
        $groupedParameters = array();
        foreach ($parametersTitlesPerGroup as $group) {
            $groupedParameters[] = implode($implodeParamSymbol, $group);
        }

        return implode($implodeGroupSymbol, $groupedParameters);
    }

    public function getParametersTitlesPerGroup($useAccusative = false)
    {
        $parameters = $this->getRequest()->attributes->get('products_parameters');
        if (!$parameters) {
            return array();
        }

        $parametersTitlesPerGroup = array();
        foreach ($parameters as $param) {
            $paramTitle = $param['parameterOption']['title'];
            $paramTitle = preg_replace('/AISI(\d+)/i', 'AISI $1', $paramTitle);

            if ($useAccusative) {
                $paramTitleAccusative = $param['parameterOption']['titleAccusative'] ?: $paramTitle;
                $parametersTitlesPerGroup[$param['parameterOption']['typeId']][] = $paramTitleAccusative;
            } else {
                $parametersTitlesPerGroup[$param['parameterOption']['typeId']][] = $paramTitle;
            }
        }

        return $parametersTitlesPerGroup;
    }

    public function isCurrentPageIsHomePage()
    {
        return in_array($this->getRequest()->getRequestUri(), array('/', ''));
    }

    public function isSuspiciousReferer()
    {
        $referer = $this->getRequest()->headers->get('REFERER');
        $baseHost = $this->container->getParameter('base_host');

        return !$referer || false === strpos(parse_url($referer, PHP_URL_HOST), $baseHost);
    }

    /**
     * @param AttributesCollection $attributesCollection
     *
     * @return array
     */
    public function getAttributeValuesCombination(AttributesCollection $attributesCollection, $forCategoryPage = false)
    {
        if (null !== $this->attributeValuesCombinations) {
            return $this->attributeValuesCombinations;
        }

        $attributeValuesCombinations = array();
        $previousTitle = '';
        $previousRouteAttr = '';

        $i = 0;
        $forTwoCombination = (count($attributesCollection->getAttributesValues()) === 2) && $forCategoryPage;
        foreach ($attributesCollection->getAttributesValues() as $attributeValue) {
            if ($forTwoCombination && $i === 1) {
                $attributeValuesCombinations[$attributeValue->getSlug()] = $attributeValue->getValue();
            }
            $previousTitle .= ($i === 0 ? '' : ' ').$attributeValue->getValue();
            $previousRouteAttr .= ($i === 0 ? '' : '/').$attributeValue->getSlug();
            $attributeValuesCombinations[$previousRouteAttr] = $previousTitle;

            $i++;
        }

        return $this->attributeValuesCombinations = $attributeValuesCombinations;
    }

    public function getMetaPagination(Pagerfanta $pagerfanta = null)
    {
        if (null === $pagerfanta) {
            return '';
        }

        $links = array();
        $request = $this->getRequest();
        $routeParameters = array_merge($request->attributes->get('_route_params'), $request->query->all());
        $route = $request->attributes->get('_route');
        $twig = $this->container->get('twig');

        if ($pagerfanta->hasPreviousPage()) {
            $routeParameters['page'] = $pagerfanta->getPreviousPage();
            if ($routeParameters['page'] === 1) {
                $routeParameters['page'] = null;
            }
            $url = $this->generateAbsoluteUrl($route, $routeParameters);
            $links[] = sprintf('<link rel="prev" href="%s"/>', twig_escape_filter($twig, $url));
        }

        if ($pagerfanta->hasNextPage()) {
            $routeParameters['page'] = $pagerfanta->getNextPage();
            $url = $this->generateAbsoluteUrl($route, $routeParameters);
            $links[] = sprintf('<link rel="next" href="%s"/>', twig_escape_filter($twig, $url));
        }

        return implode("\n", $links);
    }

    public function renderStringTemplate($template, array $extraContext = [])
    {
        $em = $this->container->get('doctrine.orm.default_entity_manager');
        /* @var $em EntityManager */

        $context = [
            'category' => '',
            'category_genitive' => '',
            'category_accusative' => '',
            'category_prepositional' => '',
            'category_ablative' => '',

            'available_attributes' => '',
            'attributes_values' => new AttributesCollectionTwigSerializer(new AttributesCollection()),

            'territory' => $this->getCurrentTerritory()->getTitle(),
            'territory_locative' => $this->getLocationTitle(),
            'page' => $this->page,
            'host' => $this->getRequest()->getHost(),
            'domain_title' => $this->currentCountry->getDomainTitle(),
            'callback_phone' => $this->currentCountry->getCallbackPhone(),
        ];

        if ($this->currentCategory) {
            $context['category'] = $this->currentCategory->getTitle();
            $context['category_genitive'] = $this->currentCategory->getTitleGenitive();
            $context['category_accusative'] = $this->currentCategory->getTitleAccusative();
            $context['category_prepositional'] = $this->currentCategory->getTitlePrepositional();
            $context['category_ablative'] = $this->currentCategory->getTitleAblative();

            $attributeCategoryRepository = $em->getRepository('MetalAttributesBundle:AttributeCategory');
            $attributes = $attributeCategoryRepository->getAttributesForCategory($this->currentCategory);
            $attributesTitles = [];
            foreach ($attributes as $attribute) {
                $attributesTitles[] = $attribute->getTitle();
            }

            $context['available_attributes'] = implode(', ', $attributesTitles);
            $context['attributes_values'] = new AttributesCollectionTwigSerializer($this->attributes);
        }

        //FIXME: доработать случаи, когда категория приходит из контекста

        return $this->container->get('twig')->createTemplate($template)->render(array_replace($context, $extraContext));
    }

    /**
     * @param Company $company
     * @param City $city
     *
     * @return \Metal\CompaniesBundle\Entity\CompanyCity|null
     */
    protected function getBranchOfficeForCurrentCity(Company $company, City $city)
    {
        $key = sprintf('%d-%d', $company->getId(), $city->getId());

        if (array_key_exists($key, $this->loadedBranchOffices)) {
            return $this->loadedBranchOffices[$key];
        }

        return $this->loadedBranchOffices[$key] = $this->container
            ->get('doctrine.orm.default_entity_manager')
            ->getRepository('MetalCompaniesBundle:CompanyCity')
            ->findOneBy(array('company' => $company, 'city' => $city));
    }

    protected function normalizeGostParameter(array &$parametersTitlesPerGroup)
    {
        // на продукте нет гостов
        if ($this->projectFamily === 'product') {
            return;
        }

        if (isset($parametersTitlesPerGroup[ParameterGroup::PARAMETER_GOST])) {
            foreach ($parametersTitlesPerGroup[ParameterGroup::PARAMETER_GOST] as $i => $title) {
                if (false === mb_stripos($title, 'гост') && preg_match('/^\d{2,6}-\d{1,6}$/', $title)) {
                    $title = 'ГОСТ '.$title;
                } elseif (false === mb_stripos($title, 'ту')) {
                    $title = 'ТУ '.$title;
                }

                $parametersTitlesPerGroup[ParameterGroup::PARAMETER_GOST][$i] = $title;
            }
        }
    }

    protected function getLocationTitle()
    {
        if ($this->currentCity) {
            return $this->currentCity->getTitleLocative();
        }

        if ($this->currentRegion) {
            return $this->currentRegion->getTitleLocative();
        }

        return $this->currentCountry->getTitleLocative();
    }

    /**
     * @return TerritoryInterface
     */
    protected function getCurrentTerritory()
    {
        if ($this->currentCity) {
            return $this->currentCity;
        }

        if ($this->currentRegion) {
            return $this->currentRegion;
        }

        return $this->currentCountry;
    }

    protected function isRussiaMainSite()
    {
        return !$this->currentCity && !$this->currentRegion &&
            $this->currentCountry->getId() == Country::COUNTRY_ID_RUSSIA;
    }

    protected function isMoscow()
    {
        return $this->currentCity && $this->currentCity->getId() == City::SEO_TOP_CITY_ID;
    }

    protected function generateAbsoluteUrl($route, $routeParameters)
    {
        return $this->container->get('router')->generate($route, $routeParameters, UrlGeneratorInterface::ABSOLUTE_URL);
    }

    public function getAdditionalMetaTagsDomains()
    {
        $hreflang = '';
        $url = $this->getRequest()->getRequestUri();
        if (!$this->currentCity && !$this->currentRegion) {
            $hreflang = sprintf('<link rel="alternate" hreflang="ru-RU" href="https://www.metalloprokat.ru%s" />
            <link rel="alternate" hreflang="ru-UA" href="https://www.metalloprokat.net.ua%s" />
            <link rel="alternate" hreflang="ru-BY" href="https://www.metalaprakat.by%s" />
            <link rel="alternate" hreflang="ru-KZ" href="https://www.metalloprokat.kz%s" />', $url, $url, $url, $url);
        }
        return $hreflang;
    }
}
