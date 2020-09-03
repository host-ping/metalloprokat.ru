<?php

/* markup/html/portal/_portal_layout.html.twig */
class __TwigTemplate_c8d5d724d7d62a4516e585e292a0b9af extends Twig_Template
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
            'sidebar' => array($this, 'block_sidebar'),
            'content' => array($this, 'block_content'),
            'tabs' => array($this, 'block_tabs'),
            'search_more' => array($this, 'block_search_more'),
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
    <link rel=\"stylesheet\" href=\"./markup/css/style-portal.css\" type=\"text/css\" media=\"all\"/>
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
    public function block_body($context, array $blocks = array())
    {
        // line 13
        echo "    <div ";
        $this->displayBlock("container_arrtibutes", $context, $blocks);
        echo ">
        <div class=\"inside-container\">
            <div id=\"header\" class=\"clearfix\">
                <div class=\"wrap\">
                    ";
        // line 17
        $this->displayBlock('header', $context, $blocks);
        // line 50
        echo "                </div>
            </div>
            ";
        // line 52
        $this->displayBlock('head_banner', $context, $blocks);
        // line 53
        echo "             ";
        $this->displayBlock('menu', $context, $blocks);
        // line 158
        echo "            ";
        $this->displayBlock('search_form', $context, $blocks);
        // line 204
        echo "
            <div id=\"main\">
                ";
        // line 206
        if (array_key_exists("landingTemplate", $context)) {
            // line 207
            echo "                    <div class=\"land-background\" style=\"background-image: url('./markup/pic/bg-land.jpg'); background-size: cover;\"></div>
                ";
        }
        // line 209
        echo "                ";
        $this->displayBlock('side_announcements', $context, $blocks);
        // line 238
        echo "                <div class=\"wrap clearfix\">

                    ";
        // line 240
        $this->displayBlock('breadcrumbs', $context, $blocks);
        // line 407
        echo "
                    ";
        // line 408
        $this->displayBlock('banner', $context, $blocks);
        // line 419
        echo "                    ";
        $this->displayBlock('callback', $context, $blocks);
        // line 426
        echo "                    <div class=\"wrapper outline clearfix\"  data-redirect-url=\"http://katushkin.ru\">
                        ";
        // line 427
        $this->displayBlock('sidebar', $context, $blocks);
        // line 428
        echo "                        ";
        $this->displayBlock('content', $context, $blocks);
        // line 473
        echo "

                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id=\"footer\">
        <div class=\"footer-content wrap\">
            ";
        // line 483
        $this->displayBlock('footer', $context, $blocks);
        // line 526
        echo "        </div>
    </div>
    ";
        // line 528
        $this->env->loadTemplate("@markup/_popups.html.twig")->display($context);
    }

    // line 17
    public function block_header($context, array $blocks = array())
    {
        // line 18
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
        // line 42
        $this->displayBlock('login', $context, $blocks);
        // line 48
        echo "                        <div class=\"pixxel\"></div>
                    ";
    }

    // line 42
    public function block_login($context, array $blocks = array())
    {
        // line 43
        echo "                            <div class=\"user-block float-right\">
                                <a class=\"login js-popup-opener icon-exit\" data-popup=\"#login\" href=\"#\"><span class=\"login-text\">Вход в кабинет</span></a>
                                <a class=\"register-link js-popup-opener\" data-popup=\"#register-company\" href=\"#\">Регистрация</a>
                            </div>
                        ";
    }

    // line 52
    public function block_head_banner($context, array $blocks = array())
    {
        echo "";
    }

    // line 53
    public function block_menu($context, array $blocks = array())
    {
        // line 54
        echo "                <div class=\"main-menu-wrapper\">
                <div class=\"wrap clearfix\">
                <input type=\"checkbox\" id=\"open-menu\" class=\"open-check\">
                <div class=\"hold-toogle\">
                    <label for=\"open-menu\" class=\"toogle-menu\">
                        <span class=\"t\"></span>
                        <span class=\"c\"></span>
                        <span class=\"b\"></span>
                    </label>
                </div>
                    <div class=\"menu-holder\">
                        <label for=\"open-menu\" class=\"close-menu\">Меню</label>
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
                            <li class=\"menu-login\">
                                <a class=\"login link js-popup-opener icon-exit\" data-popup=\"#login\" href=\"#\">
                                    Вход в кабинет
                                </a>
                            </li>
                            <li class=\"menu-register\">
                                <a class=\"register-link link js-popup-opener\" data-popup=\"#register-company\" href=\"#\">Регистрация</a>
                            </li>
                            <li>
                                <div class=\"user-block login-user\">
                                    <div class=\"user-block-holder js-popover-opener clearfix\" data-popover=\"#user-menu-right\">
                                        <a href=\"#\" class=\"msgs float-left\" >
                                            <span class=\"icon-flag float-left\"></span>
                                            <span class=\"msg-count float-left\">9</span>
                                        </a>
                                        <div class=\"user-photo float-left\">
                                            <a href=\"#\">
                                                <img src=\"./markup/pic/user.png\" alt=\"image description\"/>
                                            </a>
                                        </div>
                                        <span class=\"user-name-wrapper icon-check float-left\">
                                            <a href=\"#\" class=\"user-name is-gradiented\">Специалист по маркетингу</a>
                                        </span>
                                    </div>
                                   <div id=\"user-menu-right\" class=\"u-menu drop-wrapper opacity-border\">
                                        <ul class=\"dropdown list\">
                                            ";
        // line 105
        if ((isset($context["company"]) ? $context["company"] : null)) {
            // line 106
            echo "                                                <li class=\"company-name first\">
                                                    <div class=\"rating float-right\">
                                                        <span class=\"star-mini icon-star-colored\"></span>
                                                        <span class=\"star-mini icon-star-colored\"></span>
                                                        <span class=\"star-mini icon-star-colored\"></span>
                                                    </div>
                                                    <a href=\"#\" class=\"company-name-link\">Стальторг</a>
                                                </li>
                                            ";
        }
        // line 115
        echo "                                            <li class=\"private-room\">
                                                <a class=\"link\" href=\"#\">Личный кабинет</a>
                                                <div class=\"sec-links\">
                                                    <a class=\"link\" href=\"#\">прайс-лист,</a>
                                                    <a class=\"link\" href=\"#\">статистика,</a>
                                                    <a class=\"link\" href=\"#\">оплата</a>
                                                </div>
                                            </li>
                                            <li class=\"drop-item clearfix\">
                                                <strong class=\"count ie-radius float-right\">5</strong>
                                                <a class=\"drop-link\" href=\"#\">Клиенты ожидают ответа</a>
                                            </li>
                                            <li class=\"drop-item clearfix\">
                                                <strong class=\"count ie-radius float-right\">3</strong>
                                                <a class=\"drop-link\" href=\"#\">Счета и услуги</a>

                                            </li>
                                            <li class=\"drop-item clearfix\">
                                                <strong class=\"count ie-radius float-right\">1</strong>
                                                <a class=\"drop-link\" href=\"#\">Отзывы о компании</a>
                                            </li>
                                            <li class=\"drop-item disabled clearfix\">
                                                <strong class=\"count ie-radius float-right\">2</strong>
                                                <span class=\"drop-link\">Жалобы</span>
                                            </li>
                                            <li class=\"drop-item disabled clearfix\">
                                                <strong class=\"count ie-radius float-right\">8</strong>
                                                <span class=\"drop-link\">Сообщения от модератора</span>
                                            </li>
                                            <li class=\"quit drop-item\">
                                                <a class=\"drop-link\" href=\"#\">Выход</a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <label for=\"open-menu\" class=\"overlay\"></label>
                    <div class=\"nav-overlay\">&nbsp;</div>
                </div>
            </div>
            ";
    }

    // line 158
    public function block_search_form($context, array $blocks = array())
    {
        // line 159
        echo "                <div class=\"search-block clearfix\">
                    <form action=\"#\" class=\"search-form\">
                        <fieldset id=\"search-fixed\" class=\"main-block js-fixed ";
        // line 161
        echo twig_escape_filter($this->env, (isset($context["searchClass"]) ? $context["searchClass"] : null), "html", null, true);
        echo "\">
                            <div class=\"wrap clearfix\">
                                <div class=\"search-field-wrap float-left\">
                                    <span class=\"icon-search-big\"></span>
                                    <span class=\"twitter-typeahead\" style=\"position: relative; display: inline-block; direction: ltr;\">
                                        <input type=\"text\" placeholder=\"Товар или компания\" class=\"search-input\"/>
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
                                    <button type=\"submit\" class=\"button search-submit blue-bg float-left ie-radius\">
                                        <span class=\"search-text\">Найти</span>
                                        <span class=\"search-text-mob\">
                                            <span class=\"icon-search-big\"></span>
                                        </span>
                                    </button>
                                </div>
                            </div>

                        </fieldset>
                        <fieldset class=\"more\">
                            <div class=\"wrap clearfix\">
                                <div class=\"check-holder\">
                                    <input id=\"search-more\" type=\"checkbox\" class=\"js-styled-checkbox bg-white\"/>
                                    <label for=\"search-more\">Только в «Сортовой и фасонный прокат»</label>
                                </div>
                            </div>
                        </fieldset>
                    </form>
                </div>
            ";
    }

    // line 209
    public function block_side_announcements($context, array $blocks = array())
    {
        // line 210
        echo "                    <div class=\"left-announcement-wrapper\">
                        <div class=\"js-fixed-side-banner\">
                            <div class=\"left-announcement announcement has-announcement\">
                                <img src=\"./markup/pic/left-banner-150.jpg\" alt=\"image description\"/>
                                ";
        // line 215
        echo "                                ";
        // line 216
        echo "                                ";
        // line 217
        echo "                                ";
        // line 218
        echo "                                ";
        // line 219
        echo "                                ";
        // line 220
        echo "                            </div>
                        </div>
                    </div>
                    <div class=\"right-announcement-wrapper\">
                        <div class=\"js-fixed-side-banner\">
                            <div class=\"right-announcement announcement has-announcement top-announcement\">
                                <img src=\"./markup/pic/left-banner-150.jpg\" alt=\"image description\"/>
                            </div>
                            <!--div class=\"right-announcement announcement has-announcement\">
                                <img src=\"./markup/pic/right-banner-150.jpg\" alt=\"image description\"/>
                            </div>
                            <div class=\"right-announcement announcement has-announcement\">
                                <img src=\"./markup/pic/right-banner-150.jpg\" alt=\"image description\"/>
                            </div>-->
                        </div>
                    </div>

                ";
    }

    // line 240
    public function block_breadcrumbs($context, array $blocks = array())
    {
        // line 241
        echo "                        <div class=\"breadcrumbs-wrapper\">
                            <div class=\"breadcrumbs outline\">
                                ";
        // line 243
        $this->displayBlock('breadcrumbs_button', $context, $blocks);
        // line 248
        echo "                                <div class=\"breadcrumbs_item-list clearfix js-collapsable-breadcrumbs\"
                                    data-collapsable-breadcrumbs-reserve=\".js-collapsable-breadcrumbs-reserve\">
                                    <div class=\"breadcrumbs_item home first\">
                                        <a class=\"breadcrumbs_link icon-home\" href=\"#\"></a>
                                    </div>
                                    <div class=\"wrap-openbox\">
                                        <input type=\"checkbox\" id=\"breadcrumbs-openclose\" class=\"input-openclose\">
                                        <label for=\"breadcrumbs-openclose\">&nbsp;</label>
                                        <div class=\"box-open\">
                                            <div class=\"breadcrumbs_item\">
                                                <a class=\"breadcrumbs_link js-collapsable-item\" data-collapsable-breadcrumb-priority=\"1\"
                                                   href=\"#\">Продукция из черных металлов</a>
                                            </div>
                                            <div class=\"breadcrumbs_item\">
                                                <a href=\"#\" class=\"breadcrumbs_link js-collapsable-item icon-check black js-popover-opener\"
                                                   data-collapsable-breadcrumb-priority=\"12\" data-popover=\"#p\"
                                                   data-different-position=\"true\">Сортовой и фасонный прокат</a>
                                            </div>
                                            <div class=\"breadcrumbs_item\">
                                                <a class=\"breadcrumbs_link js-collapsable-item\" data-collapsable-breadcrumb-priority=\"3\"
                                                   href=\"#\">Профиль</a>
                                            </div>
                                            <div class=\"breadcrumbs_item last\">
                                                <span class=\"breadcrumbs_link disabled\">Профиль стальной</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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

    // line 243
    public function block_breadcrumbs_button($context, array $blocks = array())
    {
        // line 244
        echo "                                    <div class=\"add is-gradiented float-right js-collapsable-breadcrumbs-reserve\">
                                        <a class=\"add-button product icon-add-btn\" href=\"#\">Добавить товары</a>
                                    </div>
                                ";
    }

    // line 408
    public function block_banner($context, array $blocks = array())
    {
        // line 409
        echo "                        <div class=\"premium-announcement announcement has-announcement\">
                            <img src=\"./markup/pic/banner.jpg\" alt=\"image description\">
                            ";
        // line 412
        echo "                                ";
        // line 413
        echo "                                ";
        // line 414
        echo "                                ";
        // line 415
        echo "                                ";
        // line 416
        echo "                            ";
        // line 417
        echo "                        </div>
                    ";
    }

    // line 419
    public function block_callback($context, array $blocks = array())
    {
        // line 420
        echo "                        <div class=\"title-callback outline\">
                            <span class=\"text-info-mob\">купить по <span data-product-text=\"\" class=\"link clickable js-popup-opener\">лучшей цене</span></span>
                            <strong>8 (800) 555-56-65</strong> <span data-popup=\"#callback-moderator\"
                                                                     class=\"callback-link link clickable js-popup-opener\">обратный звонок</span>
                        </div>
                    ";
    }

    // line 427
    public function block_sidebar($context, array $blocks = array())
    {
        echo "";
    }

    // line 428
    public function block_content($context, array $blocks = array())
    {
        // line 429
        echo "                        ";
        $this->displayBlock('tabs', $context, $blocks);
        // line 430
        echo "                            ";
        $this->displayBlock('search_more', $context, $blocks);
        // line 472
        echo "                        ";
    }

    // line 429
    public function block_tabs($context, array $blocks = array())
    {
        echo "";
    }

    // line 430
    public function block_search_more($context, array $blocks = array())
    {
        // line 431
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

    // line 483
    public function block_footer($context, array $blocks = array())
    {
        // line 484
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
        return array (  651 => 431,  648 => 430,  642 => 429,  638 => 472,  635 => 430,  632 => 429,  629 => 428,  623 => 427,  614 => 420,  611 => 419,  606 => 417,  604 => 416,  602 => 415,  600 => 414,  598 => 413,  596 => 412,  592 => 409,  589 => 408,  582 => 244,  579 => 243,  417 => 248,  415 => 243,  411 => 241,  408 => 240,  387 => 220,  385 => 219,  383 => 218,  381 => 217,  379 => 216,  377 => 215,  371 => 210,  368 => 209,  321 => 161,  317 => 159,  314 => 158,  268 => 115,  257 => 106,  255 => 105,  202 => 54,  199 => 53,  193 => 52,  185 => 43,  182 => 42,  177 => 48,  175 => 42,  149 => 18,  146 => 17,  142 => 528,  138 => 526,  136 => 483,  124 => 473,  121 => 428,  119 => 427,  116 => 426,  113 => 419,  111 => 408,  108 => 407,  106 => 240,  102 => 238,  99 => 209,  95 => 207,  93 => 206,  89 => 204,  86 => 158,  83 => 53,  81 => 52,  77 => 50,  75 => 17,  67 => 13,  64 => 12,  59 => 9,  56 => 8,  48 => 4,  1027 => 829,  1024 => 827,  1022 => 826,  1020 => 825,  1018 => 824,  1016 => 823,  1014 => 822,  1012 => 821,  1010 => 820,  1008 => 819,  1006 => 818,  1004 => 817,  1002 => 816,  1000 => 815,  998 => 814,  996 => 813,  994 => 812,  992 => 811,  990 => 810,  988 => 809,  986 => 808,  984 => 807,  982 => 806,  980 => 805,  978 => 804,  976 => 803,  973 => 801,  971 => 800,  969 => 799,  967 => 798,  965 => 797,  963 => 796,  961 => 795,  959 => 794,  957 => 793,  955 => 792,  953 => 791,  951 => 790,  949 => 789,  947 => 788,  945 => 787,  943 => 786,  941 => 785,  939 => 784,  937 => 783,  935 => 782,  933 => 781,  931 => 780,  929 => 779,  927 => 778,  925 => 777,  923 => 776,  921 => 775,  919 => 774,  917 => 773,  915 => 772,  913 => 771,  911 => 770,  909 => 769,  907 => 768,  905 => 767,  903 => 766,  901 => 765,  899 => 764,  897 => 763,  895 => 762,  893 => 761,  891 => 760,  889 => 759,  887 => 758,  885 => 757,  883 => 756,  881 => 755,  879 => 754,  877 => 753,  875 => 752,  867 => 745,  864 => 744,  859 => 125,  823 => 91,  821 => 90,  819 => 89,  817 => 88,  815 => 87,  813 => 86,  811 => 85,  809 => 84,  807 => 83,  805 => 82,  803 => 81,  801 => 80,  799 => 79,  797 => 78,  795 => 77,  793 => 76,  791 => 75,  788 => 73,  786 => 72,  783 => 70,  781 => 69,  766 => 59,  762 => 58,  752 => 53,  748 => 52,  739 => 48,  735 => 47,  724 => 39,  718 => 38,  712 => 35,  706 => 34,  701 => 31,  698 => 484,  695 => 483,  688 => 741,  72 => 127,  70 => 29,  62 => 24,  47 => 11,  45 => 3,  40 => 6,  1356 => 18,  1353 => 17,  1350 => 16,  1343 => 1301,  60 => 20,  58 => 16,  46 => 6,  43 => 5,  37 => 5,  31 => 3,);
    }
}
