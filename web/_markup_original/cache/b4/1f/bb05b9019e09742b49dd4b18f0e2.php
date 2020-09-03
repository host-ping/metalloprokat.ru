<?php

/* @markup/portal/suppliers/companies-map.html.twig */
class __TwigTemplate_b41fbb05b9019e09742b49dd4b18f0e2 extends Twig_Template
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
        echo "    ";
        $this->env->loadTemplate("@markup/ui/_map-partials/yandex_maps_initialization.html.twig")->display($context);
        // line 7
        echo "
    <div id=\"content\" class=\"content-right outline-right float-right\">
        <div class=\"main-title \">
            <h1>Компании, торгующие арматурой
                <a href=\"#\" class=\"region-link js-popover-opener\" data-popover=\"#cities\" data-different-position=\"true\">в
                    Москве и Области</a>
            </h1>
        </div>
        ";
        // line 15
        $this->displayBlock('tabs', $context, $blocks);
        // line 52
        echo "        <div class=\"map-wrapper\">
            <div id=\"map\" style=\"width: 756px; height: 540px;\">

            </div>
            ";
        // line 56
        $context["companies"] = array("0" => array("coord" => array("0" => "55.8", "1" => "37.6"), "title" => "Вик-Профи", "city_title" => "Москва", "has_products" => true, "rating_gt_two" => true, "phone" => "125478-98", "city_locative" => "Москве", "products_count" => "58976", "star_color" => "colored", "url_caption" => "www.vik-profi.ru", "updated_at" => "2015-01-01", "product" => array("title" => "Арматура", "size" => "125")), "1" => array("coord" => array("0" => "55.8", "1" => "37.5"), "title" => "Вик", "city_title" => "London"));
        // line 90
        echo "
            ";
        // line 91
        $context["center"] = null;
        // line 92
        echo "            ";
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["companies"]) ? $context["companies"] : null));
        foreach ($context['_seq'] as $context["_key"] => $context["comp"]) {
            // line 93
            echo "                ";
            if ((($this->getAttribute($this->getAttribute((isset($context["comp"]) ? $context["comp"] : null), "coord"), 0, array(), "array") != 0) && (!(isset($context["center"]) ? $context["center"] : null)))) {
                // line 94
                echo "                    ";
                $context["center"] = $this->getAttribute((isset($context["comp"]) ? $context["comp"] : null), "coord");
                // line 95
                echo "                ";
            }
            // line 96
            echo "            ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['comp'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 97
        echo "            ";
        if ((!(isset($context["center"]) ? $context["center"] : null))) {
            // line 98
            echo "                ";
            $context["center"] = array(0 => 55.8, 1 => 37.6);
            // line 99
            echo "            ";
        }
        // line 100
        echo "
            <script type=\"text/javascript\">


                var companies = ";
        // line 104
        echo twig_jsonencode_filter((isset($context["companies"]) ? $context["companies"] : null));
        echo ";

                \$(document).ready(function() {
                    var cm = new MetalMaps.CompaniesMap(\$('#map')[0], {
                        zoom: 13,
                        center: ";
        // line 109
        echo twig_jsonencode_filter((isset($context["center"]) ? $context["center"] : null));
        echo "
                    }, companies);
                });
            </script>

        </div>
    </div>
";
    }

    // line 15
    public function block_tabs($context, array $blocks = array())
    {
        // line 16
        echo "            ";
        $context["defaultActiveTab"] = "companies";
        // line 17
        echo "            <div class=\"result-tabs-wrapper outline-right clearfix\">
                <div id=\"tabs\" class=\"tabs float-left\">
                    <ul class=\"tabs-list clearfix\">
                        <li class=\"";
        // line 20
        if ((((array_key_exists("activeTab", $context)) ? (_twig_default_filter((isset($context["activeTab"]) ? $context["activeTab"] : null), (isset($context["defaultActiveTab"]) ? $context["defaultActiveTab"] : null))) : ((isset($context["defaultActiveTab"]) ? $context["defaultActiveTab"] : null))) == "products")) {
            echo " active ie-radius ";
        }
        echo "\">
                            <a class=\"link\" href=\"";
        // line 21
        echo twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('path')->getCallable(), array("portal/suppliers/products.html.twig")), "html", null, true);
        echo "\">Товары</a>
                            <span class=\"count\">2,832</span>
                        </li>
                        <li class=\"";
        // line 24
        if ((((array_key_exists("activeTab", $context)) ? (_twig_default_filter((isset($context["activeTab"]) ? $context["activeTab"] : null), (isset($context["defaultActiveTab"]) ? $context["defaultActiveTab"] : null))) : ((isset($context["defaultActiveTab"]) ? $context["defaultActiveTab"] : null))) == "companies")) {
            echo " active ie-radius ";
        }
        echo "\">
                            <a class=\"link\" href=\"";
        // line 25
        echo twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('path')->getCallable(), array("portal/suppliers/companies.html.twig")), "html", null, true);
        echo "\">Компании</a>
                            <span class=\"count\">37</span>
                        </li>
                    </ul>
                </div>
                <div class=\"map-filter float-left\">
                    <span class=\"link active icon-position-color float-left clickable js-map-toggle\">Все</span>
                    <span class=\"icon-position link float-left clickable js-map-toggle\">С товарами</span>
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
        return "@markup/portal/suppliers/companies-map.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  186 => 118,  183 => 117,  152 => 25,  146 => 24,  140 => 21,  134 => 20,  129 => 17,  126 => 16,  123 => 15,  111 => 109,  103 => 104,  97 => 100,  94 => 99,  91 => 98,  88 => 97,  82 => 96,  79 => 95,  76 => 94,  73 => 93,  68 => 92,  66 => 91,  63 => 90,  61 => 56,  55 => 52,  53 => 15,  43 => 7,  40 => 6,  37 => 5,  31 => 3,);
    }
}
