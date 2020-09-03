<?php

/* @markup/private/mini-site/_minisite_layout.html.twig */
class __TwigTemplate_b7c0ff0f5ed81c2cecb646777fc102cd extends Twig_Template
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
        $context["defaultActiveTab"] = "address";
        // line 5
        echo "    <div class=\"result-tabs-wrapper clearfix\">
        <div id=\"tabs\" class=\"tabs float-left\">
            <ul class=\"private-tabs list\">
                <li class=\"item ";
        // line 8
        if ((((array_key_exists("activeTab", $context)) ? (_twig_default_filter((isset($context["activeTab"]) ? $context["activeTab"] : null), (isset($context["defaultActiveTab"]) ? $context["defaultActiveTab"] : null))) : ((isset($context["defaultActiveTab"]) ? $context["defaultActiveTab"] : null))) == "address")) {
            echo " active ie-radius ";
        }
        echo "\">
                    <a class=\"link\" href=\"";
        // line 9
        echo twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('path')->getCallable(), array("private/mini-site/address.html.twig")), "html", null, true);
        echo "\">Адрес</a>
                </li>
                <li class=\"item ";
        // line 11
        if ((((array_key_exists("activeTab", $context)) ? (_twig_default_filter((isset($context["activeTab"]) ? $context["activeTab"] : null), (isset($context["defaultActiveTab"]) ? $context["defaultActiveTab"] : null))) : ((isset($context["defaultActiveTab"]) ? $context["defaultActiveTab"] : null))) == "header")) {
            echo " active ie-radius ";
        }
        echo "\">
                    <a class=\"link\" href=\"";
        // line 12
        echo twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('path')->getCallable(), array("private/mini-site/header.html.twig")), "html", null, true);
        echo "\">Шапка</a>
                </li>
                <li class=\"item ";
        // line 14
        if ((((array_key_exists("activeTab", $context)) ? (_twig_default_filter((isset($context["activeTab"]) ? $context["activeTab"] : null), (isset($context["defaultActiveTab"]) ? $context["defaultActiveTab"] : null))) : ((isset($context["defaultActiveTab"]) ? $context["defaultActiveTab"] : null))) == "colors")) {
            echo " active ie-radius ";
        }
        echo "\">
                    <a class=\"link\" href=\"";
        // line 15
        echo twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('path')->getCallable(), array("private/mini-site/colors.html.twig")), "html", null, true);
        echo "\">Цвета</a>
                </li>
                <li class=\"item ";
        // line 17
        if ((((array_key_exists("activeTab", $context)) ? (_twig_default_filter((isset($context["activeTab"]) ? $context["activeTab"] : null), (isset($context["defaultActiveTab"]) ? $context["defaultActiveTab"] : null))) : ((isset($context["defaultActiveTab"]) ? $context["defaultActiveTab"] : null))) == "button")) {
            echo " active ie-radius ";
        }
        echo "\">
                    <a class=\"link\" href=\"";
        // line 18
        echo twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('path')->getCallable(), array("private/mini-site/button.html.twig")), "html", null, true);
        echo "\">Кнопка</a>
                </li>
                <li class=\"item ";
        // line 20
        if ((((array_key_exists("activeTab", $context)) ? (_twig_default_filter((isset($context["activeTab"]) ? $context["activeTab"] : null), (isset($context["defaultActiveTab"]) ? $context["defaultActiveTab"] : null))) : ((isset($context["defaultActiveTab"]) ? $context["defaultActiveTab"] : null))) == "documents")) {
            echo " active ie-radius ";
        }
        echo "\">
                    <a class=\"link\" href=\"";
        // line 21
        echo twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('path')->getCallable(), array("private/mini-site/documents.html.twig")), "html", null, true);
        echo "\">Документы</a>
                </li>
                <li class=\"item ";
        // line 23
        if ((((array_key_exists("activeTab", $context)) ? (_twig_default_filter((isset($context["activeTab"]) ? $context["activeTab"] : null), (isset($context["defaultActiveTab"]) ? $context["defaultActiveTab"] : null))) : ((isset($context["defaultActiveTab"]) ? $context["defaultActiveTab"] : null))) == "news")) {
            echo " active ie-radius ";
        }
        echo "\">
                    <a class=\"link\" href=\"";
        // line 24
        echo twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('path')->getCallable(), array("private/mini-site/news.html.twig")), "html", null, true);
        echo "\">Новости</a>
                </li>
\t\t        <li class=\"item ";
        // line 26
        if ((((array_key_exists("activeTab", $context)) ? (_twig_default_filter((isset($context["activeTab"]) ? $context["activeTab"] : null), (isset($context["defaultActiveTab"]) ? $context["defaultActiveTab"] : null))) : ((isset($context["defaultActiveTab"]) ? $context["defaultActiveTab"] : null))) == "categories")) {
            echo " active ie-radius ";
        }
        echo "\">
                    <a class=\"link\" href=\"";
        // line 27
        echo twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('path')->getCallable(), array("private/mini-site/categories.html.twig")), "html", null, true);
        echo "\">Категории</a>
                </li>
                <li class=\"item ";
        // line 29
        if ((((array_key_exists("activeTab", $context)) ? (_twig_default_filter((isset($context["activeTab"]) ? $context["activeTab"] : null), (isset($context["defaultActiveTab"]) ? $context["defaultActiveTab"] : null))) : ((isset($context["defaultActiveTab"]) ? $context["defaultActiveTab"] : null))) == "products")) {
            echo " active ie-radius ";
        }
        echo "\">
                    <a class=\"link\" href=\"";
        // line 30
        echo twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('path')->getCallable(), array("private/mini-site/products.html.twig")), "html", null, true);
        echo "\">Товары</a>
                </li>
            </ul>
        </div>
    </div>
";
    }

    // line 36
    public function block_sidebar($context, array $blocks = array())
    {
        // line 37
        echo "    ";
        $context["activeMenu"] = "mini-site";
        // line 38
        echo "    ";
        $this->displayParentBlock("sidebar", $context, $blocks);
        echo "
";
    }

    public function getTemplateName()
    {
        return "@markup/private/mini-site/_minisite_layout.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  139 => 38,  136 => 37,  133 => 36,  123 => 30,  117 => 29,  112 => 27,  106 => 26,  101 => 24,  95 => 23,  90 => 21,  84 => 20,  79 => 18,  73 => 17,  68 => 15,  62 => 14,  57 => 12,  51 => 11,  46 => 9,  40 => 8,  35 => 5,  32 => 4,  29 => 3,  320 => 11,  317 => 10,  314 => 9,  47 => 13,  45 => 9,  39 => 5,  36 => 4,  30 => 2,);
    }
}
