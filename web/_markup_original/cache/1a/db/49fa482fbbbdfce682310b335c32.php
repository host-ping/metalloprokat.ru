<?php

/* @markup/private/consumers/private-demand.html.twig */
class __TwigTemplate_1adb49fa482fbbbdfce682310b335c32 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = $this->env->loadTemplate("@markup/private/consumers/_private_consumers.html.twig");

        $this->blocks = array(
            'title' => array($this, 'block_title'),
            'content' => array($this, 'block_content'),
            'tabs' => array($this, 'block_tabs'),
            'filters' => array($this, 'block_filters'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "@markup/private/consumers/_private_consumers.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 2
    public function block_title($context, array $blocks = array())
    {
        echo "Private room consumers";
    }

    // line 3
    public function block_content($context, array $blocks = array())
    {
        // line 4
        echo "    <div id=\"content\" class=\"private-room-content content-right outline-right float-right\">
        ";
        // line 5
        $this->displayBlock('tabs', $context, $blocks);
        // line 9
        echo "        ";
        $this->displayBlock('filters', $context, $blocks);
        // line 177
        echo "        <div class=\"private-view-category view-category demands\">
            <div class=\"content-scrollable js-scrollable\">
                <ul class=\"demands list private-fix-width\">
                    <li class=\"demand_item\">
                        <div class=\"demand_holder clearfix\">
                            <div class=\"demand-data float-left\">
                                <div class=\"demand-title\">
                                    <strong class=\"demand-count red-color\">№39526</strong>
                                    Разовая потребность
                                    <span class=\"demand-date\">от 28 апр 2013</span>
                                </div>
                                <table class=\"demand_product-list demand-table\">
                                    <tr class=\"row first\"> ";
        // line 190
        echo "                                        <td class=\"item col\">
                                            <p class=\"product-item\">1</p>
                                        </td>
                                        <td class=\"title col\">
                                            <p class=\"product-title\">Арматура А3</p>
                                        </td>
                                        <td class=\"count col\">
                                            <p class=\"product-count\">50 тонн</p>
                                        </td>
                                    </tr>
                                    <tr class=\"row\">
                                        <td class=\"item col\">
                                            <p class=\"product-item\">2</p>
                                        </td>
                                        <td class=\"title col\">
                                            <p class=\"product-title\">Арматура А3</p>
                                        </td>
                                        <td class=\"count col\">
                                            <p class=\"product-count\">50 тонн</p>
                                        </td>
                                    </tr>
                                    <tr class=\"row\">
                                        <td class=\"item col\">
                                            <p class=\"product-item\">3</p>
                                        </td>
                                        <td class=\"title col\">
                                            <p class=\"product-title\">Арматура А3</p>
                                        </td>
                                        <td class=\"count col\">
                                            <p class=\"product-count\">50 тонн</p>
                                        </td>
                                    </tr>
                                </table>
                                <a class=\"demand_link\" href=\"#\">Всего 8 позиций</a>
                            </div>
                            <div class=\"demand-info float-right\">
                                <p class=\"user text\">Алексей из Одинцово</p>
                                <p class=\"attached-product icon-clip text\">прикреплена к товару<a href=\"#\"> Арматура 12 А3 </a>найдена в Москве</p>
                                <p class=\"viewed icon-views text\">Просмотрена</p>
                            </div>
                        </div>
                        <ul class=\"links clearfix\">
                            <li class=\"links_report item float-left clearfix\">
                                <a href=\"#\" class=\"button report is-bordered js-tooltip-opener js-popup-opener ie-radius\" data-tooltip-title=\"Пожаловаться\" data-popup=\"#report\">
                                    <span class=\"icon-complaint\"></span>
                                </a>
                            </li>
                            <li class=\"links_contacts item default-width float-left clearfix\">
                                <a class=\"button contacts is-bordered js-popover-opener ie-radius\" href=\"#\" data-popover=\"#contact-1\" data-index=\"2\">
                                    <span class=\"text\">Контакты</span>
                                    <span class=\"icon-points float-right\"></span>
                                </a>
                                <div id=\"contact-1\" class=\"drop-wrapper contact is-bordered ie-radius\">
                                    <p class=\"text\">Для просмотра контактной информации о потребителе необходимо зарегистрироваться и авторизоваться.</p>
                                    <a class=\"button is-bordered ie-radius\" href=\"#\">Перейти на страницу потребности</a>
                                </div>
                            </li>
                            <li class=\"links_answer item default-width float-left clearfix\">
                                <a class=\"button answer is-bordered green-bg js-popup-opener ie-radius\" data-popup=\"#demand-answer-1\" href=\"#\">
                                    <span class=\"text\">Ответить</span>
                                    <span class=\"icon-back float-right\"></span>
                                </a>

                            </li>
                            <li class=\"links_read-answers item default-width float-left clearfix\">
                                <a class=\"button is-bordered js-popup-opener ie-radius\" data-popup=\"#demand-answers\" href=\"#\">
                                    <span class=\"text\">Читать ответы</span>
                                    <strong class=\"count gray80-color\">3</strong>
                                    <span class=\"read-answers icon-back float-right\"></span>
                                </a>
                            </li>
                        </ul>
                        <div id=\"demand-answer-1\" class=\"demand-answer-block popup-block opacity-border large fixed\">
                            <div class=\"popup-content\">
                                <div class=\"title-popup\">Ответ на потребность <strong class=\"demand-count red-color\">№45781</strong></div>
                                <span class=\"close-popup icon-popup-close js-popup-closer clickable\"></span>
                                <form class=\"demand-answer-form form\" action=\"#\">
                                    <fieldset>
                                        <div class=\"field-wrap\">
                                            <textarea class=\"form-textarea ie-radius\" placeholder=\"Комментарии\"></textarea>
                                        </div>
                                        <div class=\"submit-wrapper\">
                                            <input class=\"add-btn button blue-bg ie-radius\" type=\"submit\" value=\"Отправить\"/>
                                        </div>
                                    </fieldset>
                                </form>
                            </div>
                        </div>
                    </li>
                    <li class=\"demand_item\">
                        <div class=\"demand_holder clearfix\">
                            <div class=\"demand-data float-left\">
                                <div class=\"demand-title\">
                                    <strong class=\"demand-count red-color\">№39526</strong>
                                    Постоянная потребность
                                    <span class=\"demand-date\">от 28 апр 2013</span>
                                </div>
                                <table class=\"demand_product-list demand-table\">
                                    <tr class=\"row\">
                                        <td class=\"item col\">
                                            <p class=\"product-item\">1</p>
                                        </td>
                                        <td class=\"title col\">
                                            <p class=\"product-title\">Арматура А3</p>
                                        </td>
                                        <td class=\"count col\">
                                            <p class=\"product-count\">50 тонн</p>
                                        </td>
                                    </tr>
                                </table>
                                <a class=\"demand_link\" href=\"#\">Всего 8 позиций</a>
                            </div>
                            <div class=\"demand-info float-right\">
                                <p class=\"user text\">Покупатель из Одинцово</p>
                                <p class=\"attached-product icon-clip text\">прикреплена к товару<a href=\"#\"> Арматура 12 А3 </a>найдена в Москве</p>
                            </div>
                        </div>
                        <ul class=\"links clearfix\">
                            <li class=\"links_report item float-left clearfix\">
                                <a href=\"#\" class=\"button report is-bordered js-tooltip-opener js-popup-opener ie-radius\" data-tooltip-title=\"Пожаловаться\" data-popup=\"#report\">
                                    <span class=\"icon-complaint\"></span>
                                </a>
                            </li>
                            <li class=\"links_contacts item default-width float-left clearfix\">
                                <a class=\"button contacts is-bordered js-popover-opener ie-radius\" href=\"#\" data-popover=\"#contact-2\" data-index=\"2\">
                                    <span class=\"text\">Контакты и условия</span>
                                    <span class=\"icon-points float-right\"></span>
                                </a>
                                <div id=\"contact-2\" class=\"drop-wrapper contact is-bordered ie-radius\">
                                    <div class=\"user-info-block\">
                                        <p class=\"user\">Василий Ложкин</p>
                                        <p class=\"user-contact\">
                                            <span class=\"phone-text\">+7 (495) 772-65-83,</span> <a href=\"#\">vasiliy@lozhkin.com</a>
                                        </p>
                                    </div>
                                    <a class=\"button is-bordered ie-radius\" href=\"#\">Перейти на страницу потребности</a>
                                </div>
                            </li>
                            <li class=\"links_answer item default-width float-left clearfix\">
                                <a class=\"button answer is-bordered green-bg js-popup-opener ie-radius\" data-popup=\"#demand-answer-2\" href=\"#\">
                                    <span class=\"text\">Ответить</span>
                                    <span class=\"icon-back float-right\"></span>
                                </a>
                            </li>
                        </ul>
                        <div id=\"demand-answer-2\" class=\"demand-answer-block popup-block opacity-border large fixed\">
                            <div class=\"popup-content\">
                                <div class=\"title-popup\">Ответ на потребность <strong class=\"demand-count red-color\">№45781</strong></div>
                                <span class=\"close-popup icon-popup-close js-popup-closer clickable\"></span>
                                <form class=\"demand-answer-form form\" action=\"#\">
                                    <fieldset>
                                        <div class=\"field-wrap\">
                                            <textarea class=\"form-textarea ie-radius\" placeholder=\"Комментарии\"></textarea>
                                        </div>
                                        <div class=\"submit-wrapper\">
                                            <input class=\"add-btn button blue-bg ie-radius\" type=\"submit\" value=\"Отправить\"/>
                                        </div>
                                    </fieldset>
                                </form>
                            </div>
                        </div>

                    </li>
                    <li class=\"demand_item\">
                        <div class=\"demand_holder clearfix\">
                            <div class=\"demand-data float-left\">
                                <div class=\"demand-title\">
                                    <strong class=\"demand-count red-color\">№39526</strong>
                                    Разовая потребность
                                    <span class=\"demand-date\">от 28 апр 2013</span>
                                </div>
                                <table class=\"demand_product-list demand-table\">
                                    <tr class=\"row first\">
                                        <td class=\"item col\">
                                            <p class=\"product-item\">1</p>
                                        </td>
                                        <td class=\"title col\">
                                            <p class=\"product-title\">Арматура А3</p>
                                        </td>
                                        <td class=\"count col\">
                                            <p class=\"product-count\">50 тонн</p>
                                        </td>
                                    </tr>
                                    <tr class=\"row\">
                                        <td class=\"item col\">
                                            <p class=\"product-item\">2</p>
                                        </td>
                                        <td class=\"title col\">
                                            <p class=\"product-title\">Арматура А3</p>
                                        </td>
                                        <td class=\"count col\">
                                            <p class=\"product-count\">50 тонн</p>
                                        </td>
                                    </tr>
                                    <tr class=\"row\">
                                        <td class=\"item col\">
                                            <p class=\"product-item\">3</p>
                                        </td>
                                        <td class=\"title col\">
                                            <p class=\"product-title\">Арматура А3</p>
                                        </td>
                                        <td class=\"count col\">
                                            <p class=\"product-count\">50 тонн</p>
                                        </td>
                                    </tr>
                                </table>
                                <a class=\"demand_link\" href=\"#\">Всего 8 позиций</a>
                            </div>
                            <div class=\"demand-info float-right\">
                                <p class=\"user text\">Алексей из Одинцово</p>
                                <p class=\"attached-product icon-clip text\">прикреплена к товару<a href=\"#\"> Арматура 12 А3 </a>найдена в Москве</p>
                                <p class=\"viewed icon-views text\">Просмотрена</p>
                            </div>
                        </div>
                        <ul class=\"links clearfix\">
                            <li class=\"links_report item float-left clearfix\">
                                <a href=\"#\" class=\"button report is-bordered js-tooltip-opener js-popup-opener ie-radius\" data-tooltip-title=\"Пожаловаться\" data-popup=\"#report\">
                                    <span class=\"icon-complaint\"></span>
                                </a>
                            </li>
                            <li class=\"links_contacts item default-width float-left clearfix\">
                                <a class=\"button contacts is-bordered js-popover-opener ie-radius\" href=\"#\" data-popover=\"#contact-3\" data-index=\"2\">
                                    <span class=\"text\">Контакты и условия</span>
                                    <span class=\"icon-points float-right\"></span>
                                </a>
                                <div id=\"contact-3\" class=\"drop-wrapper contact is-bordered ie-radius\">
                                    <p class=\"text\">Для просмотра контактной информации о потребителе необходимо зарегистрироваться и авторизоваться.</p>
                                    <a class=\"button is-bordered ie-radius\" href=\"#\">Перейти на страницу потребности</a>
                                </div>
                            </li>
                            <li class=\"links_answer item default-width float-left clearfix\">
                                <a class=\"button answer is-bordered green-bg js-popup-opener ie-radius\" data-popup=\"#demand-answer-3\" href=\"#\">
                                    <span class=\"text\">Ответить</span>
                                    <span class=\"icon-back float-right\"></span>
                                </a>
                            </li>
                            <li class=\"links_read-answers item default-width float-left clearfix\">
                                <a class=\"button is-bordered ie-radius\" href=\"#\">
                                    <span class=\"text\">Читать ответы</span>
                                    <strong class=\"count gray80-color\">3</strong>
                                    <span class=\"read-answers icon-back float-right\"></span>
                                </a>
                            </li>
                        </ul>
                        <div id=\"demand-answer-3\" class=\"demand-answer-block popup-block opacity-border large fixed\">
                            <div class=\"popup-content\">
                                <div class=\"title-popup\">Ответ на потребность <strong class=\"demand-count red-color\">№45781</strong></div>
                                <span class=\"close-popup icon-popup-close js-popup-closer clickable\"></span>
                                <form class=\"demand-answer-form form\" action=\"#\">
                                    <fieldset>
                                        <div class=\"field-wrap\">
                                            <textarea class=\"form-textarea ie-radius\" placeholder=\"Комментарии\"></textarea>
                                        </div>
                                        <div class=\"submit-wrapper\">
                                            <input class=\"add-btn button blue-bg ie-radius\" type=\"submit\" value=\"Отправить\"/>
                                        </div>
                                    </fieldset>
                                </form>
                            </div>
                        </div>

                    </li>
                    <li class=\"demand_item\">
                        <div class=\"demand_holder clearfix\">
                            <div class=\"demand-data float-left\">
                                <div class=\"demand-title\">
                                    <strong class=\"demand-count red-color\">№39526</strong>
                                    Постоянная потребность
                                    <span class=\"demand-date\">от 28 апр 2013</span>
                                </div>
                                <table class=\"demand_product-list demand-table\">
                                    <tr class=\"row\">
                                        <td class=\"item col\">
                                            <p class=\"product-item\">1</p>
                                        </td>
                                        <td class=\"title col\">
                                            <p class=\"product-title\">Арматура А3</p>
                                        </td>
                                        <td class=\"count col\">
                                            <p class=\"product-count\">50 тонн</p>
                                        </td>
                                    </tr>
                                </table>
                                <a class=\"demand_link\" href=\"#\">Всего 8 позиций</a>
                            </div>
                            <div class=\"demand-info float-right\">
                                <p class=\"user text\">Покупатель из Одинцово</p>
                                <p class=\"attached-product icon-clip text\">прикреплена к товару<a href=\"#\"> Арматура 12 А3 </a>найдена в Москве</p>
                            </div>
                        </div>
                        <ul class=\"links clearfix\">
                            <li class=\"links_report item float-left clearfix\">
                                <a href=\"#\" class=\"button report is-bordered js-tooltip-opener js-popup-opener ie-radius\" data-tooltip-title=\"Пожаловаться\" data-popup=\"#report\">
                                    <span class=\"icon-complaint\"></span>
                                </a>
                            </li>
                            <li class=\"links_contacts item default-width float-left clearfix\">
                                <a class=\"button contacts is-bordered js-popover-opener ie-radius\" href=\"#\" data-popover=\"#contact-4\" data-index=\"2\">
                                    <span class=\"text\">Контакты</span>
                                    <span class=\"icon-points float-right\"></span>
                                </a>
                                <div id=\"contact-4\" class=\"drop-wrapper contact is-bordered ie-radius\">
                                    <div class=\"user-info-block\">
                                        <p class=\"user\">Василий Ложкин</p>
                                        <p class=\"user-contact\">
                                            <span class=\"phone-text\">+7 (495) 772-65-83,</span> <a href=\"#\">vasiliy@lozhkin.com</a>
                                        </p>
                                    </div>
                                    <a class=\"button is-bordered ie-radius\" href=\"#\">Перейти на страницу потребности</a>
                                </div>
                            </li>
                            <li class=\"links_answer item default-width float-left clearfix\">
                                <a class=\"button answer is-bordered green-bg js-popup-opener ie-radius\" data-popup=\"#demand-answer-4\" href=\"#\">
                                    <span class=\"text\">Ответить</span>
                                    <span class=\"icon-back float-right\"></span>
                                </a>
                            </li>
                        </ul>
                        <div id=\"demand-answer-4\" class=\"demand-answer-block popup-block opacity-border large fixed\">
                            <div class=\"popup-content\">
                                <div class=\"title-popup\">Ответ на потребность <strong class=\"demand-count red-color\">№45781</strong></div>
                                <span class=\"close-popup icon-popup-close js-popup-closer clickable\"></span>
                                <form class=\"demand-answer-form form\" action=\"#\">
                                    <fieldset>
                                        <div class=\"field-wrap\">
                                            <textarea class=\"form-textarea ie-radius\" placeholder=\"Комментарии\"></textarea>
                                        </div>
                                        <div class=\"submit-wrapper\">
                                            <input class=\"add-btn button blue-bg ie-radius\" type=\"submit\" value=\"Отправить\"/>
                                        </div>
                                    </fieldset>
                                </form>
                            </div>
                        </div>

                    </li>
                    <li class=\"demand_item\">
                        <div class=\"demand_holder clearfix\">
                            <div class=\"demand-data float-left\">
                                <div class=\"demand-title\">
                                    <strong class=\"demand-count red-color\">№39526</strong>
                                    Разовая потребность
                                    <span class=\"demand-date\">от 28 апр 2013</span>
                                </div>
                                <table class=\"demand_product-list demand-table\">
                                    <tr class=\"row first\">
                                        <td class=\"item col\">
                                            <p class=\"product-item\">1</p>
                                        </td>
                                        <td class=\"title col\">
                                            <p class=\"product-title\">Арматура А3</p>
                                        </td>
                                        <td class=\"count col\">
                                            <p class=\"product-count\">50 тонн</p>
                                        </td>
                                    </tr>
                                    <tr class=\"row\">
                                        <td class=\"item col\">
                                            <p class=\"product-item\">2</p>
                                        </td>
                                        <td class=\"title col\">
                                            <p class=\"product-title\">Арматура А3</p>
                                        </td>
                                        <td class=\"count col\">
                                            <p class=\"product-count\">50 тонн</p>
                                        </td>
                                    </tr>
                                    <tr class=\"row\">
                                        <td class=\"item col\">
                                            <p class=\"product-item\">3</p>
                                        </td>
                                        <td class=\"title col\">
                                            <p class=\"product-title\">Арматура А3</p>
                                        </td>
                                        <td class=\"count col\">
                                            <p class=\"product-count\">50 тонн</p>
                                        </td>
                                    </tr>
                                </table>
                                <a class=\"demand_link\" href=\"#\">Всего 8 позиций</a>
                            </div>
                            <div class=\"demand-info float-right\">
                                <p class=\"user text\">Алексей из Одинцово</p>
                                <p class=\"attached-product icon-clip text\">прикреплена к товару<a href=\"#\"> Арматура 12 А3 </a>найдена в Москве</p>
                                <p class=\"viewed icon-views text\">Просмотрена</p>
                            </div>
                        </div>
                        <ul class=\"links clearfix\">
                            <li class=\"links_report item float-left clearfix\">
                                <a href=\"#\" class=\"button report is-bordered js-tooltip-opener js-popup-opener ie-radius\" data-tooltip-title=\"Пожаловаться\" data-popup=\"#report\">
                                    <span class=\"icon-complaint\"></span>
                                </a>
                            </li>
                            <li class=\"links_contacts item default-width float-left clearfix\">
                                <a class=\"button contacts is-bordered js-popover-opener ie-radius\" href=\"#\" data-popover=\"#contact-5\" data-index=\"2\">
                                    <span class=\"text\">Контакты</span>
                                    <span class=\"icon-points float-right\"></span>
                                </a>
                                <div id=\"contact-5\" class=\"drop-wrapper contact is-bordered ie-radius\">
                                    <p class=\"text\">Для просмотра контактной информации о потребителе необходимо зарегистрироваться и авторизоваться.</p>
                                    <a class=\"button is-bordered ie-radius\" href=\"#\">Перейти на страницу потребности</a>
                                </div>
                            </li>
                            <li class=\"links_answer item default-width float-left clearfix\">
                                <a class=\"button answer is-bordered green-bg js-popup-opener ie-radius\" data-popup=\"#demand-answer\" href=\"#\">
                                    <span class=\"text\">Ответить</span>
                                    <span class=\"icon-back float-right\"></span>
                                </a>
                            </li>
                            <li class=\"links_read-answers item default-width float-left clearfix\">
                                <a class=\"button is-bordered ie-radius\" href=\"#\">
                                    <span class=\"text\">Читать ответы</span>
                                    <strong class=\"count gray80-color\">3</strong>
                                    <span class=\"read-answers icon-back float-right\"></span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class=\"demand_item\">
                        <div class=\"demand_holder clearfix\">
                            <div class=\"demand-data float-left\">
                                <div class=\"demand-title\">
                                    <strong class=\"demand-count red-color\">№39526</strong>
                                    Постоянная потребность
                                    <span class=\"demand-date\">от 28 апр 2013</span>
                                </div>
                                <table class=\"demand_product-list demand-table\">
                                    <tr class=\"row\">
                                        <td class=\"item col\">
                                            <p class=\"product-item\">1</p>
                                        </td>
                                        <td class=\"title col\">
                                            <p class=\"product-title\">Арматура А3</p>
                                        </td>
                                        <td class=\"count col\">
                                            <p class=\"product-count\">50 тонн</p>
                                        </td>
                                    </tr>
                                </table>
                                <a class=\"demand_link\" href=\"#\">Всего 8 позиций</a>
                            </div>
                            <div class=\"demand-info float-right\">
                                <p class=\"user text\">Покупатель из Одинцово</p>
                                <p class=\"attached-product icon-clip text\">прикреплена к товару<a href=\"#\"> Арматура 12 А3 </a>найдена в Москве</p>
                            </div>
                        </div>
                        <ul class=\"links clearfix\">
                            <li class=\"links_report item float-left clearfix\">
                                <a href=\"#\" class=\"button report is-bordered js-tooltip-opener js-popup-opener ie-radius\" data-tooltip-title=\"Пожаловаться\" data-popup=\"#report\">
                                    <span class=\"icon-complaint\"></span>
                                </a>
                            </li>
                            <li class=\"links_contacts item default-width float-left clearfix\">
                                <a class=\"button contacts is-bordered js-popover-opener ie-radius\" href=\"#\" data-popover=\"#contact-6\" data-index=\"2\">
                                    <span class=\"text\">Контакты</span>
                                    <span class=\"icon-points float-right\"></span>
                                </a>
                                <div id=\"contact-6\" class=\"drop-wrapper contact is-bordered ie-radius\">
                                    <div class=\"user-info-block\">
                                        <p class=\"user\">Василий Ложкин</p>
                                        <p class=\"user-contact\">
                                            <span class=\"phone-text\">+7 (495) 772-65-83,</span> <a href=\"#\">vasiliy@lozhkin.com</a>
                                        </p>
                                    </div>
                                    <a class=\"button is-bordered ie-radius\" href=\"#\">Перейти на страницу потребности</a>
                                </div>
                            </li>
                            <li class=\"links_answer item default-width float-left clearfix\">
                                <a class=\"button answer is-bordered green-bg js-popup-opener ie-radius\" data-popup=\"#demand-answer\" href=\"#\">
                                    <span class=\"text\">Ответить</span>
                                    <span class=\"icon-back float-right\"></span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class=\"see-more-block-wrapper\">
                        <div class=\"see-more-block\">
                            <a class=\"see-more button ie-radius\" href=\"#\">показать еще...</a>
                        </div>
                    </li>
                </ul>

            </div>
        </div>
    </div>
";
    }

    // line 5
    public function block_tabs($context, array $blocks = array())
    {
        // line 6
        echo "            ";
        $context["activeTab"] = "demand";
        // line 7
        echo "            ";
        $this->displayParentBlock("tabs", $context, $blocks);
        echo "
        ";
    }

    // line 9
    public function block_filters($context, array $blocks = array())
    {
        // line 10
        echo "            <div class=\"filters-block multi-column outline-right clearfix\">
                <div class=\"clearfix\">
                    <div class=\"filter-viewed-wrapper filter-item float-left\">
                        <span class=\"filter-type clickable link js-popover-opener float-left icon-check black\" data-popover=\"#filters-watch\" title=\"Непросмотренные\">Непросмотренные</span>
                        <div id=\"filters-watch\" class=\"drop-wrapper favorite-type_links opacity-border\">
                            <ul class=\"dropdown\">
                                <li class=\"drop-item\">
                                    <span class=\"drop-link current\">Непросмотренные</span>
                                </li>
                                <li class=\"drop-item\">
                                    <a class=\"drop-link\" href=\"#\">Просмотренные</a>
                                </li>
                                <li class=\"drop-item\">
                                    <a class=\"drop-link\" href=\"#\">Все</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class=\"filter-categories-wrapper filter-item float-left\">
                        <span class=\"filter-category link js-popover-opener clickable float-left icon-check black\" data-popover=\"#filters-categories\" title=\"Все категории\">Все категории</span>
                        <div id=\"filters-categories\" class=\"drop-wrapper favorite-type_links opacity-border\">
                            <ul class=\"dropdown\">
                                <li class=\"drop-item\">
                                    <span class=\"drop-link current\">Все категории</span>
                                </li>
                                <li class=\"drop-item\">
                                    <a class=\"drop-link\" href=\"#\">Прочее</a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class=\"filter-cities-wrapper filter-item float-left\">
                        <span class=\"filter-city link js-popover-opener clickable float-left icon-check black\" data-popover=\"#filters-cities\" title=\"Все города\">Все города</span>
                        <div id=\"filters-cities\" class=\"drop-wrapper favorite-type_links opacity-border\">
                            <div class=\"scroll-wrapper js-scrollable\" style=\"position: relative;\">
                                <ul class=\"dropdown\">
                                    <li class=\"drop-item\">
                                        <span class=\"drop-link current\">Все города</span>
                                    </li>
                                    <li class=\"drop-item\">
                                        <a class=\"drop-link\" href=\"/clients/demands?filterByCity=257\">Брянск</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class=\"export-block filter-item float-left\">
                        <a class=\"export link js-popover-opener\" data-popover=\"#export-demands\">Экспорт</a>

                        <div id=\"export-demands\" class=\"drop-wrapper export-feed_links opacity-border\">
                            <div class=\"dropdown\">
                                <div class=\"export-links block clearfix\">
                                    <p class=\"title export link\">Экспорт</p>
                                    <a class=\"button small-btn blue-bg float-left ie-radius\" href=\"/private-demands/export/xlsx\">XLSX</a>
                                    <a class=\"button small-btn blue-bg float-left ie-radius\" href=\"/private-demands/export/csv\">CSV</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class=\"clearfix\">
                    <div class=\"filter-period sort-period float-left\">
                        <ul class=\"sort-period list float-left\">
                            <li class=\"item float-left\">
                                <span class=\"period-link icon-check black link clickable js-popover-opener\" data-popover=\"#periods\">
                                    За все время
                                </span>

                                <div id=\"periods\" class=\"drop-wrapper period-list opacity-border\">
                                    <ul class=\"dropdown\">
                                        <li class=\"drop-item\">
                                            <a class=\"drop-link\" href=\"#\">За день <span class=\"num\">0</span></a>
                                        </li>
                                        <li class=\"drop-item\">
                                            <a class=\"drop-link\" href=\"#\">За неделю <span class=\"num\">0</span></a>
                                        </li>
                                        <li class=\"drop-item\">
                                            <a class=\"drop-link\" href=\"#\">За месяц <span class=\"num\">0</span></a>
                                        </li>
                                        <li class=\"drop-item\">
                                            <a class=\"drop-link\" href=\"#\">За год <span class=\"num\">0</span></a>
                                        </li>
                                        <li class=\"drop-item\">
                                            <span class=\"drop-link clickable js-popover-opener\" data-popover=\"#datepicker-period-from\">За период </span>
                                        </li>
                                        <li class=\"drop-item\">
                                            <span class=\"drop-link current\">За все время </span>
                                        </li>
                                    </ul>
                                </div>

                            </li>
                            <li class=\"item float-left\">
                                <div class=\"period-from float-left\">
                                    <p class=\"text\">
                                        c <span class=\"icon-calendar js-popover-opener clickable\" data-popover=\"#datepicker-period-from\"></span>
                                        <input type=\"text\" id=\"date_from\" class=\"date js-popover-opener\" data-popover=\"#datepicker-period-from\">
                                    </p>
                                </div>

                                <div class=\"period-for float-left\">
                                    <p class=\"text\">
                                        по <span class=\"icon-calendar js-popover-opener clickable\" data-popover=\"#datepicker-period-to\"></span>
                                        <input type=\"text\" id=\"date_to\" class=\"date js-popover-opener\" data-popover=\"#datepicker-period-to\">
                                    </p>

                                </div>

                            </li>
                        </ul>
                    </div>


                </div>
            </div>
            ";
        // line 127
        echo "                ";
        // line 128
        echo "                    ";
        // line 129
        echo "                    ";
        // line 130
        echo "                        ";
        // line 131
        echo "                            ";
        // line 132
        echo "                                ";
        // line 133
        echo "                            ";
        // line 134
        echo "                            ";
        // line 135
        echo "                                ";
        // line 136
        echo "                            ";
        // line 137
        echo "                        ";
        // line 138
        echo "                    ";
        // line 139
        echo "                ";
        // line 140
        echo "                ";
        // line 141
        echo "                    ";
        // line 142
        echo "                        ";
        // line 143
        echo "                        ";
        // line 144
        echo "                            ";
        // line 145
        echo "                                ";
        // line 146
        echo "                                    ";
        // line 147
        echo "                                ";
        // line 148
        echo "                                ";
        // line 149
        echo "                                    ";
        // line 150
        echo "                                ";
        // line 151
        echo "                                ";
        // line 152
        echo "                                    ";
        // line 153
        echo "                                ";
        // line 154
        echo "                            ";
        // line 155
        echo "                        ";
        // line 156
        echo "                    ";
        // line 157
        echo "                    ";
        // line 158
        echo "                        ";
        // line 159
        echo "                        ";
        // line 160
        echo "                            ";
        // line 161
        echo "                                ";
        // line 162
        echo "                                    ";
        // line 163
        echo "                                ";
        // line 164
        echo "                                ";
        // line 165
        echo "                                    ";
        // line 166
        echo "                                ";
        // line 167
        echo "                            ";
        // line 168
        echo "                        ";
        // line 169
        echo "                    ";
        // line 170
        echo "                    ";
        // line 171
        echo "                        ";
        // line 172
        echo "                        ";
        // line 173
        echo "                    ";
        // line 174
        echo "                ";
        // line 175
        echo "            ";
        // line 176
        echo "        ";
    }

    public function getTemplateName()
    {
        return "@markup/private/consumers/private-demand.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  783 => 176,  781 => 175,  779 => 174,  777 => 173,  775 => 172,  773 => 171,  771 => 170,  769 => 169,  767 => 168,  765 => 167,  763 => 166,  761 => 165,  759 => 164,  757 => 163,  755 => 162,  753 => 161,  751 => 160,  749 => 159,  747 => 158,  745 => 157,  743 => 156,  741 => 155,  739 => 154,  737 => 153,  735 => 152,  733 => 151,  731 => 150,  729 => 149,  727 => 148,  725 => 147,  723 => 146,  721 => 145,  719 => 144,  717 => 143,  715 => 142,  713 => 141,  711 => 140,  709 => 139,  707 => 138,  705 => 137,  703 => 136,  701 => 135,  699 => 134,  697 => 133,  695 => 132,  693 => 131,  691 => 130,  689 => 129,  687 => 128,  685 => 127,  567 => 10,  564 => 9,  557 => 7,  554 => 6,  551 => 5,  62 => 190,  48 => 177,  45 => 9,  43 => 5,  40 => 4,  37 => 3,  31 => 2,);
    }
}
