<?php

/* @markup/private/mini-site/colors.html.twig */
class __TwigTemplate_3c8ccb528e349e2c38947cbb7293b768 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = $this->env->loadTemplate("@markup/private/mini-site/_minisite_layout.html.twig");

        $this->blocks = array(
            'title' => array($this, 'block_title'),
            'content' => array($this, 'block_content'),
            'tabs' => array($this, 'block_tabs'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "@markup/private/mini-site/_minisite_layout.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 2
    public function block_title($context, array $blocks = array())
    {
        echo "Colors";
    }

    // line 4
    public function block_content($context, array $blocks = array())
    {
        // line 5
        echo "    <style id=\"themes\" type=\"text/css\"></style>
    <div id=\"content\" class=\"private-room-content content-right outline-right float-right\">
        <div class=\"private-scrollable content-scrollable js-scrollable\">
            <div class=\"private-fix-width\">
                ";
        // line 9
        $this->displayBlock('tabs', $context, $blocks);
        // line 13
        echo "                <div class=\"mini-site-content-wrapper\">
                    <form class=\"themes form\" action=\"#\">
                        <fieldset class=\"clearfix\">
                            <p class=\"text float-left\">Вы можете изменить цвета некоторых элементов на вашем сайте:</p>
                            <div class=\"submit-wrapper float-right\">
                                <input type=\"submit\" class=\"button green-bg ie-radius\" value=\"Сохранить\"/>
                            </div>
                        </fieldset>
                        <p class=\"title-set\">Фон</p>
                        <fieldset class=\"backgrounds colors-block is-bordered ie-radius\">
                            <input type=\"radio\" checked=\"checked\" name=\"bg\" class=\"theme-color-9fbaac colored js-theme\" value=\"b1cdbe\"
                                   data-hover-color=\"cce9da\"
                                   data-bg-color=\"9fbaac\"
                                   data-rgba=\"177,205,190,.6\"
                                   data-rgba-hover=\"204,233,218,.6\"
                                   data-placeholder=\"89a496\"/>
                            <input type=\"radio\" name=\"bg\" class=\"theme-color-b5af9e colored js-theme\" value=\"ccc6b4\"
                                   data-hover-color=\"e9e2cf\"
                                   data-bg-color=\"b5af9e\"
                                   data-rgba=\"204,198,180,.6\"
                                   data-rgba-hover=\"233,226,207,.6\"
                                   data-placeholder=\"a39d8c\"/>
                            <input type=\"radio\" name=\"bg\" class=\"theme-color-acb3bc colored js-theme\" value=\"c2c7d0\"
                                   data-hover-color=\"dfe3ec\"
                                   data-bg-color=\"acb3bc\"
                                   data-rgba=\"194,199,208,.6\"
                                   data-rgba-hover=\"223,227,236,.6\"
                                   data-placeholder=\"9a9ea7\"/>
                            <input type=\"radio\" name=\"bg\" class=\"theme-color-c7b4a3 colored js-theme\" value=\"d6c3b2\"
                                   data-hover-color=\"f2dfce\"
                                   data-bg-color=\"c7b4a3\"
                                   data-rgba=\"214,195,178,.6\"
                                   data-rgba-hover=\"242,223,206,.6\"
                                   data-placeholder=\"ad9a8a\"/>
                            <input type=\"radio\" name=\"bg\" class=\"theme-color-a7bccc colored js-theme\" value=\"b4cadb\"
                                   data-hover-color=\"d0e6f7\"
                                   data-bg-color=\"a7bccc\"
                                   data-rgba=\"180,202,219,.6\"
                                   data-rgba-hover=\"208,230,247,.6\"
                                   data-placeholder=\"8ca1b1\"/>
                            <input type=\"radio\" name=\"bg\" class=\"theme-color-c1b4cb colored js-theme\" value=\"cfc2d9\"
                                   data-hover-color=\"ebdef6\"
                                   data-bg-color=\"c1b4cb\"
                                   data-rgba=\"207,194,217,.6\"
                                   data-rgba-hover=\"235,222,246,.6\"
                                   data-placeholder=\"a699b0\"/>
                            <input type=\"radio\" name=\"bg\" class=\"theme-color-b9b9b9 colored js-theme\" value=\"c6c6c6\"
                                   data-hover-color=\"e2e2e2\"
                                   data-bg-color=\"b9b9b9\"
                                   data-rgba=\"198,198,198,.6\"
                                   data-rgba-hover=\"226,226,226,.6\"
                                   data-placeholder=\"9e9e9e\"/>
                            <input type=\"radio\" name=\"bg\" class=\"theme-color-ffffff colored js-theme\" value=\"ffffff\"
                                   data-hover-color=\"f1f1f1\"
                                   data-bg-color=\"f1f1f1\"
                                   data-rgba=\"198,198,198,.6\"
                                   data-rgba-hover=\"226,226,226,.6\"
                                   data-placeholder=\"ffffff\"/>
                        </fieldset>
                        <p class=\"title-set\">Основные кнопки и цены</p>
                        <fieldset class=\"primary-links colors-block is-bordered ie-radius\">
                            <input type=\"radio\" name=\"primary\" class=\"theme-color-f16961 colored js-theme\" value=\"f16961\"/>
                            <input type=\"radio\" checked=\"checked\" name=\"primary\" class=\"theme-color-e18141 colored js-theme\" value=\"e18141\"/>
                            <input type=\"radio\" name=\"primary\" class=\"theme-color-c7902c colored js-theme\" value=\"c7902c\"/>
                            <input type=\"radio\" name=\"primary\" class=\"theme-color-84a324 colored js-theme\" value=\"84a324\"/>
                            <input type=\"radio\" name=\"primary\" class=\"theme-color-3fab61 colored js-theme\" value=\"3fab61\"/>
                            <input type=\"radio\" name=\"primary\" class=\"theme-color-18a3d1 colored js-theme\" value=\"18a3d1\"/>
                            <input type=\"radio\" name=\"primary\" class=\"theme-color-9f8cd1 colored js-theme\" value=\"9f8cd1\"/>
                            <input type=\"radio\" name=\"primary\" class=\"theme-color-e06eb1 colored js-theme\" value=\"e06eb1\"/>
                            <input type=\"radio\" name=\"primary\" class=\"theme-color-919191 colored js-theme\" value=\"919191\"/>
                        </fieldset>
                        <p class=\"title-set\">Второстепенные кнопки и ссылки</p>
                        <fieldset class=\"secondary-links colors-block is-bordered ie-radius\">
                            <input type=\"radio\" name=\"secondary\" class=\"theme-color-f16961 colored js-theme\" value=\"f16961\"/>
                            <input type=\"radio\" name=\"secondary\" class=\"theme-color-e18141 colored js-theme\" value=\"e18141\"/>
                            <input type=\"radio\" name=\"secondary\" class=\"theme-color-c7902c colored js-theme\" value=\"c7902c\"/>
                            <input type=\"radio\" checked=\"checked\" name=\"secondary\" class=\"theme-color-84a324 colored js-theme\" value=\"84a324\"/>
                            <input type=\"radio\" name=\"secondary\" class=\"theme-color-3fab61 colored js-theme\" value=\"3fab61\"/>
                            <input type=\"radio\" name=\"secondary\" class=\"theme-color-18a3d1 colored js-theme\" value=\"18a3d1\"/>
                            <input type=\"radio\" name=\"secondary\" class=\"theme-color-9f8cd1 colored js-theme\" value=\"9f8cd1\"/>
                            <input type=\"radio\" name=\"secondary\" class=\"theme-color-e06eb1 colored js-theme\" value=\"e06eb1\"/>
                            <input type=\"radio\" name=\"secondary\" class=\"theme-color-919191 colored js-theme\" value=\"919191\"/>
                        </fieldset>
                        <fieldset class=\"result-block\">
                            <div class=\"main-background ie-radius\">
                                <div class=\"result-wrapper\">
                                    <div class=\"result-header bg clearfix\">
                                        <div class=\"r-logo float-left\">
                                            <svg width=\"108\" height=\"108\">
                                                <line x1=\"0\" y1=\"0\" x2=\"108\" y2=\"0\" stroke-width=\"2\" stroke=\"#cbb8a6\"/>
                                                <line x1=\"108\" y1=\"0\" x2=\"108\" y2=\"108\" stroke-width=\"2\" stroke=\"#cbb8a6\"/>
                                                <line x1=\"108\" y1=\"108\" x2=\"0\" y2=\"108\" stroke-width=\"2\" stroke=\"#cbb8a6\"/>
                                                <line x1=\"0\" y1=\"108\" x2=\"0\" y2=\"0\" stroke-width=\"2\" stroke=\"#cbb8a6\"/>
                                                <line x1=\"0\" y1=\"0\" x2=\"108\" y2=\"108\" stroke-width=\"2\" stroke=\"#cbb8a6\"/>
                                                <line x1=\"108\" y1=\"0\" x2=\"0\" y2=\"108\" stroke-width=\"2\" stroke=\"#cbb8a6\"/>
                                            </svg>
                                        </div>
                                        <div class=\"r-info float-left\">
                                            <div class=\"top\">
                                                <div class=\"r-title r-block long\" style=\"background: #262626;\"></div>
                                                <div class=\"r-text r-block long\" style=\"background: #919191;\"></div>
                                            </div>
                                            <div class=\"bottom\">
                                                <div class=\"r-title r-block middle\" style=\"background: #262626;\"></div>
                                                <div class=\"r-link secondary r-block middle\"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class=\"result-announcement placeholder\"></div>
                                    <div class=\"result-main clearfix\">
                                        <div class=\"result-sidebar bg float-left\" style=\"border-right-style:solid; border-right-width: 2px;\">
                                            <div class=\"r-top bg\" style=\"border-bottom-style: solid; border-bottom-width: 2px;\">
                                                <div class=\"r-title r-block small\"></div>
                                                <ul class=\"r-menu\">
                                                    <li class=\"r-item\">
                                                        <div class=\"clearfix\">
                                                            <div class=\"p-link r-block float-left small\" style=\"background: #262626;\"></div>
                                                            <div class=\"r-count r-block float-right\"  style=\"background: #919191;\"></div>
                                                        </div>
                                                        <ul class=\"r-menu clearfix\">
                                                            <li class=\"r-item clearfix\">
                                                                <div class=\"s-link secondary r-block float-left middle\"></div>
                                                                <div class=\"r-count r-block float-right\"  style=\"background: #919191;\"></div>
                                                            </li>
                                                            <li class=\"r-item clearfix\">
                                                                <div class=\"s-link secondary r-block float-left middle\"></div>
                                                                <div class=\"r-count r-block float-right\" style=\"background: #919191;\"></div>
                                                            </li>
                                                            <li class=\"r-item clearfix\">
                                                                <div class=\"s-link secondary r-block float-left middle\"></div>
                                                                <div class=\"r-count r-block float-right\"  style=\"background: #919191;\"></div>
                                                            </li>
                                                            <li class=\"r-item clearfix\">
                                                                <div class=\"s-link secondary r-block float-left middle\"></div>
                                                                <div class=\"r-count r-block float-right\"  style=\"background: #919191;\"></div>
                                                            </li>
                                                            <li class=\"r-item clearfix\">
                                                                <div class=\"s-link secondary r-block float-left middle\"></div>
                                                                <div class=\"r-count r-block float-right\"  style=\"background: #919191;\"></div>
                                                            </li>
                                                        </ul>
                                                    </li>
                                                    <li class=\"r-item clearfix\">
                                                        <div class=\"p-link r-block float-left small\" style=\"background: #262626;\"></div>
                                                        <div class=\"r-count r-block float-right\"  style=\"background: #919191;\"></div>
                                                    </li>
                                                    <li class=\"r-item clearfix\">
                                                        <div class=\"p-link r-block float-left small\" style=\"background: #262626;\"></div>
                                                        <div class=\"r-count r-block float-right\"  style=\"background: #919191;\"></div>
                                                    </li>
                                                </ul>
                                            </div>
                                            <div class=\"r-bottom\" style=\"background: #fff8e8;\">
                                                <div class=\"r-title r-block small\" style=\"background: #262626;\"></div>
                                                <ul class=\"r-list\">
                                                    <li class=\"r-item\">
                                                        <div class=\"s-link secondary r-block middle\"></div>
                                                        <div class=\"s-link secondary r-block middle\"></div>
                                                        <div class=\"r-text r-block small\" style=\"background: #919191;\"></div>
                                                        <div class=\"r-price primary r-block small\"></div>
                                                    </li>
                                                    <li class=\"r-item\">
                                                        <div class=\"s-link secondary r-block middle\"></div>
                                                        <div class=\"s-link secondary r-block middle\"></div>
                                                        <div class=\"r-text r-block small\" style=\"background: #919191;\"></div>
                                                        <div class=\"r-price primary r-block small\"></div>
                                                    </li>
                                                </ul>
                                                <div class=\"r-text r-block middle\" style=\"background: #919191;\"></div>
                                            </div>
                                        </div>
                                        <div class=\"result-content-wrapper bg float-left\">
                                            <div class=\"r-tabs bg clearfix\" style=\"border-bottom-style: solid; border-bottom-width: 2px;\">
                                                <div class=\"tabs-title r-block float-left small\" style=\"background: #262626;\"></div>
                                                <div class=\"tabs-title r-block float-left small\" style=\"background: #262626;\"></div>
                                                <div class=\"tabs-title r-block float-left small\" style=\"background: #262626;\"></div>
                                            </div>
                                            <div class=\"result-content\">
                                                <div class=\"r-content-title r-block middle\" style=\"background: #919191;\"></div>
                                                <ul class=\"r-list clearfix\">
                                                    <li class=\"r-item float-left\">
                                                        <svg width=\"124\" height=\"124\">
                                                            <line x1=\"0\" y1=\"0\" x2=\"124\" y2=\"0\" stroke-width=\"2\" stroke=\"#cbb8a6\"/>
                                                            <line x1=\"124\" y1=\"0\" x2=\"124\" y2=\"124\" stroke-width=\"2\" stroke=\"#cbb8a6\"/>
                                                            <line x1=\"124\" y1=\"124\" x2=\"0\" y2=\"124\" stroke-width=\"2\" stroke=\"#cbb8a6\"/>
                                                            <line x1=\"0\" y1=\"124\" x2=\"0\" y2=\"0\" stroke-width=\"2\" stroke=\"#cbb8a6\"/>
                                                            <line x1=\"0\" y1=\"0\" x2=\"124\" y2=\"124\" stroke-width=\"2\" stroke=\"#cbb8a6\"/>
                                                            <line x1=\"124\" y1=\"0\" x2=\"0\" y2=\"124\" stroke-width=\"2\" stroke=\"#cbb8a6\"/>
                                                        </svg>
                                                    </li>
                                                    <li class=\"r-item float-left\">
                                                        <svg width=\"124\" height=\"124\">
                                                            <line x1=\"0\" y1=\"0\" x2=\"124\" y2=\"0\" stroke-width=\"2\" stroke=\"#cbb8a6\"/>
                                                            <line x1=\"124\" y1=\"0\" x2=\"124\" y2=\"124\" stroke-width=\"2\" stroke=\"#cbb8a6\"/>
                                                            <line x1=\"124\" y1=\"124\" x2=\"0\" y2=\"124\" stroke-width=\"2\" stroke=\"#cbb8a6\"/>
                                                            <line x1=\"0\" y1=\"124\" x2=\"0\" y2=\"0\" stroke-width=\"2\" stroke=\"#cbb8a6\"/>
                                                            <line x1=\"0\" y1=\"0\" x2=\"124\" y2=\"124\" stroke-width=\"2\" stroke=\"#cbb8a6\"/>
                                                            <line x1=\"124\" y1=\"0\" x2=\"0\" y2=\"124\" stroke-width=\"2\" stroke=\"#cbb8a6\"/>
                                                        </svg>
                                                        <div class=\"r-info\">
                                                            <div class=\"r-link secondary r-block middle\"></div>
                                                            <div class=\"r-link secondary r-block middle\"></div>
                                                            <div class=\"r-text r-block middle\" style=\"background: #919191;\"></div>
                                                            <div class=\"r-price primary r-block small\"></div>
                                                            <div class=\"r-button primary r-block\"></div>
                                                        </div>
                                                    </li>
                                                    <li class=\"r-item float-left\">
                                                        <svg width=\"124\" height=\"124\">
                                                            <line x1=\"0\" y1=\"0\" x2=\"124\" y2=\"0\" stroke-width=\"2\" stroke=\"#cbb8a6\"/>
                                                            <line x1=\"124\" y1=\"0\" x2=\"124\" y2=\"124\" stroke-width=\"2\" stroke=\"#cbb8a6\"/>
                                                            <line x1=\"124\" y1=\"124\" x2=\"0\" y2=\"124\" stroke-width=\"2\" stroke=\"#cbb8a6\"/>
                                                            <line x1=\"0\" y1=\"124\" x2=\"0\" y2=\"0\" stroke-width=\"2\" stroke=\"#cbb8a6\"/>
                                                            <line x1=\"0\" y1=\"0\" x2=\"124\" y2=\"124\" stroke-width=\"2\" stroke=\"#cbb8a6\"/>
                                                            <line x1=\"124\" y1=\"0\" x2=\"0\" y2=\"124\" stroke-width=\"2\" stroke=\"#cbb8a6\"/>
                                                        </svg>
                                                    </li>
                                                    <li class=\"r-item float-left\">
                                                        <svg width=\"124\" height=\"124\">
                                                            <line x1=\"0\" y1=\"0\" x2=\"124\" y2=\"0\" stroke-width=\"2\" stroke=\"#cbb8a6\"/>
                                                            <line x1=\"124\" y1=\"0\" x2=\"124\" y2=\"124\" stroke-width=\"2\" stroke=\"#cbb8a6\"/>
                                                            <line x1=\"124\" y1=\"124\" x2=\"0\" y2=\"124\" stroke-width=\"2\" stroke=\"#cbb8a6\"/>
                                                            <line x1=\"0\" y1=\"124\" x2=\"0\" y2=\"0\" stroke-width=\"2\" stroke=\"#cbb8a6\"/>
                                                            <line x1=\"0\" y1=\"0\" x2=\"124\" y2=\"124\" stroke-width=\"2\" stroke=\"#cbb8a6\"/>
                                                            <line x1=\"124\" y1=\"0\" x2=\"0\" y2=\"124\" stroke-width=\"2\" stroke=\"#cbb8a6\"/>
                                                        </svg>
                                                    </li>
                                                    <li class=\"r-item float-left\">
                                                        <svg width=\"124\" height=\"124\">
                                                            <line x1=\"0\" y1=\"0\" x2=\"124\" y2=\"0\" stroke-width=\"2\" stroke=\"#cbb8a6\"/>
                                                            <line x1=\"124\" y1=\"0\" x2=\"124\" y2=\"124\" stroke-width=\"2\" stroke=\"#cbb8a6\"/>
                                                            <line x1=\"124\" y1=\"124\" x2=\"0\" y2=\"124\" stroke-width=\"2\" stroke=\"#cbb8a6\"/>
                                                            <line x1=\"0\" y1=\"124\" x2=\"0\" y2=\"0\" stroke-width=\"2\" stroke=\"#cbb8a6\"/>
                                                            <line x1=\"0\" y1=\"0\" x2=\"124\" y2=\"124\" stroke-width=\"2\" stroke=\"#cbb8a6\"/>
                                                            <line x1=\"124\" y1=\"0\" x2=\"0\" y2=\"124\" stroke-width=\"2\" stroke=\"#cbb8a6\"/>
                                                        </svg>
                                                    </li>
                                                    <li class=\"r-item float-left\">
                                                        <svg width=\"124\" height=\"124\">
                                                            <line x1=\"0\" y1=\"0\" x2=\"124\" y2=\"0\" stroke-width=\"2\" stroke=\"#cbb8a6\"/>
                                                            <line x1=\"124\" y1=\"0\" x2=\"124\" y2=\"124\" stroke-width=\"2\" stroke=\"#cbb8a6\"/>
                                                            <line x1=\"124\" y1=\"124\" x2=\"0\" y2=\"124\" stroke-width=\"2\" stroke=\"#cbb8a6\"/>
                                                            <line x1=\"0\" y1=\"124\" x2=\"0\" y2=\"0\" stroke-width=\"2\" stroke=\"#cbb8a6\"/>
                                                            <line x1=\"0\" y1=\"0\" x2=\"124\" y2=\"124\" stroke-width=\"2\" stroke=\"#cbb8a6\"/>
                                                            <line x1=\"124\" y1=\"0\" x2=\"0\" y2=\"124\" stroke-width=\"2\" stroke=\"#cbb8a6\"/>
                                                        </svg>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                        <div class=\"submit-wrapper float-right\">
                            <input type=\"submit\" class=\"reset-btn button gray60-bg ie-radius\" value=\"Сбросить\"/>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
";
    }

    // line 9
    public function block_tabs($context, array $blocks = array())
    {
        // line 10
        echo "                    ";
        $context["activeTab"] = "colors";
        // line 11
        echo "                    ";
        $this->displayParentBlock("tabs", $context, $blocks);
        echo "
                ";
    }

    public function getTemplateName()
    {
        return "@markup/private/mini-site/colors.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  320 => 11,  317 => 10,  314 => 9,  47 => 13,  45 => 9,  39 => 5,  36 => 4,  30 => 2,);
    }
}
