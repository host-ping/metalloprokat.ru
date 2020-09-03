<?php

/* @markup/errors/error404.html.twig */
class __TwigTemplate_0e7e0254bf446160447b7aed50e88ad2 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = $this->env->loadTemplate("markup/html/_base_layout.html.twig");

        $this->blocks = array(
            'stylesheets' => array($this, 'block_stylesheets'),
            'body' => array($this, 'block_body'),
            'header' => array($this, 'block_header'),
            'login' => array($this, 'block_login'),
            'menu' => array($this, 'block_menu'),
            'content' => array($this, 'block_content'),
            'footer' => array($this, 'block_footer'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "markup/html/_base_layout.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 2
    public function block_stylesheets($context, array $blocks = array())
    {
        // line 3
        echo "    ";
        $this->displayParentBlock("stylesheets", $context, $blocks);
        echo "
";
    }

    // line 6
    public function block_body($context, array $blocks = array())
    {
        // line 7
        echo "<div class=\"container\">
<div class=\"inside-container\">
<div id=\"header\" class=\"clearfix\">
    <div class=\"wrap\">
        ";
        // line 11
        $this->displayBlock('header', $context, $blocks);
        // line 37
        echo "    </div>
</div>

";
        // line 40
        $this->displayBlock('menu', $context, $blocks);
        // line 57
        echo "
<div id=\"main\" class=\"clearfix\">
    <div class=\"wrap clearfix\">
        <div class=\"wrapper outline clearfix\">
            ";
        // line 61
        $this->displayBlock('content', $context, $blocks);
        // line 69
        echo "        </div>
    </div>
</div>
</div>

</div>

    <div id=\"footer\">
        <div class=\"footer-content wrap\">
            ";
        // line 78
        $this->displayBlock('footer', $context, $blocks);
        // line 121
        echo "        </div>
    </div>
";
        // line 123
        $this->env->loadTemplate("@markup/_popups.html.twig")->display($context);
    }

    // line 11
    public function block_header($context, array $blocks = array())
    {
        // line 12
        echo "            <div class=\"left float-left\">
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
            ";
        // line 30
        $this->displayBlock('login', $context, $blocks);
        // line 36
        echo "        ";
    }

    // line 30
    public function block_login($context, array $blocks = array())
    {
        // line 31
        echo "                <div class=\"user-block float-right\">
                    <a class=\"login js-popup-opener icon-exit\" data-popup=\"#login\" href=\"#\">Вход в кабинет</a>
                    <a class=\"register-link js-popup-opener\" data-popup=\"#register-company\" href=\"#\">Регистрация</a>
                </div>
            ";
    }

    // line 40
    public function block_menu($context, array $blocks = array())
    {
        // line 41
        echo "    <div class=\"main-menu-wrapper\">
        <div class=\"wrap\">
            <ul id=\"menu\" class=\"main-menu clearfix\">
                <li class=\"\">
                    <a href=\"#\" class=\"supplier link icon-suppliers-color\">Поставщики</a>
                </li>
                <li class=\"\">
                    <span class=\"consumer link icon-consumers-color\">Потребители</span>
                </li>
                <li>
                    <a class=\"favorites link icon-favorite-active\" href=\"#\">Избранное <sup>24</sup></a>
                </li>
            </ul>
        </div>
    </div>
";
    }

    // line 61
    public function block_content($context, array $blocks = array())
    {
        // line 62
        echo "                <div id=\"content\" class=\"error-page content-wide content-scrollable\" style=\"text-align: center; display: table-cell;
                        text-align: center; vertical-align: middle;\">
                    <h1 class=\"title\" style=\"color: #f16961; font-size: 36px; margin-bottom: 30px;\">Ошибка 404</h1>
                    <p style=\"font-size: 16px; margin-bottom: 30px;\">К сожалению, запрошенная вами страница не существует.</p>
                    <a class=\"button white95-bg ie-radius\" href=\"/\" style=\"width: 335px; color: #262626; display: inline-block; font-weight: 600;\">Вернуться на главную</a>
                </div>
            ";
    }

    // line 78
    public function block_footer($context, array $blocks = array())
    {
        // line 79
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
        return "@markup/errors/error404.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  169 => 79,  166 => 78,  156 => 62,  153 => 61,  134 => 41,  131 => 40,  123 => 31,  120 => 30,  116 => 36,  114 => 30,  94 => 12,  91 => 11,  87 => 123,  83 => 121,  81 => 78,  70 => 69,  68 => 61,  62 => 57,  60 => 40,  55 => 37,  53 => 11,  47 => 7,  44 => 6,  37 => 3,  34 => 2,);
    }
}
