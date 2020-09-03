<?php

/* @markup/portal/suppliers/products-gallery.html.twig */
class __TwigTemplate_d2d86545d02f68b32bdf44856cd79d8f extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = $this->env->loadTemplate("markup/html/portal/suppliers/companies.html.twig");

        $this->blocks = array(
            'title' => array($this, 'block_title'),
            'banner' => array($this, 'block_banner'),
            'content' => array($this, 'block_content'),
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
        echo "Companies list (gallery)";
    }

    // line 4
    public function block_banner($context, array $blocks = array())
    {
        echo "";
    }

    // line 5
    public function block_content($context, array $blocks = array())
    {
        // line 6
        echo "    <div id=\"content\" class=\"content-right outline-right float-right\">
    <div class=\"main-title \">
        <h1>Компании, торгующие арматурой
            <span class=\"accepted-filter ie-radius\">A500C <span class=\"icon-filter-del clickable\"></span></span>
            <span class=\"accepted-filter ie-radius\">строительная <span class=\"icon-filter-del clickable\"></span></span>
            <a
                    href=\"#\" class=\"region-link js-popover-opener\" data-popover=\"#cities\"
                    data-different-position=\"true\"> <br/>в Москве и Области</a>
        </h1>
    </div>
    ";
        // line 16
        $this->displayBlock('tabs', $context, $blocks);
        // line 20
        echo "    <div id=\"product\" class=\"view-category products\">
    <ul class=\"product grid clearfix\">
    <li class=\"view-product grid_item float-left\">
        <div class=\"product-logo\">
            <a class=\"img-link pattern-big\" href=\"#\">
                <img src=\"./markup/pic/product-big.jpg\" alt=\"image description\"/>
            </a>
            <div class=\"promo-label label top\">
                <a class=\"label-link\" href=\"#\">Промо</a>
            </div>

            <div class=\"product-label label bottom\">
                <a class=\"label-link\" href=\"#\">еще 285 товаров</a>
            </div>
        </div>
        <div class=\"product-info\">
            <div class=\"title\">
                <a href=\"#\" class=\"is-gradiented\">Арматура строительная</a>
            </div>
            <p class=\"size gray60-color\">Размер 30x30</p>

            <p class=\"price gray60-color\"><strong class=\"price red-color other-currency js-helper-opener\" data-text=\"примерно <span class='red-color'>100 <span class='icon-rouble'></span></span>\">25 000 <span class=\"icon-rouble\"></span> </strong>за тонну</p>
        </div>
        <ul class=\"links clearfix\">
            <li class=\"links_send item large float-left\">
                <a class=\"send-order_send-button button send-button red-bg ie-radius\" href=\"#\">Отправить заявку</a>
            </li>
        </ul>
        <div class=\"hover-block opacity-border\">
            <div class=\"dropdown\">
                <div class=\"top\">
                    <div class=\"star-panel clearfix\">
                        <div class=\"status green-bg float-left ie-radius\">online</div>
                        <div class=\"rating float-left\">
                            <span class=\"star-mini icon-star-colored\"></span>
                            <span class=\"star-mini icon-star-colored\"></span>
                            <span class=\"star-mini icon-star-colored\"></span>
                        </div>
                    </div>
                    <div class=\"title\">
                        <a href=\"#\" class=\"is-gradiented\">Стальторг</a>
                    </div>
                    <p class=\"delivery gray60-color gray60-color\">доставка из Одинцово</p>
                    <a class=\"site\" href=\"#\">staltorg.ru</a>

                    <div class=\"contacts\">
                        <span class=\"phone-text\">+7 (495) 784</span>
                        ( <a class=\"see\" href=\"#\">показать</a> )
                        <a class=\"callback\" href=\"#\">обратный звонок</a>
                    </div>
                    <div class=\"product-label label bottom\">
                        <a class=\"label-link\" href=\"#\">еще 285 товаров</a>
                    </div>
                </div>
                <div class=\"product-info\">
                    <div class=\"title\">
                        <a href=\"#\" class=\"is-gradiented\">Арматура строительная</a>
                    </div>
                    <p class=\"size gray60-color\">Размер 30x30</p>

                    <p class=\"price gray60-color\"><strong class=\"price red-color other-currency js-helper-opener\" data-text=\"примерно <span class='red-color'>10000 <span class='icon-rouble'></span></span>\">25 000 <span class=\"icon-rouble\"></span> </strong>за тонну</p>
                </div>
                <ul class=\"links clearfix\">
                    <li class=\"links_send item large\">
                        <a class=\"send-order_send-button button send-button red-bg ie-radius\" href=\"#\">Отправить
                            заявку</a>
                    </li>
                    <li class=\"item large clearfix\">
                        <a href=\"#\" class=\"button favorites active-link is-bordered js-togglable-block ie-radius\">
                            <span class=\"text\">В Избранном</span>
                            <span class=\"icon-favorite-active float-right\"></span>
                        </a>
                        <a href=\"#\" class=\"button favorites delete blue-bg g-hidden js-togglable-block ie-radius\">
                            <span class=\"text\">Удалить</span>
                            <span class=\"icon-favorite-del float-right\"></span>
                        </a>
                    </li>
                    <li class=\"links_comment item large clearfix\">
                        <a href=\"#\" class=\"button comment is-bordered ie-radius\">
                            <span class=\"text\">Отзывы</span>
                            <span class=\"icon-comment active float-right\"></span>
                            <span class=\"count red-color float-right\">12</span>
                        </a>
                    </li>
                    <li class=\"links_report item clearfix float-right\">
                        <a href=\"#\" class=\"button report is-bordered js-tooltip-opener js-popup-opener ie-radius\"
                           data-tooltip-title=\"Пожаловаться\" data-popup=\"#report\">
                            <span class=\"icon-complaint\"></span>
                        </a>
                    </li>
                    <li class=\"updated-date float-left\">
                        <span class=\"text\">Обновлено 31 мар 2013</span>
                    </li>
                </ul>
            </div>
        </div>
    </li>
    <li class=\"view-product grid_item float-left\">
        <div class=\"product-logo\">
            <a class=\"img-link pattern-big\" href=\"#\"></a>

            <div class=\"product-label label bottom\">
                <a class=\"label-link\" href=\"#\">еще 285 товаров</a>
            </div>
        </div>
        <div class=\"product-info\">
            <div class=\"title\">
                <a href=\"#\" class=\"is-gradiented\">Шестигранник калиброванный ГОСТ ТУ</a>
            </div>
            <p class=\"size gray60-color\">Размер 30x30</p>

            <p class=\"price gray60-color\"><strong class=\"count red-color\">25 000 <span class=\"icon-rouble\"></span> </strong>за тонну</p>
        </div>
        <ul class=\"links clearfix\">
            <li class=\"links_send item large float-left\">
                <a class=\"send-order_send-button button send-button gray60-bg ie-radius\" href=\"#\">Отправить заявку</a>
            </li>
        </ul>
        <div class=\"hover-block opacity-border\">
            <div class=\"dropdown\">
                <div class=\"top\">
                    <div class=\"star-panel clearfix\">
                        <div class=\"rating float-left\">
                            <span class=\"star-mini icon-star hovered\"></span>
                            <span class=\"star-mini icon-star hovered\"></span>
                        </div>
                    </div>
                    <div class=\"title\">
                        <a href=\"#\" class=\"is-gradiented\">Стальторг</a>
                    </div>
                    <p class=\"delivery gray60-color\">доставка из Одинцово</p>
                    <a class=\"site\" href=\"#\">staltorg.ru</a>

                    <div class=\"contacts\">
                        <span class=\"phone-text\">+7 (495) 784</span>
                        ( <a class=\"see\" href=\"#\">показать</a> )
                        <a class=\"callback\" href=\"#\">обратный звонок</a>
                    </div>
                    <div class=\"product-label label bottom\">
                        <a class=\"label-link\" href=\"#\">еще 285 товаров</a>
                    </div>
                </div>
                <div class=\"product-info\">
                    <div class=\"title\">
                        <a href=\"#\" class=\"is-gradiented\">Шестигранник калиброванный ГОСТ ТУ</a>
                    </div>
                    <p class=\"size gray60-color\">Размер 30x30</p>

                    <p class=\"price gray60-color\"><strong class=\"count red-color\">25 000 <span class=\"icon-rouble\"></span> </strong>за тонну</p>
                </div>
                <ul class=\"links clearfix\">
                    <li class=\"links_send item large\">
                        <a class=\"send-order_send-button button send-button gray60-bg ie-radius\" href=\"#\">Отправить
                            заявку</a>
                    </li>
                    <li class=\"item large clearfix\">
                        <a href=\"#\" class=\"button favorites is-bordered ie-radius\">
                            <span class=\"text\">В Избранное</span>
                            <span class=\"icon-favorite float-right\"></span>
                        </a>
                    </li>
                    <li class=\"links_comment item large clearfix\">
                        <a href=\"#\" class=\"button comment is-bordered ie-radius\">
                            <span class=\"text\">Отзывы</span>
                            <span class=\"icon-comment float-right\"></span>
                        </a>
                    </li>
                    <li class=\"links_report item clearfix float-right\">
                        <a href=\"#\" class=\"button report is-bordered js-tooltip-opener js-popup-opener ie-radius\"
                           data-tooltip-title=\"Пожаловаться\" data-popup=\"#report\">
                            <span class=\"icon-complaint\"></span>
                        </a>
                    </li>
                    <li class=\"updated-date float-left\">
                        <span class=\"text\">Обновлено 31 мар 2013</span>
                    </li>
                </ul>
            </div>
        </div>
    </li>
    <li class=\"view-product grid_item float-left\">
        <div class=\"product-logo\">
            <a class=\"img-link pattern-big\" href=\"#\">
                <img src=\"./markup/pic/product-big.jpg\" alt=\"image description\"/>
            </a>
        </div>
        <div class=\"product-info\">
            <div class=\"title\">
                <a href=\"#\" class=\"is-gradiented\">Арматура строительная</a>
            </div>
            <p class=\"size gray60-color\">Размер 30x30</p>

            <p class=\"price gray60-color\"><strong class=\"count red-color\">25 000 <span class=\"icon-rouble\"></span> </strong>за тонну</p>
        </div>
        <ul class=\"links clearfix\">
            <li class=\"links_send item large float-left\">
                <a class=\"send-order_send-button button send-button red-bg ie-radius\" href=\"#\">Отправить заявку</a>
            </li>
        </ul>
        <div class=\"hover-block opacity-border\">
            <div class=\"dropdown\">
                <div class=\"top\">
                    <div class=\"star-panel clearfix\">
                        <div class=\"status green-bg float-left ie-radius\">online</div>
                        <div class=\"rating float-left\">
                            <span class=\"star-mini icon-star-colored\"></span>
                            <span class=\"star-mini icon-star-colored\"></span>
                            <span class=\"star-mini icon-star-colored\"></span>
                        </div>
                    </div>
                    <div class=\"title\">
                        <a href=\"#\" class=\"is-gradiented\">Стальторг</a>
                    </div>
                    <p class=\"delivery gray60-color\">доставка из Одинцово</p>
                    <a class=\"site\" href=\"#\">staltorg.ru</a>

                    <div class=\"contacts\">
                        <span class=\"phone-text\">+7 (495) 784</span>
                        ( <a class=\"see\" href=\"#\">показать</a> )
                        <a class=\"callback\" href=\"#\">обратный звонок</a>
                    </div>
                </div>
                <div class=\"product-info\">
                    <div class=\"title\">
                        <a href=\"#\" class=\"is-gradiented\">Арматура строительная</a>
                    </div>
                    <p class=\"size gray60-color\">Размер 30x30</p>

                    <p class=\"price gray60-color\"><strong class=\"count red-color\">25 000 <span class=\"icon-rouble\"></span> </strong>за тонну</p>
                </div>
                <ul class=\"links clearfix\">
                    <li class=\"links_send item large\">
                        <a class=\"send-order_send-button button send-button red-bg ie-radius\" href=\"#\">Отправить
                            заявку</a>
                    </li>
                    <li class=\"item large clearfix\">
                        <a href=\"#\" class=\"button favorites is-bordered\">
                            <span class=\"text\">В Избранное</span>
                            <span class=\"icon-favorite float-right\"></span>
                        </a>
                    </li>
                    <li class=\"links_comment item large clearfix\">
                        <a href=\"#\" class=\"button comment is-bordered ie-radius\">
                            <span class=\"text\">Отзывы</span>
                            <span class=\"icon-comment float-right\"></span>
                        </a>
                    </li>
                    <li class=\"links_report item clearfix float-right\">
                        <a href=\"#\" class=\"button report is-bordered js-tooltip-opener js-popup-opener ie-radius\"
                           data-tooltip-title=\"Пожаловаться\" data-popup=\"#report\">
                            <span class=\"icon-complaint\"></span>
                        </a>
                    </li>
                    <li class=\"updated-date float-left\">
                        <span class=\"text\">Обновлено 31 мар 2013</span>
                    </li>
                </ul>
            </div>
        </div>
    </li>
    <li class=\"announcement has-announcement float-left\">
        <img src=\"./markup/pic/inside-banner.jpg\" width=\"735\" height=\"90\" alt=\"image description\"/>
    </li>
    <li class=\"view-product grid_item float-left\">
        <div class=\"product-logo\">
            <a class=\"img-link pattern-big\" href=\"#\"></a>
        </div>
        <div class=\"product-info\">
            <div class=\"title\">
                <a href=\"#\" class=\"is-gradiented\">Арматура строительная</a>
            </div>
            <p class=\"size gray60-color\">Размер 30x30</p>

            <p class=\"price gray60-color\"><strong class=\"count red-color\">25 000 <span class=\"icon-rouble\"></span> </strong>за тонну</p>
        </div>
        <ul class=\"links clearfix\">
            <li class=\"links_send item large float-left\">
                <a class=\"send-order_send-button button send-button red-bg ie-radius\" href=\"#\">Отправить заявку</a>
            </li>
        </ul>
        <div class=\"hover-block opacity-border\">
            <div class=\"dropdown\">
                <div class=\"top\">
                    <div class=\"star-panel clearfix\">
                        <div class=\"status green-bg float-left ie-radius\">online</div>
                        <div class=\"rating float-left\">
                            <span class=\"star-mini icon-star-colored\"></span>
                            <span class=\"star-mini icon-star-colored\"></span>
                            <span class=\"star-mini icon-star-colored\"></span>
                        </div>
                    </div>
                    <div class=\"title\">
                        <a href=\"#\" class=\"is-gradiented\">Стальторг</a>
                    </div>
                    <p class=\"delivery gray60-color\">доставка из Одинцово</p>
                    <a class=\"site\" href=\"#\">staltorg.ru</a>

                    <div class=\"contacts\">
                        <span class=\"phone-text\">+7 (495) 784</span>
                        ( <a class=\"see\" href=\"#\">показать</a> )
                        <a class=\"callback\" href=\"#\">обратный звонок</a>
                    </div>
                </div>
                <div class=\"product-info\">
                    <div class=\"title\">
                        <a href=\"#\" class=\"is-gradiented\">Арматура строительная</a>
                    </div>
                    <p class=\"size gray60-color\">Размер 30x30</p>

                    <p class=\"price gray60-color\"><strong class=\"count red-color\">25 000 <span class=\"icon-rouble\"></span> </strong>за тонну</p>
                </div>
                <ul class=\"links clearfix\">
                    <li class=\"links_send item large\">
                        <a class=\"send-order_send-button button send-button red-bg ie-radius\" href=\"#\">Отправить
                            заявку</a>
                    </li>
                    <li class=\"item large clearfix\">
                        <a href=\"#\" class=\"button favorites is-bordered\">
                            <span class=\"text\">В Избранное</span>
                            <span class=\"icon-favorite float-right\"></span>
                        </a>
                    </li>
                    <li class=\"links_comment item large clearfix\">
                        <a href=\"#\" class=\"button comment is-bordered ie-radius\">
                            <span class=\"text\">Отзывы</span>
                            <span class=\"icon-comment float-right\"></span>
                        </a>
                    </li>
                    <li class=\"links_report item clearfix float-right\">
                        <a href=\"#\" class=\"button report is-bordered js-tooltip-opener js-popup-opener ie-radius\"
                           data-tooltip-title=\"Пожаловаться\" data-popup=\"#report\">
                            <span class=\"icon-complaint\"></span>
                        </a>
                    </li>
                    <li class=\"updated-date float-left\">
                        <span class=\"text\">Обновлено 31 мар 2013</span>
                    </li>
                </ul>
            </div>
        </div>
    </li>
    <li class=\"view-product grid_item float-left\">
        <div class=\"product-logo\">
            <a class=\"img-link pattern-big\" href=\"#\"></a>

            <div class=\"product-label label bottom\">
                <a class=\"label-link\" href=\"#\">еще 285 товаров</a>
            </div>
        </div>
        <div class=\"product-info\">
            <div class=\"title\">
                <a href=\"#\" class=\"is-gradiented\">Арматура строительная</a>
            </div>
            <p class=\"size gray60-color\">Размер 30x30</p>

            <p class=\"price gray60-color\"><strong class=\"count red-color\">25 000 <span class=\"icon-rouble\"></span> </strong>за тонну</p>
        </div>
        <ul class=\"links clearfix\">
            <li class=\"links_send item large float-left\">
                <a class=\"send-order_send-button button send-button red-bg ie-radius\" href=\"#\">Отправить заявку</a>
            </li>
        </ul>
        <div class=\"hover-block opacity-border\">
            <div class=\"dropdown\">
                <div class=\"top\">
                    <div class=\"star-panel clearfix\">
                        <div class=\"status green-bg float-left ie-radius\">online</div>
                        <div class=\"rating float-left\">
                            <span class=\"star-mini icon-star-colored\"></span>
                            <span class=\"star-mini icon-star-colored\"></span>
                            <span class=\"star-mini icon-star-colored\"></span>
                        </div>
                    </div>
                    <div class=\"title\">
                        <a href=\"#\" class=\"is-gradiented\">Стальторг</a>
                    </div>
                    <p class=\"delivery gray60-color\">доставка из Одинцово</p>
                    <a class=\"site\" href=\"#\">staltorg.ru</a>

                    <div class=\"contacts\">
                        <span class=\"phone-text\">+7 (495) 784</span>
                        ( <a class=\"see\" href=\"#\">показать</a> )
                        <a class=\"callback\" href=\"#\">обратный звонок</a>
                    </div>
                    <div class=\"product-label label bottom\">
                        <a class=\"label-link\" href=\"#\">еще 285 товаров</a>
                    </div>
                </div>
                <div class=\"product-info\">
                    <div class=\"title\">
                        <a href=\"#\" class=\"is-gradiented\">Арматура строительная</a>
                    </div>
                    <p class=\"size gray60-color\">Размер 30x30</p>

                    <p class=\"price gray60-color\"><strong class=\"count red-color\">25 000 <span class=\"icon-rouble\"></span> </strong>за тонну</p>
                </div>
                <ul class=\"links clearfix\">
                    <li class=\"links_send item large\">
                        <a class=\"send-order_send-button button send-button red-bg ie-radius\" href=\"#\">Отправить
                            заявку</a>
                    </li>
                    <li class=\"item large clearfix\">
                        <a href=\"#\" class=\"button favorites is-bordered ie-radius\">
                            <span class=\"text\">В Избранное</span>
                            <span class=\"icon-favorite float-right\"></span>
                        </a>
                    </li>
                    <li class=\"links_comment item large clearfix\">
                        <a href=\"#\" class=\"button comment is-bordered ie-radius\">
                            <span class=\"text\">Отзывы</span>
                            <span class=\"icon-comment float-right\"></span>
                        </a>
                    </li>
                    <li class=\"links_report item clearfix float-right\">
                        <a href=\"#\" class=\"button report is-bordered js-tooltip-opener js-popup-opener ie-radius\"
                           data-tooltip-title=\"Пожаловаться\" data-popup=\"#report\">
                            <span class=\"icon-complaint\"></span>
                        </a>
                    </li>
                    <li class=\"updated-date float-left\">
                        <span class=\"text\">Обновлено 31 мар 2013</span>
                    </li>
                </ul>
            </div>
        </div>
    </li>
    <li class=\"view-product grid_item float-left\">
        <div class=\"product-logo\">
            <a class=\"img-link pattern-big\" href=\"#\">
                <img src=\"./markup/pic/product-big.jpg\" alt=\"image description\"/>
            </a>

            <div class=\"product-label label bottom\">
                <a class=\"label-link\" href=\"#\">еще 285 товаров</a>
            </div>
        </div>
        <div class=\"product-info\">
            <div class=\"title\">
                <a href=\"#\" class=\"is-gradiented\">Арматура строительная</a>
            </div>
            <p class=\"size gray60-color\">Размер 30x30</p>

            <p class=\"price gray60-color\"><strong class=\"count red-color\">25 000 <span class=\"icon-rouble\"></span> </strong>за тонну</p>
        </div>
        <ul class=\"links clearfix\">
            <li class=\"links_send item large float-left\">
                <a class=\"send-order_send-button button send-button red-bg ie-radius\" href=\"#\">Отправить заявку</a>
            </li>
        </ul>
        <div class=\"hover-block opacity-border\">
            <div class=\"dropdown\">
                <div class=\"top\">
                    <div class=\"star-panel clearfix\">
                        <div class=\"status green-bg float-left ie-radius\">online</div>
                        <div class=\"rating float-left\">
                            <span class=\"star-mini icon-star-colored\"></span>
                            <span class=\"star-mini icon-star-colored\"></span>
                            <span class=\"star-mini icon-star-colored\"></span>
                        </div>
                    </div>
                    <div class=\"title\">
                        <a href=\"#\" class=\"is-gradiented\">Стальторг</a>
                    </div>
                    <p class=\"delivery gray60-color\">доставка из Одинцово</p>
                    <a class=\"site\" href=\"#\">staltorg.ru</a>

                    <div class=\"contacts\">
                        <span class=\"phone-text\">+7 (495) 784</span>
                        ( <a class=\"see\" href=\"#\">показать</a> )
                        <a class=\"callback\" href=\"#\">обратный звонок</a>
                    </div>
                    <div class=\"product-label label bottom\">
                        <a class=\"label-link\" href=\"#\">еще 285 товаров</a>
                    </div>
                </div>
                <div class=\"product-info\">
                    <div class=\"title\">
                        <a href=\"#\" class=\"is-gradiented\">Арматура строительная</a>
                    </div>
                    <p class=\"size gray60-color\">Размер 30x30</p>

                    <p class=\"price gray60-color\"><strong class=\"count red-color\">25 000 <span class=\"icon-rouble\"></span> </strong>за тонну</p>
                </div>
                <ul class=\"links clearfix\">
                    <li class=\"links_send item large\">
                        <a class=\"send-order_send-button button send-button red-bg ie-radius\" href=\"#\">Отправить
                            заявку</a>
                    </li>
                    <li class=\"item large clearfix\">
                        <a href=\"#\" class=\"button favorites is-bordered ie-radius\">
                            <span class=\"text\">В Избранное</span>
                            <span class=\"icon-favorite float-right\"></span>
                        </a>
                    </li>
                    <li class=\"links_comment item large clearfix\">
                        <a href=\"#\" class=\"button comment is-bordered ie-radius\">
                            <span class=\"text\">Отзывы</span>
                            <span class=\"icon-comment float-right\"></span>
                        </a>
                    </li>
                    <li class=\"links_report item clearfix float-right\">
                        <a href=\"#\" class=\"button report is-bordered js-tooltip-opener js-popup-opener ie-radius\"
                           data-tooltip-title=\"Пожаловаться\" data-popup=\"#report\">
                            <span class=\"icon-complaint\"></span>
                        </a>
                    </li>
                    <li class=\"updated-date float-left\">
                        <span class=\"text\">Обновлено 31 мар 2013</span>
                    </li>
                </ul>
            </div>
        </div>
    </li>
    <li class=\"special-title float-left js-special-title\">
        <p class=\"text\">Товары, доставляемые в Москву</p>
    </li>
    <li class=\"view-product grid_item float-left\">
        <div class=\"product-logo\">
            <a class=\"img-link pattern-big\" href=\"#\"></a>
        </div>
        <div class=\"product-info\">
            <div class=\"title\">
                <a href=\"#\" class=\"is-gradiented\">Арматура строительная</a>
            </div>
            <p class=\"size gray60-color\">Размер 30x30</p>

            <p class=\"price gray60-color\"><strong class=\"count red-color\">25 000 <span class=\"icon-rouble\"></span> </strong>за тонну</p>
        </div>
        <ul class=\"links clearfix\">
            <li class=\"links_send item large float-left\">
                <a class=\"send-order_send-button button send-button red-bg ie-radius\" href=\"#\">Отправить заявку</a>
            </li>
        </ul>
        <div class=\"hover-block opacity-border\">
            <div class=\"dropdown\">
                <div class=\"top\">
                    <div class=\"star-panel clearfix\">
                        <div class=\"status green-bg float-left ie-radius\">online</div>
                        <div class=\"rating float-left\">
                            <span class=\"star-mini icon-star-colored\"></span>
                            <span class=\"star-mini icon-star-colored\"></span>
                            <span class=\"star-mini icon-star-colored\"></span>
                        </div>
                    </div>
                    <div class=\"title\">
                        <a href=\"#\" class=\"is-gradiented\">Стальторг</a>
                    </div>
                    <p class=\"delivery gray60-color\">доставка из Одинцово</p>
                    <a class=\"site\" href=\"#\">staltorg.ru</a>

                    <div class=\"contacts\">
                        <span class=\"phone-text\">+7 (495) 784</span>
                        ( <a class=\"see\" href=\"#\">показать</a> )
                        <a class=\"callback\" href=\"#\">обратный звонок</a>
                    </div>
                </div>
                <div class=\"product-info\">
                    <div class=\"title\">
                        <a href=\"#\" class=\"is-gradiented\">Арматура строительная</a>
                    </div>
                    <p class=\"size gray60-color\">Размер 30x30</p>

                    <p class=\"price gray60-color\"><strong class=\"count red-color\">25 000 <span class=\"icon-rouble\"></span> </strong>за тонну</p>
                </div>
                <ul class=\"links clearfix\">
                    <li class=\"links_send item large\">
                        <a class=\"send-order_send-button button send-button red-bg ie-radius\" href=\"#\">Отправить
                            заявку</a>
                    </li>
                    <li class=\"item large clearfix\">
                        <a href=\"#\" class=\"button favorites is-bordered\">
                            <span class=\"text\">В Избранное</span>
                            <span class=\"icon-favorite float-right\"></span>
                        </a>
                    </li>
                    <li class=\"links_comment item large clearfix\">
                        <a href=\"#\" class=\"button comment is-bordered ie-radius\">
                            <span class=\"text\">Отзывы</span>
                            <span class=\"icon-comment float-right\"></span>
                        </a>
                    </li>
                    <li class=\"links_report item clearfix float-right\">
                        <a href=\"#\" class=\"button report is-bordered js-tooltip-opener js-popup-opener ie-radius\"
                           data-tooltip-title=\"Пожаловаться\" data-popup=\"#report\">
                            <span class=\"icon-complaint\"></span>
                        </a>
                    </li>
                    <li class=\"updated-date float-left\">
                        <span class=\"text\">Обновлено 31 мар 2013</span>
                    </li>
                </ul>
            </div>
        </div>
    </li>
    <li class=\"view-product grid_item float-left\">
        <div class=\"product-logo\">
            <a class=\"img-link pattern-big\" href=\"#\"></a>

            <div class=\"product-label label bottom\">
                <a class=\"label-link\" href=\"#\">еще 285 товаров</a>
            </div>
        </div>
        <div class=\"product-info\">
            <div class=\"title\">
                <a href=\"#\" class=\"is-gradiented\">Арматура строительная</a>
            </div>
            <p class=\"size gray60-color\">Размер 30x30</p>

            <p class=\"price gray60-color\"><strong class=\"count red-color\">25 000 <span class=\"icon-rouble\"></span> </strong>за тонну</p>
        </div>
        <ul class=\"links clearfix\">
            <li class=\"links_send item large float-left\">
                <a class=\"send-order_send-button button send-button red-bg ie-radius\" href=\"#\">Отправить заявку</a>
            </li>
        </ul>
        <div class=\"hover-block opacity-border\">
            <div class=\"dropdown\">
                <div class=\"top\">
                    <div class=\"star-panel clearfix\">
                        <div class=\"status green-bg float-left ie-radius\">online</div>
                        <div class=\"rating float-left\">
                            <span class=\"star-mini icon-star-colored\"></span>
                            <span class=\"star-mini icon-star-colored\"></span>
                            <span class=\"star-mini icon-star-colored\"></span>
                        </div>
                    </div>
                    <div class=\"title\">
                        <a href=\"#\" class=\"is-gradiented\">Стальторг</a>
                    </div>
                    <p class=\"delivery gray60-color\">доставка из Одинцово</p>
                    <a class=\"site\" href=\"#\">staltorg.ru</a>

                    <div class=\"contacts\">
                        <span class=\"phone-text\">+7 (495) 784</span>
                        ( <a class=\"see\" href=\"#\">показать</a> )
                        <a class=\"callback\" href=\"#\">обратный звонок</a>
                    </div>
                    <div class=\"product-label label bottom\">
                        <a class=\"label-link\" href=\"#\">еще 285 товаров</a>
                    </div>
                </div>
                <div class=\"product-info\">
                    <div class=\"title\">
                        <a href=\"#\" class=\"is-gradiented\">Арматура строительная</a>
                    </div>
                    <p class=\"size gray60-color\">Размер 30x30</p>

                    <p class=\"price gray60-color\"><strong class=\"count red-color\">25 000 <span class=\"icon-rouble\"></span> </strong>за тонну</p>
                </div>
                <ul class=\"links clearfix\">
                    <li class=\"links_send item large\">
                        <a class=\"send-order_send-button button send-button red-bg ie-radius\" href=\"#\">Отправить
                            заявку</a>
                    </li>
                    <li class=\"item large clearfix\">
                        <a href=\"#\" class=\"button favorites is-bordered ie-radius\">
                            <span class=\"text\">В Избранное</span>
                            <span class=\"icon-favorite float-right\"></span>
                        </a>
                    </li>
                    <li class=\"links_comment item large clearfix\">
                        <a href=\"#\" class=\"button comment is-bordered ie-radius\">
                            <span class=\"text\">Отзывы</span>
                            <span class=\"icon-comment float-right\"></span>
                        </a>
                    </li>
                    <li class=\"links_report item clearfix float-right\">
                        <a href=\"#\" class=\"button report is-bordered js-tooltip-opener js-popup-opener ie-radius\"
                           data-tooltip-title=\"Пожаловаться\" data-popup=\"#report\">
                            <span class=\"icon-complaint\"></span>
                        </a>
                    </li>
                    <li class=\"updated-date float-left\">
                        <span class=\"text\">Обновлено 31 мар 2013</span>
                    </li>
                </ul>
            </div>
        </div>
    </li>
    <li class=\"view-product grid_item float-left\">
        <div class=\"product-logo\">
            <a class=\"img-link pattern-big\" href=\"#\">
                <img src=\"./markup/pic/product-big.jpg\" alt=\"image description\"/>
            </a>

            <div class=\"product-label label bottom\">
                <a class=\"label-link\" href=\"#\">еще 285 товаров</a>
            </div>
        </div>
        <div class=\"product-info\">
            <div class=\"title\">
                <a href=\"#\" class=\"is-gradiented\">Арматура строительная</a>
            </div>
            <p class=\"size gray60-color\">Размер 30x30</p>

            <p class=\"price gray60-color\"><strong class=\"count red-color\">25 000 <span class=\"icon-rouble\"></span> </strong>за тонну</p>
        </div>
        <ul class=\"links clearfix\">
            <li class=\"links_send item large float-left\">
                <a class=\"send-order_send-button button send-button red-bg ie-radius\" href=\"#\">Отправить заявку</a>
            </li>
        </ul>
        <div class=\"hover-block opacity-border\">
            <div class=\"dropdown\">
                <div class=\"top\">
                    <div class=\"star-panel clearfix\">
                        <div class=\"status green-bg float-left ie-radius\">online</div>
                        <div class=\"rating float-left\">
                            <span class=\"star-mini icon-star-colored\"></span>
                            <span class=\"star-mini icon-star-colored\"></span>
                            <span class=\"star-mini icon-star-colored\"></span>
                        </div>
                    </div>
                    <div class=\"title\">
                        <a href=\"#\" class=\"is-gradiented\">Стальторг</a>
                    </div>
                    <p class=\"delivery gray60-color\">доставка из Одинцово</p>
                    <a class=\"site\" href=\"#\">staltorg.ru</a>

                    <div class=\"contacts\">
                        <span class=\"phone-text\">+7 (495) 784</span>
                        ( <a class=\"see\" href=\"#\">показать</a> )
                        <a class=\"callback\" href=\"#\">обратный звонок</a>
                    </div>
                    <div class=\"product-label label bottom\">
                        <a class=\"label-link\" href=\"#\">еще 285 товаров</a>
                    </div>
                </div>
                <div class=\"product-info\">
                    <div class=\"title\">
                        <a href=\"#\" class=\"is-gradiented\">Арматура строительная</a>
                    </div>
                    <p class=\"size gray60-color\">Размер 30x30</p>

                    <p class=\"price gray60-color\"><strong class=\"count red-color\">25 000 <span class=\"icon-rouble\"></span> </strong>за тонну</p>
                </div>
                <ul class=\"links clearfix\">
                    <li class=\"links_send item large\">
                        <a class=\"send-order_send-button button send-button red-bg ie-radius\" href=\"#\">Отправить
                            заявку</a>
                    </li>
                    <li class=\"item large clearfix\">
                        <a href=\"#\" class=\"button favorites is-bordered ie-radius\">
                            <span class=\"text\">В Избранное</span>
                            <span class=\"icon-favorite float-right\"></span>
                        </a>
                    </li>
                    <li class=\"links_comment item large clearfix\">
                        <a href=\"#\" class=\"button comment is-bordered ie-radius\">
                            <span class=\"text\">Отзывы</span>
                            <span class=\"icon-comment float-right\"></span>
                        </a>
                    </li>
                    <li class=\"links_report item clearfix float-right\">
                        <a href=\"#\" class=\"button report is-bordered js-tooltip-opener js-popup-opener ie-radius\"
                           data-tooltip-title=\"Пожаловаться\" data-popup=\"#report\">
                            <span class=\"icon-complaint\"></span>
                        </a>
                    </li>
                    <li class=\"updated-date float-left\">
                        <span class=\"text\">Обновлено 31 мар 2013</span>
                    </li>
                </ul>
            </div>
        </div>
    </li>
    <li class=\"view-product grid_item float-left\">
        <div class=\"product-logo\">
            <a class=\"img-link pattern-big\" href=\"#\"></a>
        </div>
        <div class=\"product-info\">
            <div class=\"title\">
                <a href=\"#\" class=\"is-gradiented\">Арматура строительная</a>
            </div>
            <p class=\"size gray60-color\">Размер 30x30</p>

            <p class=\"price gray60-color\"><strong class=\"count red-color\">25 000 <span class=\"icon-rouble\"></span> </strong>за тонну</p>
        </div>
        <ul class=\"links clearfix\">
            <li class=\"links_send item large float-left\">
                <a class=\"send-order_send-button button send-button red-bg ie-radius\" href=\"#\">Отправить заявку</a>
            </li>
        </ul>
        <div class=\"hover-block opacity-border\">
            <div class=\"dropdown\">
                <div class=\"top\">
                    <div class=\"star-panel clearfix\">
                        <div class=\"status green-bg float-left ie-radius\">online</div>
                        <div class=\"rating float-left\">
                            <span class=\"star-mini icon-star-colored\"></span>
                            <span class=\"star-mini icon-star-colored\"></span>
                            <span class=\"star-mini icon-star-colored\"></span>
                        </div>
                    </div>
                    <div class=\"title\">
                        <a href=\"#\" class=\"is-gradiented\">Стальторг</a>
                    </div>
                    <p class=\"delivery gray60-color\">доставка из Одинцово</p>
                    <a class=\"site\" href=\"#\">staltorg.ru</a>

                    <div class=\"contacts\">
                        <span class=\"phone-text\">+7 (495) 784</span>
                        ( <a class=\"see\" href=\"#\">показать</a> )
                        <a class=\"callback\" href=\"#\">обратный звонок</a>
                    </div>
                </div>
                <div class=\"product-info\">
                    <div class=\"title\">
                        <a href=\"#\" class=\"is-gradiented\">Арматура строительная</a>
                    </div>
                    <p class=\"size gray60-color\">Размер 30x30</p>

                    <p class=\"price gray60-color\"><strong class=\"count red-color\">25 000 <span class=\"icon-rouble\"></span> </strong>за тонну</p>
                </div>
                <ul class=\"links clearfix\">
                    <li class=\"links_send item large\">
                        <a class=\"send-order_send-button button send-button red-bg ie-radius\" href=\"#\">Отправить
                            заявку</a>
                    </li>
                    <li class=\"item large clearfix\">
                        <a href=\"#\" class=\"button favorites is-bordered\">
                            <span class=\"text\">В Избранное</span>
                            <span class=\"icon-favorite float-right\"></span>
                        </a>
                    </li>
                    <li class=\"links_comment item large clearfix\">
                        <a href=\"#\" class=\"button comment is-bordered ie-radius\">
                            <span class=\"text\">Отзывы</span>
                            <span class=\"icon-comment float-right\"></span>
                        </a>
                    </li>
                    <li class=\"links_report item clearfix float-right\">
                        <a href=\"#\" class=\"button report is-bordered js-tooltip-opener js-popup-opener ie-radius\"
                           data-tooltip-title=\"Пожаловаться\" data-popup=\"#report\">
                            <span class=\"icon-complaint\"></span>
                        </a>
                    </li>
                    <li class=\"updated-date float-left\">
                        <span class=\"text\">Обновлено 31 мар 2013</span>
                    </li>
                </ul>
            </div>
        </div>
    </li>
    <li class=\"view-product grid_item float-left\">
        <div class=\"product-logo\">
            <a class=\"img-link pattern-big\" href=\"#\"></a>

            <div class=\"product-label label bottom\">
                <a class=\"label-link\" href=\"#\">еще 285 товаров</a>
            </div>
        </div>
        <div class=\"product-info\">
            <div class=\"title\">
                <a href=\"#\" class=\"is-gradiented\">Арматура строительная</a>
            </div>
            <p class=\"size gray60-color\">Размер 30x30</p>

            <p class=\"price gray60-color\"><strong class=\"count red-color\">25 000 <span class=\"icon-rouble\"></span> </strong>за тонну</p>
        </div>
        <ul class=\"links clearfix\">
            <li class=\"links_send item large float-left\">
                <a class=\"send-order_send-button button send-button red-bg ie-radius\" href=\"#\">Отправить заявку</a>
            </li>
        </ul>
        <div class=\"hover-block opacity-border\">
            <div class=\"dropdown\">
                <div class=\"top\">
                    <div class=\"star-panel clearfix\">
                        <div class=\"status green-bg float-left ie-radius\">online</div>
                        <div class=\"rating float-left\">
                            <span class=\"star-mini icon-star-colored\"></span>
                            <span class=\"star-mini icon-star-colored\"></span>
                            <span class=\"star-mini icon-star-colored\"></span>
                        </div>
                    </div>
                    <div class=\"title\">
                        <a href=\"#\" class=\"is-gradiented\">Стальторг</a>
                    </div>
                    <p class=\"delivery gray60-color\">доставка из Одинцово</p>
                    <a class=\"site\" href=\"#\">staltorg.ru</a>

                    <div class=\"contacts\">
                        <span class=\"phone-text\">+7 (495) 784</span>
                        ( <a class=\"see\" href=\"#\">показать</a> )
                        <a class=\"callback\" href=\"#\">обратный звонок</a>
                    </div>
                    <div class=\"product-label label bottom\">
                        <a class=\"label-link\" href=\"#\">еще 285 товаров</a>
                    </div>
                </div>
                <div class=\"product-info\">
                    <div class=\"title\">
                        <a href=\"#\" class=\"is-gradiented\">Арматура строительная</a>
                    </div>
                    <p class=\"size gray60-color\">Размер 30x30</p>

                    <p class=\"price gray60-color\"><strong class=\"count red-color\">25 000 <span class=\"icon-rouble\"></span> </strong>за тонну</p>
                </div>
                <ul class=\"links clearfix\">
                    <li class=\"links_send item large\">
                        <a class=\"send-order_send-button button send-button red-bg ie-radius\" href=\"#\">Отправить
                            заявку</a>
                    </li>
                    <li class=\"item large clearfix\">
                        <a href=\"#\" class=\"button favorites is-bordered ie-radius\">
                            <span class=\"text\">В Избранное</span>
                            <span class=\"icon-favorite float-right\"></span>
                        </a>
                    </li>
                    <li class=\"links_comment item large clearfix\">
                        <a href=\"#\" class=\"button comment is-bordered ie-radius\">
                            <span class=\"text\">Отзывы</span>
                            <span class=\"icon-comment float-right\"></span>
                        </a>
                    </li>
                    <li class=\"links_report item clearfix float-right\">
                        <a href=\"#\" class=\"button report is-bordered js-tooltip-opener js-popup-opener ie-radius\"
                           data-tooltip-title=\"Пожаловаться\" data-popup=\"#report\">
                            <span class=\"icon-complaint\"></span>
                        </a>
                    </li>
                    <li class=\"updated-date float-left\">
                        <span class=\"text\">Обновлено 31 мар 2013</span>
                    </li>
                </ul>
            </div>
        </div>
    </li>
    <li class=\"view-product grid_item float-left\">
        <div class=\"product-logo\">
            <a class=\"img-link pattern-big\" href=\"#\">
                <img src=\"./markup/pic/product-big.jpg\" alt=\"image description\"/>
            </a>

            <div class=\"product-label label bottom\">
                <a class=\"label-link\" href=\"#\">еще 285 товаров</a>
            </div>
        </div>
        <div class=\"product-info\">
            <div class=\"title\">
                <a href=\"#\" class=\"is-gradiented\">Арматура строительная</a>
            </div>
            <p class=\"size gray60-color\">Размер 30x30</p>

            <p class=\"price gray60-color\"><strong class=\"count red-color\">25 000 <span class=\"icon-rouble\"></span> </strong>за тонну</p>
        </div>
        <ul class=\"links clearfix\">
            <li class=\"links_send item large float-left\">
                <a class=\"send-order_send-button button send-button red-bg ie-radius\" href=\"#\">Отправить заявку</a>
            </li>
        </ul>
        <div class=\"hover-block opacity-border\">
            <div class=\"dropdown\">
                <div class=\"top\">
                    <div class=\"star-panel clearfix\">
                        <div class=\"status green-bg float-left ie-radius\">online</div>
                        <div class=\"rating float-left\">
                            <span class=\"star-mini icon-star-colored\"></span>
                            <span class=\"star-mini icon-star-colored\"></span>
                            <span class=\"star-mini icon-star-colored\"></span>
                        </div>
                    </div>
                    <div class=\"title\">
                        <a href=\"#\" class=\"is-gradiented\">Стальторг</a>
                    </div>
                    <p class=\"delivery gray60-color\">доставка из Одинцово</p>
                    <a class=\"site\" href=\"#\">staltorg.ru</a>

                    <div class=\"contacts\">
                        <span class=\"phone-text\">+7 (495) 784</span>
                        ( <a class=\"see\" href=\"#\">показать</a> )
                        <a class=\"callback\" href=\"#\">обратный звонок</a>
                    </div>
                    <div class=\"product-label label bottom\">
                        <a class=\"label-link\" href=\"#\">еще 285 товаров</a>
                    </div>
                </div>
                <div class=\"product-info\">
                    <div class=\"title\">
                        <a href=\"#\" class=\"is-gradiented\">Арматура строительная</a>
                    </div>
                    <p class=\"size gray60-color\">Размер 30x30</p>

                    <p class=\"price gray60-color\"><strong class=\"count red-color\">25 000 <span class=\"icon-rouble\"></span> </strong>за тонну</p>
                </div>
                <ul class=\"links clearfix\">
                    <li class=\"links_send item large\">
                        <a class=\"send-order_send-button button send-button red-bg ie-radius\" href=\"#\">Отправить
                            заявку</a>
                    </li>
                    <li class=\"item large clearfix\">
                        <a href=\"#\" class=\"button favorites is-bordered ie-radius\">
                            <span class=\"text\">В Избранное</span>
                            <span class=\"icon-favorite float-right\"></span>
                        </a>
                    </li>
                    <li class=\"links_comment item large clearfix\">
                        <a href=\"#\" class=\"button comment is-bordered ie-radius\">
                            <span class=\"text\">Отзывы</span>
                            <span class=\"icon-comment float-right\"></span>
                        </a>
                    </li>
                    <li class=\"links_report item clearfix float-right\">
                        <a href=\"#\" class=\"button report is-bordered js-tooltip-opener js-popup-opener ie-radius\"
                           data-tooltip-title=\"Пожаловаться\" data-popup=\"#report\">
                            <span class=\"icon-complaint\"></span>
                        </a>
                    </li>
                    <li class=\"updated-date float-left\">
                        <span class=\"text\">Обновлено 31 мар 2013</span>
                    </li>
                </ul>
            </div>
        </div>
    </li>
    <li class=\"announcement has-announcement float-left\">
        <img src=\"./markup/pic/inside-banner.jpg\" width=\"735\" height=\"90\" alt=\"image description\"/>
    </li>
    <li class=\"special-title float-left js-special-title\">
        <p class=\"text\">Товары, доставляемые в Москву</p>
    </li>
    <li class=\"view-product grid_item float-left\">
        <div class=\"product-logo\">
            <a class=\"img-link pattern-big\" href=\"#\"></a>
        </div>
        <div class=\"product-info\">
            <div class=\"title\">
                <a href=\"#\" class=\"is-gradiented\">Арматура строительная</a>
            </div>
            <p class=\"size gray60-color\">Размер 30x30</p>

            <p class=\"price gray60-color\"><strong class=\"count red-color\">25 000 <span class=\"icon-rouble\"></span> </strong>за тонну</p>
        </div>
        <ul class=\"links clearfix\">
            <li class=\"links_send item large float-left\">
                <a class=\"send-order_send-button button send-button red-bg ie-radius\" href=\"#\">Отправить заявку</a>
            </li>
        </ul>
        <div class=\"hover-block opacity-border\">
            <div class=\"dropdown\">
                <div class=\"top\">
                    <div class=\"star-panel clearfix\">
                        <div class=\"status green-bg float-left ie-radius\">online</div>
                        <div class=\"rating float-left\">
                            <span class=\"star-mini icon-star-colored\"></span>
                            <span class=\"star-mini icon-star-colored\"></span>
                            <span class=\"star-mini icon-star-colored\"></span>
                        </div>
                    </div>
                    <div class=\"title\">
                        <a href=\"#\" class=\"is-gradiented\">Стальторг</a>
                    </div>
                    <p class=\"delivery gray60-color\">доставка из Одинцово</p>
                    <a class=\"site\" href=\"#\">staltorg.ru</a>

                    <div class=\"contacts\">
                        <span class=\"phone-text\">+7 (495) 784</span>
                        ( <a class=\"see\" href=\"#\">показать</a> )
                        <a class=\"callback\" href=\"#\">обратный звонок</a>
                    </div>
                </div>
                <div class=\"product-info\">
                    <div class=\"title\">
                        <a href=\"#\" class=\"is-gradiented\">Арматура строительная</a>
                    </div>
                    <p class=\"size gray60-color\">Размер 30x30</p>

                    <p class=\"price gray60-color\"><strong class=\"count red-color\">25 000 <span class=\"icon-rouble\"></span> </strong>за тонну</p>
                </div>
                <ul class=\"links clearfix\">
                    <li class=\"links_send item large\">
                        <a class=\"send-order_send-button button send-button red-bg ie-radius\" href=\"#\">Отправить
                            заявку</a>
                    </li>
                    <li class=\"item large clearfix\">
                        <a href=\"#\" class=\"button favorites is-bordered\">
                            <span class=\"text\">В Избранное</span>
                            <span class=\"icon-favorite float-right\"></span>
                        </a>
                    </li>
                    <li class=\"links_comment item large clearfix\">
                        <a href=\"#\" class=\"button comment is-bordered ie-radius\">
                            <span class=\"text\">Отзывы</span>
                            <span class=\"icon-comment float-right\"></span>
                        </a>
                    </li>
                    <li class=\"links_report item clearfix float-right\">
                        <a href=\"#\" class=\"button report is-bordered js-tooltip-opener js-popup-opener ie-radius\"
                           data-tooltip-title=\"Пожаловаться\" data-popup=\"#report\">
                            <span class=\"icon-complaint\"></span>
                        </a>
                    </li>
                    <li class=\"updated-date float-left\">
                        <span class=\"text\">Обновлено 31 мар 2013</span>
                    </li>
                </ul>
            </div>
        </div>
    </li>
    <li class=\"view-product grid_item float-left\">
        <div class=\"product-logo\">
            <a class=\"img-link pattern-big\" href=\"#\"></a>

            <div class=\"product-label label bottom\">
                <a class=\"label-link\" href=\"#\">еще 285 товаров</a>
            </div>
        </div>
        <div class=\"product-info\">
            <div class=\"title\">
                <a href=\"#\" class=\"is-gradiented\">Арматура строительная</a>
            </div>
            <p class=\"size gray60-color\">Размер 30x30</p>

            <p class=\"price gray60-color\"><strong class=\"count red-color\">25 000 <span class=\"icon-rouble\"></span> </strong>за тонну</p>
        </div>
        <ul class=\"links clearfix\">
            <li class=\"links_send item large float-left\">
                <a class=\"send-order_send-button button send-button red-bg ie-radius\" href=\"#\">Отправить заявку</a>
            </li>
        </ul>
        <div class=\"hover-block opacity-border\">
            <div class=\"dropdown\">
                <div class=\"top\">
                    <div class=\"star-panel clearfix\">
                        <div class=\"status green-bg float-left ie-radius\">online</div>
                        <div class=\"rating float-left\">
                            <span class=\"star-mini icon-star-colored\"></span>
                            <span class=\"star-mini icon-star-colored\"></span>
                            <span class=\"star-mini icon-star-colored\"></span>
                        </div>
                    </div>
                    <div class=\"title\">
                        <a href=\"#\" class=\"is-gradiented\">Стальторг</a>
                    </div>
                    <p class=\"delivery gray60-color\">доставка из Одинцово</p>
                    <a class=\"site\" href=\"#\">staltorg.ru</a>

                    <div class=\"contacts\">
                        <span class=\"phone-text\">+7 (495) 784</span>
                        ( <a class=\"see\" href=\"#\">показать</a> )
                        <a class=\"callback\" href=\"#\">обратный звонок</a>
                    </div>
                    <div class=\"product-label label bottom\">
                        <a class=\"label-link\" href=\"#\">еще 285 товаров</a>
                    </div>
                </div>
                <div class=\"product-info\">
                    <div class=\"title\">
                        <a href=\"#\" class=\"is-gradiented\">Арматура строительная</a>
                    </div>
                    <p class=\"size gray60-color\">Размер 30x30</p>

                    <p class=\"price gray60-color\"><strong class=\"count red-color\">25 000 <span class=\"icon-rouble\"></span> </strong>за тонну</p>
                </div>
                <ul class=\"links clearfix\">
                    <li class=\"links_send item large\">
                        <a class=\"send-order_send-button button send-button red-bg ie-radius\" href=\"#\">Отправить
                            заявку</a>
                    </li>
                    <li class=\"item large clearfix\">
                        <a href=\"#\" class=\"button favorites is-bordered ie-radius\">
                            <span class=\"text\">В Избранное</span>
                            <span class=\"icon-favorite float-right\"></span>
                        </a>
                    </li>
                    <li class=\"links_comment item large clearfix\">
                        <a href=\"#\" class=\"button comment is-bordered ie-radius\">
                            <span class=\"text\">Отзывы</span>
                            <span class=\"icon-comment float-right\"></span>
                        </a>
                    </li>
                    <li class=\"links_report item clearfix float-right\">
                        <a href=\"#\" class=\"button report is-bordered js-tooltip-opener js-popup-opener ie-radius\"
                           data-tooltip-title=\"Пожаловаться\" data-popup=\"#report\">
                            <span class=\"icon-complaint\"></span>
                        </a>
                    </li>
                    <li class=\"updated-date float-left\">
                        <span class=\"text\">Обновлено 31 мар 2013</span>
                    </li>
                </ul>
            </div>
        </div>
    </li>
    <li class=\"view-product grid_item float-left\">
        <div class=\"product-logo\">
            <a class=\"img-link pattern-big\" href=\"#\">
                <img src=\"./markup/pic/product-big.jpg\" alt=\"image description\"/>
            </a>

            <div class=\"product-label label bottom\">
                <a class=\"label-link\" href=\"#\">еще 285 товаров</a>
            </div>
        </div>
        <div class=\"product-info\">
            <div class=\"title\">
                <a href=\"#\" class=\"is-gradiented\">Арматура строительная</a>
            </div>
            <p class=\"size gray60-color\">Размер 30x30</p>

            <p class=\"price gray60-color\"><strong class=\"count red-color\">25 000 <span class=\"icon-rouble\"></span> </strong>за тонну</p>
        </div>
        <ul class=\"links clearfix\">
            <li class=\"links_send item large float-left\">
                <a class=\"send-order_send-button button send-button red-bg ie-radius\" href=\"#\">Отправить заявку</a>
            </li>
        </ul>
        <div class=\"hover-block opacity-border\">
            <div class=\"dropdown\">
                <div class=\"top\">
                    <div class=\"star-panel clearfix\">
                        <div class=\"status green-bg float-left ie-radius\">online</div>
                        <div class=\"rating float-left\">
                            <span class=\"star-mini icon-star-colored\"></span>
                            <span class=\"star-mini icon-star-colored\"></span>
                            <span class=\"star-mini icon-star-colored\"></span>
                        </div>
                    </div>
                    <div class=\"title\">
                        <a href=\"#\" class=\"is-gradiented\">Стальторг</a>
                    </div>
                    <p class=\"delivery gray60-color\">доставка из Одинцово</p>
                    <a class=\"site\" href=\"#\">staltorg.ru</a>

                    <div class=\"contacts\">
                        <span class=\"phone-text\">+7 (495) 784</span>
                        ( <a class=\"see\" href=\"#\">показать</a> )
                        <a class=\"callback\" href=\"#\">обратный звонок</a>
                    </div>
                    <div class=\"product-label label bottom\">
                        <a class=\"label-link\" href=\"#\">еще 285 товаров</a>
                    </div>
                </div>
                <div class=\"product-info\">
                    <div class=\"title\">
                        <a href=\"#\" class=\"is-gradiented\">Арматура строительная</a>
                    </div>
                    <p class=\"size gray60-color\">Размер 30x30</p>

                    <p class=\"price gray60-color\"><strong class=\"count red-color\">25 000 <span class=\"icon-rouble\"></span> </strong>за тонну</p>
                </div>
                <ul class=\"links clearfix\">
                    <li class=\"links_send item large\">
                        <a class=\"send-order_send-button button send-button red-bg ie-radius\" href=\"#\">Отправить
                            заявку</a>
                    </li>
                    <li class=\"item large clearfix\">
                        <a href=\"#\" class=\"button favorites is-bordered ie-radius\">
                            <span class=\"text\">В Избранное</span>
                            <span class=\"icon-favorite float-right\"></span>
                        </a>
                    </li>
                    <li class=\"links_comment item large clearfix\">
                        <a href=\"#\" class=\"button comment is-bordered ie-radius\">
                            <span class=\"text\">Отзывы</span>
                            <span class=\"icon-comment float-right\"></span>
                        </a>
                    </li>
                    <li class=\"links_report item clearfix float-right\">
                        <a href=\"#\" class=\"button report is-bordered js-tooltip-opener js-popup-opener ie-radius\"
                           data-tooltip-title=\"Пожаловаться\" data-popup=\"#report\">
                            <span class=\"icon-complaint\"></span>
                        </a>
                    </li>
                    <li class=\"updated-date float-left\">
                        <span class=\"text\">Обновлено 31 мар 2013</span>
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
        // line 1301
        $this->displayBlock("search_more", $context, $blocks);
        echo "
    </div>
";
    }

    // line 16
    public function block_tabs($context, array $blocks = array())
    {
        // line 17
        echo "        ";
        $context["activeTab"] = "products";
        // line 18
        echo "        ";
        $this->displayParentBlock("tabs", $context, $blocks);
        echo "
    ";
    }

    public function getTemplateName()
    {
        return "@markup/portal/suppliers/products-gallery.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  1356 => 18,  1353 => 17,  1350 => 16,  1343 => 1301,  60 => 20,  58 => 16,  46 => 6,  43 => 5,  37 => 4,  31 => 3,);
    }
}
