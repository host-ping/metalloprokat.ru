<?php

/* @markup/mini-site/_mini_layout.html.twig */
class __TwigTemplate_c2264ec51681983996db91ccf5622e08 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = $this->env->loadTemplate("markup/html/_base_layout.html.twig");

        $this->blocks = array(
            'stylesheets' => array($this, 'block_stylesheets'),
            'body' => array($this, 'block_body'),
            'header' => array($this, 'block_header'),
            'login' => array($this, 'block_login'),
            'companyBanner' => array($this, 'block_companyBanner'),
            'side_announcements' => array($this, 'block_side_announcements'),
            'banner' => array($this, 'block_banner'),
            'sidebar' => array($this, 'block_sidebar'),
            'content' => array($this, 'block_content'),
            'filters' => array($this, 'block_filters'),
            'tabs' => array($this, 'block_tabs'),
            'footer' => array($this, 'block_footer'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "markup/html/_base_layout.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 2
    public function block_stylesheets($context, array $blocks = array())
    {
        // line 3
        echo "    ";
        $this->displayParentBlock("stylesheets", $context, $blocks);
        echo "
    <link rel=\"stylesheet\" href=\"./markup/css/mini-site.css\" type=\"text/css\"/>
    <link rel=\"stylesheet\" href=\"./markup/css/private.css\" type=\"text/css\"/>

    ";
        // line 8
        echo "    <link rel=\"stylesheet\" href=\"./index.php?render_css=";
        echo twig_escape_filter($this->env, (($this->getAttribute((isset($context["query"]) ? $context["query"] : null), "css_theme", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute((isset($context["query"]) ? $context["query"] : null), "css_theme"), "default-theme")) : ("default-theme")), "html", null, true);
        echo ".css.twig\" type=\"text/css\"/>

";
    }

    // line 12
    public function block_body($context, array $blocks = array())
    {
        // line 13
        echo "<div class=\"mini-site container\">
    <div class=\"inside-container\">
        <div id=\"header\" class=\"clearfix\">
            <div class=\"wrap\">
                ";
        // line 17
        $this->displayBlock('header', $context, $blocks);
        // line 40
        echo "            </div>
        </div>
        ";
        // line 42
        $this->displayBlock('companyBanner', $context, $blocks);
        // line 77
        echo "
        <div id=\"main\" class=\"clearfix\">
            ";
        // line 79
        $this->displayBlock('side_announcements', $context, $blocks);
        // line 239
        echo "            <div class=\"wrap clearfix\">
                ";
        // line 240
        $this->displayBlock('banner', $context, $blocks);
        // line 245
        echo "
                <div class=\"wrapper outline clearfix\">
                    <div class=\"table-container\">
                        ";
        // line 248
        $this->displayBlock('sidebar', $context, $blocks);
        // line 393
        echo "                        ";
        $this->displayBlock('content', $context, $blocks);
        // line 399
        echo "                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

    <div id=\"footer\">
        <div class=\"footer-content wrap\">
            ";
        // line 409
        $this->displayBlock('footer', $context, $blocks);
        // line 452
        echo "        </div>
    </div>
";
        // line 454
        $this->env->loadTemplate("@markup/_popups.html.twig")->display($context);
        // line 455
        echo "<script>
    \$(document).ready(function () {
        \$('.js-show-all-employees').bind('click', function (e) {
            \$el = \$(e.target);
            \$el.parent().addClass('g-hidden');

            \$('.js-all-employees').removeClass('g-hidden');

            \$(window).trigger('resize');

            /* var \$rightFixedSide = \$('.right-announcement-wrapper .js-fixed-side-banner');
             \$rightFixedSide.scrollToFixed({
             limit: function () {
             return \$('#footer').offset().top - \$rightFixedSide.outerHeight(true);
             }
             })*/
        });
    });
</script>
";
    }

    // line 17
    public function block_header($context, array $blocks = array())
    {
        // line 18
        echo "                    <div class=\"left float-left\">
                        <div class=\"logo float-left\">
                            <a href=\"/\">
                                <img src=\"./markup/img/logo.png\" width=\"36\" height=\"27\" alt=\"металлопрокат.ру\"/>
                            </a>
                        </div>
                        <div class=\"logo-text float-left\">
                            <p>
                                <a class=\"header-logo-text\" href=\"#\">металлопрокат.ру</a>
                            </p>
                        </div>
                        <div class=\"location float-right\">
                            <a href=\"#\" class=\"js-popover-opener current-location\" data-popover=\"#cities\" data-index=\"1001\" data-different-position=\"true\">Москва и Область</a>
                        </div>
                    </div>
                    ";
        // line 33
        $this->displayBlock('login', $context, $blocks);
        // line 39
        echo "                ";
    }

    // line 33
    public function block_login($context, array $blocks = array())
    {
        // line 34
        echo "                        <div class=\"user-block float-right\">
                            <a class=\"login js-popup-opener icon-exit\" data-popup=\"#login\" href=\"#\">Вход в кабинет</a>
                            <a class=\"register-link js-popup-opener\" data-popup=\"#register-company\" href=\"#\">Регистрация</a>
                        </div>
                    ";
    }

    // line 42
    public function block_companyBanner($context, array $blocks = array())
    {
        // line 43
        echo "            <div class=\"company-announcement\">
                <div class=\"wrap clearfix\">
                    <form action=\"#\">
                        <label class=\"file-upload\">
                            <span class=\"admin-button button gray60-bg float-right ie-radius\">Загрузить шапку</span>
                            <input type=\"file\"/>
                        </label>
                        <div class=\"company-logo float-left\">
                            <a class=\"company-link big-pattern\" href=\"#\"></a>
                            <div class=\"photo-btns-wrapper clearfix\">
                                <div class=\"add-photo-wrapper float-left\">
                                    <label class=\"file-upload\">
                                        <span class=\"ico-upload\"></span>
                                        <input type=\"file\"/>
                                    </label>
                                </div>
                            </div>

                        </div>
                    </form>
                    <div class=\"company-info-wrapper float-left\">
                        <a class=\"admin-button button gray60-bg float-right ie-radius\" href=\"#\">изменить</a>
                        <div class=\"heading\">
                            <div class=\"company-name\">Стальторг</div>
                            <span>Продажа металлопроката</span>
                        </div>
                        <div class=\"address-phone-wrapper\">
                            <p class=\"company-phone\">+7 (495) 904-53-13</p>
                            <p class=\"company-address\">Люберцы, <a href=\"#\" class=\"link\">Ялтинская 17</a></p>
                        </div>
                    </div>
                </div>
            </div>
        ";
    }

    // line 79
    public function block_side_announcements($context, array $blocks = array())
    {
        // line 80
        echo "                <div class=\"left-announcement-wrapper\">
                    <div class=\"js-fixed-side-banner\">
                        <div class=\"left-announcement announcement has-announcement\">
                            <img src=\"./markup/pic/left-banner-150.jpg\" alt=\"image description\"/>
                            ";
        // line 85
        echo "                            ";
        // line 86
        echo "                            ";
        // line 87
        echo "                            ";
        // line 88
        echo "                            ";
        // line 89
        echo "                            ";
        // line 90
        echo "                        </div>
                    </div>
                </div>
                <div class=\"right-announcement-wrapper employees-block-wrapper\">
                    <div class=\"js-fixed-side-banner\">
                        <div class=\"js-scrollable thin-scroll\">
                            <div class=\"premium-product-block employees-block\">

                                <div class=\"company-info\">
                                    <div class=\"company-title title\">Стальторг</div>
                                    <div class=\"web-site\"><a class=\"link\" href=\"#\">www.staltorg.ru</a></div>
                                    <div class=\"web-site\"><a class=\"link\" href=\"#\">www.staltorg.ua</a></div>
                                    <div class=\"web-site\"><a class=\"link\" href=\"#\">www.staltorg.com</a></div>
                                    <div class=\"button-wrapper\">
                                        <a class=\"primary send-button button js-popup-opener ie-radius\" data-popup=\"#send-mail\" href=\"#\">Отправить заявку</a>
                                    </div>
                                </div>
                                <div class=\"topic-wrapper\">
                                    <ul class=\"topic-list\">
                                        <li class=\"item clearfix\">
                                            <div class=\"employee-info clearfix\">
                                                <div class=\"img float-right is-gradiented\">
                                                    <div class=\"img-holder\">
                                                        <a class=\"img-link pattern-big\" href=\"#\">
                                                            <img src=\"./markup/pic/userpic.jpg\" alt=\"image description\"/>
                                                        </a>
                                                    </div>
                                                </div>
                                                <div class=\"topic-info\">
                                                    <div class=\"employee-name\">Станислав Станислановский</div>
                                                    <div class=\"position\">директор по маркетингу и обеспечению</div>
                                                </div>

                                            </div>
                                            <div class=\"button-wrapper\">
                                                <a class=\"primary write-btn button js-popup-opener ie-radius\" data-popup=\"#send-mail\" href=\"#\">написать</a>
                                            </div>
                                        </li>
                                        <li class=\"item clearfix\">
                                            <div class=\"employee-info clearfix\">
                                                <div class=\"img float-right is-gradiented\">
                                                    <div class=\"img-holder\">
                                                        <a class=\"img-link pattern-big\" href=\"#\">
                                                            <img src=\"./markup/pic/userpic.jpg\" alt=\"image description\"/>
                                                        </a>
                                                    </div>
                                                </div>
                                                <div class=\"topic-info\">
                                                    <div class=\"employee-name\">Семен Меркушев</div>
                                                    <div class=\"position\">директор по маркетингу и обеспечению</div>
                                                </div>
                                            </div>
                                            <div class=\"button-wrapper\">
                                                <a class=\"primary write-btn button js-popup-opener ie-radius\" data-popup=\"#send-mail\" href=\"#\">написать</a>
                                            </div>
                                        </li>
                                        <li class=\"item clearfix\">
                                            <div class=\"employee-info clearfix\">
                                                <div class=\"img float-right is-gradiented\">
                                                    <div class=\"img-holder\">
                                                        <a class=\"img-link pattern-big\" href=\"#\">
                                                            <img src=\"./markup/pic/userpic.jpg\" alt=\"image description\"/>
                                                        </a>
                                                    </div>
                                                </div>
                                                <div class=\"topic-info\">
                                                    <div class=\"employee-name\">Станислав Станислановский</div>
                                                    <div class=\"position\">директор по маркетингу</div>
                                                </div>

                                            </div>
                                            <div class=\"button-wrapper\">
                                                <a class=\"primary write-btn button js-popup-opener ie-radius\" data-popup=\"#send-mail\" href=\"#\">написать</a>
                                            </div>
                                        </li>
                                        ";
        // line 165
        if (true) {
            // line 166
            echo "                                            <li class=\"item clearfix\">
                                                <span class=\"js-show-all-employees secondary write-btn button clickable ie-radius\">Показать всех</span>
                                            </li>

                                            <li class=\"item clearfix g-hidden js-all-employees\">
                                                <div class=\"employee-info clearfix\">
                                                    <div class=\"img float-right is-gradiented\">
                                                        <div class=\"img-holder\">
                                                            <a class=\"img-link pattern-big\" href=\"#\">
                                                                <img src=\"./markup/pic/userpic.jpg\" alt=\"image description\"/>
                                                            </a>
                                                        </div>
                                                    </div>
                                                    <div class=\"topic-info\">
                                                        <div class=\"employee-name\">Станислав Станислановский</div>
                                                        <div class=\"position\">директор по маркетингу</div>
                                                    </div>

                                                </div>
                                                <div class=\"button-wrapper\">
                                                    <a class=\"primary write-btn button js-popup-opener ie-radius\" data-popup=\"#send-mail\" href=\"#\">написать</a>
                                                </div>
                                            </li>
                                            <li class=\"item clearfix g-hidden js-all-employees\">
                                                <div class=\"employee-info clearfix\">
                                                    <div class=\"img float-right is-gradiented\">
                                                        <div class=\"img-holder\">
                                                            <a class=\"img-link pattern-big\" href=\"#\">
                                                                <img src=\"./markup/pic/userpic.jpg\" alt=\"image description\"/>
                                                            </a>
                                                        </div>
                                                    </div>
                                                    <div class=\"topic-info\">
                                                        <div class=\"employee-name\">Станислав Станислановский</div>
                                                        <div class=\"position\">директор по маркетингу</div>
                                                    </div>

                                                </div>
                                                <div class=\"button-wrapper\">
                                                    <a class=\"primary write-btn button js-popup-opener ie-radius\" data-popup=\"#send-mail\" href=\"#\">написать</a>
                                                </div>
                                            </li>
                                            <li class=\"item clearfix g-hidden js-all-employees\">
                                                <div class=\"employee-info clearfix\">
                                                    <div class=\"img float-right is-gradiented\">
                                                        <div class=\"img-holder\">
                                                            <a class=\"img-link pattern-big\" href=\"#\">
                                                                <img src=\"./markup/pic/userpic.jpg\" alt=\"image description\"/>
                                                            </a>
                                                        </div>
                                                    </div>
                                                    <div class=\"topic-info\">
                                                        <div class=\"employee-name\">Семен Меркушев</div>
                                                        <div class=\"position\">директор по маркетингу</div>
                                                    </div>
                                                </div>
                                                <div class=\"button-wrapper\">
                                                    <a class=\"primary write-btn button js-popup-opener ie-radius\" data-popup=\"#send-mail\" href=\"#\">написать</a>
                                                </div>
                                            </li>

                                        ";
        }
        // line 228
        echo "
                                    </ul>

                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            ";
    }

    // line 240
    public function block_banner($context, array $blocks = array())
    {
        // line 241
        echo "                    ";
        // line 242
        echo "                        ";
        // line 243
        echo "                    ";
        // line 244
        echo "                ";
    }

    // line 248
    public function block_sidebar($context, array $blocks = array())
    {
        // line 249
        echo "                            <div id=\"sidebar\" class=\"side-left table-cell\">
                                <div class=\"products-wrapper\">
                                    <div class=\"clearfix\">
                                        <a class=\"admin-button button gray60-bg float-right ie-radius\" href=\"#\">управлять</a>
                                        <div class=\"title float-left\">Товары</div>
                                    </div>
                                    <ul class=\"product-categories-list\">
                                        <li class=\"level-1 item js-expandable-menu-item \" data-expandable-menu-children=\".list\">
                                            <a href=\"#\" class=\"check-link\"><span class=\"icon-check black\"></span></a>
                                            <a class=\"item-link active clearfix js-expandable-menu-expander\" href=\"#armatura\">
                                                <span class=\"count float-right\">51</span>
                                                <span class=\"elem is-gradiented\">Арматура</span>
                                            </a>
                                            <ul class=\"list\">
                                                <li class=\"level-inside item\">
                                                    <a class=\"link clearfix\" href=\"#armatura-1\">
                                                        <span class=\"count float-right\">31</span>
                                                        <span class=\"elem is-gradiented\">Арматура А1</span>
                                                    </a>
                                                </li>
                                                <li class=\"level-inside item\">
                                                    <span class=\"link active clearfix\" href=\"#armatura-2\">
                                                        <span class=\"count float-right\">14</span>
                                                        <span class=\"elem is-gradiented\">Арматура 09Г2С</span>
                                                    </span>
                                                </li>
                                                <li class=\"level-inside item\">
                                                    <a class=\"link clearfix\" href=\"#armatura-3\">
                                                        <span class=\"count float-right\">103</span>
                                                        <span class=\"elem is-gradiented\">Арматура А3</span>
                                                    </a>
                                                </li>
                                                <li class=\"level-inside item\">
                                                    <a class=\"link clearfix\" href=\"#armatura-4\">
                                                        <span class=\"count float-right\">52</span>
                                                        <span class=\"elem is-gradiented\">Арматура А500С</span>
                                                    </a>
                                                </li>
                                                <li class=\"level-inside item\">
                                                    <a class=\"link clearfix\" href=\"#armatura-5\">
                                                        <span class=\"count float-right\">62</span>
                                                        <span class=\"elem is-gradiented\">Арматура 12</span>
                                                    </a>
                                                </li>
                                            </ul>
                                        </li>
                                        <li class=\"level-1 item js-expandable-menu-item\" data-expandable-menu-children=\".list\">
                                            <a href=\"#\" class=\"check-link collapsed\"><span class=\"icon-check black\"></span></a>
                                            <a class=\"item-link clearfix js-expandable-menu-expander\" href=\"#balka\">
                                                <span class=\"count float-right\">60</span>
                                                <span class=\"elem is-gradiented\">Балка</span>
                                            </a>
                                            <ul class=\"list g-hidden\">
                                                <li class=\"level-inside item\">
                                                    <a class=\"link clearfix\" href=\"#balka-1\">
                                                        <span class=\"count float-right\">31</span>
                                                        <span class=\"elem is-gradiented\">Балка Б2</span>
                                                    </a>
                                                </li>
                                                <li class=\"level-inside item\">
                                                    <a class=\"link clearfix\" href=\"#balka-2\">
                                                        <span class=\"count float-right\">14</span>
                                                        <span class=\"elem is-gradiented\">Балка Б4</span>
                                                    </a>
                                                </li>
                                            </ul>
                                        </li>
                                        <li class=\"level-1 item\">
                                            <a href=\"#\" class=\"check-link collapsed\"><span class=\"icon-check black\"></span></a>
                                            <a class=\"item-link clearfix\" href=\"#\">
                                                <span class=\"count float-right\">777</span>
                                                <span class=\"elem is-gradiented\">Заглушка</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                                <div class=\"premium-product-block\">
                                    ";
        // line 327
        echo "                                        ";
        // line 328
        echo "                                        ";
        // line 329
        echo "                                    ";
        // line 330
        echo "                                    <ul class=\"topic-list\">
                                        <li class=\"item clearfix\">
                                            <div class=\"topic-info float-left\">
                                                <a class=\"title-link link\" href=\"#\">Арматура стеклопластиковая АСП-6</a>
                                                <p class=\"text\">Размер 6, <strong class=\"price \">10 <span class=\"icon-rouble\"></span></strong> за погонный метр</p>
                                            </div>
                                            <div class=\"img float-right\">
                                                <div class=\"img-holder\">
                                                    <a href=\"#\" class=\"img-link pattern-small\">
                                                        <img src=\"./markup/pic/small-img.jpg\" alt=\"image description\"/>
                                                    </a>
                                                </div>
                                            </div>
                                        </li>
                                        <li class=\"item clearfix\">
                                            <div class=\"topic-info float-left\">
                                                <a class=\"title-link link\" href=\"#\">Арматура стеклопластиковая АСП-6</a>
                                                <p class=\"text\">Размер 6, <strong class=\"price \">10 <span class=\"icon-rouble\"></span></strong> за погонный метр</p>
                                            </div>
                                            <div class=\"img float-right\">
                                                <div class=\"img-holder\">
                                                    <a href=\"#\" class=\"img-link pattern-small\"></a>
                                                </div>
                                            </div>
                                        </li>
                                        <li class=\"item clearfix\">
                                            <div class=\"topic-info float-left\">
                                                <a class=\"title-link link\" href=\"#\">Арматура стеклопластиковая АСП-6</a>
                                                <p class=\"text\">Размер 6, <strong class=\"price \">10 <span class=\"icon-rouble\"></span></strong> за погонный метр</p>
                                            </div>
                                            <div class=\"img float-right\">
                                                <div class=\"img-holder\">
                                                    <a href=\"#\" class=\"img-link pattern-small\"></a>
                                                </div>
                                            </div>
                                        </li>
                                        <li class=\"item clearfix\">
                                            <div class=\"topic-info float-left\">
                                                <a class=\"title-link link\" href=\"#\">Арматура стеклопластиковая АСП-6</a>
                                                <p class=\"text\">Размер 6, <strong class=\"price \">10 <span class=\"icon-rouble\"></span></strong> за погонный метр</p>
                                            </div>
                                            <div class=\"img float-right\">
                                                <div class=\"img-holder\">
                                                    <a href=\"#\" class=\"img-link pattern-small\"></a>
                                                </div>
                                            </div>
                                        </li>
                                        <li class=\"item clearfix\">
                                            <div class=\"topic-info float-left\">
                                                <a class=\"title-link link\" href=\"#\">Арматура стеклопластиковая АСП-6</a>
                                                <p class=\"text\">Размер 6, <strong class=\"price \">10 <span class=\"icon-rouble\"></span></strong> за погонный метр</p>
                                            </div>
                                            <div class=\"img float-right\">
                                                <div class=\"img-holder\">
                                                    <a href=\"#\" class=\"img-link pattern-small\"></a>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                    <a class=\"add-product-text\" href=\"#\">Разместить здесь товары</a>
                                </div>
                            </div>
                        ";
    }

    // line 393
    public function block_content($context, array $blocks = array())
    {
        // line 394
        echo "                            <div class=\"js-calc-height\">
                                ";
        // line 395
        $this->displayBlock('filters', $context, $blocks);
        // line 396
        echo "                                ";
        $this->displayBlock('tabs', $context, $blocks);
        // line 397
        echo "                            </div>
                        ";
    }

    // line 395
    public function block_filters($context, array $blocks = array())
    {
        echo "";
    }

    // line 396
    public function block_tabs($context, array $blocks = array())
    {
        echo "";
    }

    // line 409
    public function block_footer($context, array $blocks = array())
    {
        // line 410
        echo "                <div class=\"footer-links-wrapper clearfix\">
                    <div class=\"footer-links\">
                        <div class=\"footer-title\">Компания</div>
                        <ul class=\"footer-links-list first\">
                            <li class=\"item\"><a href=\"#\">О сервисе</a></li>
                            <li class=\"item\"><a href=\"#\">Реклама и услуги</a></li>
                            <li class=\"item\"><a href=\"#\">Клиенты</a></li>
                            <li class=\"item\"><a href=\"#\">Отзывы</a></li>
                            <li class=\"item\"><a href=\"#\">Контактная информация</a></li>
                            <li class=\"item\"><p class=\"support\">Техническая поддержка: <span class=\"support-phone\">(495) 984-06-52</span>
                                </p></li>
                        </ul>
                    </div>
                    <div class=\"footer-links\">
                        <div class=\"footer-title\">Информация на сайте</div>
                        <ul class=\"footer-links-list\">
                            <li class=\"item\"><a href=\"#\">Компании</a></li>
                            <li class=\"item\"><a href=\"#\">Отзывы</a></li>
                            <li class=\"item\"><a href=\"#\">Товары</a></li>
                            <li class=\"item\"><a href=\"#\">Потребности</a></li>
                            <li class=\"item\"><a href=\"#\">Последние обновления</a></li>
                            <li class=\"item\"><a href=\"#\" class=\"green-color\">Добавить компанию и товары</a></li>
                        </ul>
                    </div>
                </div>
                <ul class=\"footer-links-list clearfix\">
                    <li class=\"item\">
                        <p class=\"copy\">Металлопрокат.ру © 2000-2013</p>
                    </li>
                    <li class=\"item\">
                        <a class=\"agreement\" href=\"#\" target=\"_blank\">Пользовательское соглашение</a>
                    </li>
                </ul>
                <div class=\"counters-block clearfix\">
                    <a href=\"#\" class=\"rspm-member float-left\">Действительный член РСПМ</a>

                    <div class=\"counter-container float-right\">
                        <img src=\"./markup/pic/counter1.png\" width=\"80\" height=\"31\" alt=\"image description\"/>
                        <img src=\"./markup/pic/counter2.png\" width=\"88\" height=\"31\" alt=\"image description\"/>
                    </div>
                </div>
            ";
    }

    public function getTemplateName()
    {
        return "@markup/mini-site/_mini_layout.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  590 => 410,  587 => 409,  581 => 396,  575 => 395,  570 => 397,  567 => 396,  565 => 395,  562 => 394,  559 => 393,  493 => 330,  491 => 329,  489 => 328,  487 => 327,  408 => 249,  405 => 248,  401 => 244,  399 => 243,  397 => 242,  395 => 241,  392 => 240,  378 => 228,  314 => 166,  312 => 165,  235 => 90,  233 => 89,  231 => 88,  229 => 87,  227 => 86,  225 => 85,  219 => 80,  216 => 79,  179 => 43,  176 => 42,  168 => 34,  165 => 33,  161 => 39,  159 => 33,  142 => 18,  139 => 17,  116 => 455,  114 => 454,  110 => 452,  108 => 409,  96 => 399,  93 => 393,  91 => 248,  86 => 245,  84 => 240,  81 => 239,  79 => 79,  75 => 77,  73 => 42,  69 => 40,  58 => 12,  50 => 8,  42 => 3,  396 => 341,  393 => 340,  67 => 17,  61 => 13,  55 => 12,  49 => 9,  43 => 6,  39 => 2,  36 => 3,  30 => 2,);
    }
}
