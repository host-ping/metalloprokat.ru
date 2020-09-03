<?php

/* @markup/portal/favorites/favorites-consumers.html.twig */
class __TwigTemplate_f404ef92c4e2f9810e2545eb20185632 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = $this->env->loadTemplate("markup/html/portal/_portal_layout.html.twig");

        $this->blocks = array(
            'title' => array($this, 'block_title'),
            'login' => array($this, 'block_login'),
            'content' => array($this, 'block_content'),
            'tabs' => array($this, 'block_tabs'),
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
        echo "Favorites Consumers";
    }

    // line 4
    public function block_login($context, array $blocks = array())
    {
        // line 5
        echo "    <div class=\"user-block login-user float-right\">
        <div class=\"user-block-holder js-popover-opener clearfix without-company\" data-popover=\"#user-menu\">
            <a href=\"#\" class=\"msgs disabled float-left\" >
                <span class=\"icon-flag float-left\"></span>
            </a>
            <div class=\"user-photo float-left\">
                <a href=\"#\" class=\"img-link link pattern-small ie-radius\"></a>
            </div>
            <span class=\"user-name-wrapper icon-check float-left\">
                <a href=\"#\" class=\"user-name is-gradiented\">Специалист по маркетингу</a>
            </span>

        </div>
        ";
        // line 18
        $this->env->loadTemplate("@markup/portal/partial/user-menu.html.twig")->display(array_merge($context, array("company" => false)));
        // line 19
        echo "
    </div>
";
    }

    // line 22
    public function block_content($context, array $blocks = array())
    {
        // line 23
        echo "    <div id=\"content\" class=\"content-wide float-left\">
        <div class=\"favorites main-title \">
            <h1>Избранные: <a href=\"#\" class=\"current link\">поставщики <sup>32</sup></a>, <a href=\"#\" class=\"link\">потребители <sup>6</sup></a> </h1>
        </div>
        ";
        // line 27
        $this->displayBlock('tabs', $context, $blocks);
        // line 75
        echo "        <div class=\"view-category favorites clearfix\">
            <ul class=\"company\">
                <li class=\"favorite-item clearfix\">
                    <div class=\"notes-form float-left\">
                        <form action=\"#\">
                            <fieldset>
                                <div class=\"note-wrapper is-bordered\">
                                    <div class=\"area-wrapper g-hidden\">
                                        <textarea name=\"note\" id=\"notearea\" class=\"note ie-radius\"> </textarea>
                                        <div class=\"send-button-wrapper\">
                                            <input type=\"submit\" class=\"button send-button gray60-bg ie-radius\" value=\"ok\"/>
                                            <div class=\"loading-mask\">
                                                <div class=\"spinner\"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class=\"text-wrapper g-hidden\">
                                        <span class=\"text\"></span>
                                        <span class=\"date\"></span>
                                    </div>
                                </div>
                            </fieldset>
                        </form>
                    </div>
                    <div class=\"view-company float-left\">
                        <div class=\"holder\">
                            <div class=\"top-block clearfix\">
                                <div class=\"company-logo float-left\">
                                    <a class=\"img-link pattern-small\" href=\"#\">
                                        <img src=\"./markup/pic/product-logo-tmp.jpg\" width=\"64\" height=\"64\" alt=\"image description\"/>
                                    </a>
                                </div>
                                <div class=\"company-info float-left\">
                                    <div class=\"prod-title clearfix\">
                                        <div class=\"star-panel float-right\">
                                            <div class=\"status green-bg float-left ie-radius\">online</div>
                                            <div class=\"rating float-right\">
                                                <span class=\"star-mini icon-star-colored\"></span> ";
        // line 113
        echo "                                                <span class=\"star-mini icon-star-colored\"></span> ";
        // line 114
        echo "                                                <span class=\"star-mini icon-star-colored\"></span> ";
        // line 115
        echo "                                            </div>
                                        </div>
                                        <h3 class=\"title\"><a href=\"#\" class=\"is-gradiented-bottom\">Стальторг</a></h3>
                                    </div>
                                    <p class=\"localization gray60-color\">филиал в Люберцах</p>
                                    <a class=\"site\" href=\"#\">staltorg.ru</a>
                                    <div class=\"contacts\">
                                        <span class=\"phone-text\">+7 (495) 784</span>
                                        ( <a class=\"see\" href=\"#\">показать</a> )
                                        <a class=\"callback js-popup-opener\" href=\"#\" data-popup=\"#callback\">обратный звонок</a>
                                    </div>
                                </div>
                            </div>
                            <ul class=\"links clearfix\">
                                <li class=\"links_report item float-left clearfix\">
                                    <a href=\"#\" class=\"button report is-bordered js-tooltip-opener js-popup-opener ie-radius\" data-tooltip-title=\"Пожаловаться\" data-popup=\"#report\">
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
                                <div id=\"inform-tooltip-1\" class=\"product-informer tooltip grey with-bullet g-hidden\">
                                    <p class=\"t\">Арматура стеклопластиковая АСП-6</p>
                                    <p class=\"i\">Размер 6, <strong class=\"price red-color\">10 <span class=\"icon-rouble\"></span></strong> за погонный метр</p>
                                </div>
                                <div id=\"inform-tooltip-2\" class=\"product-informer tooltip grey with-bullet g-hidden\">
                                    <p class=\"t\">Арматура стеклопластиковая АСП-6</p>
                                    <p class=\"i\">Размер 6, <strong class=\"price red-color\">20 <span class=\"icon-rouble\"></span></strong> за погонный метр</p>
                                </div>
                                <div id=\"inform-tooltip-3\" class=\"product-informer tooltip grey with-bullet g-hidden\">
                                    <p class=\"t\">Арматура стеклопластиковая АСП-6</p>
                                    <p class=\"i\">Размер 6, <strong class=\"price red-color\">20 <span class=\"icon-rouble\"></span></strong> за погонный метр</p>
                                </div>
                                <div id=\"inform-tooltip-4\" class=\"product-informer tooltip grey with-bullet g-hidden\">
                                    <p class=\"t\">Арматура стеклопластиковая АСП-6</p>
                                    <p class=\"i\">Размер 6, <strong class=\"price red-color\">20 <span class=\"icon-rouble\"></span></strong> за погонный метр</p>
                                </div>
                                <div class=\"img-wrapper\">
                                    <div class=\"img is-bordered float-left\">
                                        <div class=\"img-holder\">
                                            <a class=\"img-link pattern-small js-tooltip-opener\" data-tooltip=\"#inform-tooltip-1\" href=\"#\">
                                                <img src=\"./markup/pic/product-logo-small-tmp.jpg\" width=\"40\" height=\"40\" alt=\"image description\"/>
                                            </a>
                                        </div>
                                    </div>
                                    <div class=\"img is-bordered float-left\">
                                        <div class=\"img-holder\">
                                            <a class=\"img-link pattern-small js-tooltip-opener\" data-tooltip=\"#inform-tooltip-2\" href=\"#\">

                                            </a>
                                        </div>
                                    </div>
                                    <div class=\"img is-bordered float-left\">
                                        <div class=\"img-holder\">
                                            <a class=\"img-link pattern-small js-tooltip-opener\" data-tooltip=\"#inform-tooltip-3\" href=\"#\"></a>
                                        </div>
                                    </div>
                                    <div class=\"img is-bordered float-left\">
                                        <div class=\"img-holder\">
                                            <a class=\"img-link pattern-small js-tooltip-opener\" data-tooltip=\"#inform-tooltip-4\" href=\"#\"></a>
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
                <li class=\"favorite-item clearfix\">
                    <div class=\"notes-form float-left\">
                        <form action=\"#\">
                            <fieldset>
                                <div class=\"note-wrapper is-bordered focus\">
                                    <div class=\"area-wrapper g-hidden\">
                                        <textarea name=\"note\" class=\"note ie-radius\">Готовы поставить на след. неделе. Позвонить Максиму</textarea>
                                        <div class=\"send-button-wrapper\">
                                            <input type=\"submit\" class=\"button send-button gray60-bg ie-radius\" value=\"ok\"/>
                                        </div>
                                    </div>
                                    <div class=\"text-wrapper\">
                                        <span class=\"text\">Готовы поставить на след. неделе. Позвонить Максиму</span>
                                        <span class=\"date\">15 мар 2013 20:20</span>
                                    </div>
                                </div>
                            </fieldset>
                        </form>
                    </div>
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
                                                <span class=\"star-mini icon-star hovered\"></span> ";
        // line 238
        echo "                                                <span class=\"star-mini icon-star hovered\"></span> ";
        // line 239
        echo "                                            </div>
                                        </div>
                                        <h3 class=\"title\"><a href=\"#\" class=\"is-gradiented-bottom\">Шестигранник калиброванный</a></h3>
                                    </div>
                                    <p class=\"localization gray60-color\">филиал в Люберцах</p>
                                    <a class=\"site\" href=\"#\">staltorg.ru</a>
                                    <div class=\"contacts\">
                                        <span class=\"phone-text\">+7 (495) 784</span>
                                        ( <a class=\"see\" href=\"#\">показать</a> )
                                        <a class=\"callback js-popup-opener\" href=\"#\" data-popup=\"#callback\">обратный звонок</a>
                                    </div>
                                </div>
                            </div>
                            <ul class=\"links clearfix\">
                                <li class=\"links_report item float-left clearfix\">
                                    <a href=\"#\" class=\"button report is-bordered js-tooltip-opener js-popup-opener ie-radius\" data-tooltip-title=\"Пожаловаться\" data-popup=\"#report\">
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
                            <div class=\"clearfix\">
                                <div class=\"img-wrapper\">
                                    <div class=\"img is-bordered float-left\">
                                        <div class=\"img-holder\">
                                            <a class=\"img-link pattern-small\" href=\"#\"></a>
                                        </div>
                                    </div>
                                    <div class=\"img is-bordered float-left\">
                                        <div class=\"img-holder\">
                                            <a class=\"img-link pattern-small\" href=\"#\"></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <a href=\"#\" class=\"link\">2 избранных товара</a>
                        </div>
                        <a class=\"send-order_send-button button send-button gray60-bg ie-radius\" href=\"#\">Отправить заявку</a>
                    </div>
                    <div class=\"items overflow\"></div>
                </li>
                <li class=\"favorite-item clearfix\">
                    <div class=\"notes-form float-left\">
                        <form action=\"#\">
                            <fieldset>
                                <div class=\"note-wrapper is-bordered focus\">
                                    <div class=\"area-wrapper g-hidden\">
                                        <textarea name=\"note\" class=\"note ie-radius\">Готовы поставить на след. неделе. Позвонить Максиму</textarea>
                                        <div class=\"send-button-wrapper\">
                                            <input type=\"submit\" class=\"button send-button gray60-bg ie-radius\" value=\"ok\"/>
                                            <div class=\"loading-mask\">
                                                <div class=\"spinner\"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class=\"text-wrapper\">
                                        <span class=\"text\">Готовы поставить на след. неделе. Позвонить Максиму</span>
                                        <span class=\"date\">12 мар 2013 18:20</span>
                                    </div>
                                </div>
                            </fieldset>
                        </form>
                    </div>
                    <div class=\"view-company float-left\">
                        <div class=\"holder\">
                            <div class=\"top-block clearfix\">
                                <div class=\"company-logo float-left\">
                                    <a class=\"img-link pattern-small\" href=\"#\">
                                        <img src=\"./markup/pic/product-logo-tmp.jpg\" width=\"64\" height=\"64\" alt=\"image description\"/>
                                    </a>
                                </div>
                                <div class=\"company-info float-left\">
                                    <div class=\"prod-title clearfix\">
                                        <div class=\"star-panel float-right\">
                                            <div class=\"rating float-right\">
                                                <span class=\"star-mini icon-star default\"></span> ";
        // line 331
        echo "                                                <span class=\"star-mini icon-star default\"></span> ";
        // line 332
        echo "                                                <span class=\"star-mini icon-star default\"></span> ";
        // line 333
        echo "                                            </div>
                                        </div>
                                        <h3 class=\"title\"><a href=\"#\" class=\"is-gradiented-bottom\">Стальторг</a></h3>
                                    </div>
                                    <p class=\"localization gray60-color\">филиал в Люберцах</p>
                                    <a class=\"site\" href=\"#\">staltorg.ru</a>
                                    <div class=\"contacts\">
                                        <span class=\"phone-text\">+7 (495) 784</span>
                                        ( <a class=\"see\" href=\"#\">показать</a> )
                                        <a class=\"callback js-popup-opener\" href=\"#\" data-popup=\"#callback\">обратный звонок</a>
                                    </div>
                                </div>
                            </div>
                            <ul class=\"links clearfix\">
                                <li class=\"links_report item float-left clearfix\">
                                    <a href=\"#\" class=\"button report is-bordered js-tooltip-opener js-popup-opener ie-radius\" data-tooltip-title=\"Пожаловаться\" data-popup=\"#report\">
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
                                    <a href=\"#\" class=\"button favorites active-link is-bordered js-togglable-block ie-radius\">
                                        <span class=\"text\">В Избранном</span>
                                        <span class=\"icon-favorite-active float-right\"></span>
                                    </a>
                                    <a href=\"#\" class=\"button favorites delete blue-bg g-hidden js-togglable-block ie-radius\">
                                        <span class=\"text\">Удалить</span>
                                        <span class=\"icon-favorite-del float-right\"></span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class=\"send-order no-result float-left\">
                        <div class=\"holder clearfix\">
                            <p class=\"empty\">Компания еще не загрузила товары</p>
                        </div>
                        <a class=\"send-order_send-button button send-button blue-bg ie-radius\" href=\"#\">Запросить цены</a>
                    </div>
                </li>
                <li class=\"see-more-block-wrapper\">
                    <div class=\"see-more-block\">
                        <a class=\"see-more button\" href=\"#\">показать еще...</a>
                    </div>
                </li>
            </ul>
        </div>
    </div>
";
    }

    // line 27
    public function block_tabs($context, array $blocks = array())
    {
        // line 28
        echo "            <div class=\"favorites_result-tabs-wrapper outline clearfix\">
                <div class=\"tabs favorite-filter float-left\">
                    <ul class=\"list\">
                        <li class=\"item\">
                            <a class=\"link active\" href=\"#\">Все</a>
                        </li>
                        <li class=\"item\">
                            <a class=\"link\" href=\"#\">С пометками</a>
                        </li>
                        <li class=\"item\">
                            <a class=\"link\" href=\"#\">Без пометок</a>
                        </li>
                    </ul>
                </div>
                <div class=\"sort-view float-right clearfix\">
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
                    <div class=\"view-block float-right\">
                        <ul class=\"view-list\">
                            <li class=\"list first js-tooltip-opener active ie-radius\" data-tooltip-title=\"Список\">
                                <span class=\"item icon-view-list\"></span>
                            </li>
                            <li class=\"list pallete js-tooltip-opener disabled ie-radius\" data-tooltip-title=\"Галерея\">
                                <span class=\"item icon-view-grid\"></span>
                            </li>
                            <li class=\"list on-map js-tooltip-opener enable last ie-radius\" data-tooltip-title=\"Карта\" data-tooltip-class=\"right\">
                                <a class=\"item icon-baloon\" href=\"#\"></a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        ";
    }

    public function getTemplateName()
    {
        return "@markup/portal/favorites/favorites-consumers.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  401 => 28,  398 => 27,  340 => 333,  338 => 332,  336 => 331,  243 => 239,  241 => 238,  117 => 115,  115 => 114,  113 => 113,  74 => 75,  66 => 23,  63 => 22,  57 => 19,  55 => 18,  40 => 5,  37 => 4,  31 => 3,  72 => 27,  69 => 10,  61 => 27,  59 => 10,  53 => 6,  50 => 5,  44 => 4,  38 => 3,  32 => 2,);
    }
}
