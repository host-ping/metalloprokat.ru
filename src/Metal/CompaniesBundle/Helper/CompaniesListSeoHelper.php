<?php

namespace Metal\CompaniesBundle\Helper;

use Metal\ProductsBundle\Widget\ProductsTabsWidget;
use Metal\ProjectBundle\Helper\SeoHelper as BaseSeoHelper;

class CompaniesListSeoHelper extends BaseSeoHelper
{
    public function getMetaTitleForCompanyCatalogPage()
    {
        $page = $this->page;

        if (!$this->currentCategory) {
            $result = 'Каталог '.$this->container->getParameter('tokens.companies_type').' в '.$this->getLocationTitle().': '.$this->container->getParameter('tokens.companies').' — '.$this->currentCountry->getDomainTitle().' '.$this->getLocationTitle();

            if ($page) {
                $result .= ' — Страница '.$page;
            }

            return $result;
        }

        $template = '{{ category }} {{ attributes_values }} оптом от производителя в {{ territory_locative }} — каталог компаний, продажа, производство{% if page %} — Страница {{ page }}{% endif %} | {{ host|capitalize }}';

        return $this->renderStringTemplate($template);
    }

    public function getHeadTitleForCompanyCatalogPage(ProductsTabsWidget $productsTabsWidget)
    {
        if (!$this->currentCategory) {
            return 'Компании, торгующие '.$this->container->getParameter('tokens.product_title.instrumental').' в '.$this->getLocationTitle();
        }

        $template = <<<'TWIG'
{{ category }} {{ attributes_values }} оптом от производителя в {{ territory_locative }}.{% if page %} — Cтраница {{ page }} — {{ host }}{% endif %}.
TWIG;

        return $this->renderStringTemplate($template, $this->getExtraContext($productsTabsWidget));
    }

    public function getMetaDescriptionForCompanyCatalogPage(ProductsTabsWidget $productsTabsWidget)
    {
        if ($this->page > 1 && $this->currentCategory) {
            $template = 'Компании, торгующие {{ category_ablative|lcfirst }} {{ attributes_values }} в {{ territory_locative }} — Cтраница {{ page }} — {{ host }}.';

            return $this->renderStringTemplate($template);
        }

        if (!$this->currentCategory) {
            return ucfirst($this->container->getParameter('tokens.catalog_companies')).' в '.$this->getLocationTitle().'. '.ucfirst($this->container->getParameter('tokens.companies_info'));
        }

        $template = <<<'TWIG'
{{ category }} {{ attributes_values }} оптом от производителя в {{ territory_locative }}: — Каталог проверенных поставщиков. — {{ '%count_formatted% компания|%count_formatted% компании|%count_formatted% компаний'|pluralize(companies_count) }}. ➤ Цены. ➤ Контакты. ➤ Официальные сайты.{% if callback_phone %} Тел.:{{ callback_phone }}{% endif %}.
TWIG;

        return $this->renderStringTemplate($template, $this->getExtraContext($productsTabsWidget));
    }

    protected function getExtraContext(ProductsTabsWidget $productsTabsWidget)
    {
        $counts = $productsTabsWidget->getCounts();

        return [
            'products_count' => $counts['products_count'],
            'companies_count' => $counts['companies_count'],
        ];
    }
}
