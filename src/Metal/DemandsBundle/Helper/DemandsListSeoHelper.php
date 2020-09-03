<?php

namespace Metal\DemandsBundle\Helper;

use Metal\ProjectBundle\Helper\SeoHelper as BaseSeoHelper;

class DemandsListSeoHelper extends BaseSeoHelper
{
    public function getMetaTitleForDemandsPage()
    {
        if (!$this->currentCategory) {
            $result = 'Последние добавленные заявки и тендеры в '.$this->getLocationTitle().' — '.$this->currentCountry->getDomainTitle();

            if ($this->page) {
                $result .= ' — Страница '.$this->page;
            }

            return $result;
        }

        $template = <<<'TWIG'
Заявки на {{ category }} {{ attributes_values }} — тендеры и потребности в {{ territory_locative }}{% if page %} — Cтраница {{ page }}{% endif %} | {{ host|capitalize }}
TWIG;

        return $this->renderStringTemplate($template);
    }

    public function getHeadTitleForDemandsPage()
    {
        if (!$this->currentCategory) {
            return 'Последние добавленные заявки в '.$this->getLocationTitle();
        }

        $template = <<<'TWIG'
Заявки на {{ category }} {{ attributes_values }} — в {{ territory_locative }}.{% if page %} — Cтраница {{ page }}.{% endif %}
TWIG;

        return $this->renderStringTemplate($template);
    }

    public function getMetaDescriptionForDemandsPage($demandsCount)
    {
        if ($this->page > 1) {
            $template = '{{ category }} {{ attributes_values }} в {{ territory_locative }}. Заявки, тендеры. — Страница {{ page }} — {{ host|capitalize }}.';

            return $this->renderStringTemplate($template);
        }

        if (!$this->currentCategory) {
            return '';
        }

        $template = <<<'TWIG'
Заявки на {{ category }} {{ attributes_values }} — в {{ territory_locative }}: — Тендеры. — Спрос {{ '%count_formatted% заявка|%count_formatted% заявки|%count_formatted% заявок'|pluralize(demands_count) }}. — Реальные потребители. Каталог {{ host|capitalize }}.{% if page %} — Cтраница {{ page }}.{% endif %}
TWIG;

        return $this->renderStringTemplate($template, ['demands_count' => $demandsCount]);
    }

    public function getCanonicalUrlForDemands()
    {
        $request = $this->getRequest();
        $route = $request->attributes->get('_route');
        $routeParameters = $request->attributes->get('_route_params');
        if ($request->query->all()) {
            if ($this->getParametersTitlesPerGroup()) {
                return $this->getCanonicalRouteWithAttributes($request, $route, $routeParameters);
            }

            return $this->generateAbsoluteUrl($route, $routeParameters);
        }

        return $this->getCanonicalRouteWithAttributes($request, $route, $routeParameters);
    }
}
