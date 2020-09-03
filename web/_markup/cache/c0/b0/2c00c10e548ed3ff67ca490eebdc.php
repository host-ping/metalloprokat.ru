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
    <meta name=\"viewport\" content=\"width=device-width,initial-scale=1\"/>
    <meta name=\"viewport\" content=\"width=device-width\">
    ";
        // line 7
        $this->displayBlock('stylesheets', $context, $blocks);
        // line 30
        echo "
    

    <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,600,800&subset=latin,cyrillic-ext,latin-ext,cyrillic' rel='stylesheet' type='text/css'>

    ";
        // line 35
        $this->displayBlock('javascript', $context, $blocks);
        // line 96
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
        // line 108
        $this->displayBlock('title', $context, $blocks);
        echo "</title>
</head>
<body class=\"";
        // line 110
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
        // line 130
        $this->displayBlock('body', $context, $blocks);
        // line 175
        echo "
";
        // line 176
        $this->displayBlock('html_templates', $context, $blocks);
        // line 179
        echo "<div class=\"livebotton\">
\t<a href=\"#\">оставить заявку</a>
</div>
</body>
</html>
";
    }

    // line 7
    public function block_stylesheets($context, array $blocks = array())
    {
        // line 8
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
\t\t<link rel=\"stylesheet\" href=\"./markup/css/corporate.css\" type=\"text/css\"/>
\t\t<link rel=\"stylesheet\" href=\"./markup/css/selectize.css\" type=\"text/css\"/>
\t\t<link rel=\"stylesheet\" href=\"./markup/css/style-form.css\" type=\"text/css\"/>
\t\t
\t\t
    ";
    }

    // line 35
    public function block_javascript($context, array $blocks = array())
    {
        // line 36
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
        <script type=\"text/javascript\" src=\"./markup/js/selectize.min.js\"></script>
        




        ";
        // line 91
        echo "        ";
        // line 92
        echo "                <script type=\"text/javascript\">
            DEBUG = true;
        </script>
    ";
    }

    // line 108
    public function block_title($context, array $blocks = array())
    {
        echo "";
    }

    // line 110
    public function block_body_additional_class($context, array $blocks = array())
    {
        echo "";
    }

    // line 130
    public function block_body($context, array $blocks = array())
    {
        // line 131
        echo "    <div class=\"container\">
        <div class=\"inside-container\">
            <div id=\"header\" class=\"clearfix\">
                <div class=\"wrap\">
                    ";
        // line 135
        $this->displayBlock('header', $context, $blocks);
        // line 136
        echo "                </div>
            </div>

            ";
        // line 139
        $this->displayBlock('menu', $context, $blocks);
        // line 144
        echo "
            ";
        // line 145
        $this->displayBlock('search_form', $context, $blocks);
        // line 148
        echo "
\t\t\t<div id=\"main\" class=\"";
        // line 149
        $this->displayBlock('main_block_additional_class', $context, $blocks);
        echo " clearfix\">
\t\t\t\t";
        // line 150
        $this->displayBlock('side_announcements', $context, $blocks);
        // line 151
        echo "\t\t\t\t<div class=\"wrap clearfix\">
                    ";
        // line 152
        $this->displayBlock('breadcrumbs', $context, $blocks);
        // line 153
        echo "                    ";
        $this->displayBlock('banner', $context, $blocks);
        // line 154
        echo "\t\t\t\t\t";
        $this->displayBlock('callback', $context, $blocks);
        // line 155
        echo "                    <div class=\"main-wrapper wrapper outline clearfix\">
                        ";
        // line 156
        $this->displayBlock('content', $context, $blocks);
        // line 160
        echo "                        ";
        $this->displayBlock('sidebar', $context, $blocks);
        // line 161
        echo "                    </div>
                </div>
            </div>
            ";
        // line 164
        $this->displayBlock('buttons', $context, $blocks);
        // line 165
        echo "        </div>
    </div>

    <div id=\"footer\">
        <div class=\"footer-content wrap\">
            ";
        // line 170
        $this->displayBlock('footer', $context, $blocks);
        // line 171
        echo "        </div>
    </div>
    ";
        // line 173
        $this->displayBlock('popups', $context, $blocks);
    }

    // line 135
    public function block_header($context, array $blocks = array())
    {
        echo "";
    }

    // line 139
    public function block_menu($context, array $blocks = array())
    {
        // line 140
        echo "                <div class=\"main-menu-wrapper\">
                    <div class=\"wrap\"></div>
                </div>
            ";
    }

    // line 145
    public function block_search_form($context, array $blocks = array())
    {
        // line 146
        echo "                <div class=\"search-block clearfix\"></div>
            ";
    }

    // line 149
    public function block_main_block_additional_class($context, array $blocks = array())
    {
        echo "";
    }

    // line 150
    public function block_side_announcements($context, array $blocks = array())
    {
        echo "";
    }

    // line 152
    public function block_breadcrumbs($context, array $blocks = array())
    {
        echo "";
    }

    // line 153
    public function block_banner($context, array $blocks = array())
    {
        echo "";
    }

    // line 154
    public function block_callback($context, array $blocks = array())
    {
        echo "";
    }

    // line 156
    public function block_content($context, array $blocks = array())
    {
        // line 157
        echo "                            ";
        $this->displayBlock('tabs', $context, $blocks);
        // line 158
        echo "                            ";
        $this->displayBlock('search_more', $context, $blocks);
        // line 159
        echo "                        ";
    }

    // line 157
    public function block_tabs($context, array $blocks = array())
    {
        echo "";
    }

    // line 158
    public function block_search_more($context, array $blocks = array())
    {
        echo "";
    }

    // line 160
    public function block_sidebar($context, array $blocks = array())
    {
        echo "";
    }

    // line 164
    public function block_buttons($context, array $blocks = array())
    {
        echo "";
    }

    // line 170
    public function block_footer($context, array $blocks = array())
    {
        echo "";
    }

    // line 173
    public function block_popups($context, array $blocks = array())
    {
        echo "";
    }

    // line 176
    public function block_html_templates($context, array $blocks = array())
    {
        // line 177
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
        return array (  387 => 177,  384 => 176,  378 => 173,  372 => 170,  366 => 164,  360 => 160,  354 => 158,  348 => 157,  344 => 159,  341 => 158,  338 => 157,  335 => 156,  329 => 154,  323 => 153,  305 => 149,  300 => 146,  297 => 145,  290 => 140,  287 => 139,  281 => 135,  277 => 173,  273 => 171,  271 => 170,  264 => 165,  262 => 164,  257 => 161,  254 => 160,  252 => 156,  243 => 153,  241 => 152,  238 => 151,  236 => 150,  232 => 149,  229 => 148,  227 => 145,  224 => 144,  222 => 139,  217 => 136,  215 => 135,  209 => 131,  206 => 130,  200 => 110,  194 => 108,  187 => 92,  185 => 91,  148 => 36,  145 => 35,  120 => 8,  117 => 7,  108 => 179,  106 => 176,  103 => 175,  78 => 110,  73 => 108,  59 => 96,  57 => 35,  50 => 30,  48 => 7,  40 => 1,  392 => 226,  389 => 225,  383 => 215,  339 => 173,  336 => 172,  330 => 171,  324 => 170,  320 => 214,  317 => 152,  314 => 171,  311 => 150,  308 => 169,  302 => 165,  299 => 164,  249 => 155,  246 => 154,  216 => 86,  213 => 85,  170 => 44,  167 => 43,  159 => 34,  156 => 33,  152 => 39,  150 => 33,  136 => 21,  133 => 20,  129 => 270,  125 => 268,  123 => 225,  112 => 216,  109 => 215,  107 => 169,  104 => 168,  101 => 130,  99 => 116,  94 => 113,  92 => 85,  89 => 84,  87 => 43,  82 => 40,  80 => 20,  72 => 16,  69 => 15,  63 => 13,  58 => 10,  55 => 9,  46 => 4,  43 => 3,  31 => 4,  28 => 3,);
    }
}
