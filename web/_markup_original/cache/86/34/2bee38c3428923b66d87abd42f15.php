<?php

/* @markup/corporate/_corporate_layout.html.twig */
class __TwigTemplate_86342bee38c3428923b66d87abd42f15 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = $this->env->loadTemplate("@markup/_base_layout.html.twig");

        $this->blocks = array(
            'stylesheets' => array($this, 'block_stylesheets'),
            'body_additional_class' => array($this, 'block_body_additional_class'),
            'body' => array($this, 'block_body'),
            'header' => array($this, 'block_header'),
            'header_menu' => array($this, 'block_header_menu'),
            'content' => array($this, 'block_content'),
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

    // line 2
    public function block_stylesheets($context, array $blocks = array())
    {
        // line 3
        echo "    ";
        $this->displayParentBlock("stylesheets", $context, $blocks);
        echo "
    <link rel=\"stylesheet\" href=\"./markup/css/corporate.css\" type=\"text/css\"/>

";
    }

    // line 8
    public function block_body_additional_class($context, array $blocks = array())
    {
        echo "corporate";
    }

    // line 9
    public function block_body($context, array $blocks = array())
    {
        // line 10
        echo "    <div class=\"container\">
        <div class=\"inside-container\">
            <div id=\"header\" class=\"corporate-header clearfix\">
                <div class=\"wrap clearfix\">
                    ";
        // line 14
        $this->displayBlock('header', $context, $blocks);
        // line 54
        echo "                </div>
            </div>
            <div id=\"main\" class=\"clearfix\">
                <div class=\"wrap clearfix\">
                    <div class=\"wrapper outline clearfix\">
                        ";
        // line 59
        $this->displayBlock('content', $context, $blocks);
        // line 60
        echo "                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id=\"footer\">
        <div class=\"footer-content wrap\">
            ";
        // line 68
        $this->displayBlock('footer', $context, $blocks);
        // line 99
        echo "        </div>
    </div>
    ";
        // line 101
        $this->env->loadTemplate("@markup/_popups.html.twig")->display($context);
    }

    // line 14
    public function block_header($context, array $blocks = array())
    {
        // line 15
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
                        </div>
                        ";
        // line 27
        $this->displayBlock('header_menu', $context, $blocks);
        // line 52
        echo "
                    ";
    }

    // line 27
    public function block_header_menu($context, array $blocks = array())
    {
        // line 28
        echo "                            ";
        $context["defaultActiveMenu"] = "about";
        // line 29
        echo "                            <div class=\"corporate-menu float-left\">
                                <ul class=\"menu clearfix\">
                                    <li class=\"item float-left ";
        // line 31
        if ((((array_key_exists("activeMenu", $context)) ? (_twig_default_filter((isset($context["activeMenu"]) ? $context["activeMenu"] : null), (isset($context["defaultActiveMenu"]) ? $context["defaultActiveMenu"] : null))) : ((isset($context["defaultActiveMenu"]) ? $context["defaultActiveMenu"] : null))) == "about")) {
            echo " active ";
        }
        echo "\">
                                        <a class=\"link ie-radius\" href=\"";
        // line 32
        echo twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('path')->getCallable(), array("corporate/about.html.twig")), "html", null, true);
        echo "\">О компании</a>
                                    </li>
                                    <li class=\"item float-left ";
        // line 34
        if ((((array_key_exists("activeMenu", $context)) ? (_twig_default_filter((isset($context["activeMenu"]) ? $context["activeMenu"] : null), (isset($context["defaultActiveMenu"]) ? $context["defaultActiveMenu"] : null))) : ((isset($context["defaultActiveMenu"]) ? $context["defaultActiveMenu"] : null))) == "media")) {
            echo " active ";
        }
        echo "\">
                                        <a class=\"link ie-radius\" href=\"";
        // line 35
        echo twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('path')->getCallable(), array("corporate/announcements.html.twig")), "html", null, true);
        echo "\">Медийная реклама</a>
                                    </li>
                                    <li class=\"item float-left ";
        // line 37
        if ((((array_key_exists("activeMenu", $context)) ? (_twig_default_filter((isset($context["activeMenu"]) ? $context["activeMenu"] : null), (isset($context["defaultActiveMenu"]) ? $context["defaultActiveMenu"] : null))) : ((isset($context["defaultActiveMenu"]) ? $context["defaultActiveMenu"] : null))) == "services")) {
            echo " active ";
        }
        echo "\">
                                        <a class=\"link ie-radius\" href=\"";
        // line 38
        echo twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('path')->getCallable(), array("corporate/services.html.twig")), "html", null, true);
        echo "\">Услуги</a>
                                    </li>
                                    <li class=\"item float-left ";
        // line 40
        if ((((array_key_exists("activeMenu", $context)) ? (_twig_default_filter((isset($context["activeMenu"]) ? $context["activeMenu"] : null), (isset($context["defaultActiveMenu"]) ? $context["defaultActiveMenu"] : null))) : ((isset($context["defaultActiveMenu"]) ? $context["defaultActiveMenu"] : null))) == "clients")) {
            echo " active ";
        }
        echo "\">
                                        <a class=\"link ie-radius\" href=\"";
        // line 41
        echo twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('path')->getCallable(), array("corporate/clients.html.twig")), "html", null, true);
        echo "\">Клиенты</a>
                                    </li>
                                    <li class=\"item float-left ";
        // line 43
        if ((((array_key_exists("activeMenu", $context)) ? (_twig_default_filter((isset($context["activeMenu"]) ? $context["activeMenu"] : null), (isset($context["defaultActiveMenu"]) ? $context["defaultActiveMenu"] : null))) : ((isset($context["defaultActiveMenu"]) ? $context["defaultActiveMenu"] : null))) == "reviews")) {
            echo " active ";
        }
        echo "\">
                                        <a class=\"link ie-radius\" href=\"";
        // line 44
        echo twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('path')->getCallable(), array("corporate/reviews.html.twig")), "html", null, true);
        echo "\">Отзывы</a>
                                    </li>
                                    <li class=\"item float-left ";
        // line 46
        if ((((array_key_exists("activeMenu", $context)) ? (_twig_default_filter((isset($context["activeMenu"]) ? $context["activeMenu"] : null), (isset($context["defaultActiveMenu"]) ? $context["defaultActiveMenu"] : null))) : ((isset($context["defaultActiveMenu"]) ? $context["defaultActiveMenu"] : null))) == "contacts")) {
            echo " active ";
        }
        echo "\">
                                        <a class=\"link ie-radius\" href=\"";
        // line 47
        echo twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('path')->getCallable(), array("corporate/contacts.html.twig")), "html", null, true);
        echo "\">Контакты</a>
                                    </li>
                                </ul>
                            </div>
                        ";
    }

    // line 59
    public function block_content($context, array $blocks = array())
    {
        echo "";
    }

    // line 68
    public function block_footer($context, array $blocks = array())
    {
        // line 69
        echo "                <div class=\"footer-links-wrapper\">
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

                <ul class=\"footer-links-list last\">
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
        return "@markup/corporate/_corporate_layout.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  205 => 69,  202 => 68,  196 => 59,  187 => 47,  181 => 46,  176 => 44,  170 => 43,  165 => 41,  159 => 40,  154 => 38,  148 => 37,  143 => 35,  137 => 34,  132 => 32,  126 => 31,  122 => 29,  119 => 28,  116 => 27,  111 => 52,  109 => 27,  95 => 15,  92 => 14,  88 => 101,  84 => 99,  82 => 68,  72 => 60,  70 => 59,  63 => 54,  61 => 14,  55 => 10,  46 => 8,  37 => 3,  34 => 2,  52 => 9,  49 => 7,  42 => 5,  39 => 4,  36 => 3,  30 => 2,);
    }
}
