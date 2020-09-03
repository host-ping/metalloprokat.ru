<?php

/* @markup/corporate/clients.html.twig */
class __TwigTemplate_026f994e7f39173bce47ea588ff9627f extends Twig_Template
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
        echo "Corporate site - Clients";
    }

    // line 3
    public function block_header_menu($context, array $blocks = array())
    {
        // line 4
        echo "    ";
        $context["activeMenu"] = "clients";
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
        <div class=\"clients-wrapper clearfix\">
            <ul class=\"clients float-left\">
                <li class=\"client float-left\">
                    <div class=\"client-logo\">
                        <span class=\"img ie-radius\">
                            <img src=\"./markup/pic/userpic.jpg\" alt=\"\"/>
                        </span>
                    </div>
                    <p class=\"client-title\">Стальторг</p>
                    <div class=\"drop-wrapper opacity-border\" style=\"display: block;\">
                        <div class=\"dropdown table-container\">
                            <div class=\"client-info-wrapper table-cell\">
                                <div class=\"client-logo \" >
                                <span class=\"img ie-radius\">
                                    <img src=\"./markup/pic/userpic.jpg\" alt=\"\"/>
                                </span>
                                </div>
                                ";
        // line 27
        echo "                            </div>
                            <div class=\"reviews-block table-cell\">
                                <ul class=\"reviews list\">
                                    <li class=\"icon-positive item\">
                                        <p class=\"text\">
                                            Покупал у них водонагреватель и в принципе никаких претензий к ним нет.
                                            Покупал у них водонагреватель и в принципе никаких претензий к ним нет.
                                            Покупал у них водонагреватель и в принципе никаких претензий к ним нет.
                                            Покупал у них водонагреватель и в принципе никаких претензий к ним нет.
                                            Покупал у них водонагреватель и в принципе никаких претензий к ним нет.
                                            Покупал у них водонагреватель и в принципе никаких претензий к ним нет.
                                        </p>
                                        <div class=\"info\">
                                            <span class=\"author\">Николай Чистяков</span>,
                                            <span class=\"period\">Менеджер</span>
                                        </div>
                                    </li>
                                </ul>
                            </div>

                        </div>

                    </div>
                </li>
                <li class=\"client float-left\">
                    <div class=\"client-logo\">
                        <span class=\"img ie-radius\"></span>
                    </div>
                    <p class=\"client-title\">Стальторг</p>
                    <div class=\"drop-wrapper opacity-border\">
                        <div class=\"dropdown table-container\">
                            <div class=\"client-info-wrapper table-cell\">
                                <div class=\"client-logo \" >
                                <span class=\"img ie-radius\">
                                    <img src=\"./markup/pic/userpic.jpg\" alt=\"\"/>
                                </span>
                                </div>
                                ";
        // line 65
        echo "                            </div>
                            <div class=\"reviews-block table-cell\">
                                <ul class=\"reviews list\">
                                    <li class=\"icon-positive item\">
                                        <p class=\"text\">
                                            Покупал у них водонагреватель и в принципе никаких претензий к ним нет.
                                        </p>
                                        <div class=\"info\">
                                            <span class=\"author\">Николай Чистяков</span>,
                                            <span class=\"period\">Менеджер</span>
                                        </div>
                                    </li>
                                </ul>
                            </div>

                        </div>

                    </div>
                </li>
                <li class=\"client float-left\">
                    <div class=\"client-logo\">
                        <span class=\"img ie-radius\"></span>
                    </div>
                    <p class=\"client-title\">Стальторг</p>
                    <div class=\"drop-wrapper opacity-border\">
                        <div class=\"dropdown table-container\">
                            <div class=\"client-info-wrapper table-cell\">
                                <div class=\"client-logo \" >
                                <span class=\"img ie-radius\">
                                    <img src=\"./markup/pic/userpic.jpg\" alt=\"\"/>
                                </span>
                                </div>
                                ";
        // line 98
        echo "                            </div>
                            <div class=\"reviews-block table-cell\">
                                <ul class=\"reviews list\">
                                    <li class=\"icon-positive item\">
                                        <p class=\"text\">
                                            Покупал у них водонагреватель и в принципе никаких претензий к ним нет.
                                        </p>
                                        <div class=\"info\">
                                            <span class=\"author\">Николай Чистяков</span>,
                                            <span class=\"period\">Менеджер</span>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </li>
                <li class=\"client float-left\">
                    <div class=\"client-logo\">
                        <span class=\"img ie-radius\"></span>
                    </div>
                    <p class=\"client-title\">Стальторг</p>
                    <div class=\"drop-wrapper opacity-border right-position\">
                        <div class=\"dropdown table-container\">
                            <div class=\"client-info-wrapper table-cell\">
                                <div class=\"client-logo \" >
                                <span class=\"img ie-radius\">
                                    <img src=\"./markup/pic/userpic.jpg\" alt=\"\"/>
                                </span>
                                </div>
                                ";
        // line 129
        echo "                            </div>
                            <div class=\"reviews-block table-cell\">
                                <ul class=\"reviews list\">
                                    <li class=\"icon-positive item\">
                                        <p class=\"text\">
                                            Покупал у них водонагреватель и в принципе никаких претензий к ним нет.
                                        </p>
                                        <div class=\"info\">
                                            <span class=\"author\">Николай Чистяков</span>,
                                            <span class=\"period\">Менеджер</span>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </li>
                <li class=\"client float-left\">
                    <div class=\"client-logo\">
                        <span class=\"img ie-radius\"></span>
                    </div>
                    <p class=\"client-title\">Стальторг</p>
                    <div class=\"drop-wrapper opacity-border right-position\">
                        <div class=\"dropdown table-container\">
                            <div class=\"client-info-wrapper table-cell\">
                                <div class=\"client-logo \" >
                                <span class=\"img ie-radius\">
                                    <img src=\"./markup/pic/userpic.jpg\" alt=\"\"/>
                                </span>
                                </div>
                                ";
        // line 160
        echo "                            </div>
                            <div class=\"reviews-block table-cell\">
                                <ul class=\"reviews list\">
                                    <li class=\"icon-positive item\">
                                        <p class=\"text\">
                                            Покупал у них водонагреватель и в принципе никаких претензий к ним нет.
                                        </p>
                                        <div class=\"info\">
                                            <span class=\"author\">Николай Чистяков</span>,
                                            <span class=\"period\">Менеджер</span>
                                        </div>
                                    </li>
                                </ul>
                            </div>

                        </div>

                    </div>
                </li>
                <li class=\"client float-left\">
                    <div class=\"client-logo\">
                        <span class=\"img ie-radius\"></span>
                    </div>
                    <p class=\"client-title\">Стальторг</p>
                </li>
                <li class=\"client float-left\">
                    <div class=\"client-logo\">
                        <span class=\"img ie-radius\"></span>
                    </div>
                    <p class=\"client-title\">Стальторг</p>
                </li>
                <li class=\"client float-left\">
                    <div class=\"client-logo\">
                        <span class=\"img ie-radius\"></span>
                    </div>
                    <p class=\"client-title\">Стальторг</p>
                </li>
                <li class=\"client float-left\">
                    <div class=\"client-logo\">
                        <span class=\"img ie-radius\"></span>
                    </div>
                    <p class=\"client-title\">Стальторг</p>
                </li>
                <li class=\"client float-left\">
                    <div class=\"client-logo\">
                        <span class=\"img ie-radius\"></span>
                    </div>
                    <p class=\"client-title\">Стальторг</p>
                </li>
                <li class=\"client float-left\">
                    <div class=\"client-logo\">
                        <span class=\"img ie-radius\"></span>
                    </div>
                    <p class=\"client-title\">Стальторг</p>
                </li>
                <li class=\"client float-left\">
                    <div class=\"client-logo\">
                        <span class=\"img ie-radius\"></span>
                    </div>
                    <p class=\"client-title\">Стальторг</p>
                </li>
                <li class=\"client float-left\">
                    <div class=\"client-logo\">
                        <span class=\"img ie-radius\"></span>
                    </div>
                    <p class=\"client-title\">Стальторг</p>
                </li>
            </ul>
        </div>
    </div>
";
    }

    public function getTemplateName()
    {
        return "@markup/corporate/clients.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  209 => 160,  177 => 129,  145 => 98,  111 => 65,  72 => 27,  52 => 8,  49 => 7,  42 => 5,  39 => 4,  36 => 3,  30 => 2,);
    }
}
