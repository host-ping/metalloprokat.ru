<?php

namespace Metal\ProductsBundle\Helper;

use Metal\CategoriesBundle\Entity\Category;
use Metal\CategoriesBundle\Entity\ParameterGroup;
use Metal\ProductsBundle\Widget\ProductsTabsWidget;
use Metal\ProjectBundle\Entity\SeoTemplate;
use Metal\ProjectBundle\Helper\SeoHelper as BaseSeoHelper;
use Metal\TerritorialBundle\Entity\Country;

class ProductsListSeoHelper extends BaseSeoHelper
{
    /**
     * @var SeoTemplate|false|null
     */
    protected $seoTemplate;

    public function getAdditionalMetaTagsForSearchResults($items = array())
    {
        if ($this->getSeoTemplate()) {
            if ($this->currentCategory && ($this->currentCategory->getNoindex() || !count($items))) {
                return self::META_TAG_NOINDEX_NOFOLLOW;
            }

            if ($this->getRequest()->query->get('order')) {
                return self::META_TAG_NOINDEX_NOFOLLOW;
            }

            //return null;
        }

        return parent::getAdditionalMetaTagsForSearchResults($items);
    }

    public function getCanonicalUrlForSearchResults()
    {
        if ($this->getSeoTemplate()) {
            $request = $this->getRequest();
            $routeParameters = $request->attributes->get('_route_params');
            $routeParameters['page'] = $this->page;
            $route = $request->attributes->get('_route');

            return $this->generateAbsoluteUrl($route, $routeParameters);
        }

        return parent::getCanonicalUrlForSearchResults();
    }

    public function getMetaTitleForCategoryPage(Category $category = null, ProductsTabsWidget $productsTabsWidget = null)
    {
        // правила для 2+ страницы
        if ($this->page) {
            $template = '{{ category }} {{ attributes_values }} в {{ territory_locative }}. — Страница {{ page }}.';

            return $this->renderStringTemplate($template);
        }

        if (!$category) {
            if ($this->projectFamily === 'product' && $this->getRequest()->attributes->get('_route') === 'MetalProductsBundle:Products:products_list_without_sort') {
                $result = 'Последние добавленные предложения в '.$this->getLocationTitle().' — '.$this->currentCountry->getDomainTitle();
            } else {
                $result = 'Товары и услуги '.$this->container->getParameter('tokens.companies_type').' в '.$this->getLocationTitle().', цены и прайс-листы — '.$this->currentCountry->getDomainTitle();
            }

            if ($this->page) {
                $result .= ' — Страница '.$this->page;
            }

            return $result;
        }

        // динамические правила из базы
        if ($productsTabsWidget && !$this->page && $seoTemplate = $this->getSeoTemplate()) {
            if ($template = $seoTemplate->getMetadata()->getTitle()) {
                return $this->renderStringTemplate($template, $this->getExtraContext($productsTabsWidget));
            }
        }

        // динамические правила из базы - старое
        if ($metaTitle = $this->getMetadataProperty('metadata.title')) {
            if ($this->page) {
                $metaTitle .= ' — Страница '.$this->page;
            }

            return $metaTitle;
        }

        // общие правила
        if ($this->projectFamily === 'metalloprokat') {
            if ($category->getId()) {
                $titleAccusative = lcfirst($category->getTitleAccusative());
            }
            else {
                $titleAccusative = $category->getTitle();
            }
            $template = $category->getTitle().' {{ attributes_values }} купить в {{ territory_locative }}, цены на '.$titleAccusative.' — стоимость, поставщики';
            if ($this->currentCity || $this->currentRegion) {
                $template = $category->getTitle().' {{ attributes_values }} купить в {{ territory_locative }}, цены на '.$titleAccusative.' — стоимость, поставщики | Металлопрокат';
            }

            return $this->renderStringTemplate($template);
        }

        if ($parametersTitlesPerGroup = $this->getParametersTitlesPerGroup()) {
            $categoryTitleWithParameters = $category->getTitle();
            if ($this->projectFamily !== 'metalloprokat' || !in_array(Category::CATEGORY_ID_TRUBA, $category->getBranchIds())) {
                $this->normalizeGostParameter($parametersTitlesPerGroup);
                $parametersTitles = $this->implodeParametersGroups($parametersTitlesPerGroup, ' и ', ' ');

                $categoryTitleWithParameters .= ' '.$this->implodeParametersGroups($parametersTitlesPerGroup, ' и ', ' ');
            } else {
                $parametersTypeOrder = array(
                    ParameterGroup::PARAMETER_VID,
                    ParameterGroup::PARAMETER_TIP,
                    ParameterGroup::PARAMETER_GOST,
                    ParameterGroup::PARAMETER_MARKA,
                    ParameterGroup::PARAMETER_RAZMER,
                );

                $this->normalizeGostParameter($parametersTitlesPerGroup);
                $parametersTitles = $this->implodeParametersGroups($this->getParametersTitlesPerGroup(true));

                $parametersTitlesForTrubaSt = '';
                $categoryTitleParts = explode(' ', $categoryTitleWithParameters, 2);
                list($categoryTitleWithParameters) = $categoryTitleParts;
                $usedTypesIds = array();
                if (count($categoryTitleParts) == 2) {
                    foreach ($parametersTypeOrder as $parameterTypeId) {
                        if (isset($parametersTitlesPerGroup[$parameterTypeId])) {
                            $categoryTitleWithParameters .= ' '.$this->implodeParametersForGroup($parametersTitlesPerGroup[$parameterTypeId]);
                            $usedTypesIds[$parameterTypeId] = true;
                            break;
                        }
                    }

                    if ($this->projectFamily !== 'metalloprokat' || $category->getId() !== Category::CATEGORY_ID_TRUBA_ST) {
                        $categoryTitleWithParameters .= ' '.$categoryTitleParts[1];
                    }
                }

                $usedTypeIds = [];
                foreach ($parametersTypeOrder as $parameterTypeId) {
                    $usedTypeIds[$parameterTypeId] = true;
                    if (isset($parametersTitlesPerGroup[$parameterTypeId]) && empty($usedTypesIds[$parameterTypeId])) {
                        $categoryTitleWithParameters .= ' '.$this->implodeParametersForGroup($parametersTitlesPerGroup[$parameterTypeId]);
                    }
                    if (isset($parametersTitlesPerGroup[$parameterTypeId]) && $this->projectFamily === 'metalloprokat' && $category->getId() === Category::CATEGORY_ID_TRUBA_ST) {
                        $parametersTitlesForTrubaSt .= ' '.$this->implodeParametersForGroup($this->getParametersTitlesPerGroup(true)[$parameterTypeId]);
                    }
                }

                foreach ($parametersTitlesPerGroup as $parameterTypeId => $parametersTitlesInGroup) {
                    if (!isset($usedTypeIds[$parameterTypeId])) {
                        $categoryTitleWithParameters .= ' '.$this->implodeParametersForGroup($parametersTitlesPerGroup[$parameterTypeId]);
                    }
                }

                $parametersTitlesForTrubaSt = trim($parametersTitlesForTrubaSt);
                if ($parametersTitlesForTrubaSt) {
                    $parametersTitles = $parametersTitlesForTrubaSt;
                }

            }
            // Арматура Ат800 в Екатеринбурге купить, цены на арматуру - Металлопрокат.ру
            if ($this->currentCity || $this->currentRegion) {
                $result = $categoryTitleWithParameters.' в '.$this->getLocationTitle().' '.$this->container->getParameter('tokens.buy_from').', цены на '.$category->getTitleAccusativeForEmbed().' - '.$this->currentCountry->getDomainTitle();
                if ($this->projectFamily === 'product' || ($this->projectFamily === 'metalloprokat' && (Category::CATEGORY_ID_TRUBA_ST === $category->getId()))) {
                    $result = $categoryTitleWithParameters.' '.$this->container->getParameter('tokens.buy_from').' в '.$this->getLocationTitle().' — продажа, цены на '.$this->getSeoTitleAccusativeForEmbed($category).' '.$parametersTitles.' — '.$this->currentCountry->getDomainTitle();
                }
            } else {
                $result = $categoryTitleWithParameters.' '.$this->container->getParameter('tokens.buy_from').', цены на '.$category->getTitleAccusativeForEmbed().' - '.$this->currentCountry->getDomainTitle();
                if ($this->projectFamily === 'product' || ($this->projectFamily === 'metalloprokat' && (Category::CATEGORY_ID_TRUBA_ST === $category->getId()))) {
                    $result = $categoryTitleWithParameters.' '.$this->container->getParameter('tokens.buy_from').' — продажа, цены на '.$this->getSeoTitleAccusativeForEmbed($category).' '.$parametersTitles.' — '.$this->currentCountry->getDomainTitle();
                }
            }

            if ($this->page) {
                $result .= ' — Страница '.$this->page;
            }

            return $result;
        }
        // Металлопрофиль в Екатеринбурге купить, цены на металлопрофиль - Металлопрокат.ру
        if ($this->currentCity || $this->currentRegion) {
            $result = $category->getTitle().' в '.$this->getLocationTitle().' '.$this->container->getParameter('tokens.buy_from').', цены на '.$category->getTitleAccusativeForEmbed().' - '.$this->currentCountry->getDomainTitle();
            if ($this->projectFamily === 'product') {
                $result = $category->getTitle().' оптом в '.$this->getLocationTitle().' купить, сравнить цены - '.$this->currentCountry->getDomainTitle();
            }
        } else {
            $result = $category->getTitle().' '.$this->container->getParameter('tokens.buy_from').', цены на '.$category->getTitleAccusativeForEmbed().' - '.$this->currentCountry->getDomainTitle();
            if ($this->projectFamily === 'product') {
                $result = $category->getTitle().' оптом купить, сравнить цены - '.$this->currentCountry->getDomainTitle();
            }
        }

        return $result;
    }

    public function getMetaDescriptionForCategoryPage(Category $category = null, ProductsTabsWidget $productsTabsWidget = null)
    {
        // правила для 2+ страницы
        if ($this->page) {
            $template = '{{ category }} {{ attributes_values }} в {{ territory_locative }}. ✔ Цены. ✔ Широкий ассортимент. ✔ Подбор по параметрам. — Страница {{ page }}.';

            return $this->renderStringTemplate($template);
        }

        // динамические правила из базы
        if ($productsTabsWidget && !$this->page && $seoTemplate = $this->getSeoTemplate()) {
            if ($template = $seoTemplate->getMetadata()->getDescription()) {
                return $this->renderStringTemplate($template, $this->getExtraContext($productsTabsWidget));
            }
        }

        // общие правила
        if ('metalloprokat' === $this->projectFamily && $productsTabsWidget) {
            $template =
                <<<'TWIG'
{{ category }} {{ attributes_values }} купить в {{ territory_locative }} по лучшей цене оптом и в розницу. ✔ {{ '%count_formatted% поставщик|%count_formatted% поставщика|%count_formatted% поставщиков'|pluralize(companies_count) }}. ✔ Прямые продажи. ✔ Продукция в наличии. ♻ Контакты компаний без посредников. Каталог {{ host|lcfirst }}.
TWIG;

            if ($this->currentCity || $this->currentRegion) {
                $template = <<<'TWIG'
{{ category }} {{ attributes_values }} купить в {{ territory_locative }} оптом и в розницу. ✔ Сортировка по цене. ✔ Широкий ассортимент. ✔ {{ '%count_formatted% поставщик|%count_formatted% поставщика|%count_formatted% поставщиков'|pluralize(companies_count) }} и {{ '%count_formatted% предложение|%count_formatted% предложения|%count_formatted% предложений'|pluralize(products_count) }}. ♻ Прямые контакты производителей в каталоге Металлопрокат.
TWIG;
            }

            return $this->renderStringTemplate($template, $this->getExtraContext($productsTabsWidget));
        }

        if (!$category) {
            return 'Товары и услуги '.$this->container->getParameter('tokens.companies_type').' в '.$this->getLocationTitle().', цены и прайс-листы — '.$this->currentCountry->getDomainTitle().' '.$this->getLocationTitle();
        }

        if ($metaDescription = $this->getMetadataProperty('metadata.description')) {
            return $metaDescription;
        }

        if ($parametersTitlesPerGroup = $this->getParametersTitlesPerGroup()) {
            $parametersTypeOrder = array(
                ParameterGroup::PARAMETER_RAZMER,
                ParameterGroup::PARAMETER_MARKA,
                ParameterGroup::PARAMETER_GOST,
                ParameterGroup::PARAMETER_KLASS,
                ParameterGroup::PARAMETER_TIP,
                ParameterGroup::PARAMETER_VID,
            );

            $parametersTitles = [];
            $usedTypeIds = [];
            foreach ($parametersTypeOrder as $parameterTypeId) {
                if (isset($parametersTitlesPerGroup[$parameterTypeId])) {
                    $parametersTitles[] = $this->implodeParametersForGroup($parametersTitlesPerGroup[$parameterTypeId], ' ');
                    $usedTypeIds[$parameterTypeId] = true;
                }
            }

            foreach ($parametersTitlesPerGroup as $parameterTypeId => $parametersTitlesInGroup) {
                if (!isset($usedTypeIds[$parameterTypeId])) {
                    $parametersTitles[] = $this
                        ->implodeParametersForGroup($parametersTitlesInGroup, ' ');
                }
            }

            $parametersString = implode(' ', $parametersTitles);

            return $category->getTitle().' '.$parametersString.' купить в '.$this->getLocationTitle().'. '.$category->getTitle().' '.$parametersString.' - каталог с фото, цены, описание, прайс-листы и технические характеристики. '.$category->getTitle().' '.$parametersString.' - продажа оптом и в розницу с доставкой от компаний в '.$this->getLocationTitle();
        }

        if ($this->projectFamily === 'product') {
            return $category->getTitle().' оптом, сравнить цены и купить в '.$this->getLocationTitle().'. '.$category->getTitle().' - каталог с фото, цены, описание, прайс-листы компаний.';
        }

        return $category->getTitle().' купить в '.$this->getLocationTitle().'. '.$category->getTitle().' - каталог с фото, цены, описание, прайс-листы и технические характеристики. '.$category->getTitle().' - продажа оптом и в розницу с доставкой от компаний в '.$this->getLocationTitle();
    }

    public function getHeadTitleForCategoryPage(Category $category = null, ProductsTabsWidget $productsTabsWidget = null)
    {
        // правила для 2+ страницы
        if ($this->page) {
            $template = '{{ category }} {{ attributes_values }} в {{ territory_locative }} — Страница {{ page }}';

            return $this->renderStringTemplate($template);
        }

        if (!$category) {
            return 'Предложения в '.$this->getLocationTitle();
        }

        // динамические правила из базы
        if ($productsTabsWidget && !$this->page && $seoTemplate = $this->getSeoTemplate()) {
            if ($template = $seoTemplate->getMetadata()->getH1Title()) {
                return $this->renderStringTemplate($template, $this->getExtraContext($productsTabsWidget));
            }
        }

        // динамические правила из базы - старое
        if ($h1Title = $this->getMetadataProperty('metadata.h1Title')) {
            return $h1Title;
        }

        $categoryIn = ' в ';
        if ($this->projectFamily === 'product') {
            $categoryIn = ' оптом в ';
        }

        $addVolume = ' — цена за 1 '.$category->getVolumeTypeTitle();

        if ($parametersTitlesPerGroup = $this->getParametersTitlesPerGroup()) {
            $categoryTitle = $this->getTitleForCategory($category);
            if ($this->projectFamily === 'metalloprokat') {
                $this->normalizeGostParameter($parametersTitlesPerGroup);
                $parametersTitles = $this->implodeParametersGroups($parametersTitlesPerGroup, ' ', ' ');
            } else {
                $parametersTitles = $this->implodeParametersGroups($parametersTitlesPerGroup);
            }

            if ($this->currentCity || $this->currentRegion) {
                return $categoryTitle.' '.$parametersTitles.$categoryIn.$this->getLocationTitle().$addVolume;
            }

            return $categoryTitle.' '.$parametersTitles.$addVolume;
        }

        if ($this->currentCity || $this->currentRegion) {
            return $category->getTitle().$categoryIn.$this->getLocationTitle().$addVolume;
        }

        return $category->getTitle().$addVolume;
    }

    public function getHeadTitleForHumansForCategoryPage()
    {
        $addVolume = ' — цена за 1 '.$this->currentCategory->getVolumeTypeTitle();

        if ($h1Title = $this->getMetadataProperty('metadata.h1Title')) {
            return $h1Title.$addVolume;
        }

        return $this->getTitleForCategory($this->currentCategory).$addVolume;
    }

    public function getDescriptionForCategoryPage()
    {
        $request = $this->getRequest();
        $queryBag = $request->query;
        $tab = $request->attributes->get('tab', 'products');
        $view = $queryBag->get('view');
        $order = $queryBag->get('order');
        $page = $queryBag->get('page', 1);

        if ($tab === 'products' && !$view && !$order && $page == 1) {
            return $this->getMetadataProperty('description');
        }

        return '';
    }

    public function getSeoTextForCategoryPage(ProductsTabsWidget $productsTabsWidget)
    {
        if (!$this->currentCategory || $this->page > 1 || $this->getRequest()->query->get('order')) {
            return null;
        }

        if ($productsTabsWidget && !$this->page && $seoTemplate = $this->getSeoTemplate()) {
            if ($template = $seoTemplate->getTextBlock()) {
                return $this->renderStringTemplate($template, $this->getExtraContext($productsTabsWidget));
            }
        }

        if ($this->projectFamily === 'metalloprokat') {
            $template = '';

            if ($this->currentCountry->getId() == Country::COUNTRY_ID_RUSSIA) {
                $template = <<<'TWIG'
✔ Каталог Металлопрокат.ру предлагает купить {{ category_accusative }} {{ attributes_values }} в России — {{ '%count_formatted% предложение|%count_formatted% предложения|%count_formatted% предложений'|pluralize(products_count) }} — Прямые поставки — Лучшие цены. 
Стоимость от производителя, продажа оптом и в розницу. {% if available_attributes %}Можно фильтровать по: {{ available_attributes }}.{% endif %} Заказать с доставкой.
TWIG;
                if ($this->currentCity || $this->currentRegion) {
                    $template = <<<'TWIG'
✔ Металлопрокат.ру поможет купить {{ category_accusative }} {{ attributes_values }} в {{ territory_locative }} — {{ '%count_formatted% предложение|%count_formatted% предложения|%count_formatted% предложений'|pluralize(products_count) }} — Выгодные цены — Широкий ассортимент. 
Продажа оптом и в розницу от производителя. Заказать с доставкой к дому. Стоимость ниже рынка. {% if available_attributes %}Можно фильтровать по: {{ available_attributes }}.{% endif %}
TWIG;
                }
            } elseif ($this->currentCountry->getId() == Country::COUNTRY_ID_UKRAINE) {
                $template = <<<'TWIG'
✔ Металлопрокат предлагает купить {{ category_accusative }} {{ attributes_values }} в Украине — Сортировка по стоимости — Цены производителя — {{ '%count_formatted% предложение|%count_formatted% предложения|%count_formatted% предложений'|pluralize(products_count) }}. Продажа оптом и в розницу со склада. Заказать с доставкой.
TWIG;

                if ($this->currentCity || $this->currentRegion) {
                    $template = <<<'TWIG'
✔ В Металлопрокате можно купить {{ category_accusative }} {{ attributes_values }} в {{ territory_locative }} — Минимальная цена — От поставщика — {{ '%count_formatted% предложение|%count_formatted% предложения|%count_formatted% предложений'|pluralize(products_count) }}. Оптом и в розницу со склада. Возможность заказать с доставкой по городу.
TWIG;
                }
            } elseif ($this->currentCountry->getId() == Country::COUNTRY_ID_BELORUSSIA) {
                $template = <<<'TWIG'
✔ Metalaprakat.by предлагает купить {{ category_accusative }} {{ attributes_values }} в Беларуси — От производителя — Оптовые цены — {{ '%count_formatted% предложение|%count_formatted% предложения|%count_formatted% предложений'|pluralize(products_count) }}. Стоимость ниже рынка, продажа в розницу и оптом.
TWIG;

                if ($this->currentCity || $this->currentRegion) {
                    $template = <<<'TWIG'
Metalaprakat.by — купить {{ category_accusative }} {{ attributes_values }} в {{ territory_locative }} ✔ Цена поставщика ✔ Большой выбор ✔ В каталоге {{ '%count_formatted% предложение|%count_formatted% предложения|%count_formatted% предложений'|pluralize(products_count) }}. Продажа в розницу и оптом для крупных и мелких заказчиков. Низкая стоимость.
TWIG;
                }
            } elseif ($this->currentCountry->getId() == Country::COUNTRY_ID_KAZAKHSTAN) {
                $template = <<<'TWIG'
✔ Metalloprokat.kz предлагает купить {{ category_accusative }} {{ attributes_values }} в Казахстане — Большой выбор из {{ '%count_formatted% предложение|%count_formatted% предложения|%count_formatted% предложений'|pluralize(products_count) }} — Конкурентные цены — Оптом и в розницу. Продажа всех товаров в нашем каталоге осуществляется напрямую от производителя.
TWIG;

                if ($this->currentCity || $this->currentRegion) {
                    $template = <<<'TWIG'
✔ Metalloprokat.kz предлагает купить {{ category_accusative }} {{ attributes_values }} в {{ territory_locative }} — Выбрать из {{ '%count_formatted% предложение|%count_formatted% предложения|%count_formatted% предложений'|pluralize(products_count) }} — Цены поставщика — Розница и оптом. Только прямые продажи. Самый большой каталог в стране.
TWIG;
                }
            }

            return $this->renderStringTemplate($template, $this->getExtraContext($productsTabsWidget));
        }

        $template = <<<'TWIG'
Если вы хотите купить {{ category_accusative }} {{ attributes_values }} с доставкой в {{ territory_locative }} по лучшей цене, наш каталог из {{ '%count_formatted% предложение|%count_formatted% предложения|%count_formatted% предложений'|pluralize(products_count) }} в вашем распоряжении.
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

    /**
     * @return SeoTemplate|false|null
     */
    protected function getSeoTemplate()
    {
        if (null !== $this->seoTemplate) {
            return $this->seoTemplate;
        }

        if (!$this->currentCategory || !$this->attributes) {
            return $this->seoTemplate = false;
        }

        $seoTemplate = $this->container->get('doctrine.orm.default_entity_manager')
            ->getRepository('MetalProjectBundle:SeoTemplate')
            ->findBestSeoTemplate($this->currentCategory, $this->attributes);

        return $this->seoTemplate = $seoTemplate ?: false;
    }
}
