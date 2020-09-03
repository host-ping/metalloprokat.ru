<?php

/* @markup/portal/favorites/no-favorites.html.twig */
class __TwigTemplate_79d5668bd291bb6df299b5bf9cd6f8be extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = $this->env->loadTemplate("@markup/portal/favorites/favorites-consumers.html.twig");

        $this->blocks = array(
            'search_form' => array($this, 'block_search_form'),
            'breadcrumbs' => array($this, 'block_breadcrumbs'),
            'banner' => array($this, 'block_banner'),
            'content' => array($this, 'block_content'),
            'tabs' => array($this, 'block_tabs'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "@markup/portal/favorites/favorites-consumers.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 2
    public function block_search_form($context, array $blocks = array())
    {
        echo "";
    }

    // line 3
    public function block_breadcrumbs($context, array $blocks = array())
    {
        echo "";
    }

    // line 4
    public function block_banner($context, array $blocks = array())
    {
        echo "";
    }

    // line 5
    public function block_content($context, array $blocks = array())
    {
        // line 6
        echo "    <div id=\"content\" class=\"content-wide float-left\">
        <div class=\"favorites main-title \">
            <h1>Избранные: <a href=\"#\" class=\"current link\">поставщики <sup>32</sup></a>, <a href=\"#\" class=\"link\">потребители <sup>6</sup></a> </h1>
        </div>
        ";
        // line 10
        $this->displayBlock('tabs', $context, $blocks);
        // line 27
        echo "        <div class=\"no-favorites js-calc-height\">
            <span class=\"text\">Нет избранных поставщиков</span>
        </div>
    </div>
";
    }

    // line 10
    public function block_tabs($context, array $blocks = array())
    {
        // line 11
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
            </div>
        ";
    }

    public function getTemplateName()
    {
        return "@markup/portal/favorites/no-favorites.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  72 => 11,  69 => 10,  61 => 27,  59 => 10,  53 => 6,  50 => 5,  44 => 4,  38 => 3,  32 => 2,);
    }
}
