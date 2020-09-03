<?php

/* @markup/corporate/reviews.html.twig */
class __TwigTemplate_31499074da1d6bd14552a1ef8e352687 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = $this->env->loadTemplate("@markup/corporate/_corporate_layout.html.twig");

        $this->blocks = array(
            'title' => array($this, 'block_title'),
            'header_menu' => array($this, 'block_header_menu'),
            'content' => array($this, 'block_content'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "@markup/corporate/_corporate_layout.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 2
    public function block_title($context, array $blocks = array())
    {
        echo "Corporate site - Reviews";
    }

    // line 3
    public function block_header_menu($context, array $blocks = array())
    {
        // line 4
        echo "    ";
        $context["activeMenu"] = "reviews";
        // line 5
        echo "    ";
        $this->displayParentBlock("header_menu", $context, $blocks);
        echo "
";
    }

    // line 7
    public function block_content($context, array $blocks = array())
    {
        // line 8
        echo "    <div id=\"content\" class=\"corporate-content content-wide clearfix\">
        <div class=\"reviews-wrapper\">
            <ul class=\"reviews\">
                <li class=\"review\">
                    <div class=\"review-text\">
                        <p class=\"text\">Мы в восторге от того, что стали клиентами! Денег столько, что фантазия иссякла!</p>
                    </div>
                    <div class=\"user-info clearfix\">
                        <div class=\"user-photo float-left ie-radius\">
                            <img src=\"./markup/pic/userpic1.jpg\" alt=\"image description\"/>
                        </div>
                        <div class=\"user float-left\">
                            <p class=\"text\">Михаил Лесной, <a href=\"#\" class=\"link\">Авангард Престиж</a></p>
                        </div>
                    </div>
                </li>
                <li class=\"review\">
                    <div class=\"review-text\">
                        <p class=\"text\">Мы в восторге от того, что стали клиентами! Денег столько, что фантазия иссякла!</p>
                    </div>
                    <div class=\"user-info clearfix\">
                        <div class=\"user-photo float-left ie-radius\">
                            <img src=\"./markup/pic/userpic1.jpg\" alt=\"image description\"/>
                        </div>
                        <div class=\"user float-left\">
                            <p class=\"text\">Михаил Лесной, <a href=\"#\" class=\"link\">Авангард Престиж</a></p>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </div>
";
    }

    public function getTemplateName()
    {
        return "@markup/corporate/reviews.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  52 => 8,  49 => 7,  42 => 5,  39 => 4,  36 => 3,  30 => 2,);
    }
}
