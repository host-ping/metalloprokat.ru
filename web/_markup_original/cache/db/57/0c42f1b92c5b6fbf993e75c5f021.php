<?php

/* @css/default-theme.css.twig */
class __TwigTemplate_db570c42f1b92c5b6fbf993e75c5f021 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        $context["colors"] = array("bg_color" => "#a7bccc", "background" => "#d3e7f5", "secondary" => "#e18141", "primary" => "#f16961", "hover_color" => "#e0f5ff", "rgba" => "213,232,245,.2", "rgb_hover" => "224,245,255", "argb_hover_start" => "#02e0f5ff", "argb_hover_finish" => "#ffe0f5ff", "argb_background_start" => "#02d3e7f5", "argb_background_finish" => "#ffd3e7f5");
        // line 14
        $this->env->loadTemplate("markup/css/minisite-themes/theme-template.css.twig")->display($context);
        // line 15
        echo "
";
    }

    public function getTemplateName()
    {
        return "@css/default-theme.css.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  23 => 15,  21 => 14,  19 => 1,);
    }
}
