<?php

/* @markup/private/management/company-creation.html.twig */
class __TwigTemplate_f37f4d1cbb2afc9fd82a946f3f5c9b45 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = $this->env->loadTemplate("@markup/private/management/_managemet_layout.html.twig");

        $this->blocks = array(
            'title' => array($this, 'block_title'),
            'content' => array($this, 'block_content'),
            'tabs' => array($this, 'block_tabs'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "@markup/private/management/_managemet_layout.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 2
    public function block_title($context, array $blocks = array())
    {
        echo "Компания";
    }

    // line 3
    public function block_content($context, array $blocks = array())
    {
        // line 4
        echo "        <div id=\"content\" class=\"private-room-content content-right outline-right float-right\">
            <div class=\"private-scrollable content-scrollable js-scrollable\">
                <div class=\"private-fix-width\">
                    ";
        // line 7
        $this->displayBlock('tabs', $context, $blocks);
        // line 11
        echo "                    <div class=\"management-content-wrapper clearfix\">
                        <form class=\"register-form form float-left\" action=\"#\">
                            <fieldset class=\"left-group\">
                                <div class=\"group clearfix\">
                                    <div class=\"user-company-wrapper field-wrap float-left\">
                                        <input class=\"register-company form-text ie-radius\" type=\"text\" placeholder=\"Компания\"/>
                                    </div>
                                    <div class=\"company-type-wrapper field-wrap float-left\">
                                        <select name=\"company-type\" id=\"company-type\" class=\"form-select hight\">
                                            <option value=\"-1\"> </option>
                                            <option value=\"1\">ЗАО</option>
                                            <option value=\"2\">OАО</option>
                                            <option value=\"3\">OOО</option>
                                        </select>
                                    </div>
                                </div>
                                <div class=\"group clearfix\">
                                    <div class=\"field-wrap float-left\">
                                        <input type=\"text\" class=\"form-text ie-radius\" placeholder=\"Город\"/>
                                    </div>
                                    <div class=\"field-wrap float-left\">
                                        <input type=\"text\" class=\"phone form-text ie-radius\" placeholder=\"Телефон\"/>
                                    </div>
                                </div>
                            </fieldset>

                            <div class=\"submit-wrapper\">
                                <input type=\"submit\" class=\"save-btn button blue-bg ie-radius\" value=\"Сохранить\"/>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    ";
    }

    // line 7
    public function block_tabs($context, array $blocks = array())
    {
        // line 8
        echo "                        ";
        $context["activeTab"] = "company2";
        // line 9
        echo "                        ";
        $this->displayParentBlock("tabs", $context, $blocks);
        echo "
                    ";
    }

    public function getTemplateName()
    {
        return "@markup/private/management/company-creation.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  91 => 9,  88 => 8,  85 => 7,  46 => 11,  44 => 7,  39 => 4,  36 => 3,  30 => 2,);
    }
}
