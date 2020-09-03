<?php

/* @markup/portal/suppliers/company-map-static.html.twig */
class __TwigTemplate_b13a1b50b1c072bf93a8a6fd30739790 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = $this->env->loadTemplate("markup/html/portal/_portal_layout.html.twig");

        $this->blocks = array(
            'title' => array($this, 'block_title'),
            'content' => array($this, 'block_content'),
            'tabs' => array($this, 'block_tabs'),
            'sidebar' => array($this, 'block_sidebar'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "markup/html/portal/_portal_layout.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_title($context, array $blocks = array())
    {
        echo "Map";
    }

    // line 5
    public function block_content($context, array $blocks = array())
    {
        // line 6
        echo "    <div id=\"content\" class=\"content-right outline-right float-right\">
        <div class=\"main-title \">
            <h1>Компании, торгующие арматурой
                <a href=\"#\" class=\"region-link js-popover-opener\" data-popover=\"#cities\" data-different-position=\"true\">в
                    Москве и Области</a>
            </h1>
        </div>
        ";
        // line 13
        $this->displayBlock('tabs', $context, $blocks);
        // line 50
        echo "        <div class=\"map-wrapper\">
            <img style=\"top: 345px; left: 665px; position: absolute;\" src=\"./markup/img/red-point.png\" />
            <img style=\"top: 345px; left: 665px; position: absolute;\" src=\"./markup/img/blue-point.png\" />
            <img src=\"./markup/pic/map.jpg\" alt=\"image description\"/>
            <div id=\"balloon\" class=\"drop-wrapper map-balloon opacity-border\" style=\"display: block; position: absolute;\">
                <div class=\"dropdown\">
                    <div class=\"rating clearfix\">
                        <span class=\"star-mini icon-star-colored\"></span>
                        <span class=\"star-mini icon-star-colored\"></span>
                        <span class=\"star-mini icon-star-colored\"></span>
                    </div>
                    <div class=\"title\"><a href=\"#\">СтальИнтекс</a></div>
                    <p class=\"localization gray60-color\">филиал в Люберцах</p>
                    <a class=\"site\" href=\"#\">staltorg.ru</a>

                    <div class=\"contacts\">
                        <span class=\"phone-text\">+7 (495) 784</span>
                        ( <a class=\"see\" href=\"#\">показать</a> )
                        <a class=\"callback js-popup-opener\" href=\"#\" data-popup=\"#callback\">обратный звонок</a>
                    </div>
                    <div class=\"more clearfix\">
                        <div class=\"promo-label label float-left\">
                            <a class=\"label-link\" href=\"#\">Промо</a>
                        </div>
                    </div>
                    <div class=\"more clearfix\">

                        <div class=\"product-label label float-left\">
                            <a class=\"label-link\" href=\"#\">еще 95 товаров</a>
                        </div>
                        <div class=\"date float-left\">
                            <span class=\"updated-date\">Обновлено 8 фев 2013</span>
                        </div>
                    </div>
                </div>
                <div class=\"dropdown white95-bg\">
                    <div class=\"product-info\">
                        <strong><a href=\"#\" class=\"small-link\">Арматура строительная</a></strong>

                        <p class=\"size gray60-color\">Размер 30x30</p>

                        <p class=\"price gray60-color\">от <strong class=\"red-color\">25 000 <span class=\"icon-rouble\"></span> </strong>за тонну</p>

                    </div>
                </div>
            </div>
            <div id=\"balloon-1\" class=\"drop-wrapper map-balloon simple opacity-border\" style=\"display: block; position: absolute;\">
                <div class=\"dropdown\">
                    <div class=\"rating clearfix\">
                        <span class=\"star-mini icon-star-colored\"></span>
                        <span class=\"star-mini icon-star-colored\"></span>
                        <span class=\"star-mini icon-star-colored\"></span>
                    </div>
                    <div class=\"title\"><a href=\"#\">СтальИнтекс</a></div>
                    <p class=\"localization gray60-color\">филиал в Люберцах</p>
                    <a class=\"site\" href=\"#\">staltorg.ru</a>

                    <div class=\"contacts\">
                        <span class=\"phone-text\">+7 (495) 784</span>
                        ( <a class=\"see\" href=\"#\">показать</a> )
                        <a class=\"callback js-popup-opener\" href=\"#\" data-popup=\"#callback\">обратный звонок</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
";
    }

    // line 13
    public function block_tabs($context, array $blocks = array())
    {
        // line 14
        echo "            ";
        $context["defaultActiveTab"] = "companies";
        // line 15
        echo "            <div class=\"result-tabs-wrapper outline-right clearfix\">
                <div id=\"tabs\" class=\"tabs float-left\">
                    <ul class=\"tabs-list clearfix\">
                        <li class=\"";
        // line 18
        if ((((array_key_exists("activeTab", $context)) ? (_twig_default_filter((isset($context["activeTab"]) ? $context["activeTab"] : null), (isset($context["defaultActiveTab"]) ? $context["defaultActiveTab"] : null))) : ((isset($context["defaultActiveTab"]) ? $context["defaultActiveTab"] : null))) == "products")) {
            echo " active ie-radius ";
        }
        echo "\">
                            <a class=\"link\" href=\"";
        // line 19
        echo twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('path')->getCallable(), array("portal/suppliers/products.html.twig")), "html", null, true);
        echo "\">Товары</a>
                            <span class=\"count\">2,832</span>
                        </li>
                        <li class=\"";
        // line 22
        if ((((array_key_exists("activeTab", $context)) ? (_twig_default_filter((isset($context["activeTab"]) ? $context["activeTab"] : null), (isset($context["defaultActiveTab"]) ? $context["defaultActiveTab"] : null))) : ((isset($context["defaultActiveTab"]) ? $context["defaultActiveTab"] : null))) == "companies")) {
            echo " active ie-radius ";
        }
        echo "\">
                            <a class=\"link\" href=\"";
        // line 23
        echo twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('path')->getCallable(), array("portal/suppliers/companies.html.twig")), "html", null, true);
        echo "\">Компании</a>
                            <span class=\"count\">37</span>
                        </li>
                    </ul>
                </div>
                <div class=\"map-filter float-left\">
                    <span class=\"link active icon-position-color float-left clickable\">Все</span>
                    <span class=\"icon-position link float-left clickable\">С товарами</span>
                </div>
                <div class=\"sort-view float-right clearfix\">
                    <div class=\"view-block float-right\">
                        <ul class=\"view-list\">
                            <li class=\"list first js-tooltip-opener active ie-radius\" data-tooltip-title=\"Список\">
                                <span class=\"item icon-view-list\"></span>
                            </li>
                            <li class=\"list pallete js-tooltip-opener disabled ie-radius\" data-tooltip-title=\"Галерея\">
                                <span class=\"item icon-view-grid\"></span>
                            </li>
                            <li class=\"list on-map js-tooltip-opener enable last ie-radius\" data-tooltip-title=\"Карта\"
                                data-tooltip-class=\"right\">
                                <a class=\"item icon-baloon\" href=\"#\"></a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        ";
    }

    // line 117
    public function block_sidebar($context, array $blocks = array())
    {
        // line 118
        echo "    <div id=\"sidebar\" class=\"side-left outline-left float-left static\">
        <div class=\"sidebar-content js-fixed-filters\">
            <div class=\"side-inside\">
                <div class=\"drop-wrapper drop-right opacity-border\" id=\"dropdown-categories-1\">
                    <ul class=\"dropdown\">
                        <li class=\"drop-item first\">
                            <span class=\"drop-link\">Арматура</span>
                            <ul class=\"level-inside\">
                                <li class=\"drop-item\">
                                    <a class=\"drop-link\" href=\"#\">Арматура A1</a>
                                </li>
                                <li class=\"drop-item\">
                                    <a class=\"drop-link\" href=\"#\">Арматура A2</a>
                                </li>
                                <li class=\"drop-item\">
                                    <a class=\"drop-link\" href=\"#\">Арматура A3</a>
                                </li>
                                <li class=\"drop-item\">
                                    <a class=\"drop-link\" href=\"#\">Арматура A3</a>
                                </li>
                                <li class=\"drop-item\">
                                    <a class=\"drop-link\" href=\"#\">Арматура A3</a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
                <div class=\"drop-wrapper drop-right opacity-border\" id=\"dropdown-categories-2\">
                    <ul class=\"dropdown\">
                        <li class=\"drop-item first\">
                            <a href=\"#\" class=\"drop-link\">Балка</a>
                            <ul class=\"level-inside\">
                                <li class=\"drop-item\">
                                    <a class=\"drop-link\" href=\"#\">Балка A1</a>
                                </li>
                                <li class=\"drop-item\">
                                    <a class=\"drop-link\" href=\"#\">Балка A2</a>
                                </li>
                                <li class=\"drop-item\">
                                    <a class=\"drop-link\" href=\"#\">Балка A3</a>
                                </li>
                                <li class=\"drop-item\">
                                    <a class=\"drop-link\" href=\"#\">Балка A3</a>
                                </li>
                                <li class=\"drop-item\">
                                    <a class=\"drop-link\" href=\"#\">Балка A3</a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
                <div class=\"drop-wrapper drop-right opacity-border\" id=\"dropdown-categories-7\">
                    <ul class=\"dropdown\">
                        <li class=\"drop-item first\">
                            <span class=\"drop-link\">Швеллер</span>
                            <ul class=\"level-inside\">
                                <li class=\"drop-item\">
                                    <a class=\"drop-link\" href=\"#\">Швеллер A1</a>
                                </li>
                                <li class=\"drop-item\">
                                    <a class=\"drop-link\" href=\"#\">Швеллер A2</a>
                                </li>
                                <li class=\"drop-item\">
                                    <a class=\"drop-link\" href=\"#\">Швеллер A3</a>
                                </li>
                                <li class=\"drop-item\">
                                    <a class=\"drop-link\" href=\"#\">Швеллер A3</a>
                                </li>
                                <li class=\"drop-item\">
                                    <a class=\"drop-link\" href=\"#\">Швеллер A3</a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
            <div class=\"side-wrap js-scrollable-filters js-scrollable\">
                <div class=\"filters\">
                    <form action=\"#\">
                        <div class=\"local filter\">
                            <div class=\"title\"><a href=\"#\" class=\"js-popover-opener\" data-popover=\"#cities\"
                                                  data-different-position=\"true\">Москва и Область</a></div>
                            <ul class=\"product\">
                                <li class=\"item js-dropdown-opener\"
                                    data-display-dropdown=\"#dropdown-categories-1\">
                                    <a class=\"clearfix\" href=\"#\">
                                        <span class=\"count float-right\">51</span>
                                        <span class=\"elem is-gradiented\">Арматура</span>
                                    </a>
                                </li>
                                <li class=\"item js-dropdown-opener\"
                                    data-display-dropdown=\"#dropdown-categories-2\">
                                    <a class=\"clearfix\" href=\"#\">
                                        <span class=\"count float-right\">60</span>
                                        <span class=\"elem is-gradiented\">Балка</span>
                                    </a>
                                </li>
                                <li class=\"item g-hidden js-dropdown-opener\" data-expandable-section=\"categories\">
                                    <a class=\"clearfix\" href=\"#\">
                                        <span class=\"count float-right\">777</span>
                                        <span class=\"elem is-gradiented\">Заглушка</span>
                                    </a>
                                </li>
                                <li class=\"item g-hidden js-dropdown-opener\" data-expandable-section=\"categories\">
                                    <a class=\"clearfix\" href=\"#\">
                                        <span class=\"count float-right\">777</span>
                                        <span class=\"elem is-gradiented\">Заглушка</span>
                                    </a>
                                </li>
                                <li class=\"item g-hidden js-dropdown-opener\" data-expandable-section=\"categories\">
                                    <a class=\"clearfix\" href=\"#\">
                                        <span class=\"count float-right\">777</span>
                                        <span class=\"elem is-gradiented\">Заглушка</span>
                                    </a>
                                </li>
                                <li class=\"item g-hidden js-dropdown-opener\" data-expandable-section=\"categories\">
                                    <a class=\"clearfix\" href=\"#\">
                                        <span class=\"count float-right\">777</span>
                                        <span class=\"elem is-gradiented\">Заглушка</span>
                                    </a>
                                </li>
                                <li class=\"item g-hidden js-dropdown-opener\" data-expandable-section=\"categories\">
                                    <a class=\"clearfix\" href=\"#\">
                                        <span class=\"count float-right\">777</span>
                                        <span class=\"elem is-gradiented\">Заглушка</span>
                                    </a>
                                </li>
                                <li class=\"item g-hidden js-dropdown-opener\" data-expandable-section=\"categories\"
                                    data-display-dropdown=\"#dropdown-categories-7\">
                                    <a class=\"clearfix\" href=\"#\">
                                        <span class=\"count float-right\">40</span>
                                        <span class=\"elem is-gradiented\">Швеллер</span>
                                    </a>
                                </li>
                            </ul>
                            <a class=\"local js-show-hide-link\" href=\"#\">Показать все</a>
                            <a class=\"size js-show-hide-link g-hidden\" href=\"#\">Скрыть</a>
                        </div>
                        <div class=\"size filter\">
                            <div class=\"title\">Размер</div>
                            <ul class=\"size-list clearfix\">
                                <li class=\"float-left\">
                                    <input id=\"size-10\" type=\"checkbox\"
                                           class=\"js-styled-checkbox js-show-button-on-change bg-grey\"/>
                                    <label for=\"size-10\"><a href=\"#\" class=\"link\">10</a></label>
                                </li>
                                <li class=\"float-left\">
                                    <input id=\"size-12\" type=\"checkbox\"
                                           class=\"js-styled-checkbox js-show-button-on-change bg-grey\"/>
                                    <label for=\"size-12\"><a href=\"#\" class=\"link\">12</a></label>
                                </li>
                                <li class=\"float-left\">
                                    <input id=\"size-14\" type=\"checkbox\"
                                           class=\"js-styled-checkbox js-show-button-on-change bg-grey\"/>
                                    <label for=\"size-14\"><a href=\"#\" class=\"link\">14</a></label>
                                </li>
                                <li class=\"float-left\">
                                    <input id=\"size-16\" type=\"checkbox\"
                                           class=\"js-styled-checkbox js-show-button-on-change bg-grey\"/>
                                    <label for=\"size-16\">16</label>
                                </li>
                                <li class=\"float-left\">
                                    <input id=\"size-20\" type=\"checkbox\"
                                           class=\"js-styled-checkbox js-show-button-on-change bg-grey\"/>
                                    <label for=\"size-20\">20</label>
                                </li>
                                <li class=\"float-left\">
                                    <input id=\"size-24\" type=\"checkbox\" checked=\"checked\"
                                           class=\"js-styled-checkbox js-show-button-on-change bg-greys\"/>
                                    <label for=\"size-24\">24</label>
                                </li>
                                <li class=\"float-left\">
                                    <input id=\"size-36\" type=\"checkbox\" checked=\"checked\"
                                           class=\"js-styled-checkbox js-show-button-on-change bg-grey\"/>
                                    <label for=\"size-36\">36</label>
                                </li>
                                <li class=\"float-left\">
                                    <input id=\"size-48\" type=\"checkbox\"
                                           class=\"js-styled-checkbox js-show-button-on-change bg-grey\"/>
                                    <label for=\"size-48\">48</label>
                                </li>
                                <li class=\"float-left\">
                                    <input id=\"size-54\" type=\"checkbox\"
                                           class=\"js-styled-checkbox js-show-button-on-change bg-grey\"/>
                                    <label for=\"size-54\">54</label>
                                </li>
                                <li class=\"float-left g-hidden\" data-hidable=\"true\">
                                    <input id=\"size-55\" type=\"checkbox\"
                                           class=\"js-styled-checkbox js-show-button-on-change bg-grey\"/>
                                    <label for=\"size-55\">55</label>
                                </li>
                                <li class=\"float-left g-hidden\" data-hidable=\"true\">
                                    <input id=\"size-56\" type=\"checkbox\"
                                           class=\"js-styled-checkbox js-show-button-on-change bg-grey\"/>
                                    <label for=\"size-56\">56</label>
                                </li>
                                <li class=\"float-left g-hidden\" data-hidable=\"true\">
                                    <input id=\"size-57\" type=\"checkbox\"
                                           class=\"js-styled-checkbox js-show-button-on-change bg-grey\"/>
                                    <label for=\"size-57\">57</label>
                                </li>
                                <li class=\"float-left g-hidden\" data-hidable=\"true\">
                                    <input id=\"size-58\" type=\"checkbox\"
                                           class=\"js-styled-checkbox js-show-button-on-change bg-grey\"/>
                                    <label for=\"size-58\">58</label>
                                </li>
                            </ul>
                            <a class=\"size js-show-hide-link\" href=\"#\">Показать все</a>
                            <a class=\"size js-show-hide-link g-hidden\" href=\"#\">Скрыть</a>
                        </div>
                        <div class=\"price filter\">
                            <div class=\"amount-title clearfix\">
                                <div class=\"title float-left\">Цена</div>
                                <span id=\"amount\" class=\"price-text float-right\"></span>
                                <input class=\"min-price js-show-button-on-change\" type=\"hidden\" name=\"min-price\"/>
                                <input class=\"max-price js-show-button-on-change\" type=\"hidden\" name=\"max-price\"/>
                            </div>
                            <div id=\"slider-range\"></div>
                            <div class=\"price-length clearfix\">
                                <span class=\"price-label float-left\">10000</span>
                                <span class=\"price-label float-right\">300000</span>
                            </div>
                        </div>
                        <div class=\"type filter\">
                            <ul class=\"type-list\">
                                <li>
                                    <input id=\"type-1\" class=\"js-styled-checkbox js-show-button-on-change bg-grey\"
                                           type=\"checkbox\"/>
                                    <label for=\"type-1\">Производство</label>
                                </li>
                                <li>
                                    <input id=\"type-2\" class=\"js-styled-checkbox js-show-button-on-change bg-grey\"
                                           type=\"checkbox\"/>
                                    <label for=\"type-2\">Резка</label>
                                </li>
                                <li>
                                    <input id=\"type-3\" class=\"js-styled-checkbox js-show-button-on-change bg-grey\"
                                           type=\"checkbox\"/>
                                    <label for=\"type-3\">Гибка</label>
                                </li>
                                <li>
                                    <input id=\"type-4\" class=\"js-styled-checkbox js-show-button-on-change bg-grey\"
                                           type=\"checkbox\"/>
                                    <label for=\"type-4\">Доставка</label>
                                </li>
                            </ul>
                        </div>
                        <div class=\"toggle-filter filter\">
                            <div class=\"sell-type-list toggle-block js-toggle-block ie-radius\">
                                <label class=\"item-link wholesale float-left  ie-radius\">
                                    <input type=\"radio\" name=\"selling_type\" value=\"wholesale\"
                                           class=\"not-styling js-show-button-on-change js-toggle-button\"/> Опт
                                </label>

                                <label class=\"item-link retail active float-left ie-radius\">
                                    <input type=\"radio\" name=\"selling_type\" checked=\"checked\" value=\"retail\"
                                           class=\"not-styling js-show-button-on-change js-toggle-button\"/> Розница
                                </label>

                                <label class=\"item-link all float-left ie-radius\">
                                    <input type=\"radio\" name=\"selling_type\" value=\"all\"
                                           class=\"not-styling js-show-button-on-change js-toggle-button\"/> Все
                                </label>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class=\"submit-wrapper js-show-all g-hidden\">
                <a class=\"button show-btn link blue-bg clearfix ie-radius\" href=\"#\">
                    <span class=\"text float-left\">показать</span>
                    <span class=\"count float-right\">583</span>
                </a>
            </div>
        </div>
    </div>
";
    }

    public function getTemplateName()
    {
        return "@markup/portal/suppliers/company-map-static.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  184 => 118,  181 => 117,  150 => 23,  144 => 22,  138 => 19,  132 => 18,  127 => 15,  124 => 14,  121 => 13,  51 => 50,  49 => 13,  40 => 6,  37 => 5,  31 => 3,);
    }
}
