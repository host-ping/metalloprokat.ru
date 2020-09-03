<?php

/* @markup/content/blog/blog.html.twig */
class __TwigTemplate_6a2332611dd097e7cec74f7d77bc5804 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = $this->env->loadTemplate("@markup/content/_content_layout.html.twig");

        $this->blocks = array(
            'content' => array($this, 'block_content'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "@markup/content/_content_layout.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_content($context, array $blocks = array())
    {
        // line 4
        echo "    <div id=\"content\" class=\"content-wide table-container outline-left\">
        <div class=\"left table-cell\">
            <div class=\"blog-img\">
                <img src=\"/markup/pic/blog-img.jpeg\" alt=\"image description\">
            </div>
            <div class=\"blog-img-description\">
                <p class=\"text\">А вот для витражей «Тиффани» понадобится чуть больше времени и материалов. Необходима
                    витражная фольга и набор цветного стекла. В идеале не помешают навыки работы с паяльником.
                    Максимально простой в изготовлении – пленочный витраж. На стекло просто наклеивается предварительно
                    раскроенная по эскизу пленка. Работа может быть выполнена как на горизонтальной поверхности, так
                    и на вертикальной без снятия полотна. Узор можно создать практически любой, в этом нам
                    поспособствует огромный выбор самоклеящихся пленок.
                    Стоит помнить, что работа с витражами – достаточно кропотливый труд, требующий не только терпения,
                    но и усидчивости и внимания.</p>
            </div>
            <div id=\"tabs\" class=\"result-tabs-wrapper tabs demand_tabs catalog-tabs clearfix\">
                <ul class=\"tabs-list clearfix\">
                    <li class=\"item active js-tabs\" data-tab=\"#answers-tab\">
                        <span class=\"count float-right\">1</span>
                        <span class=\"link\">Комментарии</span>
                    </li>
                </ul>
            </div>
            <div class=\"tabs-content\">
                <div id=\"answers-tab\" class=\"similar-list reviews-block answers\">
                    <ul class=\"reviews answers-list\">
                        <li class=\"item outline\">
                            <div class=\"answer clearfix\">
                                <div class=\"text-wrapper information\">
                                    <p class=\"text\">
                                        Если вы хотите минимальные расходы, то ни в коем случае не выпиливайте оконные и
                                        дверные проемы после окончательной сборки. Раньше лес был дешевле, народ делал, что
                                        хотел, сейчас подобное может привести к дополнительным тратам. Сначала отметьте, где у
                                        вас будет дверь и окна, выпилите проемы еще на стадии бревен. Целыми можно оставить
                                        каждое третье или четвертое бревно от венца. После сборки окончательно выпилить проем
                                        гораздо проще, а не распиленные бревна будут выполнять связующую роль в сборке.
                                    </p>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>


            </div>
        </div>
        <div class=\"product-right right table-cell\">
            <div class=\"info-wrapper white-bg\">

                <div class=\"questions-block\">
                    <h3 class=\"title\">Вопросы</h3>

                    <ul class=\"questions list\">
                        <li class=\"item\">
                            <div class=\"title\"><strong>Хочу камин</strong></div>
                            <p class=\"text\">
                                Ребят, хочу камин. Этим, в принципе, все сказано. Дом свой, без соседей безо всего. Имеется
                            </p>
                            <div class=\"info\">
                                <a href=\"#\" class=\"author\">nicolas</a>,
                                <span class=\"period\">3 недели назад</span>
                            </div>
                        </li>
                        <li class=\"item\">
                            <div class=\"title\"><strong>Лепнина из пенопласта для фасадных работ</strong></div>
                            <p class=\"text\">
                                Скажите, а можно использовать лепнину из пенопласта для фасадных работ? Хочу придать более
                            </p>
                            <div class=\"info\">
                                <a href=\"#\" class=\"author\">admin</a>,
                                <span class=\"period\">3 недели назад</span>
                            </div>
                        </li>
                        <li class=\"item\">
                            <div class=\"title\"><strong>Выбор толщины магнезитовой плиты</strong></div>
                            <p class=\"text\">
                                А скажите мне, пожалуйста, от чего зависит выбор толщины магнезитовой плиты? Разбег толщины
                            </p>
                            <div class=\"info\">
                                <a href=\"#\" class=\"author\">Николай Чистяков</a>,
                                <span class=\"period\">3 недели назад</span>
                            </div>
                        </li>
                    </ul>
                    <div class=\"block-links\">
                        <a class=\"add-link link\" href=\"#\">Добавить вопрос</a>
                        <a class=\"all-link link\" href=\"#\">Все вопросы</a>
                    </div>
                </div>
            </div>

        </div>

    </div>
";
    }

    public function getTemplateName()
    {
        return "@markup/content/blog/blog.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  31 => 4,  28 => 3,);
    }
}
