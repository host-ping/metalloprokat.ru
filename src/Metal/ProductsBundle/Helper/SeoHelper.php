<?php

namespace Metal\ProductsBundle\Helper;

use Metal\CompaniesBundle\Repository\CompanyCityRepository;
use Metal\ProductsBundle\Entity\Product;
use Metal\ProjectBundle\Helper\SeoHelper as BaseSeoHelper;

class SeoHelper extends BaseSeoHelper
{
    public function getMetaTitleForProductPage(Product $product)
    {
        $template = <<<'TWIG'
{{ product }} в {{ territory_locative }} купить от компании {{ company }}{% if page %} — Страница {{ page }}{% endif %} 
TWIG;

        if ($this->isMoscow()) {
            $template = <<<'TWIG'
{{ product }} в {{ territory_locative }} купить от компании {{ company }}{% if page %} — Страница {{ page }}{% endif %}
TWIG;
        }

        return $this->renderStringTemplate($template, $this->getExtraContext($product));
    }

    public function getMetaDescriptionForProductPage(Product $product)
    {
        if ($this->page) {
            return null;
        }

        if ($product->getIsContractPrice()) {
            $template = <<<'TWIG'
{{ product }} в наличии купить с доставкой со склада в {{ territory_locative }} от компании «{{ company }}». Прямые поставки. Звоните по тел. {{ companyPhone }}.
TWIG;
        }
        else {
            $template = <<<'TWIG'
{{ product }} в наличии купить по цене {{ price }} с доставкой со склада в {{ territory_locative }} от компании «{{ company }}». Прямые поставки. Звоните по тел. {{ companyPhone }}.
TWIG;
        }

        return $this->renderStringTemplate($template, $this->getExtraContext($product));
    }

    public function getHeadTitleForProductPage(Product $product)
    {
        return $product->getTitle().' в '.$this->currentCity->getTitleLocative();
    }

    public function getProductTitleForSeo(Product $product)
    {
        $productString = $product->getTitle().' купить в '.$this->currentCity->getTitleLocative().' по';
        $formater = $this->container->get('sonata.intl.templating.helper.number');

        if ($product->isContractPrice()) {
            $priceTitle = ' договорной цене';
        } else {
            $priceTitle = ' цене ';
            if ($product->getIsPriceFrom()) {
                $priceTitle .= 'от ';
            }

            if ($product->getAttribute('normalized_price')) {
                $priceTitle .= $formater->formatDecimal($product->getAttribute('normalized_price')).' '.$product->getCurrency()->getFallbackToken();
            } else {
                $priceTitle .= $formater->formatDecimal($product->getPrice()).' '.$this->currentCountry->getCurrency()->getFallbackToken();
            }

            if ($product->getMeasureId()) {
                $priceTitle .= ' за '.$product->getMeasure()->getTokenPriceForWidget();
            }
        }

        return $productString.$priceTitle.'.';
    }

    public function getAdditionalMetaTagsForProduct(Product $product)
    {
        $request = $this->getRequest();

        if (!$this->currentCity || $this->page > 1 || $request->query->get('tab')) {
            return self::META_TAG_NOINDEX_NOFOLLOW;
        }
    }

    public function getCanonicalUrlForProduct(Product $product)
    {
        $request = $this->getRequest();

        $canonicalRequired = true; // на www или при просмотре области всегда нужен
        if ($this->currentCity) {
            $currentBranchOffice = $this->getBranchOfficeForCurrentCity($product->getCompany(), $this->currentCity);

            $canonicalRequired = false;
            if (!$currentBranchOffice) { // открыли товар в городе, где вообще нет филиала
                $canonicalRequired = true;
            } elseif (!$product->getBranchOffice()) { // продукт не привязан к филиалу
                $canonicalRequired = true;
            } elseif ($product->getBranchOffice()->getCity()->getId() != $this->currentCity->getId()) {
                $canonicalRequired = true;
                // если смотрим товар через административныц центр области, а город с товаром без слага
                if ($this->currentCity->isAdministrativeCenter() && !$product->getBranchOffice()->getCity()->getSlug()) {
                    /** @var CompanyCityRepository $companyCityRepository */
                    $companyCityRepository = $this->container
                        ->get('doctrine.orm.default_entity_manager')
                        ->getRepository('MetalCompaniesBundle:CompanyCity');

                    $branchOffice = $companyCityRepository
                        ->getBranchOfficeInRegionWithoutSlug($product->getCompany(), $this->currentCity);

                    $canonicalRequired = !$branchOffice || !$branchOffice->getHasProducts();
                }
            }
        }

        if (!$canonicalRequired && !$request->query->get('tab')) {
            return;
        }

        $cityToRedirect = $product->getBranchOffice() ? $product->getBranchOffice()->getCity() : $product->getCompany()->getCity();
        $route = $request->attributes->get('_route');
        $routeParameters = $request->attributes->get('_route_params');
        $routeParameters['tab'] = null;
        $routeParameters['subdomain'] = $cityToRedirect->getSlugWithFallback();

        return $this->generateAbsoluteUrl($route, $routeParameters);
    }

    private function getExtraContext(Product $product)
    {
        $phone = ($product->getCompany()->getAttribute('phones_string') ? $product->getCompany()->getAttribute('phones_string') : ($product->getCompany()->getContactInfo()->getPhonesAsString() ? $product->getCompany()->getContactInfo()->getPhonesAsString() : $product->getCompany()->getPhonesAsString()));
        $phoneToShow = preg_replace("/[^-()0-9+\s]/", "", mb_substr($phone, 0, 18));

        $phoneArr = preg_split("/[,;]/", $phone);
        if (count($phoneArr) > 0) {
            $phoneToShow = preg_replace("/[^-()0-9+\s]/", "", $phoneArr[0]);
        }

        return [
            'product' => $this->getUniqueProductTitle($product),
            'company' => preg_replace('/["]/',"", $product->getCompany()->getTitle()),
            'price' => $product->getPrice()." ".$product->getCurrency()->getToken()." за ".$product->getMeasure()->getToken(),
            'companyPhone' => trim($phoneToShow)
        ];
    }

    private function getUniqueProductTitle(Product $product)
    {
        $productTitle = self::limitText($product->getTitle());
        if ($product->getIsTitleNonUnique()) {
            $productTitle = $productTitle.', '.$this->container->getParameter('tokens.product_volume_title').' '.$product->getSize();
        }

        return $productTitle;
    }
}
