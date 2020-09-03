<?php

/* @markup/portal/suppliers/products.html.twig */
class __TwigTemplate_022dbbad6ff540cde801277c51b9f48f extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = $this->env->loadTemplate("markup/html/portal/suppliers/companies.html.twig");

        $this->blocks = array(
            'title' => array($this, 'block_title'),
            'content' => array($this, 'block_content'),
            'links' => array($this, 'block_links'),
            'tabs' => array($this, 'block_tabs'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "markup/html/portal/suppliers/companies.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_title($context, array $blocks = array())
    {
        echo "Products list";
    }

    // line 4
    public function block_content($context, array $blocks = array())
    {
        // line 5
        echo "    <div id=\"content\" class=\"content-right outline-right float-right\">
    <div class=\"main-title \">
        <h1>Компании, торгующие арматурой
            <span class=\"accepted-filter ie-radius\">A500C <span class=\"icon-filter-del clickable\"></span></span>
            <span class=\"accepted-filter ie-radius\">строительная <span class=\"icon-filter-del clickable\"></span></span>
            <span class=\"accepted-filter ie-radius\">строительная <span class=\"icon-filter-del clickable\"></span></span>
            <span class=\"accepted-filter ie-radius\">строительная <span class=\"icon-filter-del clickable\"></span></span>
            <a href=\"#\" class=\"region-link js-popover-opener\" data-popover=\"#cities\" data-different-position=\"true\">в
                Москве и Области</a>
        </h1>
    </div>
    ";
        // line 16
        $this->displayBlock('links', $context, $blocks);
        // line 236
        echo "    ";
        $this->displayBlock('tabs', $context, $blocks);
        // line 240
        echo "    <div id=\"product\" class=\"view-category products\">
    <ul class=\"product list\">
        <li class=\"view-product item\" itemscope itemtype=\"http://schema.org/Offer\">
            <div class=\"all-product-holder clearfix\">
                <div class=\"product-logo is-bordered float-left\">
                    <a class=\"img-link pattern-big\" href=\"#\">
                        <img src=\"./markup/pic/p-logo2.jpg\" alt=\"image description\"/>
                    </a>
                </div>
                <div class=\"holder float-left\">
                    <div class=\"container top-block clearfix\">
                        <div class=\"product-info float-left\">
                            <div class=\"title\">
                                <a href=\"#\" class=\"is-gradiented-bottom\" itemprop=\"name\">Арматура строительная длинная</a>
                            </div>
                            <p class=\"size gray60-color\">Размер любой. Оптом и в розницу</p>

                            <p class=\"price gray60-color\">от
                                <strong class=\"count red-color other-currency js-helper-opener\" data-text=\"примерно <span class='red-color'>100 <span class='icon-rouble'></span></span>\">
                                    <span itemprop=\"price\" content=\"25000\">25 000 </span>
                                    <span class=\"icon-euro\"></span>
                                    <span class=\"currency g-hidden\">₽</span>
                                    <span class=\"g-hidden\" itemprop=\"priceCurrency\">RUB</span>
                                </strong>за тонну
                            </p>

                            <div class=\"information\">
                                <span class=\"updated-date\">Обновлено 8 фев 2013</span>
                            </div>
                        </div>
                        <div class=\"company-info float-right\">
                            <div class=\"holder float-left\">
                                <div class=\"star-panel clearfix\">
                                    <div class=\"medals-panel float-left\">
                                        <div class=\"medal js-popover-opener float-left\" data-popover=\"#pop-0\">
                                            <span class=\"medal-count\">10</span>
                                        </div>
                                        <div id=\"pop-0\" class=\"drop-wrapper opacity-border\">
                                            <div class=\"dropdown\">
                                                <p class=\"text\">
                                                    6 лет платного размещения, пакет <a href=\"#\" class=\"link\">Платиновый</a>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class=\"rating float-left\">
                                        <span class=\"star-mini icon-star hovered\"></span>
                                        <span class=\"star-mini icon-star hovered\"></span>
                                    </div>
                                </div>
                                <div class=\"title\">
                                    <a href=\"#\" class=\"small-link is-gradiented-bottom\">НПК Специальная металлургия</a>
                                </div>
                                <p class=\"localization gray60-color\">филиал в Люберцах</p>
                                <a class=\"site is-gradiented\" href=\"#\">staltorg.ru</a>
                            </div>
                            <div class=\"company-logo float-right\">
                                <a class=\"img-link pattern-small\" href=\"#\"></a>
                            </div>
                            <div class=\"contacts float-left\">
                                <span class=\"phone-text\" itemprop=\"telephone\">+7 (495) 784</span>
                                ( <a class=\"see\" href=\"#\">показать тел.</a> )
                                <a class=\"callback\" href=\"#\">обратный звонок</a>
                            </div>
                        </div>
                    </div>
                    <ul class=\"links clearfix\">
                        <li class=\"links_report item float-left clearfix\">
                            <a href=\"#\" class=\"button report is-bordered js-tooltip-opener js-popup-opener ie-radius\"
                               data-tooltip-title=\"Пожаловаться\" data-popup=\"#report\">
                                <span class=\"icon-complaint\"></span>
                            </a>
                        </li>
                        <li class=\"links_comment item medium float-left clearfix\">
                            <a href=\"#\" class=\"button comment is-bordered ie-radius\">
                                <span class=\"text\">Отзывы</span>
                                <span class=\"icon-comment float-right\"></span>
                            </a>
                        </li>
                        <li class=\"item medium float-left clearfix\">
                            <a href=\"#\" class=\"button favorites active-link is-bordered js-togglable-block ie-radius\">
                                <span class=\"text\">В Избранном</span>
                                <span class=\"icon-favorite-active float-right\"></span>
                            </a>
                            <a href=\"#\" class=\"button favorites delete blue-bg g-hidden js-togglable-block ie-radius\">
                                <span class=\"text\">Удалить</span>
                                <span class=\"icon-favorite-del float-right\"></span>
                            </a>
                        </li>
                        <li class=\"links_send item medium float-left\">
                            <a class=\"send-order_send-button button send-button gray60-bg ie-radius\" href=\"#\">Отправить
                                заявку</a>
                        </li>
                    </ul>
                </div>
            </div>
        </li>
        <li class=\"view-product item\">
            <div class=\"all-product-holder clearfix\">
                <div class=\"product-logo is-bordered  float-left\">
                    <a href=\"#\" class=\"img-link pattern-big\"></a>

                    <div class=\"promo-label label top\">
                        <a class=\"label-link\" href=\"#\">Промо</a>
                    </div>
                    <div class=\"product-label label bottom\">
                        <a class=\"label-link\" href=\"#\">еще 285 товаров</a>
                    </div>
                </div>
                <div class=\"holder float-left\">
                    <div class=\"container top-block clearfix\">
                        <div class=\"product-info float-left\">
                            <div class=\"title\"><a href=\"#\" class=\"is-gradiented-bottom\">Арматура строительная</a></div>
                            <p class=\"size gray60-color\">Размер 30x30</p>

                            <p class=\"price gray60-color\">от <strong class=\"count red-color\">25 000 <span class=\"icon-hrivna\"></span> </strong>за тонну
                            </p>

                            <div class=\"information\">
                                <span class=\"updated-date\">Обновлено 8 фев 2013</span>
                            </div>
                        </div>
                        <div class=\"company-info float-right\">
                            <div class=\"holder float-left\">
                                <div class=\"star-panel clearfix\">
                                    <div class=\"medals-panel float-left\">
                                        <div class=\"medal paid js-popover-opener float-left\" data-popover=\"#pop-1\">
                                            <span class=\"medal-count\">4</span>
                                        </div>
                                        <div id=\"pop-1\" class=\"drop-wrapper opacity-border\">
                                            <div class=\"dropdown\">
                                                <p class=\"text\">
                                                    6 лет платного размещения, пакет <a href=\"#\" class=\"link\">Платиновый</a>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class=\"status green-bg float-left ie-radius\">online</div>
                                    <div class=\"rating float-left\">
                                        <span class=\"star-mini icon-star-colored metalindex\"></span>
                                        <span class=\"star-mini icon-star-colored\"></span>
                                        <span class=\"star-mini icon-star-colored\"></span>
                                    </div>
                                </div>
                                <div class=\"title\">
                                    <a href=\"#\" class=\"small-link is-gradiented-bottom\">Русская горная металлургическая
                                        компания - Юг</a>
                                </div>
                                <p class=\"localization gray60-color\">филиал в Ростове-на-дону</p>
                                <a class=\"site is-gradiented\" href=\"#\">staltorg.ru</a>
                            </div>
                            <div class=\"company-logo float-right\">
                                <a class=\"img-link pattern-small\" href=\"#\">
                                    <img src=\"./markup/pic/product-logo-tmp.jpg\" width=\"64\" height=\"64\"
                                         alt=\"image description\"/>
                                </a>
                            </div>
                            <div class=\"contacts float-left\">
                                <span class=\"phone-text\">+7 (495) 7843423</span>
                                ( <a class=\"see\" href=\"#\">показать тел.</a> )
                                <a class=\"callback\" href=\"#\">обратный звонок</a>
                            </div>
                        </div>
                    </div>
                    <ul class=\"links clearfix\">
                        <li class=\"links_report item float-left clearfix\">
                            <a href=\"#\" class=\"button report is-bordered js-tooltip-opener js-popup-opener ie-radius\"
                               data-tooltip-title=\"Пожаловаться\" data-popup=\"#report\">
                                <span class=\"icon-complaint\"></span>
                            </a>
                        </li>
                        <li class=\"links_comment item medium float-left clearfix\">
                            <a href=\"#\" class=\"button comment is-bordered ie-radius\">
                                <span class=\"text\">Отзывы</span>
                                <span class=\"icon-comment float-right\"></span>
                            </a>
                        </li>
                        <li class=\"item medium float-left clearfix\">
                            <a href=\"#\" class=\"button favorites is-bordered ie-radius\">
                                <span class=\"text\">В Избранное</span>
                                <span class=\"icon-favorite float-right\"></span>
                            </a>
                        </li>
                        <li class=\"links_send item medium float-left\">
                            <a class=\"send-order_send-button button send-button red-bg js-popup-opener ie-radius\" href=\"#\"
                               data-popup=\"#demand-request\">Отправить заявку</a>
                        </li>
                    </ul>
                </div>

            </div>
        </li>
        <li class=\"announcement has-announcement\">
            <img src=\"./markup/pic/inside-banner.jpg\" width=\"735\" height=\"90\" alt=\"image description\"/>
        </li>
        <li class=\"special-title\">
            <p class=\"text\">Товары, доставляемые в Москву</p>
        </li>
        <li class=\"view-product item\">
            <div class=\"all-product-holder clearfix\">
                <div class=\"product-logo is-bordered float-left\">
                    <a class=\"img-link pattern-big\" href=\"#\">
                        <img src=\"./markup/pic/p-logo2.jpg\" alt=\"image description\"/>
                    </a>
                </div>
                <div class=\"holder float-left\">
                    <div class=\"container top-block clearfix\">
                        <div class=\"product-info float-left\">
                            <div class=\"title\"><a href=\"#\" class=\"is-gradiented-bottom\">Арматура строительная</a></div>
                            <p class=\"size gray60-color\">Размер 30x30</p>

                            <p class=\"price gray60-color\">от <strong class=\"count red-color\">25 000 <span class=\"icon-tenge\"></span> </strong>за тонну
                            </p>

                            <div class=\"information\">
                                <span class=\"updated-date\">Обновлено 8 фев 2013</span>
                            </div>
                        </div>
                        <div class=\"company-info float-right\">
                            <div class=\"holder float-left\">
                                <div class=\"star-panel clearfix\">
                                    <div class=\"rating float-left\">
                                        <span class=\"star-mini icon-star hovered\"></span>
                                        <span class=\"star-mini icon-star hovered\"></span>
                                    </div>
                                </div>
                                <div class=\"title\">
                                    <a href=\"#\" class=\"small-link is-gradiented-bottom\">Стальторг</a>
                                </div>
                                <p class=\"localization gray60-color\">филиал в Люберцах</p>
                                <a class=\"site is-gradiented\" href=\"#\">staltorg.ru</a>
                            </div>
                            <div class=\"company-logo float-right\">
                                <a class=\"img-link pattern-small\" href=\"#\"></a>
                            </div>
                            <div class=\"contacts float-left\">
                                <span class=\"phone-text\">+7 (495) 784</span>
                                ( <a class=\"see\" href=\"#\">показать тел.</a> )
                                <a class=\"callback\" href=\"#\">обратный звонок</a>
                            </div>
                        </div>
                    </div>
                    <ul class=\"links clearfix\">
                        <li class=\"links_report item float-left clearfix\">
                            <a href=\"#\" class=\"button report is-bordered js-tooltip-opener js-popup-opener ie-radius\"
                               data-tooltip-title=\"Пожаловаться\" data-popup=\"#report\">
                                <span class=\"icon-complaint\"></span>
                            </a>
                        </li>
                        <li class=\"links_comment item medium float-left clearfix\">
                            <a href=\"#\" class=\"button comment is-bordered ie-radius\">
                                <span class=\"text\">Отзывы</span>
                                <span class=\"icon-comment float-right\"></span>
                            </a>
                        </li>
                        <li class=\"item medium float-left clearfix\">
                            <a href=\"#\" class=\"button favorites active-link is-bordered js-togglable-block ie-radius\">
                                <span class=\"text\">В Избранном</span>
                                <span class=\"icon-favorite-active float-right\"></span>
                            </a>
                            <a href=\"#\" class=\"button favorites delete blue-bg g-hidden js-togglable-block ie-radius\">
                                <span class=\"text\">Удалить</span>
                                <span class=\"icon-favorite-del float-right\"></span>
                            </a>
                        </li>
                        <li class=\"links_send item medium float-left\">
                            <a class=\"send-order_send-button button send-button gray60-bg ie-radius\" href=\"#\">Отправить
                                заявку</a>
                        </li>
                    </ul>
                </div>
            </div>
        </li>
        <li class=\"special-title\">
            <p class=\"text\">Товары, доставляемые в Москву</p>
        </li>
        <li class=\"view-product item\">
            <div class=\"all-product-holder clearfix\">
                <div class=\"product-logo is-bordered float-left\">
                    <a class=\"img-link pattern-big\" href=\"#\">
                        <img src=\"./markup/pic/p-logo3.jpg\" alt=\"image description\"/>
                    </a>
                </div>
                <div class=\"holder float-left\">
                    <div class=\"container top-block clearfix\">
                        <div class=\"product-info float-left\">
                            <div class=\"title\"><a href=\"#\" class=\"is-gradiented-bottom\">Шестигранник калиброванный</a></div>
                            <p class=\"size gray60-color\">Размер 30x30</p>

                            <p class=\"price gray60-color\">от <strong class=\"count red-color\">25 000 <span class=\"icon-rouble\"></span> </strong>за тонну
                            </p>

                            <div class=\"information\">
                                <span class=\"updated-date\">Обновлено 8 фев 2013</span>
                            </div>
                        </div>
                        <div class=\"company-info float-right\">
                            <div class=\"holder float-left\">
                                <div class=\"star-panel clearfix\">
                                    <div class=\"rating float-left\">
                                        <span class=\"star-mini icon-star default\"></span>
                                        <span class=\"star-mini icon-star default\"></span>
                                        <span class=\"star-mini icon-star default\"></span>
                                    </div>
                                </div>
                                <div class=\"title\">
                                    <a href=\"#\" class=\"small-link is-gradiented-bottom\">Стальторг</a>
                                </div>
                                <p class=\"localization gray60-color\">филиал в Люберцах</p>
                                <a class=\"site is-gradiented\" href=\"#\">staltorg.ru</a>
                            </div>
                            <div class=\"company-logo float-right\">
                                <a class=\"img-link pattern-small\" href=\"#\">
                                    <img src=\"./markup/pic/c-logo1.jpg\" width=\"64\" height=\"64\" alt=\"image description\"/>
                                </a>
                            </div>
                            <div class=\"contacts float-left\">
                                <span class=\"phone-text\">+7 (495) 784</span>
                                ( <a class=\"see\" href=\"#\">показать тел.</a> )
                                <a class=\"callback\" href=\"#\">обратный звонок</a>
                            </div>
                        </div>
                    </div>
                    <ul class=\"links clearfix\">
                        <li class=\"links_report item float-left clearfix\">
                            <a href=\"#\" class=\"button report is-bordered js-tooltip-opener js-popup-opener ie-radius\"
                               data-tooltip-title=\"Пожаловаться\" data-popup=\"#report\">
                                <span class=\"icon-complaint\"></span>
                            </a>
                        </li>
                        <li class=\"links_comment item medium float-left clearfix\">
                            <a href=\"#\" class=\"button comment is-bordered ie-radius\">
                                <span class=\"text\">Отзывы</span>
                                <span class=\"icon-comment active float-right\"></span>
                                <span class=\"count red-color float-right\">12</span>
                            </a>
                        </li>
                        <li class=\"item medium float-left clearfix\">
                            <a href=\"#\" class=\"button favorites is-bordered ie-radius\">
                                <span class=\"text\">В Избранное</span>
                                <span class=\"icon-favorite float-right\"></span>
                            </a>
                        </li>
                        <li class=\"links_send item medium float-left\">
                            <a class=\"send-order_send-button button send-button red-bg ie-radius\" href=\"#\">Отправить
                                заявку</a>
                        </li>
                    </ul>
                </div>

            </div>
        </li>
        <li class=\"announcement has-announcement\">
            <img src=\"./markup/pic/inside-banner.jpg\" width=\"735\" height=\"90\" alt=\"image description\"/>
        </li>
        <li class=\"view-product item\">
            <div class=\"all-product-holder clearfix\">
                <div class=\"product-logo is-bordered float-left\">
                    <a class=\"img-link pattern-big\" href=\"#\">
                        <img src=\"./markup/pic/p-logo3.jpg\" alt=\"image description\"/>
                    </a>
                </div>
                <div class=\"holder float-left\">
                    <div class=\"container top-block clearfix\">
                        <div class=\"product-info float-left\">
                            <div class=\"title\"><a href=\"#\" class=\"is-gradiented-bottom\">Шестигранник калиброванный</a></div>
                            <p class=\"size gray60-color\">Размер 30x30</p>

                            <p class=\"price gray60-color\">от <strong class=\"count red-color\">25 000 <span class=\"icon-rouble\"></span> </strong>за тонну
                            </p>

                            <div class=\"information\">
                                <span class=\"updated-date\">Обновлено 8 фев 2013</span>
                            </div>
                        </div>
                        <div class=\"company-info float-right\">
                            <div class=\"holder float-left\">
                                <div class=\"star-panel clearfix\">
                                    <div class=\"rating float-left\">
                                        <span class=\"star-mini icon-star default\"></span>
                                        <span class=\"star-mini icon-star default\"></span>
                                        <span class=\"star-mini icon-star default\"></span>
                                    </div>
                                </div>
                                <div class=\"title\">
                                    <a href=\"#\" class=\"small-link is-gradiented-bottom\">Стальторг</a>
                                </div>
                                <p class=\"localization gray60-color\">филиал в Люберцах</p>
                                <a class=\"site is-gradiented\" href=\"#\">staltorg.ru</a>
                            </div>
                            <div class=\"company-logo float-right\">
                                <a class=\"img-link pattern-small\" href=\"#\">
                                    <img src=\"./markup/pic/c-logo1.jpg\" width=\"64\" height=\"64\" alt=\"image description\"/>
                                </a>
                            </div>
                            <div class=\"contacts float-left\">
                                <span class=\"phone-text\">+7 (495) 784</span>
                                ( <a class=\"see\" href=\"#\">показать тел.</a> )
                                <a class=\"callback\" href=\"#\">обратный звонок</a>
                            </div>
                        </div>
                    </div>
                    <ul class=\"links clearfix\">
                        <li class=\"links_report item float-left clearfix\">
                            <a href=\"#\" class=\"button report is-bordered js-tooltip-opener js-popup-opener ie-radius\"
                               data-tooltip-title=\"Пожаловаться\" data-popup=\"#report\">
                                <span class=\"icon-complaint\"></span>
                            </a>
                        </li>
                        <li class=\"links_comment item medium float-left clearfix\">
                            <a href=\"#\" class=\"button comment is-bordered ie-radius\">
                                <span class=\"text\">Отзывы</span>
                                <span class=\"icon-comment active float-right\"></span>
                                <span class=\"count red-color float-right\">12</span>
                            </a>
                        </li>
                        <li class=\"item medium float-left clearfix\">
                            <a href=\"#\" class=\"button favorites is-bordered ie-radius\">
                                <span class=\"text\">В Избранное</span>
                                <span class=\"icon-favorite float-right\"></span>
                            </a>
                        </li>
                        <li class=\"links_send item medium float-left\">
                            <a class=\"send-order_send-button button send-button red-bg ie-radius\" href=\"#\">Отправить
                                заявку</a>
                        </li>
                    </ul>
                </div>

            </div>
        </li>
        <li class=\"special-title empty-text\">
            <p class=\"text\"></p>
        </li>
        <li class=\"view-product item\">
            <div class=\"all-product-holder clearfix\">
                <div class=\"product-logo is-bordered float-left\">
                    <a class=\"img-link pattern-big\" href=\"#\">
                        <img src=\"./markup/pic/p-logo3.jpg\" alt=\"image description\"/>
                    </a>
                </div>
                <div class=\"holder float-left\">
                    <div class=\"container top-block clearfix\">
                        <div class=\"product-info float-left\">
                            <div class=\"title\"><a href=\"#\" class=\"is-gradiented-bottom\">Шестигранник калиброванный</a></div>
                            <p class=\"size gray60-color\">Размер 30x30</p>

                            <p class=\"price gray60-color\">от <strong class=\"count red-color\">25 000 <span class=\"icon-rouble\"></span> </strong>за тонну
                            </p>

                            <div class=\"information\">
                                <span class=\"updated-date\">Обновлено 8 фев 2013</span>
                            </div>
                        </div>
                        <div class=\"company-info float-right\">
                            <div class=\"holder float-left\">
                                <div class=\"star-panel clearfix\">
                                    <div class=\"rating float-left\">
                                        <span class=\"star-mini icon-star default\"></span>
                                        <span class=\"star-mini icon-star default\"></span>
                                        <span class=\"star-mini icon-star default\"></span>
                                    </div>
                                </div>
                                <div class=\"title\">
                                    <a href=\"#\" class=\"small-link is-gradiented-bottom\">Стальторг</a>
                                </div>
                                <p class=\"localization gray60-color\">филиал в Люберцах</p>
                                <a class=\"site is-gradiented\" href=\"#\">staltorg.ru</a>
                            </div>
                            <div class=\"company-logo float-right\">
                                <a class=\"img-link pattern-small\" href=\"#\">
                                    <img src=\"./markup/pic/c-logo1.jpg\" width=\"64\" height=\"64\" alt=\"image description\"/>
                                </a>
                            </div>
                            <div class=\"contacts float-left\">
                                <span class=\"phone-text\">+7 (495) 784</span>
                                ( <a class=\"see\" href=\"#\">показать тел.</a> )
                                <a class=\"callback\" href=\"#\">обратный звонок</a>
                            </div>
                        </div>
                    </div>
                    <ul class=\"links clearfix\">
                        <li class=\"links_report item float-left clearfix\">
                            <a href=\"#\" class=\"button report is-bordered js-tooltip-opener js-popup-opener ie-radius\"
                               data-tooltip-title=\"Пожаловаться\" data-popup=\"#report\">
                                <span class=\"icon-complaint\"></span>
                            </a>
                        </li>
                        <li class=\"links_comment item medium float-left clearfix\">
                            <a href=\"#\" class=\"button comment is-bordered ie-radius\">
                                <span class=\"text\">Отзывы</span>
                                <span class=\"icon-comment active float-right\"></span>
                                <span class=\"count red-color float-right\">12</span>
                            </a>
                        </li>
                        <li class=\"item medium float-left clearfix\">
                            <a href=\"#\" class=\"button favorites is-bordered ie-radius\">
                                <span class=\"text\">В Избранное</span>
                                <span class=\"icon-favorite float-right\"></span>
                            </a>
                        </li>
                        <li class=\"links_send item medium float-left\">
                            <a class=\"send-order_send-button button send-button red-bg ie-radius\" href=\"#\">Отправить
                                заявку</a>
                        </li>
                    </ul>
                </div>

            </div>
        </li>
        <li class=\"view-product item\" itemscope itemtype=\"http://schema.org/Offer\">
            <div class=\"all-product-holder clearfix\">
                <div class=\"product-logo is-bordered float-left\">
                    <a class=\"img-link pattern-big\" href=\"#\">
                        <img src=\"./markup/pic/p-logo2.jpg\" alt=\"image description\"/>
                    </a>
                </div>
                <div class=\"holder float-left\">
                    <div class=\"container top-block clearfix\">
                        <div class=\"product-info float-left\">
                            <div class=\"title\">
                                <a href=\"#\" class=\"is-gradiented-bottom\" itemprop=\"name\">Арматура строительная</a>
                            </div>
                            <p class=\"size gray60-color\">Размер 30x30</p>

                            <p class=\"price gray60-color\">от
                                <strong class=\"count red-color\">
                                    <span itemprop=\"price\">25 000 </span>
                                    <span class=\"icon-rouble\"></span>
                                    <span class=\"currency g-hidden\">₽</span>
                                    <span class=\"g-hidden\" itemprop=\"priceCurrency\">RUB</span>
                                </strong>за тонну
                            </p>

                            <div class=\"information\">
                                <span class=\"updated-date\">Обновлено 8 фев 2013</span>
                            </div>
                        </div>
                        <div class=\"company-info float-right\">
                            <div class=\"holder float-left\">
                                <div class=\"star-panel clearfix\">
                                    <div class=\"rating float-left\">
                                        <span class=\"star-mini icon-star hovered\"></span>
                                        <span class=\"star-mini icon-star hovered\"></span>
                                    </div>
                                </div>
                                <div class=\"title\">
                                    <a href=\"#\" class=\"small-link is-gradiented-bottom\">Стальторг</a>
                                </div>
                                <p class=\"localization gray60-color\">филиал в Люберцах</p>
                                <a class=\"site is-gradiented\" href=\"#\">staltorg.ru</a>
                            </div>
                            <div class=\"company-logo float-right\">
                                <a class=\"img-link pattern-small\" href=\"#\"></a>
                            </div>
                            <div class=\"contacts float-left\">
                                <span class=\"phone-text\">+7 (495) 784</span>
                                ( <a class=\"see\" href=\"#\">показать тел.</a> )
                                <a class=\"callback\" href=\"#\">обратный звонок</a>
                            </div>
                        </div>
                    </div>
                    <ul class=\"links clearfix\">
                        <li class=\"links_report item float-left clearfix\">
                            <a href=\"#\" class=\"button report is-bordered js-tooltip-opener js-popup-opener ie-radius\"
                               data-tooltip-title=\"Пожаловаться\" data-popup=\"#report\">
                                <span class=\"icon-complaint\"></span>
                            </a>
                        </li>
                        <li class=\"links_comment item medium float-left clearfix\">
                            <a href=\"#\" class=\"button comment is-bordered ie-radius\">
                                <span class=\"text\">Отзывы</span>
                                <span class=\"icon-comment float-right\"></span>
                            </a>
                        </li>
                        <li class=\"item medium float-left clearfix\">
                            <a href=\"#\" class=\"button favorites active-link is-bordered js-togglable-block ie-radius\">
                                <span class=\"text\">В Избранном</span>
                                <span class=\"icon-favorite-active float-right\"></span>
                            </a>
                            <a href=\"#\" class=\"button favorites delete blue-bg g-hidden js-togglable-block ie-radius\">
                                <span class=\"text\">Удалить</span>
                                <span class=\"icon-favorite-del float-right\"></span>
                            </a>
                        </li>
                        <li class=\"links_send item medium float-left\">
                            <a class=\"send-order_send-button button send-button gray60-bg ie-radius\" href=\"#\">Отправить
                                заявку</a>
                        </li>
                    </ul>
                </div>
            </div>
        </li>
        <li class=\"see-more-block-wrapper\">
            <div class=\"see-more-block\">
                <a class=\"see-more button ie-radius\" href=\"#\">показать еще...</a>
            </div>
        </li>
    </ul>

    </div>
        ";
        // line 841
        $this->displayBlock("search_more", $context, $blocks);
        echo "
    </div>
";
    }

    // line 16
    public function block_links($context, array $blocks = array())
    {
        // line 17
        echo "        <div class=\"product-links-wrapper clearfix\">
        <div class=\"product-link float-left\">
                    <span class=\"category-title clickable float-left js-popover-opener\" data-popover=\"#cat-1\">
                        <span class=\"name float-left\"> Лист стальной, металлический</span>
                        <span class=\"icon-check black float-left\"></span>
                        <span class=\"is-gradiented\"></span>
                    </span>

            <div id=\"cat-1\" class=\"drop-wrapper product-links-list opacity-border\">
                <ul class=\"dropdown\">
                    <li class=\"drop-item\">
                        <a class=\"drop-link current\" href=\"#\">Арматура</a>
                    </li>
                    <li class=\"drop-item\">
                        <a class=\"drop-link\" href=\"#\">Арматура А2</a>
                    </li>
                    <li class=\"drop-item\">
                        <a class=\"drop-link\" href=\"#\">Арматура А3</a>
                    </li>
                    <li class=\"drop-item\">
                        <a class=\"drop-link\" href=\"#\">Арматура А4</a>
                    </li>
                    <li class=\"drop-item\">
                        <a class=\"drop-link\" href=\"#\">Арматура А5</a>
                    </li>
                    <li class=\"drop-item\">
                        <a class=\"drop-link\" href=\"#\">Арматура А6</a>
                    </li>
                    <li class=\"drop-item\">
                        <a class=\"drop-link\" href=\"#\">Арматура А7</a>
                    </li>
                    <li class=\"drop-item\">
                        <a class=\"drop-link\" href=\"#\">Арматура А8</a>
                    </li>
                </ul>
            </div>
        </div>
        <div class=\"product-link float-left\">
                    <span class=\"category-title clickable float-left js-popover-opener\" data-popover=\"#cat-2\">
                        <span class=\"name float-left\">Балка</span>
                        <span class=\"icon-check black float-left\"></span>
                        <span class=\"is-gradiented\"></span>
                    </span>

            <div id=\"cat-2\" class=\"drop-wrapper product-links-list opacity-border\">
                <ul class=\"dropdown\">
                    <li class=\"drop-item\">
                        <a class=\"drop-link current\" href=\"#\">Балка</a>
                    </li>
                    <li class=\"drop-item\">
                        <a class=\"drop-link\" href=\"#\">Балка А2</a>
                    </li>
                    <li class=\"drop-item\">
                        <a class=\"drop-link\" href=\"#\">Балка А3</a>
                    </li>
                    <li class=\"drop-item\">
                        <a class=\"drop-link\" href=\"#\">Балка А4</a>
                    </li>
                    <li class=\"drop-item\">
                        <a class=\"drop-link\" href=\"#\">Балка А5</a>
                    </li>
                    <li class=\"drop-item\">
                        <a class=\"drop-link\" href=\"#\">Балка А6</a>
                    </li>
                    <li class=\"drop-item\">
                        <a class=\"drop-link\" href=\"#\">Балка А7</a>
                    </li>
                    <li class=\"drop-item\">
                        <a class=\"drop-link\" href=\"#\">Балка А8</a>
                    </li>
                </ul>
            </div>
        </div>
        <div class=\"product-link float-left\">
                    <span class=\"category-title clickable float-left js-popover-opener\" data-popover=\"#cat-3\">
                        <span class=\"name float-left\">Труба</span>
                        <span class=\"icon-check black float-left\"></span>
                        <span class=\"is-gradiented\"></span>
                    </span>

            <div id=\"cat-3\" class=\"drop-wrapper product-links-list opacity-border\">
                <ul class=\"dropdown\">
                    <li class=\"drop-item\">
                        <a class=\"drop-link current\" href=\"#\">Труба</a>
                    </li>
                    <li class=\"drop-item\">
                        <a class=\"drop-link\" href=\"#\">Труба А2</a>
                    </li>
                    <li class=\"drop-item\">
                        <a class=\"drop-link\" href=\"#\">Труба А3</a>
                    </li>
                    <li class=\"drop-item\">
                        <a class=\"drop-link\" href=\"#\">Труба А4</a>
                    </li>
                    <li class=\"drop-item\">
                        <a class=\"drop-link\" href=\"#\">Труба А5</a>
                    </li>
                    <li class=\"drop-item\">
                        <a class=\"drop-link\" href=\"#\">Труба А6</a>
                    </li>
                    <li class=\"drop-item\">
                        <a class=\"drop-link\" href=\"#\">Труба А7</a>
                    </li>
                    <li class=\"drop-item\">
                        <a class=\"drop-link\" href=\"#\">Труба А8</a>
                    </li>
                </ul>
            </div>
        </div>
        <div class=\"product-link float-left\">
                    <span class=\"category-title clickable float-left js-popover-opener\" data-popover=\"#cat-4\">
                        <span class=\"name float-left\">Арматура</span>
                        <span class=\"icon-check black float-left\"></span>
                        <span class=\"is-gradiented\"></span>
                    </span>

            <div id=\"cat-4\" class=\"drop-wrapper product-links-list opacity-border\">
                <ul class=\"dropdown\">
                    <li class=\"drop-item\">
                        <a class=\"drop-link current\" href=\"#\">Арматура</a>
                    </li>
                    <li class=\"drop-item\">
                        <a class=\"drop-link\" href=\"#\">Арматура А2</a>
                    </li>
                    <li class=\"drop-item\">
                        <a class=\"drop-link\" href=\"#\">Арматура А3</a>
                    </li>
                    <li class=\"drop-item\">
                        <a class=\"drop-link\" href=\"#\">Арматура А4</a>
                    </li>
                    <li class=\"drop-item\">
                        <a class=\"drop-link\" href=\"#\">Арматура А5</a>
                    </li>
                    <li class=\"drop-item\">
                        <a class=\"drop-link\" href=\"#\">Арматура А6</a>
                    </li>
                    <li class=\"drop-item\">
                        <a class=\"drop-link\" href=\"#\">Арматура А7</a>
                    </li>
                    <li class=\"drop-item\">
                        <a class=\"drop-link\" href=\"#\">Арматура А8</a>
                    </li>
                </ul>
            </div>
        </div>
        <div class=\"product-link float-left\">
                    <span class=\"category-title clickable float-left js-popover-opener\" data-popover=\"#cat-5\">
                        <span class=\"name float-left\">Арматура</span>
                        <span class=\"icon-check black float-left\"></span>
                        <span class=\"is-gradiented\"></span>
                    </span>

            <div id=\"cat-5\" class=\"drop-wrapper product-links-list opacity-border\">
                <ul class=\"dropdown\">
                    <li class=\"drop-item\">
                        <a class=\"drop-link current\" href=\"#\">Арматура</a>
                    </li>
                    <li class=\"drop-item\">
                        <a class=\"drop-link\" href=\"#\">Арматура А2</a>
                    </li>
                    <li class=\"drop-item\">
                        <a class=\"drop-link\" href=\"#\">Арматура А3</a>
                    </li>
                    <li class=\"drop-item\">
                        <a class=\"drop-link\" href=\"#\">Арматура А4</a>
                    </li>
                    <li class=\"drop-item\">
                        <a class=\"drop-link\" href=\"#\">Арматура А5</a>
                    </li>
                    <li class=\"drop-item\">
                        <a class=\"drop-link\" href=\"#\">Арматура А6</a>
                    </li>
                    <li class=\"drop-item\">
                        <a class=\"drop-link\" href=\"#\">Арматура А7</a>
                    </li>
                    <li class=\"drop-item\">
                        <a class=\"drop-link\" href=\"#\">Арматура А8</a>
                    </li>
                </ul>
            </div>
        </div>
        <div class=\"product-link float-left\">
                    <span class=\"category-title clickable float-left js-popover-opener\" data-popover=\"#cat-6\">
                        <span class=\"name float-left\">Арматура</span>
                        <span class=\"icon-check black float-left\"></span>
                        <span class=\"is-gradiented\"></span>
                    </span>

            <div id=\"cat-6\" class=\"drop-wrapper product-links-list opacity-border\">
                <ul class=\"dropdown\">
                    <li class=\"drop-item\">
                        <a class=\"drop-link current\" href=\"#\">Арматура</a>
                    </li>
                    <li class=\"drop-item\">
                        <a class=\"drop-link\" href=\"#\">Арматура А2</a>
                    </li>
                    <li class=\"drop-item\">
                        <a class=\"drop-link\" href=\"#\">Арматура А3</a>
                    </li>
                    <li class=\"drop-item\">
                        <a class=\"drop-link\" href=\"#\">Арматура А4</a>
                    </li>
                    <li class=\"drop-item\">
                        <a class=\"drop-link\" href=\"#\">Арматура А5</a>
                    </li>
                    <li class=\"drop-item\">
                        <a class=\"drop-link\" href=\"#\">Арматура А6</a>
                    </li>
                    <li class=\"drop-item\">
                        <a class=\"drop-link\" href=\"#\">Арматура А7</a>
                    </li>
                    <li class=\"drop-item\">
                        <a class=\"drop-link\" href=\"#\">Арматура А8</a>
                    </li>
                </ul>
            </div>
        </div>
        </div>
    ";
    }

    // line 236
    public function block_tabs($context, array $blocks = array())
    {
        // line 237
        echo "        ";
        $context["activeTab"] = "products";
        // line 238
        echo "        ";
        $this->displayParentBlock("tabs", $context, $blocks);
        echo "
    ";
    }

    public function getTemplateName()
    {
        return "@markup/portal/suppliers/products.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  899 => 238,  896 => 237,  893 => 236,  671 => 17,  668 => 16,  661 => 841,  58 => 240,  55 => 236,  53 => 16,  40 => 5,  37 => 4,  31 => 3,);
    }
}
