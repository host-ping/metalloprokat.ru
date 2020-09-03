<?php

/* @markup/corporate/announcements.html.twig */
class __TwigTemplate_b45488537e6d43931abc10c000c975ae extends Twig_Template
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
        echo "Corporate site - About";
    }

    // line 3
    public function block_header_menu($context, array $blocks = array())
    {
        // line 4
        echo "    ";
        $context["activeMenu"] = "media";
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
        <div class=\"announcement-info-wrapper float-right\">
            <h1 class=\"title\">Реклама</h1>
            <table class=\"announcement-info\">
                <thead>
                    <tr class=\"col\">
                        <td class=\"name col\">Название</td>
                        <td class=\"size col\">Размер</td>
                        <td class=\"price col\">Стоимость за месяц</td>
                        <td class=\"order col\"></td>
                    </tr>
                </thead>
                <tbody>
                    <tr class=\"row odd\">
                        <td class=\"name col\">
                            <p>Премиум</p>
                        </td>
                        <td class=\"size col\">
                            <p>960x90</p>
                        </td>
                        <td class=\"price col\">
                            <p>27000 <span class=\"icon-rouble\"></span></p>
                            <p class=\"status-text green-color\">Свободен</p>
                        </td>
                        <td class=\"order col\">
                            <a class=\"buy-btn button green-bg ie-radius\" href=\"#\">Заказать</a>
                        </td>
                    </tr>
                    <tr class=\"row even\">
                        <td class=\"name col\">
                            <p>Центральный-1</p>
                        </td>
                        <td class=\"size col\">
                            <p>960x90</p>
                        </td>
                        <td class=\"price col\">
                            <p>27000 <span class=\"icon-rouble\"></span></p>
                            <p class=\"status-text blue-color\">Зарезервирован с 29.01.2014 по 29.01.2015</p>
                        </td>
                        <td class=\"order col\">
                            <a class=\"buy-btn button green-bg ie-radius\" href=\"#\">Заказать</a>
                        </td>
                    </tr>
                    <tr class=\"row odd\">
                        <td class=\"name col\">
                            <p>Центральный-1</p>
                        </td>
                        <td class=\"size col\">
                            <p>960x90</p>
                        </td>
                        <td class=\"price col\">
                            <p>27000 <span class=\"icon-rouble\"></span></p>
                            <p class=\"status-text red-color\">Выкуплен с 29.01.2013 по 29.01.2014</p>
                        </td>
                        <td class=\"order col\">
                            <a class=\"buy-btn button green-bg ie-radius\" href=\"#\">Заказать</a>
                        </td>
                    </tr>
                    <tr class=\"row even\">
                        <td class=\"name col\">
                            <p>Центральный-1</p>
                        </td>
                        <td class=\"size col\">
                            <p>960x90</p>
                        </td>
                        <td class=\"price col\">
                            <p>27000 <span class=\"icon-rouble\"></span></p>
                            <p class=\"status-text green-color\">Свободен</p>
                        </td>
                        <td class=\"order col\">
                            <a class=\"buy-btn button green-bg ie-radius\" href=\"#\">Заказать</a>
                        </td>
                    </tr>
                    <tr class=\"row odd\">
                        <td class=\"name col\">
                            <p>Центральный-1</p>
                        </td>
                        <td class=\"size col\">
                            <p>960x90</p>
                        </td>
                        <td class=\"price col\">
                            <p>27000 <span class=\"icon-rouble\"></span></p>
                            <p class=\"status-text green-color\">Свободен</p>
                        </td>
                        <td class=\"order col\">
                            <a class=\"buy-btn button green-bg ie-radius\" href=\"#\">Заказать</a>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class=\"button-block\">
                <ul class=\"list\">
                    <li class=\"item\">
                        <span class=\"link clickable js-popup-opener\" data-popup=\"#requirements-for-announcement\">Требования к баннеру</span>
                    </li>
                </ul>
            </div>
        </div>
        <div class=\"schema float-left\">
            <img src=\"/markup/pic/mockup.png\" alt=\"image descriptions\" width=\"200\" class=\"clickable js-popup-opener\" data-popup=\"#photo\"/>
        </div>
    </div>
";
    }

    public function getTemplateName()
    {
        return "@markup/corporate/announcements.html.twig";
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
