<?php

/* markup/html/portal/suppliers/companies.html.twig */
class __TwigTemplate_338d9592b8f9d89b308cfa0b16a735fe extends Twig_Template
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
        echo "Companies list";
    }

    // line 5
    public function block_content($context, array $blocks = array())
    {
        // line 6
        echo "    <div id=\"content\" class=\"content-right outline-right float-right\">
    <div class=\"main-title \">
        <h1>Арматура
            ";
        // line 10
        echo "            ";
        // line 11
        echo "            <a
                    href=\"#\" class=\"region-link js-popover-opener icon-check\" data-popover=\"#cities\"
                    data-different-position=\"true\">в Москве и Области</a>

        </h1>
        <ul class=\"filter-city\">
            <li class=\"filter-item\">
                <input name=\"concrete_city\"
                       value=\"1\"
                       id=\"concrete-city-id\"
                       class=\"js-styled-checkbox js-preload-items-count-on-change bg-grey\"
                       type=\"checkbox\"/>
                <label for=\"concrete-city-id\"
                       title=\"Искать только в ";
        // line 24
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["city"]) ? $context["city"] : null), "titleLocative"), "html", null, true);
        echo " (без учета филиалов и доставки)\">Искать только
                    в Москве</label>
            </li>
        </ul>
    </div>
    ";
        // line 29
        $this->displayBlock('tabs', $context, $blocks);
        // line 127
        echo "    <div id=\"company\" class=\"view-category companies\">
    <ul class=\"company list clearfix\">
    <li class=\"company-item clearfix\">
        <div class=\"view-company float-left\">
            <div class=\"holder\">
                <div class=\"top-block clearfix\">
                    <div class=\"company-logo float-left\">
                        <a class=\"img-link pattern-small\" href=\"#\">
                            <img src=\"./markup/pic/product-logo-tmp.jpg\" width=\"64\" height=\"64\"
                                 alt=\"image description\"/>
                        </a>
                        <div class=\"promo-label label company-label top\">
                            <a class=\"label-link\" href=\"#\">Промо</a>
                        </div>
                    </div>
                    <div class=\"company-info float-left\">
                        <div class=\"prod-title clearfix\">
                            <div class=\"star-panel float-right\">

                                <div class=\"status float-left green-bg ie-radius\">online</div>
                                <div class=\"rating float-right\">
                                    <span class=\"star-mini icon-star-colored default\"></span>
                                    <span class=\"star-mini icon-star-colored default\"></span>
                                    <span class=\"star-mini icon-star-colored default\"></span>
                                </div>
                            </div>
                            <div class=\"title\"><a href=\"#\" class=\"is-gradiented-bottom\">Стальторг</a></div>
                        </div>
                        <p class=\"localization gray60-color\">филиал в Люберцах</p>
                        <a class=\"site\" href=\"#\">staltorg.ru</a>

                        <div class=\"contacts clearfix\">
\t\t\t\t\t\t\t\t\t\t<span class=\"phone-text js-hide-phone float-left\">
                                            <span class=\"curr-phone is-gradiented float-left\">+7 (495) 7844565</span>
                                        </span>

                            <a class=\"callback js-popup-opener\" href=\"#\" data-popup=\"#callback\">обратный звонок</a>

                            <div id=\"more-phones-company-1\" class=\"more-phones drop-wrapper opacity-border\">
                                <div class=\"dropdown\">
                                    <span class=\"phone-item\">+7 (495) 7844565</span>
                                    <span class=\"phone-item\">+7 (495) 7844565</span>
                                    <span class=\"phone-item\">+7 (495) 7844565</span>
                                    <span class=\"phone-item\">+7 (495) 7844565</span>
                                    <span class=\"phone-item\">+7 (495) 7844565</span>
                                </div>
                            </div>
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
                </ul>
            </div>
        </div>
        <div class=\"send-order float-left\">
            <div class=\"holder clearfix\">
                <div class=\"clearfix\" style=\"position: relative;\">
                    <div id=\"inform-tooltip-11\" class=\"product-informer tooltip grey with-bullet g-hidden\">
                        <p class=\"t\">Арматура стеклопластиковая АСП-6</p>

                        <p class=\"i\">Размер 6, <strong class=\"price red-color\">10 <span class=\"icon-rouble\"></span></strong> за погонный метр</p>
                    </div>
                    <div id=\"inform-tooltip-12\" class=\"product-informer tooltip grey with-bullet g-hidden\">
                        <p class=\"t\">Арматура стеклопластиковая АСП-6</p>

                        <p class=\"i\">Размер 6, <strong class=\"price red-color\">20 <span class=\"icon-rouble\"></span></strong> за погонный метр</p>
                    </div>
                    <div id=\"inform-tooltip-13\" class=\"product-informer tooltip grey with-bullet g-hidden\">
                        <p class=\"t\">Арматура стеклопластиковая АСП-6</p>

                        <p class=\"i\">Размер 6, <strong class=\"price red-color\">20 <span class=\"icon-rouble\"></span></strong> за погонный метр</p>
                    </div>
                    <div id=\"inform-tooltip-14\" class=\"product-informer tooltip grey with-bullet g-hidden\">
                        <p class=\"t\">Арматура стеклопластиковая АСП-6</p>

                        <p class=\"i\">Размер 6, <strong class=\"price red-color\">20 <span class=\"icon-rouble\"></span></strong> за погонный метр</p>
                    </div>
                    <div class=\"img-wrapper\">
                        <div class=\"img is-bordered float-left\">
                            <div class=\"img-holder\">
                                <a class=\"img-link pattern-small js-tooltip-opener\" data-tooltip=\"#inform-tooltip-11\"
                                   href=\"#\">
                                    <img src=\"./markup/pic/product-logo-small-tmp.jpg\" width=\"40\" height=\"40\"
                                         alt=\"image description\"/>
                                </a>
                            </div>
                        </div>
                        <div class=\"img is-bordered float-left\">
                            <div class=\"img-holder\">
                                <a class=\"img-link pattern-small js-tooltip-opener\" data-tooltip=\"#inform-tooltip-12\"
                                   href=\"#\">

                                </a>
                            </div>
                        </div>
                        <div class=\"img is-bordered float-left\">
                            <div class=\"img-holder\">
                                <a class=\"img-link pattern-small js-tooltip-opener\" data-tooltip=\"#inform-tooltip-13\"
                                   href=\"#\"></a>
                            </div>
                        </div>
                        <div class=\"img is-bordered float-left\">
                            <div class=\"img-holder\">
                                <a class=\"img-link pattern-small js-tooltip-opener\" data-tooltip=\"#inform-tooltip-14\"
                                   href=\"#\"></a>
                            </div>
                        </div>
                        <div class=\"img is-bordered float-left\">
                            <div class=\"img-holder\">
                                <a class=\"img-link js-popup-opener\" data-popup=\"#favorite-product\" href=\"#\">
                                    <span class=\"icon-points\"></span>
                                </a>
                            </div>
                        </div>
                    </div>

                </div>
                <a href=\"#\" class=\"link\">12 избранных товаров</a>
            </div>
            <a class=\"send-order_send-button button send-button red-bg ie-radius\" href=\"#\">Отправить заявку</a>
        </div>
    </li>
    <li class=\"announcement has-announcement\">
        <img src=\"./markup/pic/inside-banner.jpg\" width=\"735\" height=\"90\" alt=\"image description\"/>
    </li>
    <li class=\"special-title\">
        <p class=\"text\">Компании, доставляющие товары в Москву</p>
    </li>
    <li class=\"company-item clearfix\">
        <div class=\"view-company float-left\">
            <div class=\"holder\">
                <div class=\"top-block clearfix\">
                    <div class=\"company-logo float-left\">
                        <a class=\"img-link pattern-small\" href=\"#\"></a>
                    </div>
                    <div class=\"company-info float-left\">
                        <div class=\"prod-title clearfix\">
                            <div class=\"star-panel float-right\">
                                <div class=\"rating float-right\">
                                    <span class=\"star-mini icon-star hovered\"></span>
                                    <span class=\"star-mini icon-star hovered\"></span>
                                </div>
                            </div>
                            <div class=\"title\"><a href=\"#\" class=\"is-gradiented-bottom\">Шестигранник калиброванный
                                    калиброванный калиброванный</a></div>
                        </div>
                        <p class=\"localization gray60-color\">филиал в Люберцах</p>
                        <a class=\"site\" href=\"#\">staltorg.ru</a>

                        <div class=\"contacts clearfix\">
\t\t\t\t\t\t\t\t\t\t<span class=\"phone-text js-hide-phone float-left\">
                                            <span class=\"curr-phone is-gradiented float-left\">+7 (495) 7844565</span>
                                        </span>

                            <a class=\"callback js-popup-opener\" href=\"#\" data-popup=\"#callback\">обратный звонок</a>

                            <div id=\"more-phones-company-2\" class=\"more-phones drop-wrapper opacity-border\">
                                <div class=\"dropdown\">
                                    <span class=\"phone-item\">+7 (495) 7844565</span>
                                    <span class=\"phone-item\">+7 (495) 7844565</span>
                                    <span class=\"phone-item\">+7 (495) 7844565</span>
                                    <span class=\"phone-item\">+7 (495) 7844565</span>
                                    <span class=\"phone-item\">+7 (495) 7844565</span>
                                </div>
                            </div>
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
                </ul>
            </div>
        </div>
        <div class=\"send-order float-left\">
            <div class=\"holder clearfix\">
                <div class=\"product-info float-left\">
                    <div class=\"title\"><a href=\"#\" class=\"small-link\">Арматура строительная</a></div>
                    <p class=\"size gray60-color\">Размер 30x30</p>

                    <p class=\"price gray60-color\">от <strong class=\"count red-color\">25 000 <span class=\"icon-rouble\"></span> </strong>за тонну</p>

                    <div class=\"information\">
                        <span class=\"updated-date\">Обновлено 8 фев 2013</span>
                        <span class=\"dot\">.</span>
                        <a href=\"#\" class=\"all-prod\">Всего 286 товаров</a>
                    </div>
                </div>
                <div class=\"img is-bordered float-right\">
                    <div class=\"img-holder\">
                        <a class=\"img-link pattern-small\" href=\"#\"></a>
                    </div>
                </div>
            </div>
            <a class=\"send-order_send-button button send-button gray60-bg ie-radius\" href=\"#\">Отправить заявку</a>
        </div>
    </li>

    <li class=\"special-title empty-text\">
        <p class=\"text\"></p>
    </li>
    <li class=\"company-item clearfix\">
        <div class=\"view-company float-left\">
            <div class=\"holder\">
                <div class=\"top-block clearfix\">
                    <div class=\"company-logo float-left\">
                        <a class=\"img-link pattern-small\" href=\"#\"></a>
                    </div>
                    <div class=\"company-info float-left\">
                        <div class=\"prod-title clearfix\">
                            <div class=\"star-panel float-right\">
                                <div class=\"rating float-right\">
                                    <span class=\"star-mini icon-star hovered\"></span>
                                    <span class=\"star-mini icon-star hovered\"></span>
                                </div>
                            </div>
                            <div class=\"title\"><a href=\"#\" class=\"is-gradiented-bottom\">Шестигранник калиброванный
                                    калиброванный калиброванный</a></div>
                        </div>
                        <p class=\"localization gray60-color\">филиал в Люберцах</p>
                        <a class=\"site\" href=\"#\">staltorg.ru</a>

                        <div class=\"contacts clearfix\">
\t\t\t\t\t\t\t\t\t\t<span class=\"phone-text js-hide-phone float-left\">
                                            <span class=\"curr-phone is-gradiented float-left\">+7 (495) 7844565</span>
                                        </span>

                            <a class=\"callback js-popup-opener\" href=\"#\" data-popup=\"#callback\">обратный звонок</a>

                            <div id=\"more-phones-company-3\" class=\"more-phones drop-wrapper opacity-border\">
                                <div class=\"dropdown\">
                                    <span class=\"phone-item\">+7 (495) 7844565</span>
                                    <span class=\"phone-item\">+7 (495) 7844565</span>
                                    <span class=\"phone-item\">+7 (495) 7844565</span>
                                    <span class=\"phone-item\">+7 (495) 7844565</span>
                                    <span class=\"phone-item\">+7 (495) 7844565</span>
                                </div>
                            </div>
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
                </ul>
            </div>
        </div>
        <div class=\"send-order float-left\">
            <div class=\"holder clearfix\">
                <div class=\"product-info float-left\">
                    <div class=\"title\"><a href=\"#\" class=\"small-link\">Арматура строительная</a></div>
                    <p class=\"size gray60-color\">Размер 30x30</p>

                    <p class=\"price gray60-color\">от <strong class=\"count red-color\">25 000 <span class=\"icon-rouble\"></span> </strong>за тонну</p>

                    <div class=\"information\">
                        <span class=\"updated-date\">Обновлено 8 фев 2013</span>
                        <span class=\"dot\">.</span>
                        <a href=\"#\" class=\"all-prod\">Всего 286 товаров</a>
                    </div>
                </div>
                <div class=\"img is-bordered float-right\">
                    <div class=\"img-holder\">
                        <a class=\"img-link pattern-small\" href=\"#\"></a>
                    </div>
                </div>
            </div>
            <a class=\"send-order_send-button button send-button gray60-bg ie-radius\" href=\"#\">Отправить заявку</a>
        </div>
    </li>

    <li class=\"special-title\">
        <p class=\"text\">Компании, доставляющие товары в Москву</p>
    </li>
    <li class=\"company-item clearfix\">
        <div class=\"view-company float-left\">
            <div class=\"holder\">
                <div class=\"top-block clearfix\">
                    <div class=\"company-logo float-left\">
                        <a class=\"img-link pattern-small\" href=\"#\"></a>
                    </div>
                    <div class=\"company-info float-left\">
                        <div class=\"prod-title clearfix\">
                            <div class=\"star-panel float-right\">
                                <div class=\"rating float-right\">
                                    <span class=\"star-min -hovered\"></span>
                                    <span class=\"star-mini icon-star hovered\"></span>
                                </div>
                            </div>
                            <div class=\"title\"><a href=\"#\" class=\"is-gradiented-bottom\">Шестигранник калиброванный
                                    калиброванный калиброванный</a></div>
                        </div>
                        <p class=\"localization gray60-color\">филиал в Люберцах</p>
                        <a class=\"site\" href=\"#\">staltorg.ru</a>

                        <div class=\"contacts clearfix\">
\t\t\t\t\t\t\t\t\t\t<span class=\"phone-text js-hide-phone float-left\">
                                            <span class=\"curr-phone is-gradiented float-left\">+7 (495) 7844565</span>
                                        </span>

                            <a class=\"callback js-popup-opener\" href=\"#\" data-popup=\"#callback\">обратный звонок</a>

                            <div id=\"more-phones-company-4\" class=\"more-phones drop-wrapper opacity-border\">
                                <div class=\"dropdown\">
                                    <span class=\"phone-item\">+7 (495) 7844565</span>
                                    <span class=\"phone-item\">+7 (495) 7844565</span>
                                    <span class=\"phone-item\">+7 (495) 7844565</span>
                                    <span class=\"phone-item\">+7 (495) 7844565</span>
                                    <span class=\"phone-item\">+7 (495) 7844565</span>
                                </div>
                            </div>
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
                </ul>
            </div>
        </div>
        <div class=\"send-order float-left\">
            <div class=\"holder clearfix\">
                <div class=\"product-info float-left\">
                    <div class=\"title\"><a href=\"#\" class=\"small-link\">Арматура строительная</a></div>
                    <p class=\"size gray60-color\">Размер 30x30</p>

                    <p class=\"price gray60-color\">от <strong class=\"count red-color\">25 000 <span class=\"icon-rouble\"></span> </strong>за тонну</p>

                    <div class=\"information\">
                        <span class=\"updated-date\">Обновлено 8 фев 2013</span>
                        <span class=\"dot\">.</span>
                        <a href=\"#\" class=\"all-prod\">Всего 286 товаров</a>
                    </div>
                </div>
                <div class=\"img is-bordered float-right\">
                    <div class=\"img-holder\">
                        <a class=\"img-link pattern-small\" href=\"#\"></a>
                    </div>
                </div>
            </div>
            <a class=\"send-order_send-button button send-button gray60-bg ie-radius\" href=\"#\">Отправить заявку</a>
        </div>
    </li>

    <li class=\"announcement has-announcement\">
        <img src=\"./markup/pic/inside-banner.jpg\" width=\"735\" height=\"90\" alt=\"image description\"/>
    </li>
    <li class=\"company-item clearfix\">
        <div class=\"view-company float-left\">
            <div class=\"holder\">
                <div class=\"top-block clearfix\">
                    <div class=\"company-logo float-left\">
                        <a class=\"img-link pattern-small\" href=\"#\"></a>
                    </div>
                    <div class=\"company-info float-left\">
                        <div class=\"prod-title clearfix\">
                            <div class=\"star-panel float-right\">
                                <div class=\"rating float-right\">
                                    <span class=\"star-mini icon-star hovered\"></span>
                                    <span class=\"star-mini icon-star hovered\"></span>
                                </div>
                            </div>
                            <div class=\"title\"><a href=\"#\" class=\"is-gradiented-bottom\">Шестигранник калиброванный
                                    калиброванный калиброванный</a></div>
                        </div>
                        <p class=\"localization gray60-color\">филиал в Люберцах</p>
                        <a class=\"site\" href=\"#\">staltorg.ru</a>

                        <div class=\"contacts clearfix\">
\t\t\t\t\t\t\t\t\t\t<span class=\"phone-text js-hide-phone float-left\">
                                            <span class=\"curr-phone is-gradiented float-left\">+7 (495) 7844565</span>
                                        </span>

                            <a class=\"callback js-popup-opener\" href=\"#\" data-popup=\"#callback\">обратный звонок</a>

                            <div id=\"more-phones-company-5\" class=\"more-phones drop-wrapper opacity-border\">
                                <div class=\"dropdown\">
                                    <span class=\"phone-item\">+7 (495) 7844565</span>
                                    <span class=\"phone-item\">+7 (495) 7844565</span>
                                    <span class=\"phone-item\">+7 (495) 7844565</span>
                                    <span class=\"phone-item\">+7 (495) 7844565</span>
                                    <span class=\"phone-item\">+7 (495) 7844565</span>
                                </div>
                            </div>
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
                </ul>
            </div>
        </div>
        <div class=\"send-order float-left\">
            <div class=\"holder clearfix\">
                <div class=\"product-info float-left\">
                    <div class=\"title\"><a href=\"#\" class=\"small-link\">Арматура строительная</a></div>
                    <p class=\"size gray60-color\">Размер 30x30</p>

                    <p class=\"price gray60-color\">от <strong class=\"count red-color\">25 000 <span class=\"icon-rouble\"></span> </strong>за тонну</p>

                    <div class=\"information\">
                        <span class=\"updated-date\">Обновлено 8 фев 2013</span>
                        <span class=\"dot\">.</span>
                        <a href=\"#\" class=\"all-prod\">Всего 286 товаров</a>
                    </div>
                </div>
                <div class=\"img is-bordered float-right\">
                    <div class=\"img-holder\">
                        <a class=\"img-link pattern-small\" href=\"#\"></a>
                    </div>
                </div>
            </div>
            <a class=\"send-order_send-button button send-button gray60-bg ie-radius\" href=\"#\">Отправить заявку</a>
        </div>
    </li>

    <li class=\"company-item clearfix\">
        <div class=\"view-company float-left\">
            <div class=\"holder\">
                <div class=\"top-block clearfix\">
                    <div class=\"company-logo float-left\">
                        <a class=\"img-link pattern-small\" href=\"#\">
                            <img src=\"./markup/pic/product-logo-tmp.jpg\" width=\"64\" height=\"64\"
                                 alt=\"image description\"/>
                        </a>
                    </div>
                    <div class=\"company-info float-left\">
                        <div class=\"prod-title clearfix\">
                            <div class=\"star-panel float-right\">
                                <div class=\"rating float-right\">
                                    <span class=\"star-mini icon-star default\"></span>
                                    <span class=\"star-mini icon-star default\"></span>
                                    <span class=\"star-mini icon-star default\"></span>
                                </div>
                            </div>
                            <div class=\"title\"><a href=\"#\" class=\"is-gradiented-bottom\">Стальторг</a></div>
                        </div>
                        <p class=\"localization gray60-color\">филиал в Люберцах</p>
                        <a class=\"site\" href=\"#\">staltorg.ru</a>

                        <div class=\"contacts clearfix\">
\t\t\t\t\t\t\t\t\t\t<span class=\"phone-text js-hide-phone float-left\">
                                            <span class=\"curr-phone is-gradiented float-left\">+7 (495) 7844565</span>
                                        </span>

                            <a class=\"callback js-popup-opener\" href=\"#\" data-popup=\"#callback\">обратный звонок</a>

                            <div id=\"more-phones-company-6\" class=\"more-phones drop-wrapper opacity-border\">
                                <div class=\"dropdown\">
                                    <span class=\"phone-item\">+7 (495) 7844565</span>
                                    <span class=\"phone-item\">+7 (495) 7844565</span>
                                    <span class=\"phone-item\">+7 (495) 7844565</span>
                                    <span class=\"phone-item\">+7 (495) 7844565</span>
                                    <span class=\"phone-item\">+7 (495) 7844565</span>
                                </div>
                            </div>
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
                        <a href=\"#\" class=\"button favorites active-link is-bordered ie-radius js-togglable-block\">
                            <span class=\"text\">В Избранном</span>
                            <span class=\"icon-favorite-active float-right\"></span>
                        </a>
                        <a href=\"#\" class=\"button favorites delete blue-bg g-hidden ie-radius js-togglable-block\">
                            <span class=\"text\">Удалить</span>
                            <span class=\"icon-favorite-del float-right\"></span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class=\"send-order no-result float-left\">
            <div class=\"holder clearfix\">
                <div class=\"empty\">Компания торгует арматурой, трубой, квадратом и
                    <a class=\"link js-popover-opener\" data-popover=\"#company-categories-1\">еще 10</a>

                    <div id=\"company-categories-1\" class=\"drop-wrapper company-categories-list opacity-border\">
                        <div class=\"js-scrollable\">
                            <div class=\"dropdown\">
                                <p class=\"black15-color\">металлоконструкцией, крепежными изделиями</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class=\"empty\">
                    Осуществляет доставку в Москву, Екатеринбург, Санкт-Петербург и
                    <a class=\"link js-popover-opener\" data-popover=\"#company-cities-1\">еще 10</a>
                    <div id=\"company-cities-1\" class=\"drop-wrapper company-cities-list opacity-border\">
                        <div class=\"js-scrollable\">
                            <div class=\"dropdown\">
                                <p class=\"black15-color\">Краснодар, Сочи, Анапа, Новороссийск</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <a class=\"send-order_send-button button send-button blue-bg ie-radius\" href=\"#\">Запросить цены</a>
        </div>
    </li>
    <li class=\"see-more-block-wrapper\">
        <div class=\"see-more-block\">
            <a class=\"see-more button ie-radius\" href=\"#\">показать еще...</a>
            <div class=\"loading-mask\">
                <div class=\"spinner\"></div>
            </div>
        </div>

    </li>
    </ul>

    </div>
";
        // line 741
        $this->displayBlock("search_more", $context, $blocks);
        echo "
    </div>
";
    }

    // line 29
    public function block_tabs($context, array $blocks = array())
    {
        // line 30
        echo "        ";
        $context["defaultActiveTab"] = "companies";
        // line 31
        echo "        <div class=\"result-tabs-wrapper outline-right clearfix\">
            <div id=\"tabs\" class=\"tabs float-left\">
                <ul class=\"clearfix\">
                    <li class=\"";
        // line 34
        if ((((array_key_exists("activeTab", $context)) ? (_twig_default_filter((isset($context["activeTab"]) ? $context["activeTab"] : null), (isset($context["defaultActiveTab"]) ? $context["defaultActiveTab"] : null))) : ((isset($context["defaultActiveTab"]) ? $context["defaultActiveTab"] : null))) == "products")) {
            echo " active ie-radius ";
        }
        echo "\">
                        <a class=\"link\" href=\"";
        // line 35
        echo twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('path')->getCallable(), array("portal/suppliers/products.html.twig")), "html", null, true);
        echo "\">Товары</a>
                        <span class=\"count\">2,832</span>
                    </li>
                    <li class=\"";
        // line 38
        if ((((array_key_exists("activeTab", $context)) ? (_twig_default_filter((isset($context["activeTab"]) ? $context["activeTab"] : null), (isset($context["defaultActiveTab"]) ? $context["defaultActiveTab"] : null))) : ((isset($context["defaultActiveTab"]) ? $context["defaultActiveTab"] : null))) == "companies")) {
            echo " active ie-radius ";
        }
        echo "\">
                        <a class=\"link\" href=\"";
        // line 39
        echo twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('path')->getCallable(), array("portal/suppliers/companies.html.twig")), "html", null, true);
        echo "\">Компании</a>
                        <span class=\"count\">37</span>
                    </li>
                    <li>
                        <span class=\"link icon-check black js-popover-opener\" data-popover=\"#more-tabs\">Еще</span>
                        <div id=\"more-tabs\" class=\"drop-wrapper more-tabs opacity-border\">
                            <ul class=\"dropdown clearfix\">
                                <li class=\"drop-item\">
                                    <a href=\"";
        // line 47
        echo twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('path')->getCallable(), array("portal/suppliers/product-catalog/manufactures.html.twig")), "html", null, true);
        echo "\"
                                       class=\"drop-link ";
        // line 48
        if ((((array_key_exists("activeTab", $context)) ? (_twig_default_filter((isset($context["activeTab"]) ? $context["activeTab"] : null), (isset($context["defaultActiveTab"]) ? $context["defaultActiveTab"] : null))) : ((isset($context["defaultActiveTab"]) ? $context["defaultActiveTab"] : null))) == "manufactures")) {
            echo "current";
        }
        echo "\">Производители</a>
                                    <span class=\"count\">12</span>
                                </li>
                                <li class=\"drop-item\">
                                    <a href=\"";
        // line 52
        echo twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('path')->getCallable(), array("portal/suppliers/product-catalog/brands.html.twig")), "html", null, true);
        echo "\"
                                       class=\"drop-link ";
        // line 53
        if ((((array_key_exists("activeTab", $context)) ? (_twig_default_filter((isset($context["activeTab"]) ? $context["activeTab"] : null), (isset($context["defaultActiveTab"]) ? $context["defaultActiveTab"] : null))) : ((isset($context["defaultActiveTab"]) ? $context["defaultActiveTab"] : null))) == "brands")) {
            echo "current";
        }
        echo "\">Бренды</a>
                                    <span class=\"count\">500</span>

                                </li>
                                <li class=\"drop-item\">
                                    <a href=\"";
        // line 58
        echo twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('path')->getCallable(), array("portal/suppliers/product-catalog/products.html.twig")), "html", null, true);
        echo "\"
                                       class=\"drop-link ";
        // line 59
        if ((((array_key_exists("activeTab", $context)) ? (_twig_default_filter((isset($context["activeTab"]) ? $context["activeTab"] : null), (isset($context["defaultActiveTab"]) ? $context["defaultActiveTab"] : null))) : ((isset($context["defaultActiveTab"]) ? $context["defaultActiveTab"] : null))) == "client-products")) {
            echo "current";
        }
        echo "\">Продукты</a>
                                    <span class=\"count\">25789</span>

                                </li>
                            </ul>
                        </div>
                    </li>

                </ul>
            </div>
            ";
        // line 69
        if (twig_in_filter((isset($context["activeTab"]) ? $context["activeTab"] : null), array(0 => "products", 1 => "companies"))) {
            // line 70
            echo "                <div class=\"sort-view float-right clearfix\">
                    ";
            // line 72
            echo "                        ";
            // line 73
            echo "
                        ";
            // line 75
            echo "                            ";
            // line 76
            echo "                                ";
            // line 77
            echo "                                    ";
            // line 78
            echo "                                    ";
            // line 79
            echo "                                    ";
            // line 80
            echo "                                ";
            // line 81
            echo "                                ";
            // line 82
            echo "                                    ";
            // line 83
            echo "                                    ";
            // line 84
            echo "                                       ";
            // line 85
            echo "                                    ";
            // line 86
            echo "                                       ";
            // line 87
            echo "                                ";
            // line 88
            echo "                            ";
            // line 89
            echo "                        ";
            // line 90
            echo "                    ";
            // line 91
            echo "                    <div class=\"order-block float-left\">
                        <a class=\"order-link icon-check black link js-popover-opener\" href=\"#\" data-popover=\"#order\">По
                            рейтингу</a>

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
            ";
        }
        // line 125
        echo "        </div>
    ";
    }

    // line 744
    public function block_sidebar($context, array $blocks = array())
    {
        // line 745
        echo "    <div id=\"sidebar\" class=\"side-left outline-left float-left static\">
    <div class=\"sidebar-content js-fixed-filters\">
    ";
        // line 748
        echo "        ";
        // line 749
        echo "            ";
        // line 750
        echo "                ";
        // line 751
        echo "                    ";
        // line 752
        echo "                        ";
        // line 753
        echo "                        ";
        // line 754
        echo "                            ";
        // line 755
        echo "                                ";
        // line 756
        echo "                            ";
        // line 757
        echo "                            ";
        // line 758
        echo "                                ";
        // line 759
        echo "                            ";
        // line 760
        echo "                            ";
        // line 761
        echo "                                ";
        // line 762
        echo "                            ";
        // line 763
        echo "                            ";
        // line 764
        echo "                                ";
        // line 765
        echo "                            ";
        // line 766
        echo "                            ";
        // line 767
        echo "                                ";
        // line 768
        echo "                            ";
        // line 769
        echo "                            ";
        // line 770
        echo "                                ";
        // line 771
        echo "                            ";
        // line 772
        echo "                            ";
        // line 773
        echo "                                ";
        // line 774
        echo "                            ";
        // line 775
        echo "                            ";
        // line 776
        echo "                                ";
        // line 777
        echo "                            ";
        // line 778
        echo "                            ";
        // line 779
        echo "                                ";
        // line 780
        echo "                            ";
        // line 781
        echo "                            ";
        // line 782
        echo "                                ";
        // line 783
        echo "                            ";
        // line 784
        echo "                            ";
        // line 785
        echo "                                ";
        // line 786
        echo "                            ";
        // line 787
        echo "                            ";
        // line 788
        echo "                                ";
        // line 789
        echo "                            ";
        // line 790
        echo "                            ";
        // line 791
        echo "                                ";
        // line 792
        echo "                            ";
        // line 793
        echo "                        ";
        // line 794
        echo "                    ";
        // line 795
        echo "                ";
        // line 796
        echo "            ";
        // line 797
        echo "
        ";
        // line 799
        echo "        ";
        // line 800
        echo "            ";
        // line 801
        echo "                ";
        // line 802
        echo "                    ";
        // line 803
        echo "                    ";
        // line 804
        echo "                        ";
        // line 805
        echo "                            ";
        // line 806
        echo "                        ";
        // line 807
        echo "                        ";
        // line 808
        echo "                            ";
        // line 809
        echo "                        ";
        // line 810
        echo "                        ";
        // line 811
        echo "                            ";
        // line 812
        echo "                        ";
        // line 813
        echo "                        ";
        // line 814
        echo "                            ";
        // line 815
        echo "                        ";
        // line 816
        echo "                        ";
        // line 817
        echo "                            ";
        // line 818
        echo "                        ";
        // line 819
        echo "                    ";
        // line 820
        echo "                ";
        // line 821
        echo "            ";
        // line 822
        echo "        ";
        // line 823
        echo "
    ";
        // line 825
        echo "    <div class=\"side-wrap js-scrollable-filters js-scrollable\">
    <div class=\"filters\">
    <form action=\"#\">
    <div class=\"local filter\">
        <div class=\"title\"><a href=\"#\" class=\"js-popover-opener icon-check black\" data-popover=\"#cities\"
                              data-different-position=\"true\">Москва и Область</a></div>
        <div class=\"category-title title is-gradiented\">Листовой и плоский прокат</div>
        <ul class=\"product product-categories-list\">
            <li class=\"level-1 item js-expandable-menu-item expanded\" data-expandable-menu-children=\".list\">
                <span class=\"item-link active clearfix js-expandable-menu-expander\" href=\"#armatura\">
                    <span class=\"count float-right\">51</span>
                    <span class=\"elem is-gradiented\">Арматура<i class=\"icon-check black\"></i></span>
                </span>
                <ul class=\"list\">
                    <li class=\"level-inside item\">
                        <a class=\"link clearfix\" href=\"#armatura-1\">
                            <span class=\"count float-right\">31</span>
                            <span class=\"elem is-gradiented\">Арматура А1</span>
                        </a>
                    </li>
                    <li class=\"level-inside item\">
                        <span class=\"link active clearfix\" href=\"#armatura-2\">
                            <span class=\"count float-right\">14</span>
                            <span class=\"elem is-gradiented\">Арматура 09Г2С</span>
                        </span>
                    </li>
                    <li class=\"level-inside item\">
                        <a class=\"link clearfix\" href=\"#armatura-3\">
                            <span class=\"count float-right\">103</span>
                            <span class=\"elem is-gradiented\">Арматура А3</span>
                        </a>
                    </li>
                    <li class=\"level-inside item\">
                        <a class=\"link clearfix\" href=\"#armatura-4\">
                            <span class=\"count float-right\">52</span>
                            <span class=\"elem is-gradiented\">Арматура А500С</span>
                        </a>
                    </li>
                    <li class=\"level-inside item\">
                        <a class=\"link clearfix\" href=\"#armatura-5\">
                            <span class=\"count float-right\">62</span>
                            <span class=\"elem is-gradiented\">Арматура 12</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li class=\"level-1 item js-expandable-menu-item collapsed\" data-expandable-menu-children=\".list\">
                <a class=\"item-link clearfix js-expandable-menu-expander\" href=\"#balka\">
                    <span class=\"count float-right\">60</span>
                    <span class=\"elem is-gradiented\">Балка<i class=\"icon-check black\"></i></span>
                </a>
                <ul class=\"list g-hidden\">
                    <li class=\"level-inside item\">
                        <a class=\"link clearfix\" href=\"#balka-1\">
                            <span class=\"count float-right\">31</span>
                            <span class=\"elem is-gradiented\">Балка Б2</span>
                        </a>
                    </li>
                    <li class=\"level-inside item\">
                        <a class=\"link clearfix\" href=\"#balka-2\">
                            <span class=\"count float-right\">14</span>
                            <span class=\"elem is-gradiented\">Балка Б4</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li class=\"level-1 item\">
                <a class=\"item-link clearfix\" href=\"#\">
                    <span class=\"count float-right\">777</span>
                    <span class=\"elem is-gradiented\">Заглушка</span>
                </a>
            </li>


        </ul>
        <a class=\"link js-expandable js-categories-expand\" href=\"#\" data-expandable=\"categories\">Показать все</a>
        <a class=\"link js-expandable js-categories-expand g-hidden\" href=\"#\" data-expandable=\"categories\">Скрыть</a>
    </div>
    <div class=\"size filter\">
        <div class=\"title\">Размер в 2 колонки</div>
        <div class=\"loading-mask\">
            <div class=\"spinner\"></div>
        </div>
        <ul class=\"size-list list two-column clearfix\">
            <li class=\"filter-item float-left\">
                <input id=\"size-10\" type=\"checkbox\" class=\"  js-styled-checkbox js-show-button-on-change bg-grey\"
                       disabled=\"disabled\"/>
                <label for=\"size-10\"><a href=\"#\" class=\"link\">10</a></label>
            </li>
            <li class=\"filter-item float-left\">
                <input id=\"size-12\" type=\"checkbox\" class=\"  js-styled-checkbox js-show-button-on-change bg-grey\"/>
                <label for=\"size-12\"><a href=\"#\" class=\"link\">12</a></label>
            </li>
            <li class=\"filter-item float-left\">
                <input id=\"size-14\" type=\"checkbox\" class=\"  js-styled-checkbox js-show-button-on-change bg-grey\"/>
                <label for=\"size-14\"><a href=\"#\" class=\"link\">14</a></label>
            </li>
            <li class=\"filter-item float-left\">
                <input id=\"size-16\" type=\"checkbox\" class=\"  js-styled-checkbox js-show-button-on-change bg-grey\"/>
                <label for=\"size-16\">16</label>
            </li>
            <li class=\"filter-item float-left\">
                <input id=\"size-20\" type=\"checkbox\" class=\" js-styled-checkbox js-show-button-on-change bg-grey\"/>
                <label for=\"size-20\">20</label>
            </li>
            <li class=\"filter-item float-left\">
                <input id=\"size-24\" type=\"checkbox\" checked=\"checked\"
                       class=\" js-styled-checkbox js-show-button-on-change bg-grey\"/>
                <label for=\"size-24\">24</label>
            </li>
            <li class=\"filter-item float-left\">
                <input id=\"size-36\" type=\"checkbox\" checked=\"checked\"
                       class=\" js-styled-checkbox js-show-button-on-change bg-grey\"/>
                <label for=\"size-36\">36</label>
            </li>
            <li class=\"filter-item float-left\">
                <input id=\"size-48\" type=\"checkbox\" class=\" js-styled-checkbox js-show-button-on-change bg-grey\"/>
                <label for=\"size-48\">48</label>
            </li>
            <li class=\"filter-item float-left\">
                <input id=\"size-54\" type=\"checkbox\" class=\" js-styled-checkbox js-show-button-on-change bg-grey\"/>
                <label for=\"size-54\">54</label>
            </li>
            <li class=\"filter-item g-hidden float-left\" data-expandable-section=\"size\">
                <input id=\"size-55\" type=\"checkbox\" class=\" js-styled-checkbox js-show-button-on-change bg-grey\"/>
                <label for=\"size-55\">55</label>
            </li>
            <li class=\"filter-item g-hidden float-left\" data-expandable-section=\"size\">
                <input id=\"size-56\" type=\"checkbox\" class=\" js-styled-checkbox js-show-button-on-change bg-grey\"/>
                <label for=\"size-56\">56</label>
            </li>
            <li class=\"filter-item g-hidden float-left\" data-expandable-section=\"size\">
                <input id=\"size-57\" type=\"checkbox\" class=\" js-styled-checkbox js-show-button-on-change bg-grey\"/>
                <label for=\"size-57\">57</label>
            </li>
            <li class=\"filter-item g-hidden float-left\" data-expandable-section=\"size\">
                <input id=\"size-58\" type=\"checkbox\" class=\" js-styled-checkbox js-show-button-on-change bg-grey\"/>
                <label for=\"size-58\">58</label>
            </li>
        </ul>
        <a class=\"link js-expandable js-categories-expand\" href=\"#\" data-expandable=\"size\">Показать все</a>
        <a class=\"link js-expandable js-categories-expand g-hidden\" href=\"#\" data-expandable=\"size\">Скрыть</a>
    </div>
    <div class=\"size filter\">
        <div class=\"title\">Размер</div>
        <ul class=\"size-list list three-column clearfix\">
            <li class=\"filter-item float-left\">
                <input id=\"size-10\" type=\"checkbox\" class=\" js-styled-checkbox js-show-button-on-change bg-grey\"/>
                <label for=\"size-10\"><a href=\"#\" class=\"link\">10</a></label>
            </li>
            <li class=\"filter-item float-left\">
                <input id=\"size-12\" type=\"checkbox\" class=\" js-styled-checkbox js-show-button-on-change bg-grey\"/>
                <label for=\"size-12\"><a href=\"#\" class=\"link\">12</a></label>
            </li>
            <li class=\"filter-item float-left\">
                <input id=\"size-14\" type=\"checkbox\" class=\" js-styled-checkbox js-show-button-on-change bg-grey\"/>
                <label for=\"size-14\"><a href=\"#\" class=\"link\">14</a></label>
            </li>
            <li class=\"filter-item float-left\">
                <input id=\"size-16\" type=\"checkbox\" class=\" js-styled-checkbox js-show-button-on-change bg-grey\"/>
                <label for=\"size-16\">16</label>
            </li>
            <li class=\"filter-item float-left\">
                <input id=\"size-20\" type=\"checkbox\" class=\" js-styled-checkbox js-show-button-on-change bg-grey\"/>
                <label for=\"size-20\">20</label>
            </li>
            <li class=\"filter-item float-left\">
                <input id=\"size-24\" type=\"checkbox\" checked=\"checked\"
                       class=\" js-styled-checkbox js-show-button-on-change bg-grey\"/>
                <label for=\"size-24\">24</label>
            </li>
            <li class=\"filter-item float-left\">
                <input id=\"size-36\" type=\"checkbox\" checked=\"checked\"
                       class=\" js-styled-checkbox js-show-button-on-change bg-grey\" disabled=\"disabled\"/>
                <label for=\"size-36\">36</label>
            </li>
            <li class=\"filter-item float-left\">
                <input id=\"size-48\" type=\"checkbox\" class=\" js-styled-checkbox js-show-button-on-change bg-grey\"/>
                <label for=\"size-48\">48</label>
            </li>
            <li class=\"filter-item float-left\">
                <input id=\"size-54\" type=\"checkbox\" class=\" js-styled-checkbox js-show-button-on-change bg-grey\"/>
                <label for=\"size-54\">54</label>
            </li>
            <li class=\"filter-item g-hidden float-left\" data-expandable-section=\"size\">
                <input id=\"size-55\" type=\"checkbox\" class=\" js-styled-checkbox js-show-button-on-change bg-grey\"/>
                <label for=\"size-55\">55</label>
            </li>
            <li class=\"filter-item g-hidden float-left\" data-expandable-section=\"size\">
                <input id=\"size-56\" type=\"checkbox\" class=\" js-styled-checkbox js-show-button-on-change bg-grey\"/>
                <label for=\"size-56\">56</label>
            </li>
            <li class=\"filter-item g-hidden float-left\" data-expandable-section=\"size\">
                <input id=\"size-57\" type=\"checkbox\" class=\" js-styled-checkbox js-show-button-on-change bg-grey\"/>
                <label for=\"size-57\">57</label>
            </li>
            <li class=\"filter-item g-hidden float-left\" data-expandable-section=\"size\">
                <input id=\"size-58\" type=\"checkbox\" class=\" js-styled-checkbox js-show-button-on-change bg-grey\"/>
                <label for=\"size-58\">58</label>
            </li>
        </ul>
        <a class=\"link js-expandable js-categories-expand\" href=\"#\" data-expandable=\"size\">Показать все</a>
        <a class=\"link js-expandable js-categories-expand g-hidden\" href=\"#\" data-expandable=\"size\">Скрыть</a>
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
        <ul class=\"type-list list\">
            <li class=\"filter-item\">
                <input id=\"type-1\" class=\" js-styled-checkbox js-show-button-on-change bg-grey\" type=\"checkbox\"/>
                <label for=\"type-1\">Производство</label>
            </li>
            <li class=\"filter-item\">
                <input id=\"type-2\" class=\" js-styled-checkbox js-show-button-on-change bg-grey\" type=\"checkbox\"/>
                <label for=\"type-2\">Резка</label>
            </li>
            <li class=\"filter-item\">
                <input id=\"type-3\" class=\" js-styled-checkbox js-show-button-on-change bg-grey\" type=\"checkbox\"/>
                <label for=\"type-3\">Гибка</label>
            </li>
            <li class=\"filter-item\">
                <input id=\"type-4\" class=\" js-styled-checkbox js-show-button-on-change bg-grey\" type=\"checkbox\"/>
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
        <div class=\"text-wrapper\">
            <p class=\"text\">
                В каталоге можно сравнить цены на молоко и выбрать компанию-поставщика для того, чтобы купить молоко оптом в Екатеринбурге.
            </p>
        </div>
    </div>
    </div>
    <div class=\"submit-wrapper js-show-all g-hidden\">
        <a class=\"button show-btn link blue-bg clearfix disabled ie-radius\">
            <span class=\"text float-left\">показать</span>
            <span class=\"count float-right\">58</span>
        </a>
    </div>
    </div>
    </div>
";
    }

    public function getTemplateName()
    {
        return "markup/html/portal/suppliers/companies.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  1023 => 825,  1020 => 823,  1018 => 822,  1016 => 821,  1014 => 820,  1012 => 819,  1010 => 818,  1008 => 817,  1006 => 816,  1004 => 815,  1002 => 814,  1000 => 813,  998 => 812,  996 => 811,  994 => 810,  992 => 809,  990 => 808,  988 => 807,  986 => 806,  984 => 805,  982 => 804,  980 => 803,  978 => 802,  976 => 801,  974 => 800,  972 => 799,  969 => 797,  967 => 796,  965 => 795,  963 => 794,  961 => 793,  959 => 792,  957 => 791,  955 => 790,  953 => 789,  951 => 788,  949 => 787,  947 => 786,  945 => 785,  943 => 784,  941 => 783,  939 => 782,  937 => 781,  935 => 780,  933 => 779,  931 => 778,  929 => 777,  927 => 776,  925 => 775,  923 => 774,  921 => 773,  919 => 772,  917 => 771,  915 => 770,  913 => 769,  911 => 768,  909 => 767,  907 => 766,  905 => 765,  903 => 764,  901 => 763,  899 => 762,  897 => 761,  895 => 760,  893 => 759,  891 => 758,  889 => 757,  887 => 756,  885 => 755,  883 => 754,  881 => 753,  879 => 752,  877 => 751,  875 => 750,  873 => 749,  871 => 748,  867 => 745,  864 => 744,  859 => 125,  823 => 91,  821 => 90,  819 => 89,  817 => 88,  815 => 87,  813 => 86,  811 => 85,  809 => 84,  807 => 83,  805 => 82,  803 => 81,  801 => 80,  799 => 79,  797 => 78,  795 => 77,  793 => 76,  791 => 75,  788 => 73,  786 => 72,  783 => 70,  781 => 69,  766 => 59,  762 => 58,  752 => 53,  748 => 52,  739 => 48,  735 => 47,  724 => 39,  718 => 38,  712 => 35,  706 => 34,  701 => 31,  698 => 30,  695 => 29,  688 => 741,  72 => 127,  70 => 29,  62 => 24,  47 => 11,  45 => 10,  40 => 6,  1356 => 18,  1353 => 17,  1350 => 16,  1343 => 1301,  60 => 20,  58 => 16,  46 => 6,  43 => 5,  37 => 5,  31 => 3,);
    }
}
