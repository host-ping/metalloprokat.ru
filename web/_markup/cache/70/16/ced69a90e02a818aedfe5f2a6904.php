<?php

/* @markup/portal/suppliers/products-copy.html.twig */
class __TwigTemplate_7016ced69a90e02a818aedfe5f2a6904 extends Twig_Template
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
        <li class=\"view-product item item-small\">
            <div class=\"all-product-holder clearfix\">
                <div class=\"product-logo is-bordered  float-left\" style=\"width:70px; height:70px;\">
                    <span class=\"pattern-big\" style=\"width:70px; height:70px;\">
                                                                <img src=\"https://www.metalloprokat.ru/media/cache/products_sq136/products/de/de53cb3f15c5d65cfc221cc6c9243536053a839d.jpeg\" style=\"width:70px; height: 70px;\">
                                    </span>
                </div>
                <div class=\"holder float-left\">
                    <div class=\"container top-block clearfix\">
                        <div class=\"product-info float-left\">
                            <div class=\"title\">Сортовой и фасонный прокат  оптом</div>
                            <p class=\"price gray60-color\">
                                                            от <strong class=\"red-color\">
                                    10&nbsp;000
                                    <span class=\"icon-rouble\"></span>
                                </strong>

                                                            за
                                    <span>шт</span>
                                                    </p>

                            <p class=\"localization gray60-color\" style=\"font-size:13px; margin-top:5px;\">
                                Россия
                            </p>
                        </div>
                    </div>
                </div>
                <div class=\"company-info float-right\" style=\"text-align: left\">
                                                        <strong class=\"phone-company big-size title-callback\" style=\"color:#18a3d1; font-weight:600\">8 (800) 555-56-65</strong>
                            <p style=\"color:grey;\">Бесплатный звонок по России</p>

                    <span class=\"send-order_send-button button clickable send-button red-bg ie-radius\" style=\"margin-top:15px;\" popup-opener=\"#request-demand\" data-request-demand-url=\"/spros/form\" data-product-text=\"Сортовой и фасонный прокат \">Купить</span>
                </div>
            </div>
        </li>
         <li class=\"special-title item\"></li>
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
                        <li class=\"links_comment item medium width-181 float-left clearfix\">
                            <a href=\"#\" class=\"button comment is-bordered ie-radius\">
                                <span class=\"text\">Отзывы</span>
                                <span class=\"icon-comment float-right\"></span>
                            </a>
                        </li>
                        <li class=\"item medium width-181 float-left clearfix\">
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
                        <li class=\"links_comment item width-181 medium float-left clearfix\">
                            <a href=\"#\" class=\"button comment is-bordered ie-radius\">
                                <span class=\"text\">Отзывы</span>
                                <span class=\"icon-comment float-right\"></span>
                            </a>
                        </li>
                        <li class=\"item medium width-181 float-left clearfix\">
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
                        <li class=\"links_comment item medium width-181 float-left clearfix\">
                            <a href=\"#\" class=\"button comment is-bordered ie-radius\">
                                <span class=\"text\">Отзывы</span>
                                <span class=\"icon-comment float-right\"></span>
                            </a>
                        </li>
                        <li class=\"item medium width-181 float-left clearfix\">
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
                        <li class=\"links_comment item medium width-181 float-left clearfix\">
                            <a href=\"#\" class=\"button comment is-bordered ie-radius\">
                                <span class=\"text\">Отзывы</span>
                                <span class=\"icon-comment active float-right\"></span>
                                <span class=\"count red-color float-right\">12</span>
                            </a>
                        </li>
                        <li class=\"item medium width-181 float-left clearfix\">
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
                        <li class=\"links_comment item medium width-181 float-left clearfix\">
                            <a href=\"#\" class=\"button comment is-bordered ie-radius\">
                                <span class=\"text\">Отзывы</span>
                                <span class=\"icon-comment active float-right\"></span>
                                <span class=\"count red-color float-right\">12</span>
                            </a>
                        </li>
                        <li class=\"item medium width-181 float-left clearfix\">
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
                        <li class=\"links_comment item medium width-181 float-left clearfix\">
                            <a href=\"#\" class=\"button comment is-bordered ie-radius\">
                                <span class=\"text\">Отзывы</span>
                                <span class=\"icon-comment active float-right\"></span>
                                <span class=\"count red-color float-right\">12</span>
                            </a>
                        </li>
                        <li class=\"item medium width-181 float-left clearfix\">
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
                        <li class=\"links_comment item medium width-181 float-left clearfix\">
                            <a href=\"#\" class=\"button comment is-bordered ie-radius\">
                                <span class=\"text\">Отзывы</span>
                                <span class=\"icon-comment float-right\"></span>
                            </a>
                        </li>
                        <li class=\"item medium width-181 float-left clearfix\">
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
        <li class=\"see-more-block-wrapper mobile-show\">
            <div class=\"see-more-block\">
                <a class=\"see-more button ie-radius\" href=\"#\">показать еще...</a>
            </div>
        </li>
        <li class=\"see-more-block-wrapper mobile-hidden\">
                <style type=\"text/css\">
                    .pagination .disabled-link {
                        pointer-events: none;
                        cursor: default;
                    }

                    .center {
                        text-align: center;
                    }

                    .pagination {
                        display: inline-block;
                    }

                    .pagination a {
                        color: #919191;
                        float: left;
                        padding: 5px 12px;
                        text-decoration: none;
                        transition: background-color .3s;
                        border: 1px solid #e2e2e2;
                        margin: 0 4px;
                    }

                    .pagination span {
                        color: #919191;
                        float: left;
                        padding: 5px 12px;
                        text-decoration: none;
                        transition: background-color .3s;
                        border: 1px solid #e2e2e2;
                        margin: 0 4px;
                    }

                    .pagination .active {
                        background-color: #f1f1f1;
                        color: #262626;
                        border: 1px solid #e2e2e2;
                        pointer-events: none;
                        cursor: default;
                    }

                    .pagination a:hover:not(.active) {
                        background-color: #18a3d1;
                        color: #fff;
                    }
                </style>

                <div class=\"center\">
                    <div class=\"pagination\">
                        <a class=\"button disabled-link ie-radius\">«</a>
                        <span class=\"active button ie-radius\">1</span>
                        <a class=\" button ie-radius\" href=\"/sort/?page=2\">2</a>
                        <a class=\" button ie-radius\" href=\"/sort/?page=3\">3</a>
                        <a class=\" button ie-radius\" href=\"/sort/?page=4\">4</a>
                        <a class=\" button ie-radius\" href=\"/sort/?page=5\">5</a>
                        <a class=\" button ie-radius\" href=\"/sort/?page=6\">6</a>
                        <a class=\" button ie-radius\" href=\"/sort/?page=7\">7</a>
                        <a class=\" button ie-radius\" href=\"/sort/?page=8\">8</a>
                        <a class=\" button ie-radius\" href=\"/sort/?page=31930\">»</a>
                        <div class=\"loading-mask big g-hidden\">
                            <div class=\"spinner\"></div>
                            <div class=\"overflow\"></div>
                        </div>
                    </div>
                </div>
            </li>
    </ul>

    </div>
    <div class=\"product-links-wrapper clearfix\">
                        <div class=\"product-link float-left\">
                    <span class=\"category-title clickable float-left js-popover-opener\" data-popover=\"#subcategory-7\">
                        <span class=\"name float-left\">Арматура</span>
                        <span class=\"icon-check black float-left\"></span>
                        <span class=\"is-gradiented\"></span>
                    </span>

                    <div id=\"subcategory-7\" class=\"drop-wrapper product-links-list opacity-border\">
                        <ul class=\"dropdown\">
                            <li class=\"drop-item\">
                                <span class=\"drop-link current\">Арматура</span>
                            </li>
                                                        <li class=\"drop-item\">
                                    <a class=\"drop-link\" href=\"/sort/armatura/a1/\">Арматура А1</a>
                                </li>
                                                        <li class=\"drop-item\">
                                    <a class=\"drop-link\" href=\"/sort/armatura/a3/\">Арматура А3</a>
                                </li>
                                                        <li class=\"drop-item\">
                                    <a class=\"drop-link\" href=\"/sort/armatura/a500s/\">Арматура А500С</a>
                                </li>
                                                        <li class=\"drop-item\">
                                    <a class=\"drop-link\" href=\"/sort/armatura/10/\">Арматура 10</a>
                                </li>
                                                        <li class=\"drop-item\">
                                    <a class=\"drop-link\" href=\"/sort/armatura/12/\">Арматура 12</a>
                                </li>
                                                        <li class=\"drop-item\">
                                    <a class=\"drop-link\" href=\"/sort/armatura/5781-82/\">Арматура 5781-82</a>
                                </li>
                                                        <li class=\"drop-item\">
                                    <a class=\"drop-link\" href=\"/sort/armatura/5781-88/\">Арматура 5781-88</a>
                                </li>
                                                        <li class=\"drop-item\">
                                    <a class=\"drop-link\" href=\"/sort/armatura/14/\">Арматура 14</a>
                                </li>
                                                        <li class=\"drop-item\">
                                    <a class=\"drop-link\" href=\"/sort/armatura/25g2s/\">Арматура 25Г2С</a>
                                </li>
                                                        <li class=\"drop-item\">
                                    <a class=\"drop-link\" href=\"/sort/armatura/35gs/\">Арматура 35ГС</a>
                                </li>
                                                </ul>
                    </div>
                </div>
                        <div class=\"product-link float-left\">
                    <span class=\"category-title clickable float-left js-popover-opener\" data-popover=\"#subcategory-25\">
                        <span class=\"name float-left\">Балка двутавровая</span>
                        <span class=\"icon-check black float-left\"></span>
                        <span class=\"is-gradiented\"></span>
                    </span>

                    <div id=\"subcategory-25\" class=\"drop-wrapper product-links-list opacity-border\">
                        <ul class=\"dropdown\">
                            <li class=\"drop-item\">
                                <span class=\"drop-link current\">Балка двутавровая</span>
                            </li>
                                                        <li class=\"drop-item\">
                                    <a class=\"drop-link\" href=\"/sort/balka/3ps5/\">Балка двутавровая 3ПС5</a>
                                </li>
                                                        <li class=\"drop-item\">
                                    <a class=\"drop-link\" href=\"/sort/balka/3sp5/\">Балка двутавровая 3СП5</a>
                                </li>
                                                        <li class=\"drop-item\">
                                    <a class=\"drop-link\" href=\"/sort/balka/09g2s/\">Балка двутавровая 09Г2С</a>
                                </li>
                                                        <li class=\"drop-item\">
                                    <a class=\"drop-link\" href=\"/sort/balka/8239-89/\">Балка двутавровая 8239-89</a>
                                </li>
                                                        <li class=\"drop-item\">
                                    <a class=\"drop-link\" href=\"/sort/balka/26020-83/\">Балка двутавровая 26020-83</a>
                                </li>
                                                        <li class=\"drop-item\">
                                    <a class=\"drop-link\" href=\"/sort/balka/sto-aschm-20-93/\">Балка двутавровая СТО АСЧМ 20-93</a>
                                </li>
                                                        <li class=\"drop-item\">
                                    <a class=\"drop-link\" href=\"/sort/balka/s255/\">Балка двутавровая С255</a>
                                </li>
                                                        <li class=\"drop-item\">
                                    <a class=\"drop-link\" href=\"/sort/balka/s345/\">Балка двутавровая С345</a>
                                </li>
                                                        <li class=\"drop-item\">
                                    <a class=\"drop-link\" href=\"/sort/balka/st3/\">Балка двутавровая Ст3</a>
                                </li>
                                                        <li class=\"drop-item\">
                                    <a class=\"drop-link\" href=\"/sort/balka/st3sp/\">Балка двутавровая Ст3сп</a>
                                </li>
                                                </ul>
                    </div>
                </div>
                        <div class=\"product-link float-left\">
                    <span class=\"category-title clickable float-left js-popover-opener\" data-popover=\"#subcategory-29\">
                        <span class=\"name float-left\">Квадрат</span>
                        <span class=\"icon-check black float-left\"></span>
                        <span class=\"is-gradiented\"></span>
                    </span>

                    <div id=\"subcategory-29\" class=\"drop-wrapper product-links-list opacity-border\">
                        <ul class=\"dropdown\">
                            <li class=\"drop-item\">
                                <span class=\"drop-link current\">Квадрат</span>
                            </li>
                                                        <li class=\"drop-item\">
                                    <a class=\"drop-link\" href=\"/sort/kvadrat/gk/\">Квадрат горячекатаный</a>
                                </li>
                                                        <li class=\"drop-item\">
                                    <a class=\"drop-link\" href=\"/sort/kvadrat/stal/\">Квадрат сталь</a>
                                </li>
                                                        <li class=\"drop-item\">
                                    <a class=\"drop-link\" href=\"/sort/kvadrat/09g2s/\">Квадрат 09Г2С</a>
                                </li>
                                                        <li class=\"drop-item\">
                                    <a class=\"drop-link\" href=\"/sort/kvadrat/2591-88/\">Квадрат 2591-88</a>
                                </li>
                                                        <li class=\"drop-item\">
                                    <a class=\"drop-link\" href=\"/sort/kvadrat/20/\">Квадрат 20</a>
                                </li>
                                                        <li class=\"drop-item\">
                                    <a class=\"drop-link\" href=\"/sort/kvadrat/40x/\">Квадрат 40Х</a>
                                </li>
                                                        <li class=\"drop-item\">
                                    <a class=\"drop-link\" href=\"/sort/kvadrat/200/\">Квадрат 200</a>
                                </li>
                                                        <li class=\"drop-item\">
                                    <a class=\"drop-link\" href=\"/sort/kvadrat/st10/\">Квадрат Ст10</a>
                                </li>
                                                        <li class=\"drop-item\">
                                    <a class=\"drop-link\" href=\"/sort/kvadrat/st20/\">Квадрат Ст20</a>
                                </li>
                                                        <li class=\"drop-item\">
                                    <a class=\"drop-link\" href=\"/sort/kvadrat/st45/\">Квадрат Ст45</a>
                                </li>
                                                </ul>
                    </div>
                </div>
                        <div class=\"product-link float-left\">
                    <span class=\"category-title clickable float-left js-popover-opener\" data-popover=\"#subcategory-30\">
                        <span class=\"name float-left\">Круг</span>
                        <span class=\"icon-check black float-left\"></span>
                        <span class=\"is-gradiented\"></span>
                    </span>

                    <div id=\"subcategory-30\" class=\"drop-wrapper product-links-list opacity-border\">
                        <ul class=\"dropdown\">
                            <li class=\"drop-item\">
                                <span class=\"drop-link current\">Круг</span>
                            </li>
                                                        <li class=\"drop-item\">
                                    <a class=\"drop-link\" href=\"/sort/krug/stal/\">Круг сталь</a>
                                </li>
                                                        <li class=\"drop-item\">
                                    <a class=\"drop-link\" href=\"/sort/krug/09g2s/\">Круг 09Г2С</a>
                                </li>
                                                        <li class=\"drop-item\">
                                    <a class=\"drop-link\" href=\"/sort/krug/2590-88/\">Круг 2590-88</a>
                                </li>
                                                        <li class=\"drop-item\">
                                    <a class=\"drop-link\" href=\"/sort/krug/30/\">Круг 30</a>
                                </li>
                                                        <li class=\"drop-item\">
                                    <a class=\"drop-link\" href=\"/sort/krug/20x/\">Круг 20Х</a>
                                </li>
                                                        <li class=\"drop-item\">
                                    <a class=\"drop-link\" href=\"/sort/krug/30xgsa/\">Круг 30ХГСА</a>
                                </li>
                                                        <li class=\"drop-item\">
                                    <a class=\"drop-link\" href=\"/sort/krug/40x/\">Круг 40Х</a>
                                </li>
                                                        <li class=\"drop-item\">
                                    <a class=\"drop-link\" href=\"/sort/krug/40xn/\">Круг 40ХН</a>
                                </li>
                                                        <li class=\"drop-item\">
                                    <a class=\"drop-link\" href=\"/sort/krug/40xn2ma/\">Круг 40ХН2МА</a>
                                </li>
                                                        <li class=\"drop-item\">
                                    <a class=\"drop-link\" href=\"/sort/krug/st45/\">Круг Ст45</a>
                                </li>
                                                </ul>
                    </div>
                </div>
                        <div class=\"product-link float-left\">
                    <span class=\"category-title clickable float-left js-popover-opener\" data-popover=\"#subcategory-1030\">
                        <span class=\"name float-left\">Круг жаропрочный</span>
                        <span class=\"icon-check black float-left\"></span>
                        <span class=\"is-gradiented\"></span>
                    </span>

                    <div id=\"subcategory-1030\" class=\"drop-wrapper product-links-list opacity-border\">
                        <ul class=\"dropdown\">
                            <li class=\"drop-item\">
                                <span class=\"drop-link current\">Круг жаропрочный</span>
                            </li>
                                                        <li class=\"drop-item\">
                                    <a class=\"drop-link\" href=\"/sort/krug/krug-zharoproch/12x1mf/\">Круг жаропрочный 12Х1МФ</a>
                                </li>
                                                        <li class=\"drop-item\">
                                    <a class=\"drop-link\" href=\"/sort/krug/krug-zharoproch/20/\">Круг жаропрочный 20</a>
                                </li>
                                                        <li class=\"drop-item\">
                                    <a class=\"drop-link\" href=\"/sort/krug/krug-zharoproch/30/\">Круг жаропрочный 30</a>
                                </li>
                                                        <li class=\"drop-item\">
                                    <a class=\"drop-link\" href=\"/sort/krug/krug-zharoproch/25h1mf/\">Круг жаропрочный 25Х1МФ</a>
                                </li>
                                                        <li class=\"drop-item\">
                                    <a class=\"drop-link\" href=\"/sort/krug/krug-zharoproch/25h2m1f/\">Круг жаропрочный 25Х2М1Ф</a>
                                </li>
                                                        <li class=\"drop-item\">
                                    <a class=\"drop-link\" href=\"/sort/krug/krug-zharoproch/30xma/\">Круг жаропрочный 30ХМА</a>
                                </li>
                                                        <li class=\"drop-item\">
                                    <a class=\"drop-link\" href=\"/sort/krug/krug-zharoproch/38x2myua/\">Круг жаропрочный 38Х2МЮА</a>
                                </li>
                                                        <li class=\"drop-item\">
                                    <a class=\"drop-link\" href=\"/sort/krug/krug-zharoproch/xn60vt/\">Круг жаропрочный ХН60ВТ</a>
                                </li>
                                                        <li class=\"drop-item\">
                                    <a class=\"drop-link\" href=\"/sort/krug/krug-zharoproch/hn77tyur/\">Круг жаропрочный ХН77ТЮР</a>
                                </li>
                                                        <li class=\"drop-item\">
                                    <a class=\"drop-link\" href=\"/sort/krug/krug-zharoproch/xn78t/\">Круг жаропрочный ХН78Т</a>
                                </li>
                                                </ul>
                    </div>
                </div>
                        <div class=\"product-link float-left\">
                    <span class=\"category-title clickable float-left js-popover-opener\" data-popover=\"#subcategory-100\">
                        <span class=\"name float-left\">Поковки</span>
                        <span class=\"icon-check black float-left\"></span>
                        <span class=\"is-gradiented\"></span>
                    </span>

                    <div id=\"subcategory-100\" class=\"drop-wrapper product-links-list opacity-border\">
                        <ul class=\"dropdown\">
                            <li class=\"drop-item\">
                                <span class=\"drop-link current\">Поковки</span>
                            </li>
                                                        <li class=\"drop-item\">
                                    <a class=\"drop-link\" href=\"/sort/pokovki/stal/\">Поковки сталь</a>
                                </li>
                                                        <li class=\"drop-item\">
                                    <a class=\"drop-link\" href=\"/sort/pokovki/5xnm/\">Поковки 5ХНМ</a>
                                </li>
                                                        <li class=\"drop-item\">
                                    <a class=\"drop-link\" href=\"/sort/pokovki/kruglie/\">Поковки круглые</a>
                                </li>
                                                        <li class=\"drop-item\">
                                    <a class=\"drop-link\" href=\"/sort/pokovki/09g2s/\">Поковки 09Г2С</a>
                                </li>
                                                        <li class=\"drop-item\">
                                    <a class=\"drop-link\" href=\"/sort/pokovki/rect/\">Поковки прямоугольная</a>
                                </li>
                                                        <li class=\"drop-item\">
                                    <a class=\"drop-link\" href=\"/sort/pokovki/40x/\">Поковки 40Х</a>
                                </li>
                                                        <li class=\"drop-item\">
                                    <a class=\"drop-link\" href=\"/sort/pokovki/st20/\">Поковки Ст20</a>
                                </li>
                                                        <li class=\"drop-item\">
                                    <a class=\"drop-link\" href=\"/sort/pokovki/st35/\">Поковки Ст35</a>
                                </li>
                                                        <li class=\"drop-item\">
                                    <a class=\"drop-link\" href=\"/sort/pokovki/st40/\">Поковки Ст40</a>
                                </li>
                                                        <li class=\"drop-item\">
                                    <a class=\"drop-link\" href=\"/sort/pokovki/st45/\">Поковки Ст45</a>
                                </li>
                                                </ul>
                    </div>
                </div>
                        <div class=\"product-link float-left\">
                    <span class=\"category-title clickable float-left js-popover-opener\" data-popover=\"#subcategory-32\">
                        <span class=\"name float-left\">Полоса стальная</span>
                        <span class=\"icon-check black float-left\"></span>
                        <span class=\"is-gradiented\"></span>
                    </span>

                    <div id=\"subcategory-32\" class=\"drop-wrapper product-links-list opacity-border\">
                        <ul class=\"dropdown\">
                            <li class=\"drop-item\">
                                <span class=\"drop-link current\">Полоса стальная</span>
                            </li>
                                                        <li class=\"drop-item\">
                                    <a class=\"drop-link\" href=\"/sort/polosa/goryachekatanaya/\">Полоса стальная горячекатаная</a>
                                </li>
                                                        <li class=\"drop-item\">
                                    <a class=\"drop-link\" href=\"/sort/polosa/103-76/\">Полоса стальная 103-76</a>
                                </li>
                                                        <li class=\"drop-item\">
                                    <a class=\"drop-link\" href=\"/sort/polosa/09g2s/\">Полоса стальная 09Г2С</a>
                                </li>
                                                        <li class=\"drop-item\">
                                    <a class=\"drop-link\" href=\"/sort/polosa/4405-75/\">Полоса стальная 4405-75</a>
                                </li>
                                                        <li class=\"drop-item\">
                                    <a class=\"drop-link\" href=\"/sort/polosa/12/\">Полоса стальная 12</a>
                                </li>
                                                        <li class=\"drop-item\">
                                    <a class=\"drop-link\" href=\"/sort/polosa/16/\">Полоса стальная 16</a>
                                </li>
                                                        <li class=\"drop-item\">
                                    <a class=\"drop-link\" href=\"/sort/polosa/20/\">Полоса стальная 20</a>
                                </li>
                                                        <li class=\"drop-item\">
                                    <a class=\"drop-link\" href=\"/sort/polosa/40/\">Полоса стальная 40</a>
                                </li>
                                                        <li class=\"drop-item\">
                                    <a class=\"drop-link\" href=\"/sort/polosa/st3sp/\">Полоса стальная Ст3сп</a>
                                </li>
                                                        <li class=\"drop-item\">
                                    <a class=\"drop-link\" href=\"/sort/polosa/st45/\">Полоса стальная Ст45</a>
                                </li>
                                                </ul>
                    </div>
                </div>
                        <div class=\"product-link float-left\">
                    <span class=\"category-title clickable float-left js-popover-opener\" data-popover=\"#subcategory-39\">
                        <span class=\"name float-left\">Уголок металлический</span>
                        <span class=\"icon-check black float-left\"></span>
                        <span class=\"is-gradiented\"></span>
                    </span>

                    <div id=\"subcategory-39\" class=\"drop-wrapper product-links-list opacity-border\">
                        <ul class=\"dropdown\">
                            <li class=\"drop-item\">
                                <span class=\"drop-link current\">Уголок металлический</span>
                            </li>
                                                        <li class=\"drop-item\">
                                    <a class=\"drop-link\" href=\"/sort/ugolok/3sp5/\">Уголок металлический 3СП5</a>
                                </li>
                                                        <li class=\"drop-item\">
                                    <a class=\"drop-link\" href=\"/sort/ugolok/stal/\">Уголок металлический сталь</a>
                                </li>
                                                        <li class=\"drop-item\">
                                    <a class=\"drop-link\" href=\"/sort/ugolok/09g2s/\">Уголок металлический 09Г2С</a>
                                </li>
                                                        <li class=\"drop-item\">
                                    <a class=\"drop-link\" href=\"/sort/ugolok/8509-86/\">Уголок металлический 8509-86</a>
                                </li>
                                                        <li class=\"drop-item\">
                                    <a class=\"drop-link\" href=\"/sort/ugolok/8509-93/\">Уголок металлический 8509-93</a>
                                </li>
                                                        <li class=\"drop-item\">
                                    <a class=\"drop-link\" href=\"/sort/ugolok/8510-86/\">Уголок металлический 8510-86</a>
                                </li>
                                                        <li class=\"drop-item\">
                                    <a class=\"drop-link\" href=\"/sort/ugolok/75/\">Уголок металлический 75</a>
                                </li>
                                                        <li class=\"drop-item\">
                                    <a class=\"drop-link\" href=\"/sort/ugolok/100/\">Уголок металлический 100</a>
                                </li>
                                                        <li class=\"drop-item\">
                                    <a class=\"drop-link\" href=\"/sort/ugolok/st3/\">Уголок металлический Ст3</a>
                                </li>
                                                        <li class=\"drop-item\">
                                    <a class=\"drop-link\" href=\"/sort/ugolok/st3sp/\">Уголок металлический Ст3сп</a>
                                </li>
                                                </ul>
                    </div>
                </div>
                        <div class=\"product-link float-left\">
                    <span class=\"category-title clickable float-left js-popover-opener\" data-popover=\"#subcategory-38\">
                        <span class=\"name float-left\">Швеллер</span>
                        <span class=\"icon-check black float-left\"></span>
                        <span class=\"is-gradiented\"></span>
                    </span>

                    <div id=\"subcategory-38\" class=\"drop-wrapper product-links-list opacity-border\">
                        <ul class=\"dropdown\">
                            <li class=\"drop-item\">
                                <span class=\"drop-link current\">Швеллер</span>
                            </li>
                                                        <li class=\"drop-item\">
                                    <a class=\"drop-link\" href=\"/sort/shveller/gk/\">Швеллер горячекатаный</a>
                                </li>
                                                        <li class=\"drop-item\">
                                    <a class=\"drop-link\" href=\"/sort/shveller/stal/\">Швеллер сталь</a>
                                </li>
                                                        <li class=\"drop-item\">
                                    <a class=\"drop-link\" href=\"/sort/shveller/gnutij/\">Швеллер гнутый</a>
                                </li>
                                                        <li class=\"drop-item\">
                                    <a class=\"drop-link\" href=\"/sort/shveller/09g2s/\">Швеллер 09Г2С</a>
                                </li>
                                                        <li class=\"drop-item\">
                                    <a class=\"drop-link\" href=\"/sort/shveller/10xsnd/\">Швеллер 10ХСНД</a>
                                </li>
                                                        <li class=\"drop-item\">
                                    <a class=\"drop-link\" href=\"/sort/shveller/8240-97/\">Швеллер 8240-97</a>
                                </li>
                                                        <li class=\"drop-item\">
                                    <a class=\"drop-link\" href=\"/sort/shveller/8240-98/\">Швеллер 8240-98</a>
                                </li>
                                                        <li class=\"drop-item\">
                                    <a class=\"drop-link\" href=\"/sort/shveller/8278-83/\">Швеллер 8278-83</a>
                                </li>
                                                        <li class=\"drop-item\">
                                    <a class=\"drop-link\" href=\"/sort/shveller/st3/\">Швеллер Ст3</a>
                                </li>
                                                        <li class=\"drop-item\">
                                    <a class=\"drop-link\" href=\"/sort/shveller/st3sp/\">Швеллер Ст3сп</a>
                                </li>
                                                </ul>
                    </div>
                </div>
                        <div class=\"product-link float-left\">
                    <span class=\"category-title clickable float-left js-popover-opener\" data-popover=\"#subcategory-40\">
                        <span class=\"name float-left\">Шестигранник</span>
                        <span class=\"icon-check black float-left\"></span>
                        <span class=\"is-gradiented\"></span>
                    </span>

                    <div id=\"subcategory-40\" class=\"drop-wrapper product-links-list opacity-border\">
                        <ul class=\"dropdown\">
                            <li class=\"drop-item\">
                                <span class=\"drop-link current\">Шестигранник</span>
                            </li>
                                                        <li class=\"drop-item\">
                                    <a class=\"drop-link\" href=\"/sort/shestigrannik/gk/\">Шестигранник горячекатаный</a>
                                </li>
                                                        <li class=\"drop-item\">
                                    <a class=\"drop-link\" href=\"/sort/shestigrannik/stal/\">Шестигранник сталь</a>
                                </li>
                                                        <li class=\"drop-item\">
                                    <a class=\"drop-link\" href=\"/sort/shestigrannik/kalibrovannyi/\">Шестигранник калиброванный</a>
                                </li>
                                                        <li class=\"drop-item\">
                                    <a class=\"drop-link\" href=\"/sort/shestigrannik/2879-88/\">Шестигранник 2879-88</a>
                                </li>
                                                        <li class=\"drop-item\">
                                    <a class=\"drop-link\" href=\"/sort/shestigrannik/8560-78/\">Шестигранник 8560-78</a>
                                </li>
                                                        <li class=\"drop-item\">
                                    <a class=\"drop-link\" href=\"/sort/shestigrannik/30xgsa/\">Шестигранник 30ХГСА</a>
                                </li>
                                                        <li class=\"drop-item\">
                                    <a class=\"drop-link\" href=\"/sort/shestigrannik/40x/\">Шестигранник 40Х</a>
                                </li>
                                                        <li class=\"drop-item\">
                                    <a class=\"drop-link\" href=\"/sort/shestigrannik/st20/\">Шестигранник Ст20</a>
                                </li>
                                                        <li class=\"drop-item\">
                                    <a class=\"drop-link\" href=\"/sort/shestigrannik/st35/\">Шестигранник Ст35</a>
                                </li>
                                                        <li class=\"drop-item\">
                                    <a class=\"drop-link\" href=\"/sort/shestigrannik/st45/\">Шестигранник Ст45</a>
                                </li>
                                                </ul>
                    </div>
                </div>
                </div>
        ";
        // line 1397
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
        return "@markup/portal/suppliers/products-copy.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  1455 => 238,  1452 => 237,  1449 => 236,  1227 => 17,  1224 => 16,  1217 => 1397,  58 => 240,  55 => 236,  53 => 16,  40 => 5,  37 => 4,  31 => 3,);
    }
}
