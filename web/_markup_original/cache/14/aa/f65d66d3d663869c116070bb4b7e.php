<?php

/* @markup/mini-site/product-list.html.twig */
class __TwigTemplate_14aaf65d66d3d663869c116070bb4b7e extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = $this->env->loadTemplate("@markup/mini-site/_mini_layout.html.twig");

        $this->blocks = array(
            'title' => array($this, 'block_title'),
            'content' => array($this, 'block_content'),
            'filters' => array($this, 'block_filters'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "@markup/mini-site/_mini_layout.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 2
    public function block_title($context, array $blocks = array())
    {
        echo "Mini site - Product list";
    }

    // line 3
    public function block_content($context, array $blocks = array())
    {
        // line 4
        echo "
    <div id=\"content\" class=\"content table-cell\">
    <div class=\"company-menu clearfix\">
        <a class=\"admin-button button gray60-bg float-right ie-radius\" href=\"";
        // line 7
        echo twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('path')->getCallable(), array("private/mini-site/colors.html.twig")), "html", null, true);
        echo "\">поменять цвета</a>
        <ul class=\"list\">
            <li class=\"item\">
                <a class=\"link\" href=\"";
        // line 10
        echo twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('path')->getCallable(), array("mini-site/about.html.twig")), "html", null, true);
        echo "\">О компании</a>
            </li>
            <li class=\"item\">
                <a href=\"";
        // line 13
        echo twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('path')->getCallable(), array("mini-site/reviews.html.twig")), "html", null, true);
        echo "\" class=\"link\">Отзывы</a>
            </li>
            <li class=\"item\">
                <a href=\"";
        // line 16
        echo twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('path')->getCallable(), array("mini-site/documents.html.twig")), "html", null, true);
        echo "\" class=\"link\">Документы</a>
            </li>
            <li class=\"item\">
                <a href=\"";
        // line 19
        echo twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('path')->getCallable(), array("mini-site/contacts.html.twig")), "html", null, true);
        echo "\" class=\"link\">Контакты</a>
            </li>
        </ul>
    </div>
        ";
        // line 23
        $this->displayBlock('filters', $context, $blocks);
        // line 224
        echo "        <h1 class=\"minisite-title product-list-title\">Арматура</h1>
        <ul class=\"product similar-list\">
            <li class=\"item clearfix\">
                <div class=\"img is-bordered float-right\">
                    <div class=\"img-holder\">
                        <a class=\"img-link pattern-small\" href=\"#\"></a>
                    </div>
                </div>
                <div class=\"links-wrapper float-right\">
                    <ul class=\"links clearfix\">
                        <li class=\"links_report item float-left clearfix\">
                            <a href=\"#\" class=\"button report light is-bordered js-tooltip-opener js-popup-opener ie-radius\" data-tooltip-title=\"Пожаловаться\" data-popup=\"#report\">
                                <span class=\"icon-complaint\"></span>
                            </a>
                        </li>
                        <li class=\"item float-left\">
                            <a href=\"#\" class=\"button favorites small light is-bordered ie-radius\">
                                <span class=\"icon-favorite\"></span>
                            </a>
                        </li>
                        <li class=\"links_answer item width-182 float-left clearfix\">
                            <a class=\"primary button send-button ie-radius\" href=\"#\">Отправить заявку</a>
                        </li>
                    </ul>
                </div>
                <div class=\"left\">
                    <p class=\"title is-gradiented\">
                        <a class=\"product-link link\" href=\"#\">Арматура стеклопластиковая АСП-6 Арматура стеклопластиковая АСП-6 Арматура стеклопластиковая АСП-6 Арматура стеклопластиковая АСП-6</a>
                    </p>
                    <div class=\"info\">
                        <p class=\"text\">Размер 6, <strong class=\"price\">10 <span class=\"icon-rouble\"></span></strong> за погонный метр</p>
                    </div>
                </div>

            </li>
            <li class=\"item clearfix\">
                <div class=\"img is-bordered float-right\">
                    <div class=\"img-holder\">
                        <a class=\"img-link pattern-small\" href=\"#\"></a>
                    </div>
                </div>
                <div class=\"links-wrapper float-right\">
                    <ul class=\"links clearfix\">
                        <li class=\"links_report item float-left clearfix\">
                            <a href=\"#\" class=\"button report light is-bordered js-tooltip-opener js-popup-opener ie-radius\" data-tooltip-title=\"Пожаловаться\" data-popup=\"#report\">
                                <span class=\"icon-complaint\"></span>
                            </a>
                        </li>
                        <li class=\"item float-left\">
                            <a href=\"#\" class=\"button favorites small light is-bordered ie-radius\">
                                <span class=\"icon-favorite\"></span>
                            </a>
                        </li>
                        <li class=\"links_answer item width-182 float-left clearfix\">
                            <a class=\"primary button send-button ie-radius\" href=\"#\">Отправить заявку</a>
                        </li>
                    </ul>
                </div>
                <div class=\"left\">
                    <p class=\"title is-gradiented\">
                        <a class=\"product-link link\" href=\"#\">Арматура стеклопластиковая АСП-6</a>
                    </p>
                    <div class=\"info\">
                        <p class=\"text\">Размер 6, <strong class=\"price\">10 <span class=\"icon-rouble\"></span></strong> за погонный метр</p>
                    </div>
                </div>

            </li>
            <li class=\"item clearfix\">
                <div class=\"img is-bordered float-right\">
                    <div class=\"img-holder\">
                        <a class=\"img-link pattern-small\" href=\"#\"></a>
                    </div>
                </div>
                <div class=\"links-wrapper float-right\">
                    <ul class=\"links clearfix\">
                        <li class=\"links_report item float-left clearfix\">
                            <a href=\"#\" class=\"button report light is-bordered js-tooltip-opener js-popup-opener ie-radius\" data-tooltip-title=\"Пожаловаться\" data-popup=\"#report\">
                                <span class=\"icon-complaint\"></span>
                            </a>
                        </li>
                        <li class=\"item float-left\">
                            <a href=\"#\" class=\"button favorites small light is-bordered ie-radius\">
                                <span class=\"icon-favorite\"></span>
                            </a>
                        </li>
                        <li class=\"links_answer item width-182 float-left clearfix\">
                            <a class=\"primary button send-button ie-radius\" href=\"#\">Отправить заявку</a>
                        </li>
                    </ul>
                </div>
                <div class=\"left\">
                    <p class=\"title is-gradiented\">
                        <a class=\"product-link link\" href=\"#\">Арматура стеклопластиковая АСП-6</a>
                    </p>
                    <div class=\"info\">
                        <p class=\"text\">Размер 6, <strong class=\"price\">10 <span class=\"icon-rouble\"></span></strong> за погонный метр</p>
                    </div>
                </div>

            </li>
            <li class=\"item announcement clearfix\">
                ";
        // line 327
        echo "            </li>
            <li class=\"item clearfix\">
                <div class=\"img is-bordered float-right\">
                    <div class=\"img-holder\">
                        <a class=\"img-link pattern-small\" href=\"#\"></a>
                    </div>
                </div>
                <div class=\"links-wrapper float-right\">
                    <ul class=\"links clearfix\">
                        <li class=\"links_report item float-left clearfix\">
                            <a href=\"#\" class=\"button report light is-bordered js-tooltip-opener js-popup-opener ie-radius\" data-tooltip-title=\"Пожаловаться\" data-popup=\"#report\">
                                <span class=\"icon-complaint\"></span>
                            </a>
                        </li>
                        <li class=\"item float-left\">
                            <a href=\"#\" class=\"button favorites small light is-bordered ie-radius\">
                                <span class=\"icon-favorite\"></span>
                            </a>
                        </li>
                        <li class=\"links_answer item width-182 float-left clearfix\">
                            <a class=\"primary button send-button ie-radius\" href=\"#\">Отправить заявку</a>
                        </li>
                    </ul>
                </div>
                <div class=\"left\">
                    <p class=\"title is-gradiented\">
                        <a class=\"product-link link\" href=\"#\">Арматура стеклопластиковая АСП-6</a>
                    </p>
                    <div class=\"info\">
                        <p class=\"text\">Размер 6, <strong class=\"price\">10 <span class=\"icon-rouble\"></span></strong> за погонный метр</p>
                    </div>
                </div>

            </li>
            <li class=\"item clearfix\">
                <div class=\"img is-bordered float-right\">
                    <div class=\"img-holder\">
                        <a class=\"img-link pattern-small\" href=\"#\"></a>
                    </div>
                </div>
                <div class=\"links-wrapper float-right\">
                    <ul class=\"links clearfix\">
                        <li class=\"links_report item float-left clearfix\">
                            <a href=\"#\" class=\"button report light is-bordered js-tooltip-opener js-popup-opener ie-radius\" data-tooltip-title=\"Пожаловаться\" data-popup=\"#report\">
                                <span class=\"icon-complaint\"></span>
                            </a>
                        </li>
                        <li class=\"item float-left\">
                            <a href=\"#\" class=\"button favorites small light is-bordered ie-radius\">
                                <span class=\"icon-favorite\"></span>
                            </a>
                        </li>
                        <li class=\"links_answer item width-182 float-left clearfix\">
                            <a class=\"primary button send-button ie-radius\" href=\"#\">Отправить заявку</a>
                        </li>
                    </ul>
                </div>
                <div class=\"left\">
                    <p class=\"title is-gradiented\">
                        <a class=\"product-link link\" href=\"#\">Арматура стеклопластиковая АСП-6</a>
                    </p>
                    <div class=\"info\">
                        <p class=\"text\">Размер 6, <strong class=\"price\">10 <span class=\"icon-rouble\"></span></strong> за погонный метр</p>
                    </div>
                </div>

            </li>
            <li class=\"item clearfix\">
                <div class=\"img is-bordered float-right\">
                    <div class=\"img-holder\">
                        <a class=\"img-link pattern-small\" href=\"#\"></a>
                    </div>
                </div>
                <div class=\"links-wrapper float-right\">
                    <ul class=\"links clearfix\">
                        <li class=\"links_report item float-left clearfix\">
                            <a href=\"#\" class=\"button report light is-bordered js-tooltip-opener js-popup-opener ie-radius\" data-tooltip-title=\"Пожаловаться\" data-popup=\"#report\">
                                <span class=\"icon-complaint\"></span>
                            </a>
                        </li>
                        <li class=\"item float-left\">
                            <a href=\"#\" class=\"button favorites small light is-bordered ie-radius\">
                                <span class=\"icon-favorite\"></span>
                            </a>
                        </li>
                        <li class=\"links_answer item width-182 float-left clearfix\">
                            <a class=\"primary button send-button ie-radius\" href=\"#\">Отправить заявку</a>
                        </li>
                    </ul>
                </div>
                <div class=\"left\">
                    <p class=\"title is-gradiented\">
                        <a class=\"product-link link\" href=\"#\">Арматура стеклопластиковая АСП-6</a>
                    </p>
                    <div class=\"info\">
                        <p class=\"text\">Размер 6, <strong class=\"price\">10 <span class=\"icon-rouble\"></span></strong> за погонный метр</p>
                    </div>
                </div>

            </li>
            <li class=\"item announcement has-announcement clearfix\">
                <img src=\"./markup/pic/inside-banner.jpg\" width=\"735\" height=\"90\" alt=\"image description\">
            </li>
            <li class=\"item clearfix\">
                <div class=\"img is-bordered float-right\">
                    <div class=\"img-holder\">
                        <a class=\"img-link pattern-small\" href=\"#\"></a>
                    </div>
                </div>
                <div class=\"links-wrapper float-right\">
                    <ul class=\"links clearfix\">
                        <li class=\"links_report item float-left clearfix\">
                            <a href=\"#\" class=\"button report light is-bordered js-tooltip-opener js-popup-opener ie-radius\" data-tooltip-title=\"Пожаловаться\" data-popup=\"#report\">
                                <span class=\"icon-complaint\"></span>
                            </a>
                        </li>
                        <li class=\"item float-left\">
                            <a href=\"#\" class=\"button favorites small light is-bordered ie-radius\">
                                <span class=\"icon-favorite\"></span>
                            </a>
                        </li>
                        <li class=\"links_answer item width-182 float-left clearfix\">
                            <a class=\"primary button send-button ie-radius\" href=\"#\">Отправить заявку</a>
                        </li>
                    </ul>
                </div>
                <div class=\"left\">
                    <p class=\"title is-gradiented\">
                        <a class=\"product-link link\" href=\"#\">Арматура стеклопластиковая АСП-6</a>
                    </p>
                    <div class=\"info\">
                        <p class=\"text\">Размер 6, <strong class=\"price\">10 <span class=\"icon-rouble\"></span></strong> за погонный метр</p>
                    </div>
                </div>
            </li>
            <li class=\"see-more-block-wrapper\">
                <div class=\"see-more-block\">
                    <a class=\"see-more button js-load-more ie-radius\"
                       href=\"#\">Показать еще...</a>
                    <div class=\"loading-mask g-hidden\">
                        <div class=\"spinner\"></div>
                    </div>
                </div>

            </li>
        </ul>
    </div>
";
    }

    // line 23
    public function block_filters($context, array $blocks = array())
    {
        // line 24
        echo "            <div class=\"filters-block clearfix\">
                <div class=\"search-wrapper float-left\">
                    <form action=\"#\">
                        <fieldset>
                            <input class=\"search-field form-text ie-radius\" type=\"text\" placeholder=\"Поиск\"/>
                            <button class=\"icon-search-small search-button\" type=\"submit\">

                            </button>
                        </fieldset>
                    </form>
                </div>
                <div class=\"product-parameters table-container float-left\">
                    <div class=\"table-cell\">
                        <div class=\"product-link\">
                            <span class=\"link js-popover-opener clickable clearfix\" data-popover=\"#parameter1\">
                                <span class=\"name float-left\">Тип</span>
                                <span class=\"icon-check black float-left\"></span>
                            </span>
                            <span class=\"is-gradiented\"></span>

                            <div id=\"parameter1\" class=\"drop-wrapper popover-block views-list opacity-border\">
                                <ul class=\"dropdown\">
                                    <li class=\"drop-item first\">
                                        <span class=\"drop-link current\">Параметр 1</span>
                                    </li>
                                    <li class=\"drop-item\">
                                        <a href=\"#\" class=\"drop-link\">Параметр 2</a>
                                    </li>
                                    <li class=\"drop-item\">
                                        <a href=\"#\" class=\"drop-link\">Параметр 3</a>
                                    </li>
                                    <li class=\"drop-item\">
                                        <a href=\"#\" class=\"drop-link\">Параметр 4</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class=\"table-cell\">
                        <div class=\"product-link\">
                            <span class=\"link js-popover-opener clickable clearfix\" data-popover=\"#parameter2\">
                                <span class=\"name float-left\">Марка</span>
                                <span class=\"icon-check black float-left\"></span>
                            </span>
                            <span class=\"is-gradiented\"></span>

                            <div id=\"parameter2\" class=\"drop-wrapper popover-block views-list opacity-border\">
                                <ul class=\"dropdown\">
                                    <li class=\"drop-item first\">
                                        <span class=\"drop-link current\">Параметр 1</span>
                                    </li>
                                    <li class=\"drop-item\">
                                        <a href=\"#\" class=\"drop-link\">Параметр 2</a>
                                    </li>
                                    <li class=\"drop-item\">
                                        <a href=\"#\" class=\"drop-link\">Параметр 3</a>
                                    </li>
                                    <li class=\"drop-item\">
                                        <a href=\"#\" class=\"drop-link\">Параметр 4</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class=\"table-cell\">
                        <div class=\"product-link\">
                            <span class=\"link js-popover-opener clickable clearfix\" data-popover=\"#parameter3\">
                                <span class=\"name float-left\">ГОСТ</span>
                                <span class=\"icon-check black float-left\"></span>
                            </span>
                            <span class=\"is-gradiented\"></span>

                            <div id=\"parameter3\" class=\"drop-wrapper popover-block views-list opacity-border\">
                                <ul class=\"dropdown\">
                                    <li class=\"drop-item first\">
                                        <span class=\"drop-link current\">Параметр 1</span>
                                    </li>
                                    <li class=\"drop-item\">
                                        <a href=\"#\" class=\"drop-link\">Параметр 2</a>
                                    </li>
                                    <li class=\"drop-item\">
                                        <a href=\"#\" class=\"drop-link\">Параметр 3</a>
                                    </li>
                                    <li class=\"drop-item\">
                                        <a href=\"#\" class=\"drop-link\">Параметр 4</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class=\"table-cell\">
                        <div class=\"product-link\">
                            <span class=\"link js-popover-opener clickable clearfix\" data-popover=\"#parameter4\">
                                <span class=\"name float-left\">Вид</span>
                                <span class=\"icon-check black float-left\"></span>
                            </span>
                            <span class=\"is-gradiented\"></span>

                            <div id=\"parameter4\" class=\"drop-wrapper popover-block views-list opacity-border\">
                                <ul class=\"dropdown\">
                                    <li class=\"drop-item first\">
                                        <span class=\"drop-link current\">Параметр 1</span>
                                    </li>
                                    <li class=\"drop-item\">
                                        <a href=\"#\" class=\"drop-link\">Параметр 2</a>
                                    </li>
                                    <li class=\"drop-item\">
                                        <a href=\"#\" class=\"drop-link\">Параметр 3</a>
                                    </li>
                                    <li class=\"drop-item\">
                                        <a href=\"#\" class=\"drop-link\">Параметр 4</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class=\"table-cell\">
                        <div class=\"product-link\">
                            <span class=\"link js-popover-opener clickable clearfix\" data-popover=\"#parameter5\">
                                <span class=\"name float-left\">Размер</span>
                                <span class=\"icon-check black float-left\"></span>
                            </span>
                            <span class=\"is-gradiented\"></span>

                            <div id=\"parameter5\" class=\"drop-wrapper popover-block views-list opacity-border\">
                                <ul class=\"dropdown\">
                                    <li class=\"drop-item first\">
                                        <span class=\"drop-link current\">Параметр 1</span>
                                    </li>
                                    <li class=\"drop-item\">
                                        <a href=\"#\" class=\"drop-link\">Параметр 2</a>
                                    </li>
                                    <li class=\"drop-item\">
                                        <a href=\"#\" class=\"drop-link\">Параметр 3</a>
                                    </li>
                                    <li class=\"drop-item\">
                                        <a href=\"#\" class=\"drop-link\">Параметр 4</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class=\"sort-view float-right\">
                    <div class=\"location block float-left\">
                        <span class=\" link js-popover-opener icon-check black clickable\" data-popover=\"#city\" data-index=\"1001\">Москва</span>
                        <div id=\"city\" class=\"drop-wrapper popover-block city-list opacity-border\">
                            <ul class=\"dropdown\">
                                <li class=\"drop-item first\">
                                    <span class=\"drop-link current is-gradiented\">Москва</span>
                                </li>
                                <li class=\"drop-item\">
                                    <a href=\"#\" class=\"drop-link is-gradiented\">Днепропетровск</a>
                                </li>
                                <li class=\"drop-item\">
                                    <a href=\"#\" class=\"drop-link is-gradiented\">Санкт-Петербург</a>
                                </li>
                                <li class=\"drop-item\">
                                    <a href=\"#\" class=\"drop-link is-gradiented\">Ростов-на-Дону</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    ";
        // line 189
        echo "                        ";
        // line 190
        echo "                        ";
        // line 191
        echo "                            ";
        // line 192
        echo "                                ";
        // line 193
        echo "                                    ";
        // line 194
        echo "                                    ";
        // line 195
        echo "                                    ";
        // line 196
        echo "                                ";
        // line 197
        echo "                                ";
        // line 198
        echo "                                    ";
        // line 199
        echo "                                    ";
        // line 200
        echo "                                    ";
        // line 201
        echo "                                ";
        // line 202
        echo "                            ";
        // line 203
        echo "                        ";
        // line 204
        echo "                    ";
        // line 205
        echo "                    <div class=\"order-block block float-left\">
                        <a class=\"order-link link icon-check black link js-popover-opener\" href=\"#\" data-popover=\"#order\">По рейтингу</a>
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
        ";
    }

    public function getTemplateName()
    {
        return "@markup/mini-site/product-list.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  533 => 205,  531 => 204,  529 => 203,  527 => 202,  525 => 201,  523 => 200,  521 => 199,  519 => 198,  517 => 197,  515 => 196,  513 => 195,  511 => 194,  509 => 193,  507 => 192,  505 => 191,  503 => 190,  501 => 189,  335 => 24,  332 => 23,  181 => 327,  77 => 224,  75 => 23,  68 => 19,  62 => 16,  56 => 13,  50 => 10,  44 => 7,  39 => 4,  36 => 3,  30 => 2,);
    }
}
