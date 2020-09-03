<?php

/* @markup/portal/partial/user-menu.html.twig */
class __TwigTemplate_0f673c4148021487e04c2e86bf43da1b extends Twig_Template
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
        echo "<div id=\"user-menu\" class=\"u-menu drop-wrapper opacity-border\">
    <ul class=\"dropdown list\">
        ";
        // line 3
        if ((isset($context["company"]) ? $context["company"] : null)) {
            // line 4
            echo "            <li class=\"company-name first\">
                <div class=\"rating float-right\">
                    <span class=\"star-mini icon-star-colored\"></span>
                    <span class=\"star-mini icon-star-colored\"></span>
                    <span class=\"star-mini icon-star-colored\"></span>
                </div>
                <a href=\"#\" class=\"company-name-link\">Стальторг</a>
            </li>
        ";
        }
        // line 13
        echo "        <li class=\"private-room\">
            <a class=\"link\" href=\"#\">Личный кабинет</a>
            <div class=\"sec-links\">
                <a class=\"link\" href=\"#\">прайс-лист,</a>
                <a class=\"link\" href=\"#\">статистика,</a>
                <a class=\"link\" href=\"#\">оплата</a>
            </div>
        </li>
        <li class=\"drop-item clearfix\">
            <strong class=\"count ie-radius float-right\">5</strong>
            <a class=\"drop-link\" href=\"#\">Клиенты ожидают ответа</a>
        </li>
        <li class=\"drop-item clearfix\">
            <strong class=\"count ie-radius float-right\">3</strong>
            <a class=\"drop-link\" href=\"#\">Счета и услуги</a>

        </li>
        <li class=\"drop-item clearfix\">
            <strong class=\"count ie-radius float-right\">1</strong>
            <a class=\"drop-link\" href=\"#\">Отзывы о компании</a>
        </li>
        <li class=\"drop-item disabled clearfix\">
            <strong class=\"count ie-radius float-right\">2</strong>
            <span class=\"drop-link\">Жалобы</span>
        </li>
        <li class=\"drop-item disabled clearfix\">
            <strong class=\"count ie-radius float-right\">8</strong>
            <span class=\"drop-link\">Сообщения от модератора</span>
        </li>
        <li class=\"quit drop-item\">
            <a class=\"drop-link\" href=\"#\">Выход</a>
        </li>
    </ul>
</div>";
    }

    public function getTemplateName()
    {
        return "@markup/portal/partial/user-menu.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  36 => 13,  25 => 4,  23 => 3,  19 => 1,  401 => 28,  398 => 27,  340 => 333,  338 => 332,  336 => 331,  243 => 239,  241 => 238,  117 => 115,  115 => 114,  113 => 113,  74 => 75,  66 => 23,  63 => 22,  57 => 19,  55 => 18,  40 => 5,  37 => 4,  31 => 3,  72 => 27,  69 => 10,  61 => 27,  59 => 10,  53 => 6,  50 => 5,  44 => 4,  38 => 3,  32 => 2,);
    }
}
