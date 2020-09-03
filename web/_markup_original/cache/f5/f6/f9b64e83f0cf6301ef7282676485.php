<?php

/* @markup/private/management/_managemet_layout.html.twig */
class __TwigTemplate_f5f6f9b64e83f0cf6301ef7282676485 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = $this->env->loadTemplate("@markup/private/_private_layout.html.twig");

        $this->blocks = array(
            'tabs' => array($this, 'block_tabs'),
            'sidebar' => array($this, 'block_sidebar'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "@markup/private/_private_layout.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_tabs($context, array $blocks = array())
    {
        // line 4
        echo "    ";
        $context["defaultActiveTab"] = "products";
        // line 5
        echo "    <div class=\"result-tabs-wrapper clearfix\">
        <div id=\"tabs\" class=\"tabs float-left\">
            <ul class=\"private-tabs list\">
                <li class=\"item ";
        // line 8
        if ((((array_key_exists("activeTab", $context)) ? (_twig_default_filter((isset($context["activeTab"]) ? $context["activeTab"] : null), (isset($context["defaultActiveTab"]) ? $context["defaultActiveTab"] : null))) : ((isset($context["defaultActiveTab"]) ? $context["defaultActiveTab"] : null))) == "products")) {
            echo " active ie-radius ";
        }
        echo "\">
                    <a class=\"link\" href=\"";
        // line 9
        echo twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('path')->getCallable(), array("private/management/products.html.twig")), "html", null, true);
        echo "\">Товары</a>
                    <span class=\"counts \">2,587</span>
                </li>
                <li class=\"item ";
        // line 12
        if ((((array_key_exists("activeTab", $context)) ? (_twig_default_filter((isset($context["activeTab"]) ? $context["activeTab"] : null), (isset($context["defaultActiveTab"]) ? $context["defaultActiveTab"] : null))) : ((isset($context["defaultActiveTab"]) ? $context["defaultActiveTab"] : null))) == "company")) {
            echo " active ie-radius ";
        }
        echo "\">
                    <a class=\"link\" href=\"";
        // line 13
        echo twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('path')->getCallable(), array("private/management/company.html.twig")), "html", null, true);
        echo "\">Компания</a>
                </li>
                <li class=\"item ";
        // line 15
        if ((((array_key_exists("activeTab", $context)) ? (_twig_default_filter((isset($context["activeTab"]) ? $context["activeTab"] : null), (isset($context["defaultActiveTab"]) ? $context["defaultActiveTab"] : null))) : ((isset($context["defaultActiveTab"]) ? $context["defaultActiveTab"] : null))) == "company2")) {
            echo " active ie-radius ";
        }
        echo "\">
                    <a class=\"link\" href=\"";
        // line 16
        echo twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('path')->getCallable(), array("private/management/company-creation.html.twig")), "html", null, true);
        echo "\">Регистрация</a>
                </li>
                <li class=\"item ";
        // line 18
        if ((((array_key_exists("activeTab", $context)) ? (_twig_default_filter((isset($context["activeTab"]) ? $context["activeTab"] : null), (isset($context["defaultActiveTab"]) ? $context["defaultActiveTab"] : null))) : ((isset($context["defaultActiveTab"]) ? $context["defaultActiveTab"] : null))) == "filials")) {
            echo " active ie-radius ";
        }
        echo "\">
                    <a class=\"link\" href=\"";
        // line 19
        echo twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('path')->getCallable(), array("private/management/filials.html.twig")), "html", null, true);
        echo "\">Филиалы</a>
                </li>
                <li class=\"item ";
        // line 21
        if ((((array_key_exists("activeTab", $context)) ? (_twig_default_filter((isset($context["activeTab"]) ? $context["activeTab"] : null), (isset($context["defaultActiveTab"]) ? $context["defaultActiveTab"] : null))) : ((isset($context["defaultActiveTab"]) ? $context["defaultActiveTab"] : null))) == "employee")) {
            echo " active ie-radius ";
        }
        echo "\">
                    <a class=\"link\" href=\"";
        // line 22
        echo twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('path')->getCallable(), array("private/management/employees.html.twig")), "html", null, true);
        echo "\">Сотрудники</a>
                </li>
                <li class=\"item ";
        // line 24
        if ((((array_key_exists("activeTab", $context)) ? (_twig_default_filter((isset($context["activeTab"]) ? $context["activeTab"] : null), (isset($context["defaultActiveTab"]) ? $context["defaultActiveTab"] : null))) : ((isset($context["defaultActiveTab"]) ? $context["defaultActiveTab"] : null))) == "me")) {
            echo " active ie-radius ";
        }
        echo "\">
                    <a class=\"link\" href=\"";
        // line 25
        echo twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('path')->getCallable(), array("private/management/me.html.twig")), "html", null, true);
        echo "\">Я</a>
                </li>
            </ul>
        </div>
        <div class=\"export-products-block float-right\">
            <span class=\"export link clickable js-popover-opener\" data-popover=\"#export-feed\">Экспорт</span>
            <div id=\"export-feed\" class=\"drop-wrapper export-feed_links opacity-border\">
                <div class=\"dropdown\">
                    <div class=\"export-links block clearfix\">
                        <span class=\"title export link\">Экспорт</span>
                        <a class=\"button small-btn blue-bg float-left ie-radius\" href=\"#\">XLS</a>
                        <a class=\"button small-btn blue-bg float-left ie-radius\" href=\"#\">YML</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
";
    }

    // line 43
    public function block_sidebar($context, array $blocks = array())
    {
        // line 44
        echo "    ";
        $context["activeMenu"] = "management";
        // line 45
        echo "    ";
        $this->displayParentBlock("sidebar", $context, $blocks);
        echo "
";
    }

    public function getTemplateName()
    {
        return "@markup/private/management/_managemet_layout.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  130 => 45,  127 => 44,  124 => 43,  102 => 25,  96 => 24,  91 => 22,  85 => 21,  80 => 19,  74 => 18,  69 => 16,  63 => 15,  58 => 13,  52 => 12,  46 => 9,  35 => 5,  32 => 4,  29 => 3,  1057 => 24,  1054 => 23,  1051 => 22,  1043 => 1000,  1041 => 999,  1039 => 998,  1037 => 997,  271 => 232,  64 => 26,  62 => 22,  59 => 21,  56 => 20,  40 => 8,  37 => 3,  31 => 2,);
    }
}
