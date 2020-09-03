<?php

/* @markup/corporate/media.html.twig */
class __TwigTemplate_0414539509166b4ec77d44964eb58d05 extends Twig_Template
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
        echo "Corporate site - Media";
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
        echo "    <div id=\"content\" class=\"corporate-content content-wide\">
        <div class=\"announcement-wrapper\">
            <h1 class=\"title\">Представляем вашему вниманию распрекрасный баннер</h1>
            <img src=\"./markup/pic/media-banner.png\" alt=\"image description\"/>
        </div>
        <div class=\"media-wrapper clearfix\">
            <div class=\"announcement-privileges float-left\">
                <ol class=\"list\">
                    <li class=\"item\">
                        <div class=\"title\">Всегда на виду</div>
                        <p class=\"text\">Функционально готовый сайт, наполненный тестовым содержанием. <a class=\"link\"
                            href=\"#\">Прототип сайта</a> допускает наличие ошибок, выявление которых затруднительно до размещения реального
                            содержания Сайта. </p>
                    </li>
                    <li class=\"item\">
                        <div class=\"title\">Большой размер</div>
                        <p class=\"text\">Первая итерация сайта, когда с 1С-Битрикс интегрированы шаблоны сайта,
                            <a class=\"link\" href=\"#\">настроена структура</a> сайта (разделы и подразделы) и
                            настроены системные страницы. </p>
                    </li>
                    <li class=\"item\">
                        <div class=\"title\">Оплата за результат</div>
                        <p class=\"text\"><a class=\"link\" href=\"#\">Работа сотрудников</a> Заказчика осуществляется в стандартном административном
                            интерфейсе 1С-Битрикс без разработки для них дополнительных Интерфейсов</p>
                    </li>
                </ol>
            </div>
            <div class=\"user-reviews float-right\">
                <div class=\"block-title\">
                    <a class=\"link\" href=\"#\">Отзывы клиентов</a>
                </div>
                <div class=\"review\">
                    <p class=\"text\">Мы в восторге от того, что стали клиентами!</p>
                    <p class=\"text\">Денег столько, что фантазия иссякла!</p>
                </div>
                <div class=\"user-info-block\">
                    <div class=\"user-photo\">
                        <img class=\"ie-radius\" src=\"./markup/pic/user-img.png\" alt=\"image description\"/>
                    </div>
                    <div class=\"user-info\">
                        <p class=\"text\">Михаил Лесной</p>
                        <a class=\"link\" href=\"#\">Авангард Престиж</a>
                    </div>
                </div>
            </div>
            <div id=\"requirements-for-announcement\" class=\"requirements-for-announcement popup-block opacity-border large\">
                <div class=\"popup-content\">
                    <div class=\"title-popup\">Требования к баннеру</div>
                    <span class=\"close-popup icon-popup-close js-popup-closer clickable\"></span>
                    <ol class=\"list\">
                        <li class=\"item\">Поддерживаемые форматы: GIF, JPEG, PNG, SWF.</li>
                        <li class=\"item\">Размер файла баннера не должен превышать 50 кб</li>
                        <li class=\"item\">Альтернативный текст для баннера не может быть больше 100 символов.</li>
                        <li class=\"item\">Рекламный баннер должен иметь видимые границы (обведен в рамку, не совпадающую с цветом фона).</li>
                        <li class=\"item\">Не принимаются баннеры с мигающими крупными графическими или текстовыми элементами и/или
                            фоном, с резкими перемещениями элементов, существенно отвлекающие пользователя от
                            взаимодействия со страницей сайта (изучения содержания страницы или ввода запроса)<sup>1</sup></li>
                    </ol>
                    <div class=\"footnote\">
                        <p><sup>1</sup>Миганием, например, можно считать изменение цвета элемента (т. е. любого из параметров по шкале
                        hue-saturation-brightness) более, чем на 40%, происходящее регулярно с частотой более 1 раза в 2
                        секунды. Резким перемещением можно считать изменение расположения элемента на баннере, происходящее
                        неоднократно со скоростью более, чем на 100% от размера элемента (или на 50% от размера баннера) за 1 секунду.</p>

                        <p>Приведенные оценки не являются критерием соответствия баннера требованиям к баннерной рекламе,
                            а скорее, иллюстрацией нашей системы оценок. Баннер с описанными выше проблемами креатива
                            практически гарантированно будет отклонен как несоответствующий требованиям к баннерной
                            рекламе. Однако возможны ситуации, когда баннер с гораздо меньшими «численными» значениями
                            изменений, будет признан раздражающий, и наоборот, будет допущен баннер с бОльшими
                            значениями изменений.</p>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <div class=\"button-block outline\">
        <ul class=\"list\">
            <li class=\"item\">
                <a class=\"buy-btn button green-bg ie-radius\" href=\"#\">Купить немедленно</a>
                <span class=\"link clickable js-popup-opener\" data-popup=\"#requirements-for-announcement\">Требования к баннеру</span>
            </li>
        </ul>
    </div>
";
    }

    public function getTemplateName()
    {
        return "@markup/corporate/media.html.twig";
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
