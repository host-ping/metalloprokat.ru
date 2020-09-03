<?php

/* markup/css/minisite-themes/theme-template.css.twig */
class __TwigTemplate_6b71a24a49597496ad2b4c8e28281f79 extends Twig_Template
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
        // line 2
        echo "    /* generated at ";
        echo twig_date_format_filter($this->env, "now", "d.m.Y H:i:s");
        echo " */

    .company-announcement{
    background: ";
        // line 5
        echo $this->getAttribute((isset($context["colors"]) ? $context["colors"] : null), "background", array(), "array");
        echo ";
    }

    .company-announcement .company-info-wrapper{
    behavior: url(/markup/js/PIE.htc);
    zoom: 1;
    background-color: rgba(";
        // line 11
        echo $this->getAttribute((isset($context["colors"]) ? $context["colors"] : null), "rgb_hover", array(), "array");
        echo ", 0.85);
    -pie-background: rgba(";
        // line 12
        echo $this->getAttribute((isset($context["colors"]) ? $context["colors"] : null), "rgb_hover", array(), "array");
        echo ", 0.85);
    }

    .mini-site .side-left{
    background: #f7f1e4;
    border-right: 4px solid #fff;
    }
    .mini-site .side-left.without-products-block{
    background: ";
        // line 20
        echo $this->getAttribute((isset($context["colors"]) ? $context["colors"] : null), "background", array(), "array");
        echo ";

    }

    .mini-site .right-announcement-wrapper.employees-block-wrapper .js-fixed-side-banner{
    background: ";
        // line 25
        echo $this->getAttribute((isset($context["colors"]) ? $context["colors"] : null), "background", array(), "array");
        echo "
    }
    .mini-site .premium-product-block.employees-block{
    border-color: #fff;
    }
    .mini-site .side-left .products-wrapper{
    background: ";
        // line 31
        echo $this->getAttribute((isset($context["colors"]) ? $context["colors"] : null), "background", array(), "array");
        echo ";
    }

    .mini-site .content{
    background: ";
        // line 35
        echo $this->getAttribute((isset($context["colors"]) ? $context["colors"] : null), "background", array(), "array");
        echo ";
    }

    .company-menu{
    border-bottom: 1px solid #fff;
    }

    ";
        // line 43
        echo "    ";
        // line 44
        echo "        ";
        // line 45
        echo "    ";
        // line 46
        echo "
    .mini-site .deals .item{
    background: ";
        // line 48
        echo $this->getAttribute((isset($context["colors"]) ? $context["colors"] : null), "background", array(), "array");
        echo ";
    border-color: #fff;
    }
    .mini-site .deals .item .hover-block .dropdown{
    background: ";
        // line 52
        echo $this->getAttribute((isset($context["colors"]) ? $context["colors"] : null), "background", array(), "array");
        echo "
    }
    .mini-site .deals .item .hover-block .dropdown:hover{
    background: ";
        // line 55
        echo $this->getAttribute((isset($context["colors"]) ? $context["colors"] : null), "hover_color", array(), "array");
        echo "
    }
    .mini-site .search-wrapper .form-text{
    background: ";
        // line 58
        echo $this->getAttribute((isset($context["colors"]) ? $context["colors"] : null), "bg_color", array(), "array");
        echo ";
    border: 1px solid ";
        // line 59
        echo $this->getAttribute((isset($context["colors"]) ? $context["colors"] : null), "bg_color", array(), "array");
        echo ";
    color: ";
        // line 60
        echo $this->getAttribute((isset($context["colors"]) ? $context["colors"] : null), "hover_color", array(), "array");
        echo ";
    }

    .mini-site .search-wrapper .form-text:focus{
    background: #fff;
    color: ";
        // line 65
        echo $this->getAttribute((isset($context["colors"]) ? $context["colors"] : null), "bg_color", array(), "array");
        echo ";
    }
    .mini-site .icon-search-small:before{
    color: ";
        // line 68
        echo $this->getAttribute((isset($context["colors"]) ? $context["colors"] : null), "hover_color", array(), "array");
        echo ";
    }
    .mini-site .search-wrapper .form-text:focus + .icon-search-small:before{
    color: ";
        // line 71
        echo $this->getAttribute((isset($context["colors"]) ? $context["colors"] : null), "bg_color", array(), "array");
        echo ";
    }
    .mini-site .search-wrapper .search-field::-webkit-input-placeholder{
    color: ";
        // line 74
        echo $this->getAttribute((isset($context["colors"]) ? $context["colors"] : null), "hover_color", array(), "array");
        echo ";
    }

    .mini-site .similar-list .item .links-wrapper .report,
    .mini-site .similar-list .item .links-wrapper .favorites{
    border: 1px solid ";
        // line 79
        echo $this->getAttribute((isset($context["colors"]) ? $context["colors"] : null), "background", array(), "array");
        echo ";
    color: ";
        // line 80
        echo $this->getAttribute((isset($context["colors"]) ? $context["colors"] : null), "bg_color", array(), "array");
        echo "
    }

    .mini-site .icon-complaint:before,
    .mini-site .icon-favorite-active:before,
    .mini-site .icon-favorite:before{
    color: ";
        // line 86
        echo $this->getAttribute((isset($context["colors"]) ? $context["colors"] : null), "bg_color", array(), "array");
        echo "
    }

    .mini-site .similar-list .item:hover{
    background: ";
        // line 90
        echo $this->getAttribute((isset($context["colors"]) ? $context["colors"] : null), "hover_color", array(), "array");
        echo ";
    }

    .mini-site .company-contacts{
    border-bottom: 1px solid #fff;
    }

    .mini-site .dropdown{
    background: ";
        // line 98
        echo $this->getAttribute((isset($context["colors"]) ? $context["colors"] : null), "background", array(), "array");
        echo ";
    }

    .mini-site .similar-list .item{
    border-bottom: 1px dashed #fff;
    }

    .mini-site .result-tabs-wrapper,
    .mini-site .reviews .item,
    .mini-site .filters-block{
    border-bottom: 1px solid #fff;
    }

    .mini-site .tabs li.active{
    border-color: #fff;
    background: ";
        // line 113
        echo $this->getAttribute((isset($context["colors"]) ? $context["colors"] : null), "background", array(), "array");
        echo ";
    }

    .mini-site .tooltip{
    background: ";
        // line 117
        echo $this->getAttribute((isset($context["colors"]) ? $context["colors"] : null), "hover_color", array(), "array");
        echo ";
    border-color: ";
        // line 118
        echo $this->getAttribute((isset($context["colors"]) ? $context["colors"] : null), "background", array(), "array");
        echo ";
    }

    .mini-site .tooltip.with-bullet i{
    border-color: ";
        // line 122
        echo $this->getAttribute((isset($context["colors"]) ? $context["colors"] : null), "background", array(), "array");
        echo " transparent transparent transparent;
    }
    .mini-site .chat-bubble-arrow{
    border-color: ";
        // line 125
        echo $this->getAttribute((isset($context["colors"]) ? $context["colors"] : null), "hover_color", array(), "array");
        echo " transparent transparent transparent;

    }
    .mini-site .big-pattern.has-logo{
    background: rgba(";
        // line 129
        echo $this->getAttribute((isset($context["colors"]) ? $context["colors"] : null), "rgb_hover", array(), "array");
        echo ", .85);
    }
    .mini-site .is-gradiented:after{
    background: linear-gradient(left, RGBA(";
        // line 132
        echo $this->getAttribute((isset($context["colors"]) ? $context["colors"] : null), "rgba", array(), "array");
        echo "), ";
        echo $this->getAttribute((isset($context["colors"]) ? $context["colors"] : null), "background", array(), "array");
        echo ") !important;
    background: -moz-linear-gradient(left, RGBA(";
        // line 133
        echo $this->getAttribute((isset($context["colors"]) ? $context["colors"] : null), "rgba", array(), "array");
        echo "), ";
        echo $this->getAttribute((isset($context["colors"]) ? $context["colors"] : null), "background", array(), "array");
        echo ") !important;
    background: -webkit-linear-gradient(left, RGBA(";
        // line 134
        echo $this->getAttribute((isset($context["colors"]) ? $context["colors"] : null), "rgba", array(), "array");
        echo "), ";
        echo $this->getAttribute((isset($context["colors"]) ? $context["colors"] : null), "background", array(), "array");
        echo ") !important;
    background: -o-linear-gradient(left, RGBA(";
        // line 135
        echo $this->getAttribute((isset($context["colors"]) ? $context["colors"] : null), "rgba", array(), "array");
        echo "), ";
        echo $this->getAttribute((isset($context["colors"]) ? $context["colors"] : null), "background", array(), "array");
        echo ") !important;
    }
    .mini-site .similar-list .item:hover .is-gradiented:after{
    background: linear-gradient(left, RGBA(";
        // line 138
        echo $this->getAttribute((isset($context["colors"]) ? $context["colors"] : null), "rgb_hover", array(), "array");
        echo ", .2), ";
        echo $this->getAttribute((isset($context["colors"]) ? $context["colors"] : null), "hover_color", array(), "array");
        echo ") !important;
    background: -moz-linear-gradient(left, RGBA(";
        // line 139
        echo $this->getAttribute((isset($context["colors"]) ? $context["colors"] : null), "rgb_hover", array(), "array");
        echo ", .2), ";
        echo $this->getAttribute((isset($context["colors"]) ? $context["colors"] : null), "hover_color", array(), "array");
        echo ") !important;
    background: -webkit-linear-gradient(left, RGBA(";
        // line 140
        echo $this->getAttribute((isset($context["colors"]) ? $context["colors"] : null), "rgb_hover", array(), "array");
        echo ", .2), ";
        echo $this->getAttribute((isset($context["colors"]) ? $context["colors"] : null), "hover_color", array(), "array");
        echo ") !important;
    background: -o-linear-gradient(left, RGBA(";
        // line 141
        echo $this->getAttribute((isset($context["colors"]) ? $context["colors"] : null), "rgb_hover", array(), "array");
        echo ", .2), ";
        echo $this->getAttribute((isset($context["colors"]) ? $context["colors"] : null), "hover_color", array(), "array");
        echo ") !important;
    }
    .mini-site .similar-list .left .title .ie-gradient{
    -ms-filter: \"progid:DXImageTransform.Microsoft.gradient(GradientType=1, StartColorStr='";
        // line 144
        echo $this->getAttribute((isset($context["colors"]) ? $context["colors"] : null), "argb_background_start", array(), "array");
        echo "', EndColorStr='";
        echo $this->getAttribute((isset($context["colors"]) ? $context["colors"] : null), "argb_background_finish", array(), "array");
        echo "')\";
    }
    .mini-site .is-gradiented-bottom:after{
    background: linear-gradient(top, RGBA(";
        // line 147
        echo $this->getAttribute((isset($context["colors"]) ? $context["colors"] : null), "rgba", array(), "array");
        echo "), ";
        echo $this->getAttribute((isset($context["colors"]) ? $context["colors"] : null), "background", array(), "array");
        echo ");
    background: -moz-linear-gradient(top, RGBA(";
        // line 148
        echo $this->getAttribute((isset($context["colors"]) ? $context["colors"] : null), "rgba", array(), "array");
        echo "), ";
        echo $this->getAttribute((isset($context["colors"]) ? $context["colors"] : null), "background", array(), "array");
        echo ");
    background: -webkit-linear-gradient(top, RGBA(";
        // line 149
        echo $this->getAttribute((isset($context["colors"]) ? $context["colors"] : null), "rgba", array(), "array");
        echo "), ";
        echo $this->getAttribute((isset($context["colors"]) ? $context["colors"] : null), "background", array(), "array");
        echo ");
    background: -o-linear-gradient(top, RGBA(";
        // line 150
        echo $this->getAttribute((isset($context["colors"]) ? $context["colors"] : null), "rgba", array(), "array");
        echo "), ";
        echo $this->getAttribute((isset($context["colors"]) ? $context["colors"] : null), "background", array(), "array");
        echo ");
    }
    .mini-site .deals .item:hover .is-gradiented-bottom:after{
    background: linear-gradient(top, RGBA(";
        // line 153
        echo $this->getAttribute((isset($context["colors"]) ? $context["colors"] : null), "rgb_hover", array(), "array");
        echo ", .2), ";
        echo $this->getAttribute((isset($context["colors"]) ? $context["colors"] : null), "hover_color", array(), "array");
        echo ");
    background: -moz-linear-gradient(top, RGBA(";
        // line 154
        echo $this->getAttribute((isset($context["colors"]) ? $context["colors"] : null), "rgb_hover", array(), "array");
        echo ", .2), ";
        echo $this->getAttribute((isset($context["colors"]) ? $context["colors"] : null), "hover_color", array(), "array");
        echo ");
    background: -webkit-linear-gradient(top, RGBA(";
        // line 155
        echo $this->getAttribute((isset($context["colors"]) ? $context["colors"] : null), "rgb_hover", array(), "array");
        echo ", .2), ";
        echo $this->getAttribute((isset($context["colors"]) ? $context["colors"] : null), "hover_color", array(), "array");
        echo ");
    background: -o-linear-gradient(top, RGBA(";
        // line 156
        echo $this->getAttribute((isset($context["colors"]) ? $context["colors"] : null), "rgb_hover", array(), "array");
        echo ", .2), ";
        echo $this->getAttribute((isset($context["colors"]) ? $context["colors"] : null), "hover_color", array(), "array");
        echo ");
    }
    .mini-site .is-gradiented-bottom .ie-gradient-bottom{
    -ms-filter: \"progid:DXImageTransform.Microsoft.gradient(GradientType=0, StartColorStr='";
        // line 159
        echo $this->getAttribute((isset($context["colors"]) ? $context["colors"] : null), "argb_background_start", array(), "array");
        echo "', EndColorStr='";
        echo $this->getAttribute((isset($context["colors"]) ? $context["colors"] : null), "argb_background_finish", array(), "array");
        echo "')\";
    }
    .mini-site .item:hover .is-gradiented-bottom .ie-gradient-bottom{
    -ms-filter: \"progid:DXImageTransform.Microsoft.gradient(GradientType=0, StartColorStr='";
        // line 162
        echo $this->getAttribute((isset($context["colors"]) ? $context["colors"] : null), "argb_hover_start", array(), "array");
        echo "', EndColorStr='";
        echo $this->getAttribute((isset($context["colors"]) ? $context["colors"] : null), "argb_hover_finish", array(), "array");
        echo "')\";
    }
    .mini-site .similar-list .item:hover .left .title .ie-gradient{
    -ms-filter: \"progid:DXImageTransform.Microsoft.gradient(GradientType=1, StartColorStr='";
        // line 165
        echo $this->getAttribute((isset($context["colors"]) ? $context["colors"] : null), "argb_hover_start", array(), "array");
        echo "', EndColorStr='";
        echo $this->getAttribute((isset($context["colors"]) ? $context["colors"] : null), "argb_hover_finish", array(), "array");
        echo "')\";
    }
    .mini-site .see-more{
    background: ";
        // line 168
        echo $this->getAttribute((isset($context["colors"]) ? $context["colors"] : null), "bg_color", array(), "array");
        echo ";
    }
    .mini-site .see-more-block .loading-mask{
    background-color: ";
        // line 171
        echo $this->getAttribute((isset($context["colors"]) ? $context["colors"] : null), "bg_color", array(), "array");
        echo ";
    }
    .mini-site .is-bordered{
    border-color: ";
        // line 174
        echo $this->getAttribute((isset($context["colors"]) ? $context["colors"] : null), "bg_color", array(), "array");
        echo "
    }

    .mini-site .user.link,
    .mini-site .price{
    color: ";
        // line 179
        echo $this->getAttribute((isset($context["colors"]) ? $context["colors"] : null), "primary", array(), "array");
        echo ";
    }
    .mini-site .primary{
    background: ";
        // line 182
        echo $this->getAttribute((isset($context["colors"]) ? $context["colors"] : null), "primary", array(), "array");
        echo ";
    }

    .mini-site .deals .link,
    .mini-site .offices.icon-check:after,
    .mini-site .similar-list .link,
    .mini-site .drop-link,
    .mini-site .link{
    color: ";
        // line 190
        echo $this->getAttribute((isset($context["colors"]) ? $context["colors"] : null), "secondary", array(), "array");
        echo ";
    }
    .mini-site .secondary{
    background: ";
        // line 193
        echo $this->getAttribute((isset($context["colors"]) ? $context["colors"] : null), "secondary", array(), "array");
        echo ";
    }
";
    }

    public function getTemplateName()
    {
        return "markup/css/minisite-themes/theme-template.css.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  420 => 193,  414 => 190,  403 => 182,  397 => 179,  389 => 174,  383 => 171,  377 => 168,  369 => 165,  361 => 162,  353 => 159,  345 => 156,  339 => 155,  333 => 154,  327 => 153,  319 => 150,  313 => 149,  307 => 148,  301 => 147,  293 => 144,  285 => 141,  279 => 140,  273 => 139,  267 => 138,  259 => 135,  253 => 134,  247 => 133,  241 => 132,  235 => 129,  228 => 125,  222 => 122,  215 => 118,  211 => 117,  204 => 113,  186 => 98,  175 => 90,  168 => 86,  159 => 80,  155 => 79,  147 => 74,  141 => 71,  135 => 68,  129 => 65,  121 => 60,  117 => 59,  113 => 58,  107 => 55,  101 => 52,  94 => 48,  90 => 46,  88 => 45,  86 => 44,  84 => 43,  74 => 35,  67 => 31,  58 => 25,  50 => 20,  39 => 12,  35 => 11,  26 => 5,  23 => 15,  21 => 14,  19 => 2,);
    }
}
