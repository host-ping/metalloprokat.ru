<?php

/* @markup/ui/_map-partials/company_baloon.html.twig */
class __TwigTemplate_e7bcd64a4739c5db0bfb036734f9ef8a extends Twig_Template
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
        echo "<script type=\"text/yamaps-template\" id=\"maps__companies__baloon\">
    <div class=\"drop-wrapper map-balloon ";
        // line 2
        if (($this->getAttribute($this->getAttribute((isset($context["properties"]) ? $context["properties"] : null), "company"), "products_count") == false)) {
            echo "simple ";
        }
        echo "opacity-border\" style=\"display: block;\">
        <div class=\"dropdown\">
            <div class=\"rating clearfix\">
                ";
        // line 5
        if (($this->getAttribute($this->getAttribute((isset($context["properties"]) ? $context["properties"] : null), "company"), "rating") == 1)) {
            // line 6
            echo "                    <span class=\"star-mini ";
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["properties"]) ? $context["properties"] : null), "company"), "star_color"), "html", null, true);
            echo "\"></span>
                ";
        } elseif (($this->getAttribute($this->getAttribute((isset($context["properties"]) ? $context["properties"] : null), "company"), "rating") == 2)) {
            // line 8
            echo "                    <span class=\"star-mini ";
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["properties"]) ? $context["properties"] : null), "company"), "star_color"), "html", null, true);
            echo "\"></span>
                    <span class=\"star-mini ";
            // line 9
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["properties"]) ? $context["properties"] : null), "company"), "star_color"), "html", null, true);
            echo "\"></span>
                ";
        } elseif (($this->getAttribute($this->getAttribute((isset($context["properties"]) ? $context["properties"] : null), "company"), "rating") == 3)) {
            // line 11
            echo "                    <span class=\"star-mini ";
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["properties"]) ? $context["properties"] : null), "company"), "star_color"), "html", null, true);
            echo "\"></span>
                    <span class=\"star-mini ";
            // line 12
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["properties"]) ? $context["properties"] : null), "company"), "star_color"), "html", null, true);
            echo "\"></span>
                    <span class=\"star-mini ";
            // line 13
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["properties"]) ? $context["properties"] : null), "company"), "star_color"), "html", null, true);
            echo "\"></span>
                ";
        }
        // line 15
        echo "            </div>
            <div class=\"title\"><a target=\"_blank\" href=\"";
        // line 16
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["properties"]) ? $context["properties"] : null), "company"), "mini_site"), "html", null, true);
        echo "\">";
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["properties"]) ? $context["properties"] : null), "company"), "title"), "html", null, true);
        echo "</a></div>

            <p class=\"localization gray60-color\">";
        // line 18
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["properties"]) ? $context["properties"] : null), "company"), "city_title"), "html", null, true);
        echo "</p>
            ";
        // line 19
        if ($this->getAttribute($this->getAttribute((isset($context["properties"]) ? $context["properties"] : null), "company"), "url")) {
            // line 20
            echo "                <a target=\"_blank\" href=\"";
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["properties"]) ? $context["properties"] : null), "company"), "url"), "html", null, true);
            echo "\">";
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["properties"]) ? $context["properties"] : null), "company"), "url_caption"), "html", null, true);
            echo "</a>
            ";
        }
        // line 22
        echo "            ";
        if ($this->getAttribute($this->getAttribute((isset($context["properties"]) ? $context["properties"] : null), "company"), "phone")) {
            // line 23
            echo "                <div class=\"contacts clearfix\">
                    <span class=\"phone-text float-left\">
                        <span class=\"curr-phone float-left js-phone\"
                              data-object-id=\"";
            // line 26
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["properties"]) ? $context["properties"] : null), "company"), "id"), "html", null, true);
            echo "\"
                              data-object-kind=\"company\"
                              data-source=\"companies-list\"
                                ";
            // line 29
            if ($this->getAttribute($this->getAttribute((isset($context["properties"]) ? $context["properties"] : null), "company"), "category_id")) {
                // line 30
                echo "                                    data-category-id=\"";
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["properties"]) ? $context["properties"] : null), "company"), "category_id"), "html", null, true);
                echo "\"
                                ";
            }
            // line 32
            echo "                              data-url=\"";
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["properties"]) ? $context["properties"] : null), "company"), "show_contact_url"), "html", null, true);
            echo "\"
                              data-phone-of-company=\"";
            // line 33
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["properties"]) ? $context["properties"] : null), "company"), "id"), "html", null, true);
            echo "\"
                                >";
            // line 34
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["properties"]) ? $context["properties"] : null), "company"), "phone"), "html", null, true);
            echo "</span>
                    </span>

                    <span class=\"callback link clickable js-popup-opener\"
                          data-popup=\"#callback\"
                          data-callback-url=\"";
            // line 39
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["properties"]) ? $context["properties"] : null), "company"), "callback_url"), "html", null, true);
            echo "\"
                          data-callback-text=\"Меня интересует ";
            // line 40
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute((isset($context["properties"]) ? $context["properties"] : null), "company"), "product"), "title"), "html", null, true);
            echo " в ";
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["properties"]) ? $context["properties"] : null), "company"), "city_locative"), "html", null, true);
            echo "\"
                            >обратный звонок</span>
                </div>
            ";
        }
        // line 44
        echo "            ";
        if (($this->getAttribute($this->getAttribute((isset($context["properties"]) ? $context["properties"] : null), "company"), "visible_in_all_cities") == true)) {
            // line 45
            echo "                <div class=\"more clearfix\">
                    <div class=\"promo-label label float-left\">
                        <span class=\"label-link\">Промо</span>
                    </div>
                </div>
            ";
        }
        // line 51
        echo "            ";
        if (($this->getAttribute($this->getAttribute((isset($context["properties"]) ? $context["properties"] : null), "company"), "has_products") == true)) {
            // line 52
            echo "                <div class=\"more clearfix\">
                    <div class=\"product-label label float-left\">
                        <a class=\"label-link\" href=\"";
            // line 54
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["properties"]) ? $context["properties"] : null), "company"), "company_products_url"), "html", null, true);
            echo "\">всего ";
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["properties"]) ? $context["properties"] : null), "company"), "products_count"), "html", null, true);
            echo " ";
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["properties"]) ? $context["properties"] : null), "company"), "products_count_title"), "html", null, true);
            echo "</a>
                    </div>
                    <div class=\"date float-left\">
                        <span class=\"updated-date\">Обновлено ";
            // line 57
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["properties"]) ? $context["properties"] : null), "company"), "updated_at"), "html", null, true);
            echo "</span>
                    </div>
                </div>
            ";
        }
        // line 61
        echo "
        </div>
        <div class=\"dropdown white95-bg ";
        // line 63
        if (($this->getAttribute($this->getAttribute((isset($context["properties"]) ? $context["properties"] : null), "company"), "has_products") == false)) {
            echo "g-hidden";
        }
        echo "\">
            <div class=\"product-info\">
                <strong><a href=\"";
        // line 65
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["properties"]) ? $context["properties"] : null), "company"), "company_products_url"), "html", null, true);
        echo "\" class=\"small-link\">";
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute((isset($context["properties"]) ? $context["properties"] : null), "company"), "product"), "title"), "html", null, true);
        echo "</a></strong>
                ";
        // line 66
        if ($this->getAttribute($this->getAttribute($this->getAttribute((isset($context["properties"]) ? $context["properties"] : null), "company"), "product"), "size")) {
            // line 67
            echo "                    <p class=\"size gray60-color\">";
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute((isset($context["properties"]) ? $context["properties"] : null), "company"), "product"), "product_volume_title"), "html", null, true);
            echo " ";
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute((isset($context["properties"]) ? $context["properties"] : null), "company"), "product"), "size"), "html", null, true);
            echo "</p>
                ";
        }
        // line 69
        echo "                ";
        if ($this->getAttribute($this->getAttribute($this->getAttribute((isset($context["properties"]) ? $context["properties"] : null), "company"), "product"), "is_contract_price")) {
            // line 70
            echo "                    <p class=\"price gray60-color\">цена договорная</p>
                ";
        } else {
            // line 72
            echo "                    <p class=\"price gray60-color\">от
                        <strong class=\"red-color
                            ";
            // line 74
            if (($this->getAttribute($this->getAttribute($this->getAttribute((isset($context["properties"]) ? $context["properties"] : null), "company"), "product"), "normalized_price") > 0)) {
                echo "other-currency js-helper-opener";
            }
            echo "\"
                                data-text=\"примерно <span class='red-color'>
                            ";
            // line 76
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute((isset($context["properties"]) ? $context["properties"] : null), "company"), "product"), "normalized_price"), "html", null, true);
            echo "
                            <span class='";
            // line 77
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute((isset($context["properties"]) ? $context["properties"] : null), "company"), "product"), "country_symbol_class"), "html", null, true);
            echo "'></span></span>\"
                                >
                            ";
            // line 79
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute((isset($context["properties"]) ? $context["properties"] : null), "company"), "product"), "price"), "html", null, true);
            echo "
                            <span class=\"";
            // line 80
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute((isset($context["properties"]) ? $context["properties"] : null), "company"), "product"), "currency_symbol_class"), "html", null, true);
            echo "\"></span>
                            <span class=\"currency ";
            // line 81
            if ($this->getAttribute($this->getAttribute($this->getAttribute((isset($context["properties"]) ? $context["properties"] : null), "company"), "product"), "currency_symbol_class")) {
                echo "g-hidden";
            }
            echo "\">";
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute((isset($context["properties"]) ? $context["properties"] : null), "company"), "product"), "currency"), "html", null, true);
            echo "</span>
                        </strong>
                        ";
            // line 83
            if ($this->getAttribute($this->getAttribute($this->getAttribute((isset($context["properties"]) ? $context["properties"] : null), "company"), "product"), "measure")) {
                // line 84
                echo "                            за ";
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute((isset($context["properties"]) ? $context["properties"] : null), "company"), "product"), "measure"), "html", null, true);
                echo "
                        ";
            }
            // line 86
            echo "                    </p>
                ";
        }
        // line 88
        echo "            </div>
        </div>
        <div class=\"arrow\"></div>
    </div>
</script>
";
    }

    public function getTemplateName()
    {
        return "@markup/ui/_map-partials/company_baloon.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  256 => 88,  252 => 86,  246 => 84,  244 => 83,  235 => 81,  231 => 80,  227 => 79,  222 => 77,  218 => 76,  211 => 74,  207 => 72,  203 => 70,  200 => 69,  192 => 67,  190 => 66,  184 => 65,  173 => 61,  166 => 57,  156 => 54,  141 => 45,  113 => 33,  108 => 32,  102 => 30,  89 => 23,  86 => 22,  78 => 20,  72 => 18,  65 => 16,  62 => 15,  57 => 13,  38 => 8,  32 => 6,  30 => 5,  22 => 2,  35 => 23,  29 => 20,  26 => 19,  24 => 2,  19 => 1,  599 => 389,  596 => 388,  590 => 378,  546 => 336,  543 => 335,  537 => 334,  533 => 377,  530 => 335,  527 => 334,  524 => 333,  515 => 326,  512 => 325,  507 => 323,  505 => 322,  503 => 321,  501 => 320,  499 => 319,  497 => 318,  493 => 315,  490 => 314,  483 => 156,  480 => 155,  324 => 160,  322 => 155,  318 => 153,  315 => 152,  294 => 132,  292 => 131,  290 => 130,  288 => 129,  286 => 128,  284 => 127,  278 => 122,  275 => 121,  233 => 78,  229 => 76,  226 => 75,  202 => 53,  199 => 52,  193 => 51,  185 => 42,  182 => 41,  177 => 63,  175 => 41,  149 => 51,  142 => 433,  138 => 44,  136 => 388,  125 => 39,  122 => 378,  120 => 333,  117 => 34,  114 => 325,  112 => 314,  109 => 313,  107 => 152,  100 => 29,  96 => 119,  90 => 116,  85 => 74,  80 => 51,  74 => 16,  58 => 8,  48 => 11,  45 => 3,  186 => 118,  183 => 117,  152 => 52,  146 => 16,  140 => 21,  134 => 20,  129 => 40,  126 => 16,  123 => 15,  111 => 109,  103 => 150,  97 => 100,  94 => 26,  91 => 98,  88 => 75,  82 => 52,  79 => 95,  76 => 19,  73 => 93,  68 => 92,  66 => 12,  63 => 11,  61 => 56,  55 => 7,  53 => 12,  43 => 9,  40 => 6,  37 => 5,  31 => 3,);
    }
}
