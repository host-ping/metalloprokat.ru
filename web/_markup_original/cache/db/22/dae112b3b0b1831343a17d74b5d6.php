<?php

/* @markup/private/_private_layout.html.twig */
class __TwigTemplate_db22dae112b3b0b1831343a17d74b5d6 extends Twig_Template
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
            'user_alert' => array($this, 'block_user_alert'),
            'content' => array($this, 'block_content'),
            'tabs' => array($this, 'block_tabs'),
            'filters' => array($this, 'block_filters'),
            'sidebar' => array($this, 'block_sidebar'),
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
    <link rel=\"stylesheet\" href=\"./markup/css/private.css\" type=\"text/css\"/>
    <link rel=\"stylesheet\" href=\"./markup/css/corporate.css\" type=\"text/css\"/>
    <link rel=\"stylesheet\" href=\"./markup/css/mini-site.css\" type=\"text/css\"/>

";
    }

    // line 10
    public function block_body_additional_class($context, array $blocks = array())
    {
        echo "no-scroll private-room";
    }

    // line 11
    public function block_body($context, array $blocks = array())
    {
        // line 12
        echo "    <div class=\"private-container container\">
        <div id=\"header\" class=\"clearfix\">
            <div class=\"wrap\">
                ";
        // line 15
        $this->displayBlock('header', $context, $blocks);
        // line 32
        echo "            </div>
        </div>
        ";
        // line 34
        $this->displayBlock('user_alert', $context, $blocks);
        // line 35
        echo "        <div id=\"main\" class=\"no-padding\">
            <div class=\"wrap clearfix\">
                <div class=\"wrapper outline clearfix\">
                    ";
        // line 38
        $this->displayBlock('content', $context, $blocks);
        // line 42
        echo "                    ";
        $this->displayBlock('sidebar', $context, $blocks);
        // line 120
        echo "                </div>
            </div>
        </div>
    </div>

    ";
        // line 125
        $this->displayBlock('footer', $context, $blocks);
        // line 126
        echo "    ";
        $this->env->loadTemplate("@markup/_popups.html.twig")->display($context);
    }

    // line 15
    public function block_header($context, array $blocks = array())
    {
        // line 16
        echo "                    <div class=\"private-heading float-left\">
                        <p class=\"private-title\">Личный кабинет <span class=\"private-numb green-color\"><sup>#</sup>13512</span></p>
                    </div>
                    <div class=\"logo-block float-right\">
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
    }

    // line 34
    public function block_user_alert($context, array $blocks = array())
    {
        echo "";
    }

    // line 38
    public function block_content($context, array $blocks = array())
    {
        // line 39
        echo "                        ";
        $this->displayBlock('tabs', $context, $blocks);
        // line 40
        echo "                        ";
        $this->displayBlock('filters', $context, $blocks);
        // line 41
        echo "                    ";
    }

    // line 39
    public function block_tabs($context, array $blocks = array())
    {
        echo "";
    }

    // line 40
    public function block_filters($context, array $blocks = array())
    {
        echo "";
    }

    // line 42
    public function block_sidebar($context, array $blocks = array())
    {
        // line 43
        echo "                        ";
        $context["defaultActiveMenu"] = "home";
        // line 44
        echo "                        <div id=\"sidebar\" class=\"private-room side-left outline-left float-left\">
                            <div class=\"private-menu-wrapper js-scrollable js-calc-height-private-menu\">
                                <ul class=\"private-info list\">
                                    <li class=\"main-info item\">
                                        <div class=\"info-wrapper\">
                                            <div class=\"row-block clearfix\">
                                                <div class=\"rating float-right\">
                                                    <span class=\"star-mini icon-star-colored\"></span>
                                                    <span class=\"star-mini icon-star-colored\"></span>
                                                    <span class=\"star-mini icon-star-colored\"></span>
                                                </div>
                                                <a class=\"title is-gradiented\" href=\"#\">Стальторг</a>
                                            </div>
                                        </div>
                                        <p class=\"user\">Василий Уткин</p>
                                        <div class=\"package-info\">
                                            <p>Вы используете <strong>полный плюс, СНГ пакет</strong> услуг</p>
                                            <p>Действие услуги до <strong class=\"red-color\">16 апр 2016</strong></p>
                                        </div>
                                    </li>
                                    <li class=\"item clearfix ";
        // line 64
        if ((((array_key_exists("activeMenu", $context)) ? (_twig_default_filter((isset($context["activeMenu"]) ? $context["activeMenu"] : null), (isset($context["defaultActiveMenu"]) ? $context["defaultActiveMenu"] : null))) : ((isset($context["defaultActiveMenu"]) ? $context["defaultActiveMenu"] : null))) == "home")) {
            echo " active ";
        }
        echo "\">
                                        <a class=\"link is-gradiented\" href=\"";
        // line 65
        echo twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('path')->getCallable(), array("private/home/home.html.twig")), "html", null, true);
        echo "\">Сводка</a>
                                    </li>
                                    <li class=\"item clearfix ";
        // line 67
        if ((((array_key_exists("activeMenu", $context)) ? (_twig_default_filter((isset($context["activeMenu"]) ? $context["activeMenu"] : null), (isset($context["defaultActiveMenu"]) ? $context["defaultActiveMenu"] : null))) : ((isset($context["defaultActiveMenu"]) ? $context["defaultActiveMenu"] : null))) == "management")) {
            echo " active ";
        }
        echo "\">
                                        <strong class=\"count ie-radius float-right\">800000</strong>
                                        <a class=\"link is-gradiented\" href=\"";
        // line 69
        echo twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('path')->getCallable(), array("private/management/products.html.twig")), "html", null, true);
        echo "\">Управление информацией</a>
                                    </li>
                                    <li class=\"item clearfix ";
        // line 71
        if ((((array_key_exists("activeMenu", $context)) ? (_twig_default_filter((isset($context["activeMenu"]) ? $context["activeMenu"] : null), (isset($context["defaultActiveMenu"]) ? $context["defaultActiveMenu"] : null))) : ((isset($context["defaultActiveMenu"]) ? $context["defaultActiveMenu"] : null))) == "consumers")) {
            echo " active ";
        }
        echo "\">
                                        <strong class=\"count ie-radius float-right\">4</strong>
                                        <a class=\"link is-gradiented\" href=\"";
        // line 73
        echo twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('path')->getCallable(), array("private/consumers/private-demand.html.twig")), "html", null, true);
        echo "\">Клиенты</a>
                                    </li>
                                    <li class=\"item clearfix ";
        // line 75
        if ((((array_key_exists("activeMenu", $context)) ? (_twig_default_filter((isset($context["activeMenu"]) ? $context["activeMenu"] : null), (isset($context["defaultActiveMenu"]) ? $context["defaultActiveMenu"] : null))) : ((isset($context["defaultActiveMenu"]) ? $context["defaultActiveMenu"] : null))) == "subscriptions")) {
            echo " active ";
        }
        echo "\">
                                        <a class=\"link is-gradiented\" href=\"";
        // line 76
        echo twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('path')->getCallable(), array("private/subscriptions/demands.html.twig")), "html", null, true);
        echo "\">Подписки</a>
                                    </li>
                                    <li class=\"item clearfix ";
        // line 78
        if ((((array_key_exists("activeMenu", $context)) ? (_twig_default_filter((isset($context["activeMenu"]) ? $context["activeMenu"] : null), (isset($context["defaultActiveMenu"]) ? $context["defaultActiveMenu"] : null))) : ((isset($context["defaultActiveMenu"]) ? $context["defaultActiveMenu"] : null))) == "archive")) {
            echo " active ";
        }
        echo "\">
                                        <a class=\"link is-gradiented\" href=\"";
        // line 79
        echo twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('path')->getCallable(), array("private/archive/demands.html.twig")), "html", null, true);
        echo "\">Мои Заявки</a>
                                    </li>
                                    <li class=\"item clearfix ";
        // line 81
        if ((((array_key_exists("activeMenu", $context)) ? (_twig_default_filter((isset($context["activeMenu"]) ? $context["activeMenu"] : null), (isset($context["defaultActiveMenu"]) ? $context["defaultActiveMenu"] : null))) : ((isset($context["defaultActiveMenu"]) ? $context["defaultActiveMenu"] : null))) == "statistics")) {
            echo " active ";
        }
        echo "\">
                                        <a class=\"link is-gradiented\" href=\"";
        // line 82
        echo twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('path')->getCallable(), array("private/statistics/income.html.twig")), "html", null, true);
        echo "\">Статистика</a>
                                    </li>
                                    <li class=\"item clearfix ";
        // line 84
        if ((((array_key_exists("activeMenu", $context)) ? (_twig_default_filter((isset($context["activeMenu"]) ? $context["activeMenu"] : null), (isset($context["defaultActiveMenu"]) ? $context["defaultActiveMenu"] : null))) : ((isset($context["defaultActiveMenu"]) ? $context["defaultActiveMenu"] : null))) == "services")) {
            echo " active ";
        }
        echo "\">
                                        <strong class=\"count ie-radius float-right\">3</strong>
                                        <a class=\"link is-gradiented\" href=\"";
        // line 86
        echo twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('path')->getCallable(), array("private/services/services.html.twig")), "html", null, true);
        echo "\">Счета и услуги</a>
                                    </li>
                                    <li class=\"item clearfix ";
        // line 88
        if ((((array_key_exists("activeMenu", $context)) ? (_twig_default_filter((isset($context["activeMenu"]) ? $context["activeMenu"] : null), (isset($context["defaultActiveMenu"]) ? $context["defaultActiveMenu"] : null))) : ((isset($context["defaultActiveMenu"]) ? $context["defaultActiveMenu"] : null))) == "mini-site")) {
            echo " active ";
        }
        echo "\">
                                        <a class=\"link is-gradiented\" href=\"";
        // line 89
        echo twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('path')->getCallable(), array("private/mini-site/header.html.twig")), "html", null, true);
        echo "\">Мини-сайт</a>
                                    </li>
                                    <li class=\"item clearfix ";
        // line 91
        if ((((array_key_exists("activeMenu", $context)) ? (_twig_default_filter((isset($context["activeMenu"]) ? $context["activeMenu"] : null), (isset($context["defaultActiveMenu"]) ? $context["defaultActiveMenu"] : null))) : ((isset($context["defaultActiveMenu"]) ? $context["defaultActiveMenu"] : null))) == "support")) {
            echo " active ";
        }
        echo "\">
                                        <strong class=\"count ie-radius float-right\">800000</strong>
                                        <a class=\"link is-gradiented\" href=\"";
        // line 93
        echo twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('path')->getCallable(), array("private/support/list.html.twig")), "html", null, true);
        echo "\">Техническая поддержка</a>
                                    </li>
                                    <li class=\"quit item clearfix\">
                                        <a class=\"link\" href=\"#\">Выход</a>
                                    </li>
                                </ul>
                                <div class=\"information\">
                                    <div class=\"questions\">
                                        <p class=\"text gray60-color\">Вопросы?</p>
                                        <p class=\"text\"><span class=\"phone-text\">+7 (495) 984-06-52</span> или <a class=\"email-link\" href=\"#\">пишите</a></p>
                                    </div>
                                    <div class=\"manager\">
                                        <div class=\"manager-img float-left\">
                                            <img src=\"./markup/pic/operator.jpg\" alt=\"image description\"/>
                                        </div>
                                        <div class=\"manager-info float-left\">
                                            <div class=\"manager-name\">
                                                <p class=\"name\">Галина Шильдяева</p>
                                                <p class=\"text gray60-color\">вам поможет</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    ";
    }

    // line 125
    public function block_footer($context, array $blocks = array())
    {
        echo "";
    }

    public function getTemplateName()
    {
        return "@markup/private/_private_layout.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  307 => 125,  276 => 93,  269 => 91,  264 => 89,  258 => 88,  253 => 86,  246 => 84,  241 => 82,  235 => 81,  230 => 79,  224 => 78,  219 => 76,  213 => 75,  208 => 73,  201 => 71,  196 => 69,  189 => 67,  184 => 65,  178 => 64,  156 => 44,  153 => 43,  150 => 42,  144 => 40,  138 => 39,  134 => 41,  131 => 40,  128 => 39,  125 => 38,  119 => 34,  100 => 16,  97 => 15,  92 => 126,  90 => 125,  83 => 120,  78 => 38,  73 => 35,  71 => 34,  67 => 32,  65 => 15,  60 => 12,  57 => 11,  51 => 10,  130 => 45,  127 => 44,  124 => 43,  102 => 25,  96 => 24,  91 => 22,  85 => 21,  80 => 42,  74 => 18,  69 => 16,  63 => 15,  58 => 13,  52 => 12,  46 => 9,  35 => 5,  32 => 4,  29 => 3,  1057 => 24,  1054 => 23,  1051 => 22,  1043 => 1000,  1041 => 999,  1039 => 998,  1037 => 997,  271 => 232,  64 => 26,  62 => 22,  59 => 21,  56 => 20,  40 => 3,  37 => 2,  31 => 2,);
    }
}
