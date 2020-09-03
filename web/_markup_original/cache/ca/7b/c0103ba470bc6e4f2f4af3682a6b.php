<?php

/* @markup/corporate/about.html.twig */
class __TwigTemplate_ca7bc0103ba470bc6e4f2f4af3682a6b extends Twig_Template
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
        echo "        ";
        $context["activeMenu"] = "about";
        // line 5
        echo "        ";
        $this->displayParentBlock("header_menu", $context, $blocks);
        echo "
    ";
    }

    // line 7
    public function block_content($context, array $blocks = array())
    {
        // line 8
        echo "    <div id=\"content\" class=\"corporate-content content-wide clearfix\">
        <div class=\"company-img-wrapper float-left\">
            <div class=\"image is-bordered \">
                <img src=\"./markup/pic/photo.jpg\" alt=\"image\"/>
            </div>
        </div>
        <div class=\"about-text float-right\">
            <h1 class=\"title\">О компании</h1>
            <div id=\"default-styles\">
                <h1>Заголовок 1 уровня</h1>
                <h2>Заголовок 2 уровня</h2>
                <h3>Заголовок 3 уровня</h3>
                <h4>Заголовок 4 уровня</h4>
                <h5>Заголовок 5 уровня</h5>
                <h6>Заголовок 6 уровня</h6>

                <ul>
                    <li>ненумерованный список</li>
                    <li>ненумерованный список</li>
                    <li>ненумерованный список
                        <ul>
                            <li>ненумерованный список</li>
                            <li>ненумерованный список</li>
                            <li>ненумерованный список</li>
                        </ul>
                    </li>
                </ul>

                <ol>
                    <li>нумерованный список</li>
                    <li>нумерованный список</li>
                    <li>нумерованный список
                        <ol>
                            <li>нумерованный список</li>
                            <li>нумерованный список</li>
                            <li>нумерованный список</li>
                        </ol>
                    </li>
                </ol>
                <strong>Жирный текст</strong> <br/>
                <em>курсив</em>
                <p>абзац</p>
                <hr/>

                Таблица
                <table>
                    <thead>
                    <tr>
                        <td>заголовок</td>
                        <td>заголовок</td>
                        <td>заголовок</td>
                        <td>заголовок</td>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>текст</td>
                        <td>текст</td>
                        <td>текст</td>
                        <td>текст</td>
                    </tr>
                    </tbody>
                </table>

                <p class=\"text\">Компания <strong>«Металлопрокат.ру»</strong> была образована в 2000 году и на сегодняшний день является одной
                    из ведущих компаний, предоставляющих информационные услуги в области металлургии.</p>
                <p class=\"text\">Миссия компании заключается в обеспечении оперативного взаимодействия между потребителем и
                    поставщиком металла за счет информационных продуктов и услуг, основанных на современных, удобных и
                    доступных технологиях.</p>
                <p class=\"text\">Основой компании является интернет-портал Металлопрокат.ру — один из старейших и узнаваемых
                    онлайн-проектов металлургической отрасли. Ежедневно Портал посещает порядка 5000 пользователей, в
                    разделах Портала зарегистрировано более 30.000 компаний — производителей, трейдеров, потребителей и
                    других представителей отрасли.</p>
                <p class=\"text\">Каждый из разделов портала — это полноценный сервис для участников металлургического рынка.</p>
                <p class=\"text\">Подробнее об информационных продуктах и услугах Компании Вы можете ознакомиться в разделе
                    «Продукты и услуги».</p>
            </div>
        </div>
    </div>
";
    }

    public function getTemplateName()
    {
        return "@markup/corporate/about.html.twig";
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
