<?php

/* @markup/ui/popups.html.twig */
class __TwigTemplate_db7d3f5faac21c0fd26d2b053b9e6b22 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = $this->env->loadTemplate("@markup/_base_layout.html.twig");

        $this->blocks = array(
            'buttons' => array($this, 'block_buttons'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "@markup/_base_layout.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_buttons($context, array $blocks = array())
    {
        // line 4
        echo "    <style type=\"text/css\">
        .example-btn{
            width: 170px;
            margin: 0 10px 10px;
            text-align: center;
        }
        .footer-content.wrap,
        #footer,
         .livebotton {display: none;}
    </style>
<span class=\"button blue-bg clickable js-popup-opener float-left example-btn\" data-popup=\"#subscribe\">Подписка</span>
<span class=\"button blue-bg clickable js-popup-opener float-left example-btn\" data-popup=\"#subscribe-extended\">Подписка с фильтром</span>
<span class=\"button blue-bg clickable js-popup-opener float-left example-btn\" data-popup=\"#demand-subscription\">Управление подписками</span>
<span class=\"button blue-bg clickable js-popup-opener float-left example-btn\" data-popup=\"#demand-subscription-advance\">Управление подписками 2</span>
<span class=\"button blue-bg clickable js-popup-opener float-left example-btn\" data-popup=\"#demand-subscription-category\">Управление подписками 3</span>
<span class=\"button blue-bg clickable js-popup-opener float-left example-btn\" data-popup=\"#error\">Ошибка</span>
<span class=\"button blue-bg clickable js-popup-opener float-left example-btn\" data-popup=\"#errors\">Ошибки импорта</span>
<span class=\"button blue-bg clickable js-popup-opener float-left example-btn\" data-popup=\"#login\">Вход в кабинет</span>
<span class=\"button blue-bg clickable js-popup-opener float-left example-btn\" data-popup=\"#report\">Жалоба</span>
<span class=\"button blue-bg clickable js-popup-opener float-left example-btn\" data-popup=\"#callback\">Обратный звонок</span>
<span class=\"button blue-bg clickable js-popup-opener float-left example-btn\" data-popup=\"#change-password\">Изменить пароль</span>
<span class=\"button blue-bg clickable js-popup-opener float-left example-btn\" data-popup=\"#recover-password\">Восстановить пароль</span>
<span class=\"button blue-bg clickable js-popup-opener float-left example-btn\" data-popup=\"#change-email\">Изменить почту</span>
<span class=\"button blue-bg clickable js-popup-opener float-left example-btn\" data-popup=\"#send-mail\">Написать письмо</span>
<span class=\"button blue-bg clickable js-popup-opener float-left example-btn\" data-popup=\"#demand-answer-anonymous\">Ответ на потребность</span>
<span class=\"button blue-bg clickable js-popup-opener float-left example-btn\" data-popup=\"#order-service\">Заказ услуги</span>
<span class=\"button blue-bg clickable js-popup-opener float-left example-btn\" data-popup=\"#order-banner\">Заказ баннера</span>
<span class=\"button blue-bg clickable js-popup-opener float-left example-btn\" data-popup=\"#add-review\">Добавить отзыв</span>
<span class=\"button blue-bg clickable js-popup-opener float-left example-btn\" data-popup=\"#more-options\">Больше возможностей</span>
<span class=\"button blue-bg clickable js-popup-opener float-left example-btn\" data-popup=\"#load-payment\">Загрузить поручение</span>
<span class=\"button blue-bg clickable js-popup-opener float-left example-btn\" data-popup=\"#favorite-product\">Избранные товары</span>
<span class=\"button blue-bg clickable js-popup-opener float-left example-btn\" data-popup=\"#demand-request\">Заявка</span>
<span class=\"button blue-bg clickable js-popup-opener float-left example-btn\" data-popup=\"#register\">Уже регистрировались?</span>
<span class=\"button blue-bg clickable js-popup-opener float-left example-btn\" data-popup=\"#register-company\">уже зарегистрирована?</span>
<span class=\"button blue-bg clickable js-popup-opener float-left example-btn\" data-popup=\"#photo\">Фото</span>
<span class=\"button blue-bg clickable js-popup-opener float-left example-btn\" data-popup=\"#delete-product\">Удаление товара</span>
<span class=\"button blue-bg clickable js-popup-opener float-left example-btn\" data-popup=\"#overwrite-product\">Перезаписать товар</span>
<span class=\"button blue-bg clickable js-popup-opener float-left example-btn\" data-popup=\"#load-photo\">Загрузить фото</span>
<span class=\"button blue-bg clickable js-popup-opener float-left example-btn\" data-popup=\"#add-product\">Добавить товар</span>
<span class=\"button blue-bg clickable js-popup-opener float-left example-btn\" data-popup=\"#add-products\">Загрузить товары</span>
<span class=\"button blue-bg clickable js-popup-opener float-left example-btn\" data-popup=\"#demand-answer-authenticated\">Ответ на потребность</span>
<span class=\"button blue-bg clickable js-popup-opener float-left example-btn\" data-popup=\"#add-support-request\">Добавление завки</span>
<span class=\"button blue-bg clickable js-popup-opener float-left example-btn\" data-popup=\"#demand-answers\">Ответы на потребность</span>
<span class=\"button blue-bg clickable js-popup-opener float-left example-btn\" data-popup=\"#confirmation-email\">Подтвердите почту</span>
<span class=\"button blue-bg clickable js-popup-opener float-left example-btn\" data-popup=\"#loading-popup\">Аяксовый попап</span>
<span class=\"button blue-bg clickable js-popover-opener float-left example-btn\" data-popover=\"#loading-cities\">Аяксовый поповер</span>
<span class=\"button blue-bg clickable js-popup-opener float-left example-btn\" data-popup=\"#requirements-for-announcement\">Требования к баннеру</span>
<span class=\"button blue-bg clickable js-popup-opener float-left example-btn\" data-popup=\"#promotion-1\">Оплата продвижения</span>
<span class=\"button blue-bg clickable js-popup-opener float-left example-btn\" data-popup=\"#user-info\">Инфа о пользователе</span>
<span class=\"button blue-bg clickable js-popup-opener float-left example-btn\" data-popup=\"#user-info-2\">Инфа о пользователе 2</span>
<span class=\"button blue-bg clickable js-popup-opener float-left example-btn\" data-popup=\"#add-answer\">Оставить комментарий</span>
<span class=\"button blue-bg clickable js-popup-opener float-left example-btn\" data-popup=\"#add-content-topic\">Добавить публикацию</span>
<span class=\"button blue-bg clickable js-popover-opener float-left example-btn\" data-popover=\"#choose-section\">Выбор раздела</span>
<span class=\"button blue-bg clickable js-popover-opener float-left example-btn\" data-popover=\"#choose-section-2\">Выбор раздела нерекурс.</span>

    ";
        // line 59
        $this->env->loadTemplate("@markup/_popups.html.twig")->display($context);
    }

    public function getTemplateName()
    {
        return "@markup/ui/popups.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  88 => 59,  31 => 4,  28 => 3,);
    }
}
