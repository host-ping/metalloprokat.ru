<?php

/* markup/html/portal/_portal_layout.html.twig */
class __TwigTemplate_9a0f640b7df6fe6e6123b1bea25c1821 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = $this->env->loadTemplate("@markup/_base_layout.html.twig");

        $this->blocks = array(
            'stylesheets' => array($this, 'block_stylesheets'),
            'container_arrtibutes' => array($this, 'block_container_arrtibutes'),
            'body' => array($this, 'block_body'),
            'header' => array($this, 'block_header'),
            'login' => array($this, 'block_login'),
            'head_banner' => array($this, 'block_head_banner'),
            'menu' => array($this, 'block_menu'),
            'search_form' => array($this, 'block_search_form'),
            'side_announcements' => array($this, 'block_side_announcements'),
            'breadcrumbs' => array($this, 'block_breadcrumbs'),
            'breadcrumbs_button' => array($this, 'block_breadcrumbs_button'),
            'banner' => array($this, 'block_banner'),
            'callback' => array($this, 'block_callback'),
            'content' => array($this, 'block_content'),
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

    // line 7
    public function block_container_arrtibutes($context, array $blocks = array())
    {
        // line 8
        echo "    class=\"container portal js-announcement js-layout-announcement\"
";
    }

    // line 11
    public function block_body($context, array $blocks = array())
    {
        // line 12
        echo "    <div ";
        $this->displayBlock("container_arrtibutes", $context, $blocks);
        echo ">
        <div class=\"inside-container\">
            <div id=\"header\" class=\"clearfix\">
                <div class=\"wrap\">
                    ";
        // line 16
        $this->displayBlock('header', $context, $blocks);
        // line 49
        echo "                </div>
            </div>
            ";
        // line 51
        $this->displayBlock('head_banner', $context, $blocks);
        // line 52
        echo "            ";
        $this->displayBlock('menu', $context, $blocks);
        // line 74
        echo "
            ";
        // line 75
        $this->displayBlock('search_form', $context, $blocks);
        // line 116
        echo "
            <div id=\"main\">
                ";
        // line 118
        if (array_key_exists("landingTemplate", $context)) {
            // line 119
            echo "                    <div class=\"land-background\" style=\"background-image: url('./markup/pic/bg-land.jpg'); background-size: cover;\"></div>
                ";
        }
        // line 121
        echo "                ";
        $this->displayBlock('side_announcements', $context, $blocks);
        // line 150
        echo "                <div class=\"wrap clearfix\">

                    ";
        // line 152
        $this->displayBlock('breadcrumbs', $context, $blocks);
        // line 313
        echo "
                    ";
        // line 314
        $this->displayBlock('banner', $context, $blocks);
        // line 325
        echo "                    ";
        $this->displayBlock('callback', $context, $blocks);
        // line 332
        echo "                    <div class=\"wrapper outline clearfix\"  data-redirect-url=\"http://katushkin.ru\">
                        ";
        // line 333
        $this->displayBlock('content', $context, $blocks);
        // line 378
        echo "                        ";
        $this->displayBlock('sidebar', $context, $blocks);
        // line 379
        echo "
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id=\"footer\">
        <div class=\"footer-content wrap\">
            ";
        // line 388
        $this->displayBlock('footer', $context, $blocks);
        // line 431
        echo "        </div>
    </div>
    ";
        // line 433
        $this->env->loadTemplate("@markup/_popups.html.twig")->display($context);
    }

    // line 16
    public function block_header($context, array $blocks = array())
    {
        // line 17
        echo "                        <div class=\"left float-left\">
                            <div class=\"logo float-left\">
                                <a href=\"/\">
                                    <img src=\"./markup/img/logo.png\" width=\"36\" height=\"27\" alt=\"металлопрокат.ру\"/>
                                </a>
                            </div>
                            <div class=\"logo-text float-left\">
                                <p>
                                    <a class=\"header-logo-text\" href=\"#\">металлопрокат.ру</a>
                                </p>
                            </div>

                            <div class=\"location float-right\">
                                <span class=\"js-popover-opener icon-check current-location\" data-popover=\"#cities\" data-index=\"1001\"
                                      data-different-position=\"true\">Россия</span>
                                <span class=\"js-popover-opener icon-check current-location\" data-popover=\"#cities\" data-index=\"1001\"
                                      data-different-position=\"true\">Екатеринбург</span>
                            </div>

                        </div>
                        <div class=\"header-info-block white95-color float-left\">
                            <p class=\"title\">Отдел продаж</p>
                            <p class=\"phone\">8(800)555-56-65</p>
                        </div>
                        ";
        // line 41
        $this->displayBlock('login', $context, $blocks);
        // line 47
        echo "                        <div class=\"pixxel\"></div>
                    ";
    }

    // line 41
    public function block_login($context, array $blocks = array())
    {
        // line 42
        echo "                            <div class=\"user-block float-right\">
                                <a class=\"login js-popup-opener icon-exit\" data-popup=\"#login\" href=\"#\">Вход в кабинет</a>
                                <a class=\"register-link js-popup-opener\" data-popup=\"#register-company\" href=\"#\">Регистрация</a>
                            </div>
                        ";
    }

    // line 51
    public function block_head_banner($context, array $blocks = array())
    {
        echo "";
    }

    // line 52
    public function block_menu($context, array $blocks = array())
    {
        // line 53
        echo "                <div class=\"main-menu-wrapper\">
                    <div class=\"wrap clearfix\">
                        <div class=\"menu-holder\">
                            <ul id=\"menu\" class=\"main-menu clearfix\">
                                <li class=\"prev-active\">
                                    <a class=\"favorites link icon-idea\" href=\"#\">Идеи</a>
                                </li>
                                <li class=\"active\">
                                    <a href=\"#\" class=\"supplier link icon-suppliers-color\">Поставщики</a>
                                </li>
                                <li>
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

    // line 75
    public function block_search_form($context, array $blocks = array())
    {
        // line 76
        echo "                <div class=\"search-block clearfix\">
                    <form action=\"#\" class=\"search-form\">
                        <fieldset id=\"search-fixed\" class=\"main-block js-fixed ";
        // line 78
        echo twig_escape_filter($this->env, (isset($context["searchClass"]) ? $context["searchClass"] : null), "html", null, true);
        echo "\">
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
                                <div class=\"search-submit-wrapper float-right\">
                                    <a href=\"#\" class=\"change-location icon-check js-popover-opener float-left\"
                                       data-popover=\"#cities\" data-different-position=\"true\" data-index=\"2000\">Москва и Область</a>
                                    <input type=\"submit\" value=\"Найти\" class=\"button search-submit blue-bg float-left ie-radius\"/>
                                </div>
                            </div>

                        </fieldset>
                        <fieldset class=\"more\">
                            <div class=\"wrap clearfix\">
                                <div class=\"check-holder\">
                                    <input id=\"search-more\" type=\"checkbox\" class=\"js-styled-checkbox bg-white\"/>
                                    <label for=\"search-more\">Искать только в разделе «Сортовой и фасонный прокат»</label>
                                </div>
                            </div>
                        </fieldset>
                    </form>
                </div>
            ";
    }

    // line 121
    public function block_side_announcements($context, array $blocks = array())
    {
        // line 122
        echo "                    <div class=\"left-announcement-wrapper\">
                        <div class=\"js-fixed-side-banner\">
                            <div class=\"left-announcement announcement has-announcement\">
                                <img src=\"./markup/pic/left-banner-150.jpg\" alt=\"image description\"/>
                                ";
        // line 127
        echo "                                ";
        // line 128
        echo "                                ";
        // line 129
        echo "                                ";
        // line 130
        echo "                                ";
        // line 131
        echo "                                ";
        // line 132
        echo "                            </div>
                        </div>
                    </div>
                    <div class=\"right-announcement-wrapper\">
                        <div class=\"js-fixed-side-banner\">
                            <div class=\"right-announcement announcement has-announcement top-announcement\">
                                <img src=\"./markup/pic/right-top-banner-150.jpg\" alt=\"image description\"/>
                            </div>
                            <div class=\"right-announcement announcement has-announcement\">
                                <img src=\"./markup/pic/right-banner-150.jpg\" alt=\"image description\"/>
                            </div>
                            <div class=\"right-announcement announcement has-announcement\">
                                <img src=\"./markup/pic/right-banner-150.jpg\" alt=\"image description\"/>
                            </div>
                        </div>
                    </div>

                ";
    }

    // line 152
    public function block_breadcrumbs($context, array $blocks = array())
    {
        // line 153
        echo "                        <div class=\"breadcrumbs-wrapper\">
                            <div class=\"breadcrumbs outline\">
                                ";
        // line 155
        $this->displayBlock('breadcrumbs_button', $context, $blocks);
        // line 160
        echo "                                <ul class=\"breadcrumbs_item-list clearfix js-collapsable-breadcrumbs\"
                                    data-collapsable-breadcrumbs-reserve=\".js-collapsable-breadcrumbs-reserve\">
                                    <li class=\"breadcrumbs_item home first\">
                                        <a class=\"breadcrumbs_link icon-home\" href=\"#\"></a>
                                    </li>
                                    <li class=\"breadcrumbs_item\">
                                        <a class=\"breadcrumbs_link js-collapsable-item\" data-collapsable-breadcrumb-priority=\"1\"
                                           href=\"#\">Продукция из черных металлов</a>
                                    </li>
                                    <li class=\"breadcrumbs_item\">
                                        <a href=\"#\" class=\"breadcrumbs_link js-collapsable-item icon-check black js-popover-opener\"
                                           data-collapsable-breadcrumb-priority=\"12\" data-popover=\"#p\"
                                           data-different-position=\"true\">Сортовой и фасонный прокат</a>
                                    </li>
                                    <li class=\"breadcrumbs_item\">
                                        <a class=\"breadcrumbs_link js-collapsable-item\" data-collapsable-breadcrumb-priority=\"3\"
                                           href=\"#\">Профиль</a>
                                    </li>
                                    <li class=\"breadcrumbs_item last\">
                                        <span class=\"breadcrumbs_link disabled\">Профиль стальной</span>
                                    </li>
                                </ul>
                            </div>
                            <div id=\"p\" class=\"drop-wrapper product-list opacity-border has-child\">
                                <div class=\"categories-drops\">
                                    <div id=\"dropdown-breadcrumbs-categories-1\" data-dr=\"0\" class=\"drop-wrapper opacity-border\">
                                        <div class=\"js-scrollable\">
                                            <ul class=\"dropdown menu-drop\">
                                                <li class=\"drop-item\">
                                                    <a class=\"drop-link\" href=\"#\">Товар 1</a>
                                                </li>
                                                <li class=\"drop-item\">
                                                    <a class=\"drop-link\" href=\"#\">Товар 2</a>
                                                </li>
                                                <li class=\"drop-item\">
                                                    <a class=\"drop-link\" href=\"#\">Товар 3</a>
                                                </li>
                                                <li class=\"drop-item\">
                                                    <a class=\"drop-link\" href=\"#\">Товар 4</a>
                                                </li>
                                                <li class=\"drop-item\">
                                                    <a class=\"drop-link\" href=\"#\">Товар 5</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div id=\"dropdown-breadcrumbs-categories-2\" class=\"drop-wrapper opacity-border\">
                                        <div class=\"js-scrollable\">
                                            <ul class=\"dropdown menu-drop\">
                                                <li class=\"drop-item\">
                                                    <a class=\"drop-link\" href=\"#\">Товар 21</a>
                                                </li>
                                                <li class=\"drop-item\">
                                                    <a class=\"drop-link\" href=\"#\">Товар 22</a>
                                                </li>
                                                <li class=\"drop-item\">
                                                    <a class=\"drop-link\" href=\"#\">Товар 23</a>
                                                </li>
                                                <li class=\"drop-item\">
                                                    <a class=\"drop-link\" href=\"#\">Товар 24</a>
                                                </li>
                                                <li class=\"drop-item\">
                                                    <a class=\"drop-link\" href=\"#\">Товар 25</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div id=\"dropdown-breadcrumbs-categories-3\" class=\"drop-wrapper opacity-border\">
                                        <div class=\"js-scrollable\">
                                            <ul class=\"dropdown menu-drop\">
                                                <li class=\"drop-item\">
                                                    <a class=\"drop-link\" href=\"#\">Товар 31</a>
                                                </li>
                                                <li class=\"drop-item\">
                                                    <a class=\"drop-link\" href=\"#\">Товар 32</a>
                                                </li>
                                                <li class=\"drop-item\">
                                                    <a class=\"drop-link\" href=\"#\">Товар 33</a>
                                                </li>
                                                <li class=\"drop-item\">
                                                    <a class=\"drop-link\" href=\"#\">Товар 34</a>
                                                </li>
                                                <li class=\"drop-item\">
                                                    <a class=\"drop-link\" href=\"#\">Товар 35</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class=\"js-scrollable\">
                                    <ul class=\"dropdown menu-drop\">
                                        <li class=\"drop-item \">
                                            <span class=\"drop-link current\">Сортовой и фасонный прокат</span>
                                        </li>
                                        <li class=\"drop-item js-dropdown-opener\"
                                            data-display-dropdown=\"#dropdown-breadcrumbs-categories-1\">
                                            <a class=\"drop-link\" href=\"#\">Листовой и плоский прокат</a>

                                        </li>
                                        <li class=\"drop-item js-dropdown-opener\"
                                            data-display-dropdown=\"#dropdown-breadcrumbs-categories-2\">
                                            <a class=\"drop-link\" href=\"#\">Трубы</a>
                                        </li>
                                        <li class=\"drop-item js-dropdown-opener\"
                                            data-display-dropdown=\"#dropdown-breadcrumbs-categories-3\">
                                            <a class=\"drop-link\" href=\"#\">Трубопроводная и запорная арматура</a>
                                        </li>
                                        <li class=\"drop-item js-dropdown-opener\"
                                            data-display-dropdown=\"#dropdown-breadcrumbs-categories-1\">
                                            <a class=\"drop-link\" href=\"#\">Изделия из черных металлов (метизы)</a>
                                        </li>
                                        <li class=\"drop-item js-dropdown-opener\"
                                            data-display-dropdown=\"#dropdown-breadcrumbs-categories-1\">
                                            <a class=\"drop-link\" href=\"#\">Металлоконструкции</a>
                                        </li>
                                        <li class=\"drop-item js-dropdown-opener\"
                                            data-display-dropdown=\"#dropdown-breadcrumbs-categories-1\">
                                            <a class=\"drop-link\" href=\"#\">Крепежные изделия</a>
                                        </li>
                                        <li class=\"drop-item js-dropdown-opener\"
                                            data-display-dropdown=\"#dropdown-breadcrumbs-categories-1\">
                                            <a class=\"drop-link\" href=\"#\">Крепежные изделия</a>
                                        </li>
                                        <li class=\"drop-item js-dropdown-opener\"
                                            data-display-dropdown=\"#dropdown-breadcrumbs-categories-1\">
                                            <a class=\"drop-link\" href=\"#\">Крепежные изделия</a>
                                        </li>
                                        <li class=\"drop-item js-dropdown-opener\"
                                            data-display-dropdown=\"#dropdown-breadcrumbs-categories-1\">
                                            <a class=\"drop-link\" href=\"#\">Крепежные изделия</a>
                                        </li>
                                        <li class=\"drop-item js-dropdown-opener\"
                                            data-display-dropdown=\"#dropdown-breadcrumbs-categories-1\">
                                            <a class=\"drop-link\" href=\"#\">Крепежные изделия</a>
                                        </li>
                                        <li class=\"drop-item js-dropdown-opener\"
                                            data-display-dropdown=\"#dropdown-breadcrumbs-categories-1\">
                                            <a class=\"drop-link\" href=\"#\">Крепежные изделия</a>
                                        </li>
                                        <li class=\"drop-item js-dropdown-opener\"
                                            data-display-dropdown=\"#dropdown-breadcrumbs-categories-1\">
                                            <a class=\"drop-link\" href=\"#\">Крепежные изделия</a>
                                        </li>
                                        <li class=\"drop-item js-dropdown-opener\"
                                            data-display-dropdown=\"#dropdown-breadcrumbs-categories-1\">
                                            <a class=\"drop-link\" href=\"#\">Крепежные изделия</a>
                                        </li>
                                    </ul>
                                </div>
                                <div class=\"opacity\"></div>
                            </div>
                        </div>
                    ";
    }

    // line 155
    public function block_breadcrumbs_button($context, array $blocks = array())
    {
        // line 156
        echo "                                    <div class=\"add is-gradiented float-right js-collapsable-breadcrumbs-reserve\">
                                        <a class=\"add-button product icon-add-btn\" href=\"#\">Добавить товары</a>
                                    </div>
                                ";
    }

    // line 314
    public function block_banner($context, array $blocks = array())
    {
        // line 315
        echo "                        <div class=\"premium-announcement announcement has-announcement\">
                            <img src=\"/markup/pic/banner.jpg\" alt=\"image description\">
                            ";
        // line 318
        echo "                                ";
        // line 319
        echo "                                ";
        // line 320
        echo "                                ";
        // line 321
        echo "                                ";
        // line 322
        echo "                            ";
        // line 323
        echo "                        </div>
                    ";
    }

    // line 325
    public function block_callback($context, array $blocks = array())
    {
        // line 326
        echo "                        <div class=\"title-callback outline\">
                            купить по <span data-product-text=\"\" class=\"link clickable js-popup-opener\">лучшей цене</span>
                            <strong>8 (800) 555-56-65</strong> <span data-popup=\"#callback-moderator\"
                                                                     class=\"callback-link link clickable js-popup-opener\">обратный звонок</span>
                        </div>
                    ";
    }

    // line 333
    public function block_content($context, array $blocks = array())
    {
        // line 334
        echo "                        ";
        $this->displayBlock('tabs', $context, $blocks);
        // line 335
        echo "                            ";
        $this->displayBlock('search_more', $context, $blocks);
        // line 377
        echo "                        ";
    }

    // line 334
    public function block_tabs($context, array $blocks = array())
    {
        echo "";
    }

    // line 335
    public function block_search_more($context, array $blocks = array())
    {
        // line 336
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

    // line 378
    public function block_sidebar($context, array $blocks = array())
    {
        echo "";
    }

    // line 388
    public function block_footer($context, array $blocks = array())
    {
        // line 389
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
        return "markup/html/portal/_portal_layout.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  599 => 389,  596 => 388,  590 => 378,  546 => 336,  543 => 335,  537 => 334,  533 => 377,  530 => 335,  527 => 334,  524 => 333,  515 => 326,  512 => 325,  507 => 323,  505 => 322,  503 => 321,  501 => 320,  499 => 319,  497 => 318,  493 => 315,  490 => 314,  483 => 156,  480 => 155,  324 => 160,  322 => 155,  318 => 153,  315 => 152,  294 => 132,  292 => 131,  290 => 130,  288 => 129,  286 => 128,  284 => 127,  278 => 122,  275 => 121,  233 => 78,  229 => 76,  226 => 75,  202 => 53,  199 => 52,  193 => 51,  185 => 42,  182 => 41,  177 => 47,  175 => 41,  149 => 17,  142 => 433,  138 => 431,  136 => 388,  125 => 379,  122 => 378,  120 => 333,  117 => 332,  114 => 325,  112 => 314,  109 => 313,  107 => 152,  100 => 121,  96 => 119,  90 => 116,  85 => 74,  80 => 51,  74 => 16,  58 => 8,  48 => 4,  45 => 3,  186 => 118,  183 => 117,  152 => 25,  146 => 16,  140 => 21,  134 => 20,  129 => 17,  126 => 16,  123 => 15,  111 => 109,  103 => 150,  97 => 100,  94 => 118,  91 => 98,  88 => 75,  82 => 52,  79 => 95,  76 => 49,  73 => 93,  68 => 92,  66 => 12,  63 => 11,  61 => 56,  55 => 7,  53 => 15,  43 => 7,  40 => 6,  37 => 5,  31 => 3,);
    }
}
