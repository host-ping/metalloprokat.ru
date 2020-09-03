<?php

/* @markup/corporate/contacts.html.twig */
class __TwigTemplate_433157992ba6bd03fe89f5609c777db3 extends Twig_Template
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
        echo "Corporate site - Contacts";
    }

    // line 3
    public function block_header_menu($context, array $blocks = array())
    {
        // line 4
        echo "    ";
        $context["activeMenu"] = "contacts";
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
        echo "    <div id=\"content\" class=\"corporate-content content-wide\">
        <div class=\"contact-wrapper clearfix\">
            <div class=\"contact-left float-left\">
                <p class=\"big-phone\">+7 495 984-06-52</p>
                <ul class=\"contacts clearfix\">
                    <li class=\"item first float-left\">
                        <div class=\"head-title\">
                            <strong>Наш адрес</strong>
                        </div>
                        <p>Москва, Большая Никитская, 22/2</p>
                    </li>
                    <li class=\"item float-left\">
                        <div class=\"head-title\">
                            <strong>Адрес для бухгалтерии</strong>
                        </div>
                        <p>123308, г.Москва, ОПС № 308, а/я 29</p>
                    </li>
                    <li class=\"item float-left\">
                        <div class=\"head-title\">
                            <strong>Электронная почта</strong>
                        </div>
                        <a class=\"mail-link\" href=\"mailto:info@metalloprokat.ru\">info@metalloprokat.ru</a>
                    </li>
                </ul>
                <div class=\"btn-wrapper\">
                    <a class=\"send-button button red-bg ie-radius ie-radius\" href=\"#\">Написать письмо</a>
                </div>
            </div>
            <div class=\"map-wrapper is-bordered float-right\">
                <div class=\"heading clearfix\">
                    <a class=\"link float-right\" href=\"#\">Открыть в Яндекс.Картах</a>
                </div>
                <div class=\"map\">
                    <span class=\"map-point icon-position\" style=\"top: 50px; left: 150px;\"></span>
                    <img alt=\"image description\" src=\"./markup/pic/mini-site-map.jpg\">
                </div>
            </div>
        </div>

    </div>
";
    }

    public function getTemplateName()
    {
        return "@markup/corporate/contacts.html.twig";
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
