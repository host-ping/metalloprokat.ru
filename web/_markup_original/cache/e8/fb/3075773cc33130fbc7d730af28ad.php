<?php

/* @markup/portal/suppliers/all-products.html.twig */
class __TwigTemplate_e8fb3075773cc33130fbc7d730af28ad extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = $this->env->loadTemplate("@markup/portal/_portal_layout.html.twig");

        $this->blocks = array(
            'title' => array($this, 'block_title'),
            'breadcrumbs' => array($this, 'block_breadcrumbs'),
            'breadcrumbs_button' => array($this, 'block_breadcrumbs_button'),
            'content' => array($this, 'block_content'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "@markup/portal/_portal_layout.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_title($context, array $blocks = array())
    {
        echo "All Products";
    }

    // line 4
    public function block_breadcrumbs($context, array $blocks = array())
    {
        // line 5
        echo "    <div class=\"breadcrumbs-wrapper\">
        <div class=\"breadcrumbs outline clearfix\">
            ";
        // line 7
        $this->displayBlock('breadcrumbs_button', $context, $blocks);
        // line 12
        echo "            <ul class=\"breadcrumbs_item-list clearfix js-collapsable-breadcrumbs\" data-collapsable-breadcrumbs-reserve=\".js-collapsable-breadcrumbs-reserve\">
                <li class=\"breadcrumbs_item home first\">
                    <a class=\"breadcrumbs_link\" href=\"#\"></a>
                </li>
                <li class=\"breadcrumbs_item\">
                    <a class=\"breadcrumbs_link js-collapsable-item\" data-collapsable-breadcrumb-priority=\"1\" href=\"#\">Продукция из черных металлов</a>
                </li>
                <li class=\"breadcrumbs_item\">
                    <a href=\"#\" class=\"breadcrumbs_link js-collapsable-item icon-check black js-popover-opener\" data-collapsable-breadcrumb-priority=\"2\" data-popover=\"#p\" data-different-position=\"true\">Сортовой и фасонный прокат</a>
                </li>
                <li class=\"breadcrumbs_item\">
                    <a class=\"breadcrumbs_link js-collapsable-item\" data-collapsable-breadcrumb-priority=\"3\" href=\"#\">Профиль</a>
                </li>
                <li class=\"breadcrumbs_item last\">
                    <span class=\"breadcrumbs_link disabled\">Профиль стальной гнутый замкнутый сварной 50х50х5 12000 ГОСТ 30245-94, ст. 3СП/ПС5 3333 333 111</span>
                </li>
            </ul>
        </div>
        <div id=\"p\" class=\"drop-wrapper product-list opacity-border\">
            <div class=\"js-scrollable\">
                <ul class=\"dropdown menu-drop\">
                    <li class=\"drop-item first\">
                        <span class=\"drop-link current\">Сортовой и фасонный прокат</span>
                    </li>
                    <li class=\"drop-item\">
                        <a class=\"drop-link\" href=\"#\">Листовой и плоский прокат</a>
                    </li>
                    <li class=\"drop-item\">
                        <a class=\"drop-link\" href=\"#\">Трубы</a>
                    </li>
                    <li class=\"drop-item\">
                        <a class=\"drop-link\" href=\"#\">Трубопроводная и запорная арматура</a>
                    </li>
                    <li class=\"drop-item\">
                        <a class=\"drop-link\" href=\"#\">Изделия из черных металлов (метизы)</a>
                    </li>
                    <li class=\"drop-item\">
                        <a class=\"drop-link\" href=\"#\">Металлоконструкции</a>
                    </li>
                    <li class=\"drop-item\">
                        <a class=\"drop-link\" href=\"#\">Крепежные изделия</a>
                    </li>
                    <li class=\"drop-item\">
                        <a class=\"drop-link\" href=\"#\">Крепежные изделия</a>
                    </li>
                    <li class=\"drop-item\">
                        <a class=\"drop-link\" href=\"#\">Крепежные изделия</a>
                    </li>
                    <li class=\"drop-item\">
                        <a class=\"drop-link\" href=\"#\">Крепежные изделия</a>
                    </li>
                    <li class=\"drop-item\">
                        <a class=\"drop-link\" href=\"#\">Крепежные изделия</a>
                    </li>
                    <li class=\"drop-item\">
                        <a class=\"drop-link\" href=\"#\">Крепежные изделия</a>
                    </li><li class=\"drop-item\">
                        <a class=\"drop-link\" href=\"#\">Крепежные изделия</a>
                    </li>
                    <li class=\"drop-item\">
                        <a class=\"drop-link\" href=\"#\">Крепежные изделия</a>
                    </li>
                </ul>
            </div>
            <div class=\"opacity\"></div>
        </div>
    </div>
";
    }

    // line 7
    public function block_breadcrumbs_button($context, array $blocks = array())
    {
        // line 8
        echo "                <div class=\"add is-gradiented float-right js-collapsable-breadcrumbs-reserve\">
                    <a class=\"add-button product icon-add-btn\" href=\"#\">Добавить товары</a>
                </div>
            ";
    }

    // line 80
    public function block_content($context, array $blocks = array())
    {
        // line 81
        echo "    <div id=\"content\" class=\"content-wide table-container outline-left\">
        <div class=\"left table-cell\">
            <div class=\"company-inside\">
                <h1 class=\"product-title\">Арматура от компании Стальторг <sup>258</sup></h1>
                <div class=\"map-wrapper is-bordered\">
                    <div class=\"heading clearfix\">
                        <p class=\"yandex-map float-right\">
                            <a class=\"link\" href=\"#\">Открыть в Яндекс.Картах</a>
                        </p>
                        <p class=\"address is-gradiented\">
                            <strong>улица Ялтинская, дом 9 улица Ялтинская, дом 9 улица Ялтинская, дом 9 улица Ялтинская, дом 9</strong>
                        </p>
                    </div>
                    <div class=\"map\">
                        <span class=\"map-point icon-position\" style=\"top: 50px; left: 150px;\"></span>
                        <img src=\"./markup/pic/map-small.jpg\" alt=\"image description\"/>
                        <div class=\"map-rotator\"></div>
                        <div class=\"resizeble-btn ie-radius\"></div>
                    </div>
                </div>
                <div class=\"tabs-content\">
                    <div class=\"product-filters clearfix\">
                        <div class=\"float-left\">
                            <a class=\"on-site\" href=\"#\">Товар на сайте компании</a>
                            <span class=\"updated-date\">Обновлено 8 фев 2013</span>
                        </div>
                        <div class=\"sort-view float-right\">
                            <div class=\"order-block float-left\">
                                <a class=\"order-link icon-check black link js-popover-opener\" href=\"#\" data-popover=\"#order\">По рейтингу</a>
                                <div id=\"order\" class=\"drop-wrapper order-list opacity-border\">
                                    <ul class=\"dropdown\">
                                        <li class=\"drop-item\">
                                            <a class=\"drop-link\" href=\"#\">По рейтингу</a>
                                        </li>
                                        <li class=\"drop-item\">
                                            <a class=\"drop-link\" href=\"#\">По цене</a>
                                        </li>
                                        <li class=\"drop-item\">
                                            <a class=\"drop-link\" href=\"#\">По к-ву отзывов</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <ul class=\"product similar-list\">
                        <li class=\"item outline clearfix\">
                            <div class=\"img is-bordered float-right\">
                                <div class=\"img-holder\">
                                    <a class=\"img-link pattern-small\" href=\"#\"></a>
                                </div>
                            </div>
                            <div class=\"links-wrapper float-right\">
                                <ul class=\"links clearfix\">
                                    <li class=\"links_report item float-left clearfix\">
                                        <a href=\"#\" class=\"button report is-bordered js-tooltip-opener js-popup-opener ie-radius\" data-tooltip-title=\"Пожаловаться\" data-popup=\"#report\">
                                            <span class=\"icon-complaint\"></span>
                                        </a>
                                    </li>
                                    <li class=\"item float-left\">
                                        <a href=\"#\" class=\"button favorites active-link small is-bordered ie-radius js-togglable-block\">
                                            <span class=\"icon-favorite-active\"></span>
                                        </a>
                                        <a href=\"#\" class=\"button favorites small delete is-bordered g-hidden ie-radius js-togglable-block\">
                                            <span class=\"icon-favorite-del\"></span>
                                        </a>
                                    </li>
                                    <li class=\"links_answer item width-182 float-left clearfix\">
                                        <a class=\"send-order_send-button button send-button red-bg ie-radius\" href=\"#\">Отправить заявку</a>
                                    </li>
                                </ul>
                            </div>
                            <div class=\"left\">
                                <p class=\"title\">
                                    <a class=\"product-link\" href=\"#\">Арматура стеклопластиковая АСП-6</a>
                                </p>
                                <div class=\"info\">
                                    <div class=\"text\">
                                        <p class=\"size float-left\">Размер 6x10, 15x15, 15x20, 20x20, 30x30, 10x30, 5x10, 10x10, 15x15, 15x20, 20x20, 30x30, 10x30, 5x10, 10x10</p>
                                        <p class=\"price float-left\">
                                            <strong class=\"red-color\">10 <span class=\"icon-rouble\"></span></strong> за погонный меjbkjk gkтр
                                            <span class=\"is-gradiented\"></span>
                                        </p>
                                        <span class=\"is-gradiented\"></span>
                                    </div>
                                </div>
                            </div>

                        </li>
                        <li class=\"item outline clearfix\">
                            <div class=\"img is-bordered float-right\">
                                <div class=\"img-holder\">
                                    <a class=\"img-link pattern-small\" href=\"#\"></a>
                                </div>
                            </div>
                            <div class=\"links-wrapper float-right\">
                                <ul class=\"links clearfix\">
                                    <li class=\"links_report item float-left clearfix\">
                                        <a href=\"#\" class=\"button report is-bordered js-tooltip-opener js-popup-opener ie-radius\" data-tooltip-title=\"Пожаловаться\" data-popup=\"#report\">
                                            <span class=\"icon-complaint\"></span>
                                        </a>
                                    </li>
                                    <li class=\"item float-left\">
                                        <a href=\"#\" class=\"button favorites small is-bordered ie-radius\">
                                            <span class=\"icon-favorite\"></span>
                                        </a>
                                    </li>
                                    <li class=\"links_answer item width-182 float-left clearfix\">
                                        <a class=\"send-order_send-button button send-button red-bg ie-radius\" href=\"#\">Отправить заявку</a>
                                    </li>
                                </ul>
                            </div>
                            <div class=\"left\">
                                <p class=\"title is-gradiented\">
                                    <a class=\"product-link\" href=\"#\">Арматура стеклопластиковая АСП-6 стеклопластиковая АСП-6 стеклопластиковая АСП-6 стеклопластиковая АСП-6 стеклопластиковая АСП-6</a>
                                </p>
                                <div class=\"info\">
                                    <div class=\"text\">
                                        <p class=\"size float-left\">Размер 6x10,</p>
                                        <p class=\"price float-left\">
                                            <strong class=\"red-color\">10 <span class=\"icon-rouble\"></span></strong> за погонный меjbkjk gkтр
                                            <span class=\"is-gradiented\"></span>
                                        </p>
                                        <span class=\"is-gradiented\"></span>
                                    </div>
                                </div>
                            </div>

                        </li>
                        <li class=\"item outline clearfix\">
                            <div class=\"img is-bordered float-right\">
                                <div class=\"img-holder\">
                                    <a class=\"img-link pattern-small\" href=\"#\"></a>
                                </div>
                            </div>
                            <div class=\"links-wrapper float-right\">
                                <ul class=\"links clearfix\">
                                    <li class=\"links_report item float-left clearfix\">
                                        <a href=\"#\" class=\"button report is-bordered js-tooltip-opener js-popup-opener ie-radius\" data-tooltip-title=\"Пожаловаться\" data-popup=\"#report\">
                                            <span class=\"icon-complaint\"></span>
                                        </a>
                                    </li>
                                    <li class=\"item float-left\">
                                        <a href=\"#\" class=\"button favorites small is-bordered ie-radius\">
                                            <span class=\"icon-favorite\"></span>
                                        </a>
                                    </li>
                                    <li class=\"links_answer item width-182 float-left clearfix\">
                                        <a class=\"send-order_send-button button send-button red-bg ie-radius\" href=\"#\">Отправить заявку</a>
                                    </li>
                                </ul>
                            </div>
                            <div class=\"left\">
                                <p class=\"title is-gradiented\">
                                    <a class=\"product-link\" href=\"#\">Арматура стеклопластиковая АСП-6</a>
                                </p>
                                <div class=\"info\">
                                    <div class=\"text\">
                                        <p class=\"size float-left\">Размер 6x10,</p>
                                        <p class=\"price float-left\">
                                            <strong class=\"red-color\">10 <span class=\"icon-rouble\"></span></strong> за погонный меjbkjk gkтр
                                            <span class=\"is-gradiented\"></span>
                                        </p>
                                        <span class=\"is-gradiented\"></span>
                                    </div>
                                </div>
                            </div>

                        </li>
                        <li class=\"item outline clearfix\">
                            <div class=\"img is-bordered float-right\">
                                <div class=\"img-holder\">
                                    <a class=\"img-link pattern-small\" href=\"#\"></a>
                                </div>
                            </div>
                            <div class=\"links-wrapper float-right\">
                                <ul class=\"links clearfix\">
                                    <li class=\"links_report item float-left clearfix\">
                                        <a href=\"#\" class=\"button report is-bordered js-tooltip-opener js-popup-opener ie-radius\" data-tooltip-title=\"Пожаловаться\" data-popup=\"#report\">
                                            <span class=\"icon-complaint\"></span>
                                        </a>
                                    </li>
                                    <li class=\"item float-left\">
                                        <a href=\"#\" class=\"button favorites small is-bordered ie-radius\">
                                            <span class=\"icon-favorite\"></span>
                                        </a>
                                    </li>
                                    <li class=\"links_answer item width-182 float-left clearfix\">
                                        <a class=\"send-order_send-button button send-button red-bg ie-radius\" href=\"#\">Отправить заявку</a>
                                    </li>
                                </ul>
                            </div>
                            <div class=\"left\">
                                <p class=\"title is-gradiented\">
                                    <a class=\"product-link\" href=\"#\">Арматура стеклопластиковая АСП-6</a>
                                </p>
                                <div class=\"info\">
                                    <div class=\"text\">
                                        <p class=\"size float-left\">Размер 6x10,</p>
                                        <p class=\"price float-left\">
                                            <strong class=\"red-color\">10 <span class=\"icon-rouble\"></span></strong> за погонный меjbkjk gkтр
                                            <span class=\"is-gradiented\"></span>
                                        </p>
                                        <span class=\"is-gradiented\"></span>
                                    </div>
                                </div>
                            </div>

                        </li>
                        <li class=\"item outline clearfix\">
                            <div class=\"img is-bordered float-right\">
                                <div class=\"img-holder\">
                                    <a class=\"img-link pattern-small\" href=\"#\"></a>
                                </div>
                            </div>
                            <div class=\"links-wrapper float-right\">
                                <ul class=\"links clearfix\">
                                    <li class=\"links_report item float-left clearfix\">
                                        <a href=\"#\" class=\"button report is-bordered js-tooltip-opener js-popup-opener ie-radius\" data-tooltip-title=\"Пожаловаться\" data-popup=\"#report\">
                                            <span class=\"icon-complaint\"></span>
                                        </a>
                                    </li>
                                    <li class=\"item float-left\">
                                        <a href=\"#\" class=\"button favorites small is-bordered ie-radius\">
                                            <span class=\"icon-favorite\"></span>
                                        </a>
                                    </li>
                                    <li class=\"links_answer item width-182 float-left clearfix\">
                                        <a class=\"send-order_send-button button send-button red-bg ie-radius\" href=\"#\">Отправить заявку</a>
                                    </li>
                                </ul>
                            </div>
                            <div class=\"left\">
                                <p class=\"title is-gradiented\">
                                    <a class=\"product-link\" href=\"#\">Арматура стеклопластиковая АСП-6</a>
                                </p>
                                <div class=\"info\">
                                    <div class=\"text\">
                                        <p class=\"size float-left\">Размер 6x10,</p>
                                        <p class=\"price float-left\">
                                            <strong class=\"red-color\">10 <span class=\"icon-rouble\"></span></strong> за погонный меjbkjk gkтр
                                            <span class=\"is-gradiented\"></span>
                                        </p>
                                        <span class=\"is-gradiented\"></span>
                                    </div>
                                </div>
                            </div>

                        </li>
                        <li class=\"item outline clearfix\">
                            <div class=\"img is-bordered float-right\">
                                <div class=\"img-holder\">
                                    <a class=\"img-link pattern-small\" href=\"#\"></a>
                                </div>
                            </div>
                            <div class=\"links-wrapper float-right\">
                                <ul class=\"links clearfix\">
                                    <li class=\"links_report item float-left clearfix\">
                                        <a href=\"#\" class=\"button report is-bordered js-tooltip-opener js-popup-opener ie-radius\" data-tooltip-title=\"Пожаловаться\" data-popup=\"#report\">
                                            <span class=\"icon-complaint\"></span>
                                        </a>
                                    </li>
                                    <li class=\"item float-left\">
                                        <a href=\"#\" class=\"button favorites small is-bordered ie-radius\">
                                            <span class=\"icon-favorite\"></span>
                                        </a>
                                    </li>
                                    <li class=\"links_answer item width-182 float-left clearfix\">
                                        <a class=\"send-order_send-button button send-button red-bg ie-radius\" href=\"#\">Отправить заявку</a>
                                    </li>
                                </ul>
                            </div>
                            <div class=\"left\">
                                <p class=\"title is-gradiented\">
                                    <a class=\"product-link\" href=\"#\">Арматура стеклопластиковая АСП-6</a>
                                </p>
                                <div class=\"info\">
                                    <div class=\"text\">
                                        <p class=\"size float-left\">Размер 6x10,</p>
                                        <p class=\"price float-left\">
                                            <strong class=\"red-color\">10 <span class=\"icon-rouble\"></span></strong> за погонный меjbkjk gkтр
                                            <span class=\"is-gradiented\"></span>
                                        </p>
                                        <span class=\"is-gradiented\"></span>
                                    </div>
                                </div>
                            </div>

                        </li>
                        <li class=\"item outline clearfix\">
                            <div class=\"img is-bordered float-right\">
                                <div class=\"img-holder\">
                                    <a class=\"img-link pattern-small\" href=\"#\"></a>
                                </div>
                            </div>
                            <div class=\"links-wrapper float-right\">
                                <ul class=\"links clearfix\">
                                    <li class=\"links_report item float-left clearfix\">
                                        <a href=\"#\" class=\"button report is-bordered js-tooltip-opener js-popup-opener ie-radius\" data-tooltip-title=\"Пожаловаться\" data-popup=\"#report\">
                                            <span class=\"icon-complaint\"></span>
                                        </a>
                                    </li>
                                    <li class=\"item float-left\">
                                        <a href=\"#\" class=\"button favorites small is-bordered ie-radius\">
                                            <span class=\"icon-favorite\"></span>
                                        </a>
                                    </li>
                                    <li class=\"links_answer item width-182 float-left clearfix\">
                                        <a class=\"send-order_send-button button send-button red-bg ie-radius\" href=\"#\">Отправить заявку</a>
                                    </li>
                                </ul>
                            </div>
                            <div class=\"left\">
                                <p class=\"title is-gradiented\">
                                    <a class=\"product-link\" href=\"#\">Арматура стеклопластиковая АСП-6</a>
                                </p>
                                <div class=\"info\">
                                    <div class=\"text\">
                                        <p class=\"size float-left\">Размер 6x10,</p>
                                        <p class=\"price float-left\">
                                            <strong class=\"red-color\">10 <span class=\"icon-rouble\"></span></strong> за погонный меjbkjk gkтр
                                            <span class=\"is-gradiented\"></span>
                                        </p>
                                        <span class=\"is-gradiented\"></span>
                                    </div>
                                </div>
                            </div>

                        </li>
                        <li class=\"see-more-block-wrapper\">
                            <div class=\"see-more-block\">
                                <a class=\"see-more button ie-radius\" href=\"#\">всего 258 заявок</a>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class=\"product-right right blue table-cell\">
            <div class=\"info-wrapper white-bg\">
                <div class=\"company-info-wrapper clearfix\">
                    <div class=\"company-info float-left\">
                        <div class=\"prod-title clearfix\">
                            <div class=\"star-panel clearfix\">
                                <div class=\"status float-left green-bg ie-radius\">online</div>
                                <div class=\"rating float-left\">
                                    <span class=\"star-mini icon-star-colored\"></span> ";
        // line 428
        echo "                                    <span class=\"star-mini icon-star-colored\"></span> ";
        // line 429
        echo "                                    <span class=\"star-mini icon-star-colored\"></span> ";
        // line 430
        echo "                                </div>
                            </div>
                            <div class=\"title\"><a href=\"#\">Стальторг</a></div>
                        </div>
                        <p class=\"localization gray60-color\">филиал в Люберцах</p>
                        <a class=\"site\" href=\"#\">staltorg.ru</a>
                    </div>
                    <div class=\"company-logo float-right\">
                        <a class=\"img-link pattern-small\" href=\"#\">
                            <img src=\"./markup/pic/product-logo-tmp.jpg\" width=\"64\" height=\"64\" alt=\"image description\"/>
                        </a>
                    </div>
                </div>
                <div class=\"comment-block\">
                    <span class=\"text\">Готовы поставить на след. неделе. Позвонить Максиму</span>
                    <span class=\"date\">2 мар 2013 18:20</span>
                </div>
                <div class=\"sec-info\">
                    <p>
                        <a href=\"#\">258 товаров</a> из раздела Арматура
                    </p>
                    <p>
                        <a href=\"#\">3,814 товаров</a> на сайте
                    </p>
                </div>
                <div class=\"reviews-block\">
                    <div class=\"reviews-title\">
                        <span class=\"title \">Отзывы о компании</span>
                        <span class=\"review-count\">12</span>
                        <span class=\"icon-comment\"></span>
                    </div>

                    <ul class=\"reviews list\">
                        <li class=\"icon-positive item\">
                            <p class=\"text\">
                                Покупал у них водонагреватель и в принципе никаких претензий к ним нет.
                            </p>
                            <div class=\"info\">
                                <a href=\"#\" class=\"author\">Николай Чистяков</a>,
                                <span class=\"period\">3 недели назад</span>
                            </div>
                        </li>
                        <li class=\"icon-negative item\">
                            <p class=\"text\">
                                Покупал у них водонагреватель и в принципе никаких претензий к ним нет.
                            </p>
                            <div class=\"info\">
                                <a href=\"#\" class=\"author\">Николай Чистяков</a>,
                                <span class=\"period\">3 недели назад</span>
                            </div>
                        </li>
                        <li class=\"icon-positive item\">
                            <p class=\"text\">
                                Покупал у них водонагреватель и в принципе никаких претензий к ним нет.
                            </p>
                            <div class=\"info\">
                                <a href=\"#\" class=\"author\">Николай Чистяков</a>,
                                <span class=\"period\">3 недели назад</span>
                            </div>
                        </li>
                    </ul>
                    <div class=\"reviews-links\">
                        <a class=\"add-review link\" href=\"#\">Добавить отзыв</a>
                        <a class=\"all-reviews link\" href=\"#\">Все отзывы</a>
                    </div>
                </div>
            </div>
            <div class=\"premium-product-block\">
                <ul class=\"topic-list\">
                    <li class=\"item clearfix\">
                        <div class=\"topic-info float-left\">
                            <a class=\"title-link\" href=\"#\">Арматура стеклопластиковая АСП-6</a>
                            <p class=\"text\">Размер 6, <strong class=\"price red-color\">10 <span class=\"icon-rouble\"></span></strong> за п. м.</p>
                        </div>
                        <div class=\"img is-bordered float-right\">
                            <div class=\"img-holder\">
                                <a href=\"#\" class=\"img-link pattern-small\">
                                    <img src=\"./markup/pic/small-img.jpg\" alt=\"image description\"/>
                                </a>
                            </div>
                        </div>
                    </li>
                    <li class=\"item clearfix\">
                        <div class=\"topic-info float-left\">
                            <a class=\"title-link\" href=\"#\">Арматура стеклопластиковая АСП-6</a>
                            <p class=\"text\">Размер 6, <strong class=\"price red-color\">10 <span class=\"icon-rouble\"></span></strong> за п. м.</p>
                        </div>
                        <div class=\"img is-bordered float-right\">
                            <div class=\"img-holder\">
                                <a href=\"#\" class=\"img-link pattern-small\"></a>
                            </div>
                        </div>
                    </li>
                    <li class=\"item clearfix\">
                        <div class=\"topic-info float-left\">
                            <a class=\"title-link\" href=\"#\">Арматура стеклопластиковая АСП-6</a>
                            <p class=\"text\">Размер 6, <strong class=\"price red-color\">10 <span class=\"icon-rouble\"></span></strong> за п. м.</p>
                        </div>
                        <div class=\"img is-bordered float-right\">
                            <div class=\"img-holder\">
                                <a href=\"#\" class=\"img-link pattern-small\"></a>
                            </div>
                        </div>
                    </li>
                    <li class=\"item clearfix\">
                        <div class=\"topic-info float-left\">
                            <a class=\"title-link\" href=\"#\">Арматура стеклопластиковая АСП-6</a>
                            <p class=\"text\">Размер 6, <strong class=\"price red-color\">10 <span class=\"icon-rouble\"></span></strong> за п. м.</p>
                        </div>
                        <div class=\"img is-bordered float-right\">
                            <div class=\"img-holder\">
                                <a href=\"#\" class=\"img-link pattern-small\"></a>
                            </div>
                        </div>
                    </li>
                    <li class=\"item clearfix\">
                        <div class=\"topic-info float-left\">
                            <a class=\"title-link\" href=\"#\">Арматура стеклопластиковая АСП-6</a>
                            <p class=\"text\">Размер 6, <strong class=\"price red-color\">10 <span class=\"icon-rouble\"></span></strong> за п. м.</p>
                        </div>
                        <div class=\"img is-bordered float-right\">
                            <div class=\"img-holder\">
                                <a href=\"#\" class=\"img-link pattern-small\"></a>
                            </div>
                        </div>
                    </li>
                </ul>
                <a class=\"add-product-text\" href=\"#\">Разместить здесь товары</a>
            </div>

        </div>

    </div>
";
    }

    public function getTemplateName()
    {
        return "@markup/portal/suppliers/all-products.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  482 => 430,  480 => 429,  478 => 428,  130 => 81,  127 => 80,  120 => 8,  117 => 7,  46 => 12,  44 => 7,  40 => 5,  37 => 4,  31 => 3,);
    }
}
