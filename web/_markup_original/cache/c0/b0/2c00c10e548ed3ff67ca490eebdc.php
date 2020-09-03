<?php

/* @markup/_base_layout.html.twig */
class __TwigTemplate_c0b02c00c10e548ed3ff67ca490eebdc extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
            'stylesheets' => array($this, 'block_stylesheets'),
            'javascript' => array($this, 'block_javascript'),
            'title' => array($this, 'block_title'),
            'body_additional_class' => array($this, 'block_body_additional_class'),
            'body' => array($this, 'block_body'),
            'header' => array($this, 'block_header'),
            'menu' => array($this, 'block_menu'),
            'search_form' => array($this, 'block_search_form'),
            'main_block_additional_class' => array($this, 'block_main_block_additional_class'),
            'side_announcements' => array($this, 'block_side_announcements'),
            'breadcrumbs' => array($this, 'block_breadcrumbs'),
            'banner' => array($this, 'block_banner'),
            'callback' => array($this, 'block_callback'),
            'content' => array($this, 'block_content'),
            'tabs' => array($this, 'block_tabs'),
            'search_more' => array($this, 'block_search_more'),
            'sidebar' => array($this, 'block_sidebar'),
            'buttons' => array($this, 'block_buttons'),
            'footer' => array($this, 'block_footer'),
            'popups' => array($this, 'block_popups'),
            'html_templates' => array($this, 'block_html_templates'),
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        echo "<!DOCTYPE html>
<html>
<head>
    <meta http-equiv=\"Content-Type\" content=\"text/html;charset=UTF-8\"/>
    <meta name=\"viewport\" content=\"
        width=device-width,
        initial-scale=1
        \"/>
    ";
        // line 9
        $this->displayBlock('stylesheets', $context, $blocks);
        // line 27
        echo "
    ";
        // line 29
        echo "
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,600,800&subset=latin,cyrillic-ext,latin-ext,cyrillic' rel='stylesheet' type='text/css'>

    ";
        // line 32
        $this->displayBlock('javascript', $context, $blocks);
        // line 91
        echo "    <!--[if gte IE 8]>
        <link rel=\"stylesheet\" type=\"text/css\" href=\"./markup/css/ie-8-9.css\">
    <![endif]-->

    <!--[if IE 8]>
        <link rel=\"stylesheet\" type=\"text/css\" href=\"./markup/css/ie-8.css\">
    <![endif]-->

    <!--[if lt IE 8]>
        <link rel=\"stylesheet\" href=\"./markup/css/outdatedbrowser.css\" type=\"text/css\" media=\"all\"/>
    <![endif]-->

    <title>";
        // line 103
        $this->displayBlock('title', $context, $blocks);
        echo "</title>
</head>
<body class=\"";
        // line 105
        $this->displayBlock('body_additional_class', $context, $blocks);
        echo "\">
<!--[if lt IE 8]>
<div id=\"outdated\">
    <h6>Ваш браузер устарел!</h6>
    <p>Обновите ваш браузер для правильного отображения этого сайта. <a id=\"btnUpdateBrowser\" href=\"http://outdatedbrowser.com/ru\">Обновить мой браузер </a></p>
    <p class=\"last\"><a href=\"#\" id=\"btnCloseUpdateBrowser\" title=\"Close\">&times;</a></p>
</div>

<script type=\"text/javascript\" src=\"./markup/js/outdatedbrowser.js\"></script>
<script type=\"text/javascript\">
    \$('document').ready(function(){
        outdatedBrowser({
            bgColor: '#f25648',
            color: '#ffffff',
            lowerThan: 'transform',
            languagePath: ''
        });
    });
</script>
<![endif]-->
";
        // line 125
        $this->displayBlock('body', $context, $blocks);
        // line 170
        echo "
";
        // line 171
        $this->displayBlock('html_templates', $context, $blocks);
        // line 174
        echo "</body>
</html>
";
    }

    // line 9
    public function block_stylesheets($context, array $blocks = array())
    {
        // line 10
        echo "        <link rel=\"stylesheet\" href=\"./markup/css/normalize.css\" type=\"text/css\" media=\"all\"/>
        <link rel=\"stylesheet\" href=\"./markup/css/jquery-ui.css\" type=\"text/css\" media=\"all\"/>
        <link rel=\"stylesheet\" href=\"./markup/css/font.css\" type=\"text/css\" media=\"all\"/>
        <link rel=\"stylesheet\" href=\"./markup/css/overrides-font.css\" type=\"text/css\" media=\"all\"/>
        <link rel=\"stylesheet\" href=\"./markup/css/style.css\" type=\"text/css\" media=\"all\"/>
        <link rel=\"stylesheet\" href=\"./markup/css/layouts/layout.css\" type=\"text/css\" media=\"all\"/>
        <link rel=\"stylesheet\" href=\"./markup/css/color.css\" type=\"text/css\" media=\"all\"/>
        <link rel=\"stylesheet\" href=\"./markup/css/form.css\" type=\"text/css\" media=\"all\"/>
        <link rel=\"stylesheet\" href=\"./markup/css/buttons.css\" type=\"text/css\" media=\"all\"/>
        <link rel=\"stylesheet\" href=\"./markup/css/scroll.css\" type=\"text/css\" media=\"all\"/>
        <link rel=\"stylesheet\" href=\"./markup/css/suggest.css\" type=\"text/css\" media=\"all\"/>
        <link rel=\"stylesheet\" href=\"./markup/css/content.css\" type=\"text/css\" media=\"all\"/>
        <link rel=\"stylesheet\" href=\"./markup/js/libs/angular-ui-tree-master/dist/angular-ui-tree.css\" type=\"text/css\" media=\"all\"/>
\t\t<link media=\"only screen and (max-width: 1340px)\" rel=\"stylesheet\" href=\"./markup/css/responsive/for1280.css\" type=\"text/css\"/>
\t\t<link media=\"only screen and (max-width: 1252px)\" rel=\"stylesheet\" href=\"./markup/css/responsive/for1024.css\" type=\"text/css\"/>
\t\t<link media=\"only screen and (max-width: 1023px)\" rel=\"stylesheet\" href=\"./markup/css/responsive/small.css\" type=\"text/css\"/>
    ";
    }

    // line 32
    public function block_javascript($context, array $blocks = array())
    {
        // line 33
        echo "        <script src=\"//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.js\"></script>


        <script type=\"text/javascript\" src=\"./markup/js/libs/momentjs/moment-with-langs.js\"></script>
        <script type=\"text/javascript\" src=\"./markup/js/libs/underscorejs/underscore.js\"></script>
        <script type=\"text/javascript\" src=\"./markup/js/libs/underscore.string/underscore.string.js\"></script>
        <script type=\"text/javascript\" src=\"./markup/js/libs/clndr/clndr.js\"></script>
        <script type=\"text/javascript\" src=\"./markup/js/jquery-ui.min.js\"></script>
        <script type=\"text/javascript\" src=\"./markup/js/modernizr.js\"></script>
        <script type=\"text/javascript\" src=\"./markup/js/jquery.reject.js\"></script>
        <script type=\"text/javascript\" src=\"./markup/js/jquery.formstyler.1.4.9.js\"></script>
        <script type=\"text/javascript\" src=\"./markup/js/jquery-scrolltofixed.js\"></script>
        <script type=\"text/javascript\" src=\"./markup/js/scrollbar.js\"></script>
        <script type=\"text/javascript\" src=\"./markup/js/jquery.placeholder-enhanced.js\"></script>
        <script type=\"text/javascript\" src=\"./markup/js/Brouzie/Behaviors.js\"></script>
        <script type=\"text/javascript\" src=\"./markup/js/Brouzie/Popups.js\"></script>
        <script type=\"text/javascript\" src=\"./markup/js/Brouzie/Popovers.js\"></script>
        <script type=\"text/javascript\" src=\"./markup/js/Brouzie/ExpandableMenu.js\"></script>
        <script type=\"text/javascript\" src=\"./markup/js/Brouzie/Tabs.js\"></script>
        <script type=\"text/javascript\" src=\"./markup/js/main.js\"></script>
        <script type=\"text/javascript\" src=\"./markup/js/project.js\"></script>
        <script type=\"text/javascript\" src=\"./markup/js/respond.js\"></script>
        <script type=\"text/javascript\" src=\"./markup/js/libs/maps/infrastructure.js\"></script>
        <script type=\"text/javascript\" src=\"./markup/js/libs/maps/companyBaloon.js\"></script>
        <script type=\"text/javascript\" src=\"./markup/js/libs/maps/maps.js\"></script>
        <script type=\"text/javascript\" src=\"./markup/js/libs/Angular/angular.js\"></script>
        <script type=\"text/javascript\" src=\"./markup/js/libs/angular-ui-tree-master/dist/angular-ui-tree.js\"></script>
        <script type=\"text/javascript\" src=\"./markup/js/libs/Angular/angular-initialization.js\"></script>
        <script type=\"text/javascript\" src=\"./markup/js/libs/angular-ui-tree-master/Categories.js\"></script>




        ";
        // line 86
        echo "        ";
        // line 87
        echo "                <script type=\"text/javascript\">
            DEBUG = true;
        </script>
    ";
    }

    // line 103
    public function block_title($context, array $blocks = array())
    {
        echo "";
    }

    // line 105
    public function block_body_additional_class($context, array $blocks = array())
    {
        echo "";
    }

    // line 125
    public function block_body($context, array $blocks = array())
    {
        // line 126
        echo "    <div class=\"container\">
        <div class=\"inside-container\">
            <div id=\"header\" class=\"clearfix\">
                <div class=\"wrap\">
                    ";
        // line 130
        $this->displayBlock('header', $context, $blocks);
        // line 131
        echo "                </div>
            </div>

            ";
        // line 134
        $this->displayBlock('menu', $context, $blocks);
        // line 139
        echo "
            ";
        // line 140
        $this->displayBlock('search_form', $context, $blocks);
        // line 143
        echo "
\t\t\t<div id=\"main\" class=\"";
        // line 144
        $this->displayBlock('main_block_additional_class', $context, $blocks);
        echo " clearfix\">
\t\t\t\t";
        // line 145
        $this->displayBlock('side_announcements', $context, $blocks);
        // line 146
        echo "\t\t\t\t<div class=\"wrap clearfix\">
                    ";
        // line 147
        $this->displayBlock('breadcrumbs', $context, $blocks);
        // line 148
        echo "                    ";
        $this->displayBlock('banner', $context, $blocks);
        // line 149
        echo "\t\t\t\t\t";
        $this->displayBlock('callback', $context, $blocks);
        // line 150
        echo "                    <div class=\"main-wrapper wrapper outline clearfix\">
                        ";
        // line 151
        $this->displayBlock('content', $context, $blocks);
        // line 155
        echo "                        ";
        $this->displayBlock('sidebar', $context, $blocks);
        // line 156
        echo "                    </div>
                </div>
            </div>
            ";
        // line 159
        $this->displayBlock('buttons', $context, $blocks);
        // line 160
        echo "        </div>
    </div>

    <div id=\"footer\">
        <div class=\"footer-content wrap\">
            ";
        // line 165
        $this->displayBlock('footer', $context, $blocks);
        // line 166
        echo "        </div>
    </div>
    ";
        // line 168
        $this->displayBlock('popups', $context, $blocks);
    }

    // line 130
    public function block_header($context, array $blocks = array())
    {
        echo "";
    }

    // line 134
    public function block_menu($context, array $blocks = array())
    {
        // line 135
        echo "                <div class=\"main-menu-wrapper\">
                    <div class=\"wrap\"></div>
                </div>
            ";
    }

    // line 140
    public function block_search_form($context, array $blocks = array())
    {
        // line 141
        echo "                <div class=\"search-block clearfix\"></div>
            ";
    }

    // line 144
    public function block_main_block_additional_class($context, array $blocks = array())
    {
        echo "";
    }

    // line 145
    public function block_side_announcements($context, array $blocks = array())
    {
        echo "";
    }

    // line 147
    public function block_breadcrumbs($context, array $blocks = array())
    {
        echo "";
    }

    // line 148
    public function block_banner($context, array $blocks = array())
    {
        echo "";
    }

    // line 149
    public function block_callback($context, array $blocks = array())
    {
        echo "";
    }

    // line 151
    public function block_content($context, array $blocks = array())
    {
        // line 152
        echo "                            ";
        $this->displayBlock('tabs', $context, $blocks);
        // line 153
        echo "                            ";
        $this->displayBlock('search_more', $context, $blocks);
        // line 154
        echo "                        ";
    }

    // line 152
    public function block_tabs($context, array $blocks = array())
    {
        echo "";
    }

    // line 153
    public function block_search_more($context, array $blocks = array())
    {
        echo "";
    }

    // line 155
    public function block_sidebar($context, array $blocks = array())
    {
        echo "";
    }

    // line 159
    public function block_buttons($context, array $blocks = array())
    {
        echo "";
    }

    // line 165
    public function block_footer($context, array $blocks = array())
    {
        echo "";
    }

    // line 168
    public function block_popups($context, array $blocks = array())
    {
        echo "";
    }

    // line 171
    public function block_html_templates($context, array $blocks = array())
    {
        // line 172
        echo "    ";
        echo twig_include($this->env, $context, "@markup/ui/_components/datepicker.html.twig");
        echo "
";
    }

    public function getTemplateName()
    {
        return "@markup/_base_layout.html.twig";
    }

    public function getDebugInfo()
    {
        return array (  380 => 172,  377 => 171,  371 => 168,  365 => 165,  359 => 159,  353 => 155,  347 => 153,  341 => 152,  337 => 154,  334 => 153,  331 => 152,  328 => 151,  316 => 148,  310 => 147,  304 => 145,  298 => 144,  293 => 141,  283 => 135,  280 => 134,  274 => 130,  270 => 168,  266 => 166,  264 => 165,  257 => 160,  255 => 159,  250 => 156,  247 => 155,  245 => 151,  242 => 150,  239 => 149,  236 => 148,  234 => 147,  231 => 146,  225 => 144,  222 => 143,  220 => 140,  217 => 139,  215 => 134,  210 => 131,  208 => 130,  187 => 103,  180 => 87,  178 => 86,  143 => 33,  140 => 32,  111 => 174,  106 => 170,  104 => 125,  81 => 105,  62 => 91,  60 => 32,  52 => 27,  50 => 9,  599 => 389,  596 => 388,  590 => 378,  546 => 336,  543 => 335,  537 => 334,  533 => 377,  530 => 335,  527 => 334,  524 => 333,  515 => 326,  512 => 325,  507 => 323,  505 => 322,  503 => 321,  501 => 320,  499 => 319,  497 => 318,  493 => 315,  490 => 314,  483 => 156,  324 => 160,  322 => 149,  318 => 153,  315 => 152,  294 => 132,  292 => 131,  290 => 140,  288 => 129,  286 => 128,  284 => 127,  278 => 122,  275 => 121,  233 => 78,  229 => 145,  226 => 75,  202 => 126,  199 => 125,  193 => 105,  185 => 42,  182 => 41,  177 => 47,  175 => 41,  149 => 17,  146 => 16,  142 => 433,  138 => 431,  136 => 388,  125 => 379,  122 => 378,  114 => 325,  112 => 314,  109 => 171,  107 => 152,  103 => 150,  100 => 121,  96 => 119,  94 => 118,  90 => 116,  88 => 75,  85 => 74,  82 => 52,  80 => 51,  76 => 103,  74 => 16,  66 => 12,  63 => 11,  58 => 8,  55 => 29,  48 => 4,  45 => 3,  482 => 430,  480 => 155,  478 => 428,  130 => 81,  127 => 80,  120 => 10,  117 => 9,  46 => 12,  44 => 7,  40 => 1,  37 => 4,  31 => 3,);
    }
}
