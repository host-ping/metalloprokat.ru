<?php

/* @markup/ui/_map-partials/yandex_maps_initialization.html.twig */
class __TwigTemplate_aec71d94e257ed3370d3e7772469bde1 extends Twig_Template
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
        echo "<script src=\"http://api-maps.yandex.ru/2.1/?lang=ru_RU&amp;mode=";
        echo ((($this->getAttribute((isset($context["app"]) ? $context["app"] : null), "debug") && false)) ? ("debug") : ("release"));
        echo "\" type=\"text/javascript\"></script>
";
        // line 2
        $context["mapIcons"] = array("company" => array("icon" => "./markup/img/red-point.png", "size" => array(0 => 24, 1 => 24), "offset" => array(0 => (-12), 1 => (-12))), "company-products" => array("icon" => "./markup/img/red-point.png", "size" => array(0 => 24, 1 => 24), "offset" => array(0 => (-12), 1 => (-12))), "company-no-products" => array("icon" => "./markup/img/blue-point.png", "size" => array(0 => 16, 1 => 16), "offset" => array(0 => (-8), 1 => (-8))));
        // line 19
        echo "<script type=\"text/javascript\" id=\"map-configuration\">
    MetalMaps.iconsCfgs = ";
        // line 20
        echo twig_jsonencode_filter((isset($context["mapIcons"]) ? $context["mapIcons"] : null));
        echo ";
</script>

";
        // line 23
        $this->env->loadTemplate("@markup/ui/_map-partials/company_baloon.html.twig")->display($context);
    }

    public function getTemplateName()
    {
        return "@markup/ui/_map-partials/yandex_maps_initialization.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  35 => 23,  29 => 20,  26 => 19,  24 => 2,  19 => 1,  599 => 389,  596 => 388,  590 => 378,  546 => 336,  543 => 335,  537 => 334,  533 => 377,  530 => 335,  527 => 334,  524 => 333,  515 => 326,  512 => 325,  507 => 323,  505 => 322,  503 => 321,  501 => 320,  499 => 319,  497 => 318,  493 => 315,  490 => 314,  483 => 156,  480 => 155,  324 => 160,  322 => 155,  318 => 153,  315 => 152,  294 => 132,  292 => 131,  290 => 130,  288 => 129,  286 => 128,  284 => 127,  278 => 122,  275 => 121,  233 => 78,  229 => 76,  226 => 75,  202 => 53,  199 => 52,  193 => 51,  185 => 42,  182 => 41,  177 => 47,  175 => 41,  149 => 17,  142 => 433,  138 => 431,  136 => 388,  125 => 379,  122 => 378,  120 => 333,  117 => 332,  114 => 325,  112 => 314,  109 => 313,  107 => 152,  100 => 121,  96 => 119,  90 => 116,  85 => 74,  80 => 51,  74 => 16,  58 => 8,  48 => 4,  45 => 3,  186 => 118,  183 => 117,  152 => 25,  146 => 16,  140 => 21,  134 => 20,  129 => 17,  126 => 16,  123 => 15,  111 => 109,  103 => 150,  97 => 100,  94 => 118,  91 => 98,  88 => 75,  82 => 52,  79 => 95,  76 => 49,  73 => 93,  68 => 92,  66 => 12,  63 => 11,  61 => 56,  55 => 7,  53 => 15,  43 => 7,  40 => 6,  37 => 5,  31 => 3,);
    }
}
