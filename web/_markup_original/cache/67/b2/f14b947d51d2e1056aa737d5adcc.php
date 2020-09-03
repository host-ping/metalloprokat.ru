<?php

/* @markup/content/_content_layout.html.twig */
class __TwigTemplate_67b2f14b947d51d2e1056aa737d5adcc extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = $this->env->loadTemplate("@markup/_base_layout.html.twig");

        $this->blocks = array(
            'stylesheets' => array($this, 'block_stylesheets'),
            'container_arrtibutes' => array($this, 'block_container_arrtibutes'),
            'main_block_additional_class' => array($this, 'block_main_block_additional_class'),
            'body' => array($this, 'block_body'),
            'header' => array($this, 'block_header'),
            'login' => array($this, 'block_login'),
            'menu' => array($this, 'block_menu'),
            'search_form' => array($this, 'block_search_form'),
            'breadcrumbs' => array($this, 'block_breadcrumbs'),
            'banner' => array($this, 'block_banner'),
            'content' => array($this, 'block_content'),
            'callback' => array($this, 'block_callback'),
            'tabs' => array($this, 'block_tabs'),
            'search_more' => array($this, 'block_search_more'),
            'sidebar' => array($this, 'block_sidebar'),
            'footer' => array($this, 'block_footer'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "@markup/_base_layout.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_stylesheets($context, array $blocks = array())
    {
        // line 4
        echo "    ";
        $this->displayParentBlock("stylesheets", $context, $blocks);
        echo "

";
    }

    // line 8
    public function block_container_arrtibutes($context, array $blocks = array())
    {
        // line 9
        echo "    class=\"container portal js-announcement js-layout-announcement\"
";
    }

    // line 12
    public function block_main_block_additional_class($context, array $blocks = array())
    {
        echo "main-wide";
    }

    // line 14
    public function block_body($context, array $blocks = array())
    {
        // line 15
        echo "    <div ";
        $this->displayBlock("container_arrtibutes", $context, $blocks);
        echo ">
        <div class=\"inside-container\">
            <div id=\"header\" class=\"clearfix\">
                <div class=\"wrap\">
                    ";
        // line 19
        $this->displayBlock('header', $context, $blocks);
        // line 39
        echo "                </div>
            </div>

            ";
        // line 42
        $this->displayBlock('menu', $context, $blocks);
        // line 64
        echo "
            ";
        // line 65
        $this->displayBlock('search_form', $context, $blocks);
        // line 93
        echo "
            <div id=\"main\" class=\"main-wide clearfix\">
                <div class=\"wrap clearfix\">
                    ";
        // line 96
        $this->displayBlock('breadcrumbs', $context, $blocks);
        // line 144
        echo "                    ";
        $this->displayBlock('banner', $context, $blocks);
        // line 148
        echo "                    <div class=\"wrapper outline clearfix\"  data-redirect-url=\"http://katushkin.ru\">
                        ";
        // line 149
        $this->displayBlock('content', $context, $blocks);
        // line 195
        echo "                        ";
        $this->displayBlock('sidebar', $context, $blocks);
        // line 196
        echo "
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id=\"footer\">
        <div class=\"footer-content wrap\">
            ";
        // line 205
        $this->displayBlock('footer', $context, $blocks);
        // line 248
        echo "        </div>
    </div>
    ";
        // line 250
        $this->env->loadTemplate("@markup/_popups.html.twig")->display($context);
    }

    // line 19
    public function block_header($context, array $blocks = array())
    {
        // line 20
        echo "                        <div class=\"left float-left\">
                            <div class=\"logo float-left\">
                                <a href=\"/\">
                                    <img src=\"./markup/img/logo.png\" width=\"36\" height=\"27\" alt=\"строй.ру\"/>
                                </a>
                            </div>
                            <div class=\"logo-text float-left\">
                                <p>
                                    <a class=\"header-logo-text\" href=\"#\">строй.ру</a>
                                </p>
                            </div>
                        </div>
                        ";
        // line 32
        $this->displayBlock('login', $context, $blocks);
        // line 38
        echo "                    ";
    }

    // line 32
    public function block_login($context, array $blocks = array())
    {
        // line 33
        echo "                            <div class=\"user-block float-right\">
                                <a class=\"login js-popup-opener icon-exit\" data-popup=\"#login\" href=\"#\">Вход в кабинет</a>
                                <a class=\"register-link js-popup-opener\" data-popup=\"#register-company\" href=\"#\">Регистрация</a>
                            </div>
                        ";
    }

    // line 42
    public function block_menu($context, array $blocks = array())
    {
        // line 43
        echo "                <div class=\"main-menu-wrapper\">
                    <div class=\"wrap clearfix\">
                        <div class=\"menu-holder\">
                            <ul id=\"menu\" class=\"main-menu clearfix\">
                                <li>
                                    <a class=\"favorites link icon-idea\" href=\"#\">Идеи</a>
                                </li>
                                <li class=\"prev-active\">
                                    <a href=\"#\" class=\"supplier link icon-suppliers-color\">Поставщики</a>
                                </li>
                                <li class=\"active\">
                                    <span class=\"consumer link icon-consumers-color\">Потребители</span>
                                </li>
                                <li>
                                    <a class=\"favorites link icon-favorite-active\" href=\"#\">Избранное <sup>24</sup></a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            ";
    }

    // line 65
    public function block_search_form($context, array $blocks = array())
    {
        // line 66
        echo "                <div class=\"search-block clearfix\">
                    <form action=\"#\" class=\"search-form\">
                        <fieldset id=\"search-fixed\" class=\"main-block js-fixed\">
                            <div class=\"wrap clearfix\">
                                <div class=\"search-field-wrap float-left\">
                                    <span class=\"icon-search-big\"></span>
                                    <span class=\"twitter-typeahead\" style=\"position: relative; display: inline-block; direction: ltr;\">
                                        <input type=\"text\" placeholder=\"Введите название товара или компании\" class=\"search-input\"/>
                                        <span class=\"tt-dropdown-menu\" style=\"position: absolute; top: 100%; left: 0px; z-index: 100; display: none; right: auto;\">
                                        <div class=\"tt-dataset-0\">
                                            <ul class=\"tt-suggestions\">
                                                <li class=\"tt-suggestion tt-cursor\"><strong>Арм</strong>атура</li>
                                                <li class=\"tt-suggestion\"><strong>Арм</strong>атура вязанная</li>
                                                <li class=\"tt-suggestion\"><strong>Арм</strong>атура лежалая</li>
                                                <li class=\"tt-suggestion\"><strong>Арм</strong>атура в бухтах</li>
                                                <li class=\"tt-suggestion\"><strong>Арм</strong>атура утонченная</li>
                                            </ul>
                                        </div>
                                    </span>
                                    </span>
                                </div>
                            </div>

                        </fieldset>
                    </form>
                </div>
            ";
    }

    // line 96
    public function block_breadcrumbs($context, array $blocks = array())
    {
        // line 97
        echo "                        <div class=\"breadcrumbs-wrapper\">
                            <div class=\"breadcrumbs outline\">
                                <ul class=\"breadcrumbs_item-list clearfix js-collapsable-breadcrumbs\"
                                    data-collapsable-breadcrumbs-reserve=\".js-collapsable-breadcrumbs-reserve\">
                                    <li class=\"breadcrumbs_item home first\">
                                        <a class=\"breadcrumbs_link icon-home\" href=\"#\"></a>
                                    </li>
                                    <li class=\"breadcrumbs_item\">
                                        <a class=\"breadcrumbs_link js-collapsable-item\" data-collapsable-breadcrumb-priority=\"1\"
                                           href=\"#\">Части квартиры</a>
                                    </li>
                                    <li class=\"breadcrumbs_item\">
                                        <a href=\"#\" class=\"breadcrumbs_link js-collapsable-item icon-check black js-popover-opener\"
                                           data-collapsable-breadcrumb-priority=\"12\" data-popover=\"#p\"
                                           data-different-position=\"true\">Коммуникации</a>
                                    </li>
                                    <li class=\"breadcrumbs_item\">
                                        <a class=\"breadcrumbs_link js-collapsable-item\" data-collapsable-breadcrumb-priority=\"3\"
                                           href=\"#\">Технические средства</a>
                                    </li>
                                </ul>
                            </div>
                            <div id=\"p\" class=\"drop-wrapper product-list opacity-border\">
                                <div class=\"js-scrollable\">
                                    <ul class=\"dropdown menu-drop\">
                                        <li class=\"drop-item \">
                                            <span class=\"drop-link current\">Канализация</span>
                                        </li>
                                        <li class=\"drop-item\">
                                            <a class=\"drop-link\" href=\"#\">Электрика</a>

                                        </li>
                                        <li class=\"drop-item\">
                                            <a class=\"drop-link\" href=\"#\">Отопление</a>
                                        </li>
                                        <li class=\"drop-item\">
                                            <a class=\"drop-link\" href=\"#\">Водоснабжение</a>
                                        </li>
                                        <li class=\"drop-item\">
                                            <a class=\"drop-link\" href=\"#\">Вентиляция</a>
                                        </li>
                                    </ul>
                                </div>
                                <div class=\"opacity\"></div>
                            </div>
                        </div>
                    ";
    }

    // line 144
    public function block_banner($context, array $blocks = array())
    {
        // line 145
        echo "                        <div class=\"premium-announcement announcement\">
                        </div>
                    ";
    }

    // line 149
    public function block_content($context, array $blocks = array())
    {
        // line 150
        echo "                            ";
        $this->displayBlock('callback', $context, $blocks);
        // line 151
        echo "                            ";
        $this->displayBlock('tabs', $context, $blocks);
        // line 152
        echo "                            ";
        $this->displayBlock('search_more', $context, $blocks);
        // line 194
        echo "                        ";
    }

    // line 150
    public function block_callback($context, array $blocks = array())
    {
        echo "";
    }

    // line 151
    public function block_tabs($context, array $blocks = array())
    {
        echo "";
    }

    // line 152
    public function block_search_more($context, array $blocks = array())
    {
        // line 153
        echo "                                <div class=\"search-info-block outline-right clearfix\">
                                    <div class=\"block-title\">Не нашли что искали?</div>
                                    <div class=\"search-info-list\">
                                        <ul class=\"items\">
                                            <li class=\"item icon-lamp\">У нас есть еще <a class=\"link\" href=\"#\">18 компаний</a>, которые торгуют арматурой в Москве.</li>
                                            <li class=\"item icon-lamp\">Подпишитесь на эту страницу, мы пришлем вам письмо, как только здесь появятся товары.
                                            </li>
                                            <li class=\"item icon-lamp\">Отправьте <a class=\"link\" href=\"#\">заявку на Арматуру А3 500С</a> в Москве, мы найдем вам поставщика.</li>
                                        </ul>
                                    </div>
                                    <div class=\"more-columns clearfix\">
                                        <ul class=\"row first float-left\">
                                            <li class=\"title\">Смежные разделы</li>
                                            <li>
                                                <a href=\"#\" class=\"is-gradiented\">Балка, Катанка, Швеллер</a>
                                            </li>
                                        </ul>
                                        <ul class=\"row float-left\">
                                            <li class=\"title\">В других регионах</li>
                                            <li>
                                                <a href=\"#\" class=\"is-gradiented\">Арматура в Бобруйске</a>
                                            </li>
                                            <li>
                                                <a href=\"#\" class=\"is-gradiented\">Арматура в Гатчине</a>
                                            </li>
                                        </ul>
                                        <ul class=\"row float-left\">
                                            <li class=\"title\">В других странах</li>
                                            <li>
                                                <a href=\"#\" class=\"is-gradiented\">Арматура в Украине</a>
                                            </li>
                                            <li>
                                                <a href=\"#\" class=\"is-gradiented\">Арматура в Беларуси</a>
                                            </li>
                                            <li>
                                                <a href=\"#\" class=\"is-gradiented\">Арматура в Казахстане</a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            ";
    }

    // line 195
    public function block_sidebar($context, array $blocks = array())
    {
        echo "";
    }

    // line 205
    public function block_footer($context, array $blocks = array())
    {
        // line 206
        echo "                <div class=\"footer-links-wrapper clearfix\">
                    <div class=\"footer-links\">
                        <div class=\"footer-title\">Компания</div>
                        <ul class=\"footer-links-list first\">
                            <li class=\"item\"><a href=\"#\">О сервисе</a></li>
                            <li class=\"item\"><a href=\"#\">Реклама и услуги</a></li>
                            <li class=\"item\"><a href=\"#\">Клиенты</a></li>
                            <li class=\"item\"><a href=\"#\">Отзывы</a></li>
                            <li class=\"item\"><a href=\"#\">Контактная информация</a></li>
                            <li class=\"item\"><p class=\"support\">Техническая поддержка: <span class=\"support-phone\">(495) 984-06-52</span>
                                </p></li>
                        </ul>
                    </div>
                    <div class=\"footer-links\">
                        <div class=\"footer-title\">Информация на сайте</div>
                        <ul class=\"footer-links-list\">
                            <li class=\"item\"><a href=\"#\">Компании</a></li>
                            <li class=\"item\"><a href=\"#\">Отзывы</a></li>
                            <li class=\"item\"><a href=\"#\">Товары</a></li>
                            <li class=\"item\"><a href=\"#\">Потребности</a></li>
                            <li class=\"item\"><a href=\"#\">Последние обновления</a></li>
                            <li class=\"item\"><a href=\"#\" class=\"green-color\">Добавить компанию и товары</a></li>
                        </ul>
                    </div>
                </div>
                <ul class=\"footer-links-list clearfix\">
                    <li class=\"item\">
                        <p class=\"copy\">Металлопрокат.ру © 2000-2013</p>
                    </li>
                    <li class=\"item\">
                        <a class=\"agreement\" href=\"#\" target=\"_blank\">Пользовательское соглашение</a>
                    </li>
                </ul>
                <div class=\"counters-block clearfix\">
                    <a href=\"#\" class=\"rspm-member float-left\">Действительный член РСПМ</a>

                    <div class=\"counter-container float-right\">
                        <img src=\"./markup/pic/counter1.png\" width=\"80\" height=\"31\" alt=\"image description\"/>
                        <img src=\"./markup/pic/counter2.png\" width=\"88\" height=\"31\" alt=\"image description\"/>
                    </div>
                </div>
            ";
    }

    public function getTemplateName()
    {
        return "@markup/content/_content_layout.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  372 => 206,  369 => 205,  363 => 195,  319 => 153,  316 => 152,  310 => 151,  304 => 150,  294 => 151,  291 => 150,  288 => 149,  282 => 145,  279 => 144,  229 => 97,  226 => 96,  196 => 66,  193 => 65,  169 => 43,  166 => 42,  158 => 33,  155 => 32,  151 => 38,  149 => 32,  135 => 20,  132 => 19,  128 => 250,  124 => 248,  122 => 205,  111 => 196,  108 => 195,  103 => 148,  100 => 144,  98 => 96,  93 => 93,  91 => 65,  88 => 64,  86 => 42,  81 => 39,  71 => 15,  68 => 14,  62 => 12,  57 => 9,  54 => 8,  46 => 4,  43 => 3,  300 => 194,  297 => 152,  112 => 53,  109 => 46,  106 => 149,  99 => 38,  96 => 37,  90 => 42,  87 => 37,  85 => 36,  83 => 35,  79 => 19,  76 => 31,  49 => 8,  45 => 6,  42 => 5,  39 => 4,  33 => 3,);
    }
}
