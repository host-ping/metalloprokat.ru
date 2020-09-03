<?php

/* @markup/portal/main/content-main-page.html.twig */
class __TwigTemplate_038874ee65bcee39785c7ef730a34f37 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = $this->env->loadTemplate("@markup/content/_content_layout.html.twig");

        $this->blocks = array(
            'title' => array($this, 'block_title'),
            'search_form' => array($this, 'block_search_form'),
            'breadcrumbs' => array($this, 'block_breadcrumbs'),
            'breadcrumbs_button' => array($this, 'block_breadcrumbs_button'),
            'content' => array($this, 'block_content'),
            'callback' => array($this, 'block_callback'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "@markup/content/_content_layout.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_title($context, array $blocks = array())
    {
        echo "Main page";
    }

    // line 4
    public function block_search_form($context, array $blocks = array())
    {
        // line 5
        echo "    ";
        $context["searchClass"] = "stroy-search";
        // line 6
        echo "    <div class=\"search-block clearfix\">
        <form action=\"#\" class=\"search-form\">
            <fieldset id=\"search-fixed\" class=\"main-block js-fixed  ";
        // line 8
        echo twig_escape_filter($this->env, (isset($context["searchClass"]) ? $context["searchClass"] : null), "html", null, true);
        echo "\">
                <div class=\"wrap clearfix\">
                    <div class=\"search-field-wrap float-left\">
                        <span class=\"icon-search-big\"></span>
                        <input type=\"text\" placeholder=\"Введите название товара или компании\" class=\"search-input\"/>
                    </div>
                    <div class=\"search-submit-wrapper float-right\">
                        <a href=\"#\" class=\"change-location icon-check js-popover-opener float-left\" data-popover=\"#cities\" data-different-position=\"true\" data-index=\"2000\">Москва и Область</a>
                        <input type=\"submit\" value=\"Найти\" class=\"button search-submit blue-bg float-left ie-radius\"/>
                    </div>
                </div>
            </fieldset>
            <fieldset class=\"more\">
                <div class=\"wrap\">

                    <div class=\"info centered\">
                        <p><strong>Тысячи</strong> поставщиков с нами! <strong class=\"phone-text\">8 (800) 555-56-65</strong> <a class=\"callback js-popup-opener\" href=\"#\" data-popup=\"#callback\">обратный звонок</a></p>
                    </div>
                </div>
            </fieldset>
        </form>
    </div>
";
    }

    // line 31
    public function block_breadcrumbs($context, array $blocks = array())
    {
        // line 32
        echo "    <div class=\"breadcrumbs-wrapper\">
        <div class=\"breadcrumbs empty-breadcrumbs outline\">
            ";
        // line 35
        echo "            ";
        // line 36
        echo "            ";
        // line 37
        echo "            ";
        $this->displayBlock('breadcrumbs_button', $context, $blocks);
        // line 42
        echo "        </div>
    </div>
";
    }

    // line 37
    public function block_breadcrumbs_button($context, array $blocks = array())
    {
        // line 38
        echo "                <div class=\"add\">
                    <a class=\"add-button product icon-add-btn\" href=\"#\">Добавить прайс-лист</a>
                </div>
            ";
    }

    // line 45
    public function block_content($context, array $blocks = array())
    {
        // line 46
        echo "    ";
        $this->displayBlock('callback', $context, $blocks);
        // line 53
        echo "    <div id=\"content\" class=\"content-wide main-page\">
        <div class=\"product-categories-wrapper\">
            <ul class=\"product-categories table-container\">
                <li class=\"item table-cell without-img js-popover-centered\">
                    <div class=\"item-wrapper\">
                        <div class=\"holder js-popover-opener\" data-popover=\"#cat-1\">
                            <a class=\"link\" href=\"#\">Продукты питания</a>
                        </div>
                        <div class=\"drop-wrapper opacity-border\" id=\"cat-1\">
                            <div class=\"heading\">
                                <div class=\"block-title\">Продукция из черных металлов</div>
                            </div>
                            <ul class=\"dropdown\">
                                <li class=\"drop-item\">
                                    <a class=\"link clearfix\" href=\"#\">
                                        <span class=\"gray60-color float-right\">517593</span>
                                        <span class=\"elem is-gradiented\">Лист стальной, металлический</span>
                                    </a>
                                </li>
                                <li class=\"drop-item\">
                                    <a class=\"link clearfix\" href=\"#\">
                                        <span class=\"gray60-color float-right\">51</span>
                                        <span class=\"elem is-gradiented\">Арматура</span>
                                    </a>
                                </li>
                                <li class=\"drop-item\">
                                    <a class=\"link clearfix\" href=\"#\">
                                        <span class=\"gray60-color float-right\">51</span>
                                        <span class=\"elem is-gradiented\">Арматура</span>
                                    </a>
                                </li>
                                <li class=\"drop-item\">
                                    <a class=\"link clearfix\" href=\"#\">
                                        <span class=\"gray60-color float-right\">51</span>
                                        <span class=\"elem is-gradiented\">Арматура</span>
                                    </a>
                                </li>
                                <li class=\"drop-item\">
                                    <a class=\"link clearfix\" href=\"#\">
                                        <span class=\"gray60-color float-right\">51</span>
                                        <span class=\"elem is-gradiented\">Арматура</span>
                                    </a>
                                </li>
                                <li class=\"drop-item\">
                                    <a class=\"link clearfix\" href=\"#\">
                                        <span class=\"gray60-color float-right\">51</span>
                                        <span class=\"elem is-gradiented\">Арматура</span>
                                    </a>
                                </li>
                                <li class=\"drop-item\">
                                    <a class=\"link clearfix\" href=\"#\">
                                        <span class=\"gray60-color float-right\">51</span>
                                        <span class=\"elem is-gradiented\">Арматура</span>
                                    </a>
                                </li>
                                <li class=\"drop-item\">
                                    <a class=\"link clearfix\" href=\"#\">
                                        <span class=\"gray60-color float-right\">51</span>
                                        <span class=\"elem is-gradiented\">Арматура</span>
                                    </a>
                                </li>
                                <li class=\"drop-item\">
                                    <a class=\"link clearfix\" href=\"#\">
                                        <span class=\"gray60-color float-right\">51</span>
                                        <span class=\"elem is-gradiented\">Арматура</span>
                                    </a>
                                </li>
                                <li class=\"drop-item\">
                                    <a class=\"link clearfix\" href=\"#\">
                                        <span class=\"gray60-color float-right\">51</span>
                                        <span class=\"elem is-gradiented\">Арматура</span>
                                    </a>
                                </li>
                                <li class=\"drop-item\">
                                    <a class=\"link clearfix\" href=\"#\">
                                        <span class=\"gray60-color float-right\">51</span>
                                        <span class=\"elem is-gradiented\">Арматура</span>
                                    </a>
                                </li>
                                <li class=\"drop-item\">
                                    <a class=\"link clearfix\" href=\"#\">
                                        <span class=\"gray60-color float-right\">51</span>
                                        <span class=\"elem is-gradiented\">Арматура</span>
                                    </a>
                                </li>
                                <li class=\"drop-item\">
                                    <a class=\"link clearfix\" href=\"#\">
                                        <span class=\"gray60-color float-right\">51</span>
                                        <span class=\"elem is-gradiented\">Арматура</span>
                                    </a>
                                </li>
                                <li class=\"drop-item\">
                                    <a class=\"link clearfix\" href=\"#\">
                                        <span class=\"gray60-color float-right\">51</span>
                                        <span class=\"elem is-gradiented\">Арматура</span>
                                    </a>
                                </li>
                                <li class=\"drop-item\">
                                    <a class=\"link clearfix\" href=\"#\">
                                        <span class=\"gray60-color float-right\">51</span>
                                        <span class=\"elem is-gradiented\">Арматура</span>
                                    </a>
                                </li>
                                <li class=\"drop-item\">
                                    <a class=\"link clearfix\" href=\"#\">
                                        <span class=\"gray60-color float-right\">51</span>
                                        <span class=\"elem is-gradiented\">Арматура</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </li>
                <li class=\"item table-cell without-img\">
                    <div class=\"item-wrapper\">
                        <div class=\"holder\">
                            <a class=\"link\" href=\"#\">Сырье и сельхозпродукты</a>
                        </div>
                    </div>
                </li>
                <li class=\"item table-cell without-img\">
                    <div class=\"item-wrapper\">
                        <div class=\"holder\">
                            <a class=\"link\" href=\"#\">Оборудование и техника</a>
                        </div>
                    </div>
                </li>
                <li class=\"item table-cell without-img\">
                    <div class=\"item-wrapper\">
                        <div class=\"holder\">
                            <a class=\"link\" href=\"#\">Упаковка</a>
                        </div>
                    </div>
                </li>
            </ul>
            <ul class=\"product-categories table-container\">
                <li class=\"item table-cell without-img\">
                    <div class=\"item-wrapper\">
                        <div class=\"holder\">
                            <a class=\"link\" href=\"#\">Услуги</a>
                        </div>
                    </div>
                </li>
                <li class=\"item table-cell without-img\">
                    <div class=\"item-wrapper\">
                        <div class=\"holder\">
                            <a class=\"link\" href=\"#\">Оборудование и техника</a>
                        </div>
                    </div>
                </li>
                <li class=\"item table-cell without-img\">
                    <div class=\"item-wrapper\">
                        <div class=\"holder\">
                            <a class=\"link\" href=\"#\">Упаковка</a>
                        </div>
                    </div>
                </li>
                <li class=\"item table-cell without-img\">
                    <div class=\"item-wrapper\">
                        <div class=\"holder\">
                            <a class=\"link\" href=\"#\">Услуги</a>
                        </div>
                    </div>
                </li>
            </ul>
        </div>


        <div class=\"intro\">
            <p class=\"site-name\">Металлопрокат.ру</p>
            <p class=\"site-slogan\">
                объединяет поставщика и потребителя металлопродукции.
            </p>
            <p class=\"text\">Вы можете найти партнера двумя способами — <a href=\"#\">разместив информацию</a>
                о своих товарах или потребности, либо самостоятельно при помощи <a href=\"#\">поисковой строки</a> и <a href=\"#\">рубрикатора</a>.</p>
        </div>
        <div class=\"main-register btn-wrapper\">
            <a class=\"main-register-button button green-bg ie-radius\" href=\"#\">Зарегистрироваться и добавить компанию</a>
        </div>

    </div>
";
    }

    // line 46
    public function block_callback($context, array $blocks = array())
    {
        // line 47
        echo "        <div class=\"title-callback outline big-size\">Купить стройматериалы в России по
            <span class=\"link clickable\">лучшей цене</span>
            <strong class=\"phone-company\">8 (800) 555-56-65</strong>
            <span class=\"callback-link link clickable js-popup-opener\">обратный звонок</span>
        </div>
    ";
    }

    public function getTemplateName()
    {
        return "@markup/portal/main/content-main-page.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  300 => 47,  297 => 46,  112 => 53,  109 => 46,  106 => 45,  99 => 38,  96 => 37,  90 => 42,  87 => 37,  85 => 36,  83 => 35,  79 => 32,  76 => 31,  49 => 8,  45 => 6,  42 => 5,  39 => 4,  33 => 3,);
    }
}
