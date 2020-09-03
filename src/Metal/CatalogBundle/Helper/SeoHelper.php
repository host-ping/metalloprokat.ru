<?php

namespace Metal\CatalogBundle\Helper;

use Brouzie\Bundle\HelpersBundle\Helper\HelperAbstract;
use Metal\AttributesBundle\Entity\Attribute;
use Metal\AttributesBundle\Entity\DTO\AttributesCollection;
use Metal\CatalogBundle\Entity\Brand;
use Metal\CatalogBundle\Entity\Manufacturer;
use Metal\CatalogBundle\Entity\Product;
use Metal\CategoriesBundle\Entity\Category;
use Metal\TerritorialBundle\Entity\City;
use Metal\TerritorialBundle\Entity\Country;
use Metal\TerritorialBundle\Entity\Region;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SeoHelper extends HelperAbstract
{
    const META_TAG_NOINDEX_NOFOLLOW = '<meta name="robots" content="noindex, nofollow"/>';

    /**
     * @var Region|null
     */
    private $currentRegion;

    /**
     * @var City|null
     */
    private $currentCity;

    /**
     * @var Country
     */
    private $currentCountry;

    /**
     * @var Category|null
     */
    private $currentCategory;

    /**
     * @var AttributesCollection
     */
    private $attributes;

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
    }

    public function getAdditionalMetaTagForProductPage()
    {
        $page = $this->getRequest()->get('page');
        if ($page && $page > 1) {
            return self::META_TAG_NOINDEX_NOFOLLOW;
        }

        return null;
    }

    /**
     * @param array|\Countable $items
     *
     * @return null|string
     */
    public function getAdditionalMetaTagsForSearchResults($items = array())
    {
        if ($this->currentCategory && ($this->currentCategory->getNoindex() || !count($items))) {
            return self::META_TAG_NOINDEX_NOFOLLOW;
        }

        foreach ($this->attributes as $attribute => $attributeValues) {
            /* @var $attribute Attribute */
            if (!$attribute->getIndexableForSeo()) {
                return self::META_TAG_NOINDEX_NOFOLLOW;
            }

            if (count($attributeValues) > 1) { // если выбрано больше 1 значения атрибута в группе - не индексируем
                return self::META_TAG_NOINDEX_NOFOLLOW;
            }
        }

        return null;
    }

    public function getMetaTitleForManufacturersPage()
    {
        $page = $this->getRequest()->query->get('page');

        $headTitle = $this->getLocationTitle().' — Производители на '.$this->getCountryDomainTitle();

        if ($page) {
            $headTitle .= ' — Страница '.$page;
        }

        if (!$this->currentCategory) {
            return 'В '.$headTitle;
        }

        return $this->currentCategory->getTitle().' в '.$headTitle;
    }

    public function getHeadTitleForManufacturersPage()
    {
        if (!$this->currentCategory) {
            return 'В '.$this->getLocationTitle().' — Производители';
        }

        return $this->currentCategory->getTitle().' в '.$this->getLocationTitle().' — Производители';
    }

    public function getMetaDescriptionForManufacturersPage()
    {
        if (!$this->currentCategory) {
            return 'Купить от производителя в '.$this->getLocationTitle().' по лучшей цене на '.$this->getCountryDomainTitle().'. Посмотреть товары от производителя в каталоге';
        }

        return $this->currentCategory->getTitle().' — купить от производителя в '.$this->getLocationTitle().' по лучшей цене на '.$this->getCountryDomainTitle().'. Посмотреть товары от производителя в каталоге '.$this->currentCategory->getTitle();
    }

    public function getMetaTitleForBrandPage(Brand $brand)
    {
        if ($brand->getManufacturer()) {
            return $brand->getTitle().' от '.$brand->getManufacturer()->getTitle().' в '.$this->getLocationTitle().' — купить продукты оптом на '.$this->getCountryDomainTitle();
        }

        return $brand->getTitle().' в '.$this->getLocationTitle().' — купить продукты оптом на '.$this->getCountryDomainTitle();
    }

    public function getHeadTitleForBrandPage(Brand $brand)
    {
        return $brand->getTitle().' в '.$this->getLocationTitle();
    }

    public function getMetaDescriptionForBrandPage(Brand $brand)
    {
        return 'Продукты от производителя '.$brand->getTitle().' в '.$this->getLocationTitle().' купить оптом или в розницу по лучшей цене на '.$this->getCountryDomainTitle().' Посмотреть товары от производителя '.$brand->getTitle();
    }

    public function getMetaTitleForManufacturerPage(Manufacturer $manufacturer)
    {
        return $manufacturer->getTitle().' в '.$this->getLocationTitle().' — купить продукты оптом на '.$this->getCountryDomainTitle();
    }

    public function getHeadTitleForManufacturerPage(Manufacturer $manufacturer)
    {
        return $manufacturer->getTitle().' в '.$this->getLocationTitle();
    }

    public function getMetaDescriptionForManufacturerPage(Manufacturer $manufacturer)
    {
        return 'Продукты от производителя '.$manufacturer->getTitle().' купить в '.$this->getLocationTitle().' оптом или в розницу по лучшей цене на '.$this->getCountryDomainTitle();
    }

    public function getMetaTitleForBrandsPage()
    {
        $page = $this->getRequest()->query->get('page');

        $headTitle = $this->getHeadTitleForBrandsPage().' на '.$this->getCountryDomainTitle();

        if ($page) {
            $headTitle .= ' — Страница '.$page;
        }

        return $headTitle;
    }

    public function getHeadTitleForBrandsPage()
    {
        if (!$this->currentCategory) {
            return 'Бренды в '.$this->getLocationTitle();
        }

        return 'Бренды '.$this->currentCategory->getTitleGenitiveForEmbed().' в '.$this->getLocationTitle();
    }

    public function getMetaDescriptionForBrandsPage()
    {
        if (!$this->currentCategory) {
            return 'В '.$this->getLocationTitle().' — каталог брендов и производителей. Посмотреть товары от производителей в рубрике';
        }

        return $this->currentCategory->getTitle().' в '.$this->getLocationTitle().' — каталог брендов и производителей. Посмотреть товары от производителей в рубрике '.$this->currentCategory->getTitle();
    }

    public function getMetaTitleForProductsPage()
    {
        $page = $this->getRequest()->query->get('page');

        $headTitle = $this->getHeadTitleForProductsPage();
        if ($this->container->getParameter('project.family') === 'stroy') {
            $headTitle = $this->getHeadTitleForStroyProductsPage();
        }

        $headTitle .= ' оптом в '.$this->getLocationTitle();

        if ($page) {
            $headTitle .= ' — Страница '.$page;
        }

        return $headTitle;
    }

    //TODO: нужно согласовать что писать, когда нет категории
    public function getHeadTitleForProductsPage()
    {
        if (!$this->currentCategory) {
            return 'Продукты';
        }

        return 'Продукты '.$this->currentCategory->getTitleGenitiveForEmbed().' '.$this->attributes->toString(' и ', ', ', 'valueAccusativeForEmbed');
    }

    public function getHeadTitleForStroyProductsPage()
    {
        if (!$this->currentCategory) {
            return '';
        }

        return $this->currentCategory->getTitle().' '.$this->attributes->toString(' и ', ', ', 'valueAccusativeForEmbed');
    }

    //TODO: нужно согласовать что писать, когда нет категории
    public function getMetaDescriptionForProductsPage()
    {
        if (!$this->currentCategory) {
            return 'Каталог продуктов в '.$this->getLocationTitle();
        }

        return $this->currentCategory->getTitle().' '.$this->attributes->toString().' — каталог продуктов в '.$this->getLocationTitle().'. '.$this->currentCategory->getTitle().' выбрать продавца и купить оптом.';
    }

    public function getMetaTitleForProductPage(Product $product)
    {
        $productTitle = $product->getTitle();
        if ($product->getIsTitleNonUnique()) {
            $productTitle = $product->getTitle().' '.$product->getSize();
        }

        return $productTitle.' от '.$product->getBrand()->getValue().' — купить в '.$this->getLocationTitle();
    }

    public function getMetaDescriptionForProductPage(Product $product)
    {
        $productTitle = $product->getTitle();
        if ($product->getIsTitleNonUnique()) {
            $productTitle = $product->getTitle().' '.$product->getSize();
        }

        return $productTitle.' от '.$product->getBrand()->getValue().' — выбрать продавца и купить оптом в '.$this->getLocationTitle();
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

    protected function getCountryDomainTitle()
    {
        return $this->currentCountry->getDomainTitle();
    }

    public function getCanonicalUrlForProducts()
    {
        if (!$this->currentCategory) {
            return null;
        }

        $attributeValues = $this->attributes->getAttributesValues();
        if (count($attributeValues) < 2) {
            return null;
        }

        $request = $this->getRequest();
        $route = $request->attributes->get('_route');
        $routeParameters = $request->attributes->get('_route_params');
        //  если выбрано несколько значений атрибутов - генерируем ссылку на первый
        $routeParameters['category_slug'] = $this->currentCategory->getUrl(array_shift($attributeValues)->getSlug());

        return $this->container->get('router')->generate($route, $routeParameters, true);
    }

    public function getCanonicalUrl($noSubdomain = false)
    {
        $request = $this->getRequest();

        if (!$this->currentCity) {
            return null;
        }

        $route = $request->attributes->get('_route');
        $routeParameters = $request->attributes->get('_route_params');
        if ($noSubdomain) {
            return $request->getScheme().'://www.'.$this->container->getParameter('base_host').$this->container->get('router')->generate($route, $routeParameters);
        }

        $routeParameters['subdomain'] = 'www';

        return $this->container->get('router')->generate($route, $routeParameters, true);
    }
}
