<?php

/* @markup/private/consumers/_private_consumers.html.twig */
class __TwigTemplate_502688882db87f19582392c45acde40a extends Twig_Template
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
        $context["defaultActiveTab"] = "demand";
        // line 5
        echo "    <div class=\"result-tabs-wrapper clearfix\">
        <div id=\"tabs\" class=\"tabs float-left\">
            <ul class=\"private-tabs list\">
                <li class=\"item ";
        // line 8
        if ((((array_key_exists("activeTab", $context)) ? (_twig_default_filter((isset($context["activeTab"]) ? $context["activeTab"] : null), (isset($context["defaultActiveTab"]) ? $context["defaultActiveTab"] : null))) : ((isset($context["defaultActiveTab"]) ? $context["defaultActiveTab"] : null))) == "demand")) {
            echo " active ie-radius ";
        }
        echo "\">
                    <a class=\"link\" href=\"";
        // line 9
        echo twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('path')->getCallable(), array("private/consumers/private-demand.html.twig")), "html", null, true);
        echo "\">Заявки</a>
                    <span class=\"count red-bg ie-radius\">1</span>
                </li>
                <li class=\"item ";
        // line 12
        if ((((array_key_exists("activeTab", $context)) ? (_twig_default_filter((isset($context["activeTab"]) ? $context["activeTab"] : null), (isset($context["defaultActiveTab"]) ? $context["defaultActiveTab"] : null))) : ((isset($context["defaultActiveTab"]) ? $context["defaultActiveTab"] : null))) == "callbacks")) {
            echo " active ie-radius ";
        }
        echo "\">
                    <a class=\"link\" href=\"";
        // line 13
        echo twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('path')->getCallable(), array("private/consumers/private-callbacks.html.twig")), "html", null, true);
        echo "\">Обратные звонки</a>
                </li>
                <li class=\"item ";
        // line 15
        if ((((array_key_exists("activeTab", $context)) ? (_twig_default_filter((isset($context["activeTab"]) ? $context["activeTab"] : null), (isset($context["defaultActiveTab"]) ? $context["defaultActiveTab"] : null))) : ((isset($context["defaultActiveTab"]) ? $context["defaultActiveTab"] : null))) == "reviews")) {
            echo " active ie-radius ";
        }
        echo "\">
                    <a class=\"link\" href=\"";
        // line 16
        echo twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('path')->getCallable(), array("private/consumers/private-reviews.html.twig")), "html", null, true);
        echo "\">Отзывы</a>
                </li>
                <li class=\"item ";
        // line 18
        if ((((array_key_exists("activeTab", $context)) ? (_twig_default_filter((isset($context["activeTab"]) ? $context["activeTab"] : null), (isset($context["defaultActiveTab"]) ? $context["defaultActiveTab"] : null))) : ((isset($context["defaultActiveTab"]) ? $context["defaultActiveTab"] : null))) == "complaints")) {
            echo " active ie-radius ";
        }
        echo "\">
                    <a class=\"link\" href=\"";
        // line 19
        echo twig_escape_filter($this->env, call_user_func_array($this->env->getFunction('path')->getCallable(), array("private/consumers/private-complaints.html.twig")), "html", null, true);
        echo "\">Жалобы</a>
                </li>
            </ul>
        </div>
    </div>
";
    }

    // line 25
    public function block_sidebar($context, array $blocks = array())
    {
        // line 26
        echo "    ";
        $context["activeMenu"] = "consumers";
        // line 27
        echo "    ";
        $this->displayParentBlock("sidebar", $context, $blocks);
        echo "
";
    }

    public function getTemplateName()
    {
        return "@markup/private/consumers/_private_consumers.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  96 => 27,  93 => 26,  90 => 25,  80 => 19,  74 => 18,  69 => 16,  63 => 15,  58 => 13,  52 => 12,  46 => 9,  35 => 5,  32 => 4,  29 => 3,  783 => 176,  781 => 175,  779 => 174,  777 => 173,  775 => 172,  773 => 171,  771 => 170,  769 => 169,  767 => 168,  765 => 167,  763 => 166,  761 => 165,  759 => 164,  757 => 163,  755 => 162,  753 => 161,  751 => 160,  749 => 159,  747 => 158,  745 => 157,  743 => 156,  741 => 155,  739 => 154,  737 => 153,  735 => 152,  733 => 151,  731 => 150,  729 => 149,  727 => 148,  725 => 147,  723 => 146,  721 => 145,  719 => 144,  717 => 143,  715 => 142,  713 => 141,  711 => 140,  709 => 139,  707 => 138,  705 => 137,  703 => 136,  701 => 135,  699 => 134,  697 => 133,  695 => 132,  693 => 131,  691 => 130,  689 => 129,  687 => 128,  685 => 127,  567 => 10,  564 => 9,  557 => 7,  554 => 6,  551 => 5,  62 => 190,  48 => 177,  45 => 9,  43 => 5,  40 => 8,  37 => 3,  31 => 2,);
    }
}
