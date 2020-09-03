<?php

/* @markup/_popups.html.twig */
class __TwigTemplate_7363ff49999b01b5cd1a2712159341f2 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
            'popups' => array($this, 'block_popups'),
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        $this->displayBlock('popups', $context, $blocks);
    }

    public function block_popups($context, array $blocks = array())
    {
        // line 2
        echo "    <div id=\"subscribe\" class=\"subscribe-form popup-block opacity-border large\">
        <div class=\"popup-content\">
            <div class=\"title-popup\">Подписка</div>
            <p class=\"info\">Вы будете получать обновления в указанном разделе и регионе.</p>
            <span class=\"close-popup icon-popup-close js-popup-closer clickable\"></span>

            <form id=\"subscribe-form\" class=\"popup-form\" action=\"#\">
                <fieldset>
                    <div class=\"field-wrap\">
                        <input class=\"subscribe-form form-text ie-radius\" type=\"text\" placeholder=\"Арматура\"/>
                    </div>
                    <div class=\"field-wrap\">
                        <input class=\"subscribe-form form-text ie-radius\" type=\"text\" placeholder=\"Москва\"/>
                    </div>
                    <div class=\"field-wrap\">
                        <input class=\"subscribe-form form-email ie-radius\" type=\"email\"
                               placeholder=\"Электронная почта\"/>
                    </div>
                    <div class=\"submit-wrapper\">
                        <input class=\"popup-form_send-button send-button button blue-bg ie-radius\" type=\"submit\"
                               value=\"Подписаться\"/>
                    </div>
                </fieldset>
            </form>
            <div class=\"bottom-text\">
                <span class=\"text\">
                    Вам придет уведомление о подписке на электронную почту. Откройте ссылку в письме для подтверждения подписки.
                </span>
            </div>
        </div>
    </div>

    <div id=\"subscribe-extended\" class=\"subscribe-block popup-block opacity-border large\">
        <div class=\"popup-content\">
            <div class=\"title-popup\">Подписка</div>
            <p class=\"info\">Вы будете получать обновления в указанном разделе и регионе.</p>
            <span class=\"close-popup icon-popup-close js-popup-closer clickable\"></span>

            <form id=\"subscribe-form\" class=\"popup-form\" action=\"#\">
                <fieldset>
                    <div class=\"field-wrap\">
                        <div class=\"tag-wrapper is-bordered ie-radius form-text\">
                            <p class=\"product-name\"><strong>Арматура</strong></p>
                            <span class=\"accepted-filter ie-radius float-left\">
                                Арматура
                                <span class=\"icon-filter-del clickable\"></span>
                            </span>
                            <span class=\"accepted-filter ie-radius float-left\">
                                Арматура композитная
                                <span class=\"icon-filter-del clickable\"></span>
                            </span>
                            <span class=\"accepted-filter ie-radius float-left\">
                                Арматура
                                <span class=\"icon-filter-del clickable\"></span>
                            </span>
                            <input id=\"tags\" class=\"tag-input\" type=\"text\" placeholder=\"Add tag\"/>
                        </div>

                        <ul class=\"tt-dropdown-menu tag-dropdownt list is-bordered\">
                            <li class=\"tt-suggestion item clickable\"><strong>Арм</strong>атура</li>
                            <li class=\"tt-suggestion item clickable\"><strong>Арм</strong>атура</li>
                            <li class=\"tt-suggestion item clickable\"><strong>Арм</strong>атура</li>
                            <li class=\"tt-suggestion item clickable\"><strong>Арм</strong>атура</li>
                            <li class=\"tt-suggestion item clickable\"><strong>Арм</strong>атура</li>
                        </ul>
                    </div>
                    <div class=\"city-name-wrapper field-wrap\">
                        <input class=\"subscribe-form form-text ie-radius\" type=\"text\" placeholder=\"Москва\"/>
                    </div>
                    <div class=\"submit-wrapper\">
                        <input class=\"popup-form_send-button send-button button blue-bg ie-radius\" type=\"submit\"
                               value=\"Подписаться\"/>
                    </div>
                </fieldset>
            </form>
            <div class=\"bottom-text\">
                <span class=\"text\">
                    Вам придет уведомление о подписке на электронную почту. Откройте ссылку в письме для подтверждения подписки.
                </span>
            </div>
        </div>
    </div>

    <div id=\"error\" class=\"popup-block opacity-border large\">
        <div class=\"popup-content\">
            <p class=\"text centered\">Ошибка! Повторите попытку!</p>
            <button class=\"send-button button blue-bg ie-radius\" type=\"submit\">Ок</button>
        </div>
    </div>

    <div id=\"errors\" class=\"popup-block opacity-border large\">
        <div class=\"popup-content\">
            <div class=\"title-popup\">При импорте товаров произошли ошибки</div>

            <span class=\"close-popup icon-popup-close js-popup-closer clickable\"></span>

            <div class=\"errors-block dropdown is-bordered\">
                <div class=\"js-scrollable\">
                    <table class=\"errors-list\">
                        <tr class=\"row\">
                            <td class=\"link\">1</td>
                            <td>
                                не указан размер, неверный формат для цены, неверный формат для цены
                            </td>
                        </tr>
                        <tr class=\"row\">
                            <td class=\"link\">18</td>
                            <td>
                            </td>
                        </tr>
                        <tr class=\"row\">
                            <td class=\"link\">145</td>
                            <td>
                                не указан размер, неверный формат для цены, неверный формат для цены
                            </td>
                        </tr>
                        <tr class=\"row\">
                            <td class=\"link\">1</td>
                            <td>
                                не указан размер, неверный формат для цены, неверный формат для цены
                            </td>
                        </tr>
                        <tr class=\"row\">
                            <td class=\"link\">1</td>
                            <td>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <button class=\"send-button button blue-bg ie-radius\" type=\"submit\">Ок</button>
        </div>
    </div>

    <div id=\"login\" class=\"login-form popup-block opacity-border large\">
        <div class=\"popup-content\">
            <div class=\"title-wrapper clearfix\">
                <div class=\"title-popup float-left\">Вход в кабинет</div>
                <a class=\"register-link float-right\" href=\"#\">Регистрация</a>
            </div>
            <span class=\"close-popup icon-popup-close js-popup-closer clickable\"></span>

            <form id=\"login-form\" class=\"popup-form\" action=\"#\">
                <fieldset>
                    <div class=\"email-wrapper field-wrap\">
                        <input class=\"email form-email ie-radius\" type=\"email\" placeholder=\"Электронная почта\"/>
                    </div>
                    <div class=\"password-wrapper field-wrap\">
                        <input class=\"password form-text ie-radius\" type=\"password\" placeholder=\"Пароль\"/>
                    </div>
                    <div class=\"login-info clearfix\">
                        <div class=\"check-wrapper float-left\">
                            <input id=\"remember\" type=\"checkbox\" class=\"inform js-styled-checkbox bg-white\"/>
                            <label for=\"remember\">Запомнить меня</label>
                        </div>
                        <a class=\"forgot-link float-right\" href=\"#\">Забыл пароль</a>
                    </div>
                    <div class=\"submit-wrapper\">
                        <input class=\"popup-form_send-button send-button button blue-bg ie-radius\" type=\"submit\"
                               value=\"Войти\"/>
                    </div>
                </fieldset>
            </form>

        </div>
    </div>

    <div id=\"report\" class=\"report-form popup-block opacity-border large\">
        <div class=\"popup-content\">
            <div class=\"title-popup\">Неверная информация</div>
            <span class=\"close-popup icon-popup-close js-popup-closer clickable\"></span>

            <form id=\"report-form\" class=\"popup-form\" action=\"#\">
                <fieldset>
                    <div class=\"select-wrapper field-wrap\">
                        <select name=\"reason\" id=\"reason\" class=\"form-select\">
                            <option class=\"disabled\" value=\"-1\">Выберите причину</option>
                            <option value=\"0\">Неправильная цена</option>
                            <option value=\"1\">Не отвечают по телефону</option>
                            <option value=\"2\">Нет такой фирмы</option>
                            <option value=\"3\">Не умеют общаться</option>
                            </optgroup>
                        </select>
                    </div>
                    <div class=\"textarea-wrapper field-wrap\">
                        <textarea class=\"form-textarea ie-radius\" name=\"comment\" id=\"comment\"
                                  placeholder=\"Комментарии\"></textarea>
                    </div>
                    <div class=\"submit-wrapper\">
                        <input class=\"popup-form_send-button send-button button blue-bg ie-radius\" type=\"submit\"
                               value=\"Сообщить\"/>

                        <div class=\"loading-mask \">
                            <div class=\"spinner\"></div>
                        </div>
                    </div>
                </fieldset>
            </form>
        </div>
    </div>

    <div id=\"callback\" class=\"callback-form popup-block opacity-border large\">
        <div class=\"popup-content\">
            <div class=\"title-popup\">Перезвоните мне</div>
            <span class=\"close-popup icon-popup-close js-popup-closer clickable\"></span>

            <form id=\"callback-form\" class=\"popup-form\" action=\"#\">
                <fieldset>
                    <div class=\"group clearfix\">
                        <p class=\"text float-left is-gradiented\">Меня интересует Арматура А500С в Москве</p>

                        <div class=\"product-volume-wrapper field-wrap float-left\">
                            <input type=\"text\" class=\"volume form-text ie-radius\" placeholder=\"Объем\"/>
                        </div>
                        <div class=\"product-hundredweight-wrapper field-wrap float-left\">
                            <select class=\"hundredweight form-select\">
                                <option class=\"disabled\" value=\"-1\" disabled=\"disabled\">Тонна</option>
                                <option value=\"0\">Тонна</option>
                                <option value=\"1\">Центнер</option>
                                <option value=\"2\">Килограмм</option>
                                <option value=\"3\">Грамм</option>
                            </select>
                        </div>
                    </div>
                    <div class=\"phone-wrapper field-wrap\">
                        <input id=\"phone\" class=\"phone callback-form_phone form-text ie-radius\" type=\"text\"
                               placeholder=\"Телефон\"/>
                    </div>
                    <div class=\"inform-wrapper\">
                        <input id=\"callback_inform\" type=\"checkbox\" class=\"inform js-styled-checkbox bg-white\"/>
                        <label for=\"callback_inform\">Сообщить другим поставщикам</label>
                        <span class=\"icon-help js-helper-opener\"
                              data-text=\"Мы сообщим о вашей потребности поставщикам и вы получите гораздо больше предложений, чтобы выбрать оптимальное.\"></span>
                    </div>
                    <div class=\"submit-wrapper\">
                        <input class=\"popup-form_send-button send-button button blue-bg ie-radius\" type=\"submit\"
                               value=\"Перезвонить\"/>
                    </div>
                </fieldset>
            </form>
        </div>
    </div>

    <div id=\"change-password\" class=\"change-password popup-block opacity-border large\">
        <div class=\"popup-content\">
            <div class=\"title-popup\">Изменить пароль</div>
            <span class=\"close-popup icon-popup-close js-popup-closer clickable\"></span>

            <form id=\"change-password-form\" class=\"popup-form\" action=\"#\">
                <fieldset>
                    <div class=\"field-wrap\">
                        <input class=\"form-text ie-radius\" type=\"password\" placeholder=\"Старый пароль\"/>
                    </div>
                    <div class=\"field-wrap\">
                        <input class=\"form-text ie-radius\" type=\"password\" placeholder=\"Новый пароль\"/>
                    </div>
                    <div class=\"field-wrap\">
                        <input class=\"form-text ie-radius\" type=\"password\" placeholder=\"Повторите пароль\"/>
                    </div>
                    <div class=\"submit-wrapper\">
                        <input class=\"popup-form_send-button send-button button blue-bg ie-radius\" type=\"submit\"
                               value=\"Изменить\"/>
                    </div>
                </fieldset>
            </form>
        </div>
    </div>

    <div id=\"recover-password\" class=\"recover-password popup-block opacity-border large\">
        <div class=\"popup-content\">
            <div class=\"title-popup\">Восстановить пароль</div>
            <span class=\"close-popup icon-popup-close js-popup-closer clickable\"></span>

            <form id=\"recover-password-form\" class=\"popup-form\" action=\"#\">
                <fieldset>
                    <p class=\"text\">Введите электронную почту, указанную при регистрации. На нее будет отправлено письмо
                        с дальнейшими инструкциями.</p>

                    <div class=\"field-wrap\">
                        <input class=\"form-email ie-radius\" type=\"email\" placeholder=\"Электронная почта\"/>
                    </div>

                    <div class=\"submit-wrapper\">
                        <input class=\"popup-form_send-button send-button button blue-bg ie-radius\" type=\"submit\"
                               value=\"Отправить\"/>
                    </div>
                </fieldset>
            </form>
        </div>
    </div>

    <div id=\"change-email\" class=\"change-email popup-block opacity-border large\">
        <div class=\"popup-content\">
            <div class=\"title-popup\">Изменить почту</div>
            <span class=\"close-popup icon-popup-close js-popup-closer clickable\"></span>

            <form id=\"change-email-form\" class=\"popup-form\" action=\"#\">
                <fieldset>
                    <p class=\"text\"> На новый e-mail будет отправлено письмо с дальнейшими инструкциями</p>

                    <div class=\"field-wrap\">
                        <input class=\"form-text ie-radius\" type=\"email\" placeholder=\"Новый e-mail\"/>
                    </div>
                    <div class=\"submit-wrapper\">
                        <input class=\"send-button button blue-bg ie-radius\" type=\"submit\" value=\"Изменить\"/>
                    </div>
                </fieldset>
            </form>
        </div>
    </div>

    <div id=\"send-mail\" class=\"send-email popup-block opacity-border large\">
        <div class=\"popup-content\">
            <div class=\"title-popup\">Написать письмо</div>
            <span class=\"close-popup icon-popup-close js-popup-closer clickable\"></span>

            <form id=\"send-email-form\" class=\"popup-form\" action=\"#\">
                <fieldset>
                    <div class=\"field-wrap\">
                        <textarea class=\"form-textarea ie-radius\" placeholder=\"Текст письма\"></textarea>
                    </div>
                    <div class=\"submit-wrapper\">
                        <input class=\"send-button button blue-bg ie-radius\" type=\"submit\" value=\"Написать\"/>
                    </div>
                </fieldset>
            </form>
        </div>
    </div>

    <div id=\"demand-answer-anonymous\" class=\"demand-answer-block popup-block opacity-border large\">
        <div class=\"popup-content\">
            <div class=\"title-popup\">Ответ на потребность <strong class=\"count red-color\">№<span
                            class=\"js-demand-id-injectable\">3254</span></strong></div>
            <span class=\"close-popup icon-popup-close js-popup-closer clickable\"></span>

            <form id=\"answer-form\" class=\"answer-form popup-form\" action=\"#\">
                <fieldset>
                    <div class=\"field-groups clearfix\">
                        <div class=\"group clearfix\">
                            <div class=\"field-wrap float-left\">
                                <input class=\"form-text ie-radius\" type=\"text\"/>
                            </div>
                            <div class=\"city-wrapper field-wrap float-left\">
                                <input class=\"answer-form_city form-text ie-radius\" type=\"text\" placeholder=\"Город\"/>
                            </div>

                        </div>
                        <div class=\"group clearfix\">
                            <div class=\"user-name-wrapper field-wrap float-left\">
                                <input class=\"answer-form_user-name form-text error ie-radius\" type=\"text\"
                                       placeholder=\"Имя\"/>
                                <span class=\"icon-error-color js-helper-opener\"
                                      data-text=\"Поле не должно быть пустым\"></span>
                            </div>
                            <div class=\"phone-wrapper field-wrap float-left\">
                                <input id=\"phone\" class=\"phone answer-form_phone form-text ie-radius\" type=\"text\"
                                       placeholder=\"Телефон\"/>
                            </div>
                        </div>
                    </div>
                    <div class=\"answer_email-wrapper field-wrap\">
                        <input class=\"answer-form_email form-email ie-radius\" type=\"email\"
                               placeholder=\"Электронная почта\"/>
                    </div>
                    <div class=\"textarea-wrapper field-wrap\">
                        <textarea class=\"form-textarea error ie-radius\" name=\"answer-comment\" id=\"answer-comment\"
                                  placeholder=\"Комментарии\"></textarea>
                        <span class=\"icon-error-color js-helper-opener\" data-text=\"Поле не должно быть пустым\"></span>
                    </div>

                    <div class=\"submit-wrapper\">
                        <input class=\"popup-form_send-button send-button button blue-bg ie-radius\" type=\"submit\"
                               value=\"Отправить\"/>
                    </div>
                </fieldset>
            </form>
        </div>
    </div>

    <div id=\"order-service\" class=\"order-service-form popup-block opacity-border large\">
        <div class=\"popup-content\">
            <div class=\"title-popup\">Заказ услуги</div>
            <span class=\"close-popup icon-popup-close js-popup-closer clickable\"></span>

            <form id=\"order-service-form\" class=\"order-service popup-form\" action=\"#\">
                <fieldset>
                    <div class=\"group clearfix\">
                        <div class=\"select-wrapper field-wrap float-left\">
                            <select class=\"package-name form-select\">
                                <option class=\"disabled\" value=\"-1\" disabled=\"disabled\">Расширенный пакет</option>
                                <option value=\"0\">Расширенный пакет</option>
                                <option value=\"1\">Полный пакет</option>
                                <option value=\"2\">Базовый пакет</option>
                            </select>
                        </div>
                        <div class=\"select-wrapper field-wrap float-left\">
                            <select class=\"period form-select\">
                                <option class=\"disabled\" value=\"-1\" disabled=\"disabled\">на месяц</option>
                                <option value=\"0\">на месяц</option>
                                <option value=\"1\">на пол года</option>
                                <option value=\"2\">на год</option>
                            </select>
                        </div>
                    </div>
                    <div class=\"group clearfix\">
                        <div class=\"start-date-wrapper field-wrap float-left\">
                            <input type=\"text\" class=\"date-from form-text ie-radius\" placeholder=\"Дата\"/>
                            <span class=\"icon-calendar\"></span>
                        </div>
                        <div class=\"end-date-wrapper field-wrap float-left\">
                            <p class=\"date-to\">по 17 июн 2013</p>
                        </div>
                    </div>
                    <div class=\"textarea-wrapper field-wrap\">
                        <textarea class=\"form-textarea ie-radius\" name=\"order-service-comment\"
                                  id=\"order-service-comment\" placeholder=\"Комментарии\"></textarea>
                    </div>
                    <div class=\"group clearfix\">
                        <div class=\"field-wrap float-left\">
                            <input class=\"form-text ie-radius\" type=\"text\"/>
                        </div>
                        <div class=\"user-name-wrapper field-wrap float-left\">
                            <input class=\"user-name form-text error ie-radius\" type=\"text\" placeholder=\"Имя\"/>
                            <span class=\"icon-error-color js-helper-opener\"
                                  data-text=\"Поле не должно быть пустым\"></span>
                        </div>
                    </div>
                    <div class=\"group clearfix\">
                        <div class=\"city-wrapper field-wrap float-left\">
                            <input class=\"city form-text ie-radius\" type=\"text\" placeholder=\"Город\"/>
                        </div>
                        <div class=\"phone-wrapper field-wrap float-left\">
                            <input id=\"phone\" class=\"phone form-text ie-radius\" type=\"text\" placeholder=\"Телефон\"/>
                        </div>
                    </div>
                    <div class=\"email-wrapper field-wrap\">
                        <input class=\"form-email ie-radius\" type=\"email\" placeholder=\"Электронная почта\"/>
                    </div>

                    <div class=\"total-sum-wrapper\">
                        <span class=\"gray60-color\">к оплате</span>
                        <span class=\"sum\">19 000 <span class=\"icon-rouble\"></span></span>
                    </div>
                    <div class=\"submit-wrapper clearfix\">
                        ";
        // line 447
        echo "                        <input class=\"pay-btn button green-bg ie-radius\" type=\"submit\" value=\"Оплатить сейчас\"/>
                    </div>

                </fieldset>
            </form>
        </div>
    </div>

    <div id=\"order-banner\" class=\"order-banner-block popup-block opacity-border large\">
        <div class=\"popup-content\">
            <div class=\"title-popup\">Заказ баннера</div>
            <span class=\"close-popup icon-popup-close js-popup-closer clickable\"></span>

            <form id=\"order-banner-form\" class=\"order-banner popup-form\" action=\"#\">
                <fieldset>
                    <div class=\"group clearfix\">
                        <div class=\"start-date-wrapper field-wrap float-left\">
                            <input type=\"text\" class=\"date-from form-text ie-radius\" placeholder=\"Дата\"/>
                            <span class=\"icon-calendar\"></span>
                        </div>
                        <div class=\"end-date-wrapper field-wrap float-left\">
                            <p class=\"date-to\">по 17 июн 2013</p>
                        </div>
                    </div>
                    <div class=\"group clearfix\">
                        <div class=\"user-name-wrapper field-wrap float-left\">
                            <input class=\"user-name form-text ie-radius\" type=\"text\" placeholder=\"Имя\"/>
                        </div>
                        <div class=\"phone-wrapper field-wrap float-left\">
                            <input id=\"phone\" class=\"phone form-text ie-radius\" type=\"text\" placeholder=\"Телефон\"/>
                        </div>
                    </div>
                    <div class=\"email-wrapper field-wrap\">
                        <input class=\"form-email ie-radius\" type=\"email\" placeholder=\"Электронная почта\"/>
                    </div>
                    <div class=\"field-wrap\">
                        <input id=\"create-banner\" type=\"checkbox\" class=\"jq-checkbox js-styled-checkbox bg-white\"/>
                        <label for=\"create-banner\">Нужно изготовление баннера (+4000 <span class=\"icon-rouble\"></span>)</label>
                    </div>

                    <div class=\"total-sum-wrapper\">
                        <span class=\"gray60-color\">к оплате</span>
                        <span class=\"sum\">19 000 <span class=\"icon-rouble\"></span></span>
                    </div>
                    <div class=\"submit-wrapper clearfix\">
                        ";
        // line 493
        echo "                        <input class=\"pay-btn button green-bg ie-radius\" type=\"submit\" value=\"Оплатить сейчас\"/>
                    </div>

                </fieldset>
            </form>
        </div>
    </div>

    <div id=\"add-review\" class=\"add-review-form popup-block opacity-border large\">
        <div class=\"popup-content\">
            <div class=\"title-popup\">Добавить отзыв</div>
            <span class=\"close-popup icon-popup-close js-popup-closer clickable\"></span>

            <form id=\"add-review-form\" class=\"popup-form\" action=\"#\">
                <fieldset>
                    <div class=\"user-name-wrapper field-wrap\">
                        <input class=\"form-text ie-radius\" type=\"text\" placeholder=\"Имя\"/>
                    </div>
                    <div class=\"user-email-wrapper field-wrap\">
                        <input class=\"form-email error ie-radius\" type=\"email\" placeholder=\"Электронная почта\"/>
                        <span data-text=\"Поле не должно быть пустым\" class=\"icon-error-color js-helper-opener\"></span>
                    </div>
                    <div class=\"textarea-wrapper field-wrap\">
                        <textarea class=\"form-textarea ie-radius\" name=\"review\" id=\"review\"
                                  placeholder=\"Ваш отзыв\"></textarea>
                    </div>
                    <div class=\"toggle-block\">
                        <ul class=\"links clearfix js-toggle-block\">
                            <li class=\"positive item float-left\">
                                <label class=\"positive-button button white-bg clearfix ie-radius\">
                                    <input type=\"radio\" name=\"review_type\" value=\"positive\"
                                           class=\"not-styling js-toggle-button\"/>
                                    <span class=\"text\">Хорошо</span>
                                    <span class=\"icon-positive float-right\"></span>
                                </label>
                            </li>
                            <li class=\"negative item float-left\">
                                <label class=\"negative-button button white-bg clearfix ie-radius\">
                                    <input type=\"radio\" name=\"review_type\" checked=\"checked\" value=\"negative\"
                                           class=\"not-styling js-toggle-button\"/>
                                    <span class=\"text\">Плохо</span>
                                    <span class=\"icon-negative float-right\"></span>
                                </label>
                            </li>
                        </ul>
                    </div>

                    <div class=\"submit-wrapper\">
                        <input class=\"popup-form_send-button send-button button blue-bg ie-radius\" type=\"submit\"
                               value=\"Сообщить\"/>
                    </div>
                </fieldset>
            </form>
        </div>
    </div>

    <div id=\"more-options\" class=\"more-options-block popup-block opacity-border large\">
        <div class=\"popup-content\">
            <div class=\"title-popup\">Больше возможностей</div>
            <span class=\"close-popup icon-popup-close js-popup-closer clickable\"></span>

            <p class=\"text\">Подключите расширенный или полный пакет, чтобы воспользоваться этой услугой.</p>

            <a class=\"access-btn button green-bg ie-radius\" href=\"#\">Получить доступ</a>
        </div>
    </div>

    <div id=\"load-payment\" class=\"load-payment-block popup-block opacity-border large\">
        <div class=\"popup-content\">
            <span class=\"close-popup icon-popup-close js-popup-closer clickable\"></span>

            <p class=\"text\">Вы можете загрузить отсканированное платежное поручение. Статус счета будет изменен после
                проверки модератором.</p>

            <form id=\"load-payment-form\" class=\"popup-form\" action=\"#\">
                <fieldset>
                    <div class=\"submit-wrapper\">
                        <input type=\"submit\" class=\"load-payment-btn button blue-bg ie-radius\" value=\"Загрузить\"/>
                    </div>
                </fieldset>
            </form>
        </div>
    </div>

    <div id=\"favorite-product\" class=\"favorite-block popup-block opacity-border large\">
        <div class=\"popup-content\">
            <div class=\"title-popup\">Избранные товары компании <br/> <a href=\"#\">Стальторг</a></div>
            <span class=\"close-popup icon-popup-close js-popup-closer clickable\"></span>

            <div class=\"favorite-product-block is-bordered\">
                <div class=\"js-scrollable\">
                    <ul class=\"topic-list\">
                        <li class=\"item clearfix\">
                            <div class=\"topic-info float-left\">
                                <a class=\"title-link\" href=\"#\">Арматура стеклопластиковая АСП-6</a>

                                <p class=\"text\">Размер 6, <strong class=\"price red-color other-currency js-helper-opener\" data-text=\"примерно <span class='red-color'>100 <span class='icon-rouble'></span></span>\">10 <span class=\"icon-rouble\"></span></strong> за погонный
                                    метр</p>
                            </div>
                            <div class=\"img is-bordered float-right\">
                                <div class=\"img-holder\">
                                    <a href=\"#\" class=\"img-link pattern-small\">
                                        <img src=\"./markup/pic/small-img.jpg\" alt=\"image description\"/>
                                    </a>
                                </div>
                            </div>
                        </li>
                        <li class=\"item clearfix\">
                            <div class=\"topic-info float-left\">
                                <a class=\"title-link\" href=\"#\">Арматура стеклопластиковая АСП-6</a>

                                <p class=\"text\">Размер 6, <strong class=\"price red-color other-currency js-helper-opener\" data-text=\"примерно <span class='red-color'>100 <span class='icon-rouble'></span></span>\">10 <span class=\"icon-rouble\"></span></strong> за погонный
                                    метр</p>
                            </div>
                            <div class=\"img is-bordered float-right\">
                                <div class=\"img-holder\">
                                    <a href=\"#\" class=\"img-link pattern-small\"></a>
                                </div>
                            </div>
                        </li>
                        <li class=\"item clearfix\">
                            <div class=\"topic-info float-left\">
                                <a class=\"title-link\" href=\"#\">Арматура стеклопластиковая АСП-6</a>

                                <p class=\"text\">Размер 6, <strong class=\"price red-color\">10 <span class=\"icon-rouble\"></span></strong> за погонный
                                    метр</p>
                            </div>
                            <div class=\"img is-bordered float-right\">
                                <div class=\"img-holder\">
                                    <a href=\"#\" class=\"img-link pattern-small\"></a>
                                </div>
                            </div>
                        </li>
                        <li class=\"item clearfix\">
                            <div class=\"topic-info float-left\">
                                <a class=\"title-link\" href=\"#\">Арматура стеклопластиковая АСП-6</a>

                                <p class=\"text\">Размер 6, <strong class=\"price red-color\">10 <span class=\"icon-rouble\"></span></strong> за погонный
                                    метр</p>
                            </div>
                            <div class=\"img is-bordered float-right\">
                                <div class=\"img-holder\">
                                    <a href=\"#\" class=\"img-link pattern-small\"></a>
                                </div>
                            </div>
                        </li>
                        <li class=\"item clearfix\">
                            <div class=\"topic-info float-left\">
                                <a class=\"title-link\" href=\"#\">Арматура стеклопластиковая АСП-6</a>

                                <p class=\"text\">Размер 6, <strong class=\"price red-color\">10 <span class=\"icon-rouble\"></span></strong> за погонный
                                    метр</p>
                            </div>
                            <div class=\"img is-bordered float-right\">
                                <div class=\"img-holder\">
                                    <a href=\"#\" class=\"img-link pattern-small\"></a>
                                </div>
                            </div>
                        </li>
                        <li class=\"item clearfix\">
                            <div class=\"topic-info float-left\">
                                <a class=\"title-link\" href=\"#\">Арматура стеклопластиковая АСП-6</a>

                                <p class=\"text\">Размер 6, <strong class=\"price red-color\">10 <span class=\"icon-rouble\"></span></strong> за погонный
                                    метр</p>
                            </div>
                            <div class=\"img is-bordered float-right\">
                                <div class=\"img-holder\">
                                    <a href=\"#\" class=\"img-link pattern-small\"></a>
                                </div>
                            </div>
                        </li>
                        <li class=\"item clearfix\">
                            <div class=\"topic-info float-left\">
                                <a class=\"title-link\" href=\"#\">Арматура стеклопластиковая АСП-6</a>

                                <p class=\"text\">Размер 6, <strong class=\"price red-color\">10 <span class=\"icon-rouble\"></span></strong> за погонный
                                    метр</p>
                            </div>
                            <div class=\"img is-bordered float-right\">
                                <div class=\"img-holder\">
                                    <a href=\"#\" class=\"img-link pattern-small\"></a>
                                </div>
                            </div>
                        </li>
                        <li class=\"item clearfix\">
                            <div class=\"topic-info float-left\">
                                <a class=\"title-link\" href=\"#\">Арматура стеклопластиковая АСП-6</a>

                                <p class=\"text\">Размер 6, <strong class=\"price red-color\">10 <span class=\"icon-rouble\"></span></strong> за погонный
                                    метр</p>
                            </div>
                            <div class=\"img is-bordered float-right\">
                                <div class=\"img-holder\">
                                    <a href=\"#\" class=\"img-link pattern-small\"></a>
                                </div>
                            </div>
                        </li>
                        <li class=\"item clearfix\">
                            <div class=\"topic-info float-left\">
                                <a class=\"title-link\" href=\"#\">Арматура стеклопластиковая АСП-6</a>

                                <p class=\"text\">Размер 6, <strong class=\"price red-color\">10 <span class=\"icon-rouble\"></span></strong> за погонный
                                    метр</p>
                            </div>
                            <div class=\"img is-bordered float-right\">
                                <div class=\"img-holder\">
                                    <a href=\"#\" class=\"img-link pattern-small\"></a>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div id=\"demand-request\" class=\"demand-request-block large-popup popup-block opacity-border large\">
        <div class=\"popup-content\">
            <div class=\"title-popup\">Заявка для компании Стальторг</div>
            <span class=\"close-popup icon-popup-close js-popup-closer clickable\"></span>

            <form id=\"request-form\" class=\"request-form popup-form\" action=\"#\">
                <fieldset>
                    <div class=\"request-product-group\">
                        <div class=\"group clearfix\">
                            <div class=\"product-name-wrapper field-wrap float-left\">
                                <input type=\"text\" class=\"product-name form-text ie-radius\" placeholder=\"Продукт\"/>
                            </div>
                            <div class=\"product-volume-wrapper field-wrap float-left\">
                                <input type=\"text\" class=\"volume form-text ie-radius\" placeholder=\"Объем закупки\"/>
                            </div>
                            <div class=\"product-hundredweight-wrapper field-wrap float-left\">
                                <select class=\"hundredweight form-select\">
                                    <option class=\"disabled\" value=\"-1\" disabled=\"disabled\">Тонна</option>
                                    <option value=\"0\">Тонна</option>
                                    <option value=\"1\">Центнер</option>
                                    <option value=\"2\">Килограмм</option>
                                    <option value=\"3\">Грамм</option>
                                </select>
                            </div>
                        </div>
                        <div class=\"group btns-wrapper clearfix\">
                            <div class=\"file-wrapper float-left\">
                                <label class=\"file-upload\">
                                    <span class=\"load-file-btn ico-upload\">Загрузить из файла...</span>
                                    <input type=\"file\"/>
                                </label>
                            </div>
                            <div class=\"add-string-wrapper float-right\">
                                <a class=\"add-string-link icon-add-btn\" href=\"#\">ещё строка</a>
                            </div>
                        </div>
                        <div class=\"textarea-wrapper field-wrap\">
                            <textarea class=\"form-textarea ie-radius\" name=\"request-comment\" id=\"request-comment\"
                                      placeholder=\"Комментарии\"></textarea>
                        </div>
                    </div>
                    <div class=\"group clearfix\">
                        <div class=\"type-wrapper field-wrap float-left\">
                            <input class=\"request-type form-text ie-radius\" type=\"text\"/>
                        </div>
                        <div class=\"city-wrapper field-wrap float-left\">
                            <input class=\"report-form_city form-text ie-radius\" type=\"text\" placeholder=\"Город\"/>
                        </div>
                    </div>
                    <div class=\"group clearfix\">
                        <div class=\"user-name-wrapper field-wrap float-left\">
                            <input class=\"request-form_user-name form-text error ie-radius\" type=\"text\"
                                   placeholder=\"Имя\"/>
                            <span class=\"icon-error-color js-helper-opener\"
                                  data-text=\"Поле не должно быть пустым\"></span>
                        </div>
                        <div class=\"phone-wrapper field-wrap float-left\">
                            <input id=\"phone\" class=\"phone request-form_phone form-text ie-radius\" type=\"text\"
                                   placeholder=\"Телефон\"/>
                        </div>
                    </div>
                    <div class=\"answer_email-wrapper field-wrap\">
                        <input class=\"request-form_email form-email ie-radius\" type=\"email\"
                               placeholder=\"Электронная почта\"/>
                    </div>
                    <div class=\"favorite-wrapper field-wrap\">
                        <input id=\"favorite\" name=\"favorite-check\" type=\"checkbox\"
                               class=\"favorite js-styled-checkbox bg-white\"/>
                        <label for=\"favorite\">Добавить компанию в <span
                                    class=\"request-form_favorite-ico icon-favorite-active\"></span>Избранное</label>
                    </div>
                    <div class=\"inform-wrapper\">
                        <input id=\"request_inform\" type=\"checkbox\" class=\"inform js-styled-checkbox bg-white\"/>
                        <label for=\"request_inform\">Сообщить другим поставщикам</label>
                        <span class=\"icon-help js-helper-opener\"
                              data-text=\"Мы сообщим о вашей потребности поставщикам и вы получите гораздо больше предложений, чтобы выбрать оптимальное.\"></span>
                    </div>
                    <div class=\"submit-wrapper\">
                        <input class=\"popup-form_send-button send-button button blue-bg ie-radius\" type=\"submit\"
                               value=\"Отправить\"/>
                    </div>
                </fieldset>
            </form>
        </div>
    </div>

    <div id=\"register\" class=\"register-form popup-block opacity-border large\">
        <div class=\"popup-content\">
            <div class=\"title-popup\">Вы регистрировались ранее?</div>
            <span class=\"close-popup icon-popup-close js-popup-closer clickable\"></span>

            <p class=\"text\">
                Мы нашли пользователя, зарегистрированного с адресом <strong>paveliche@gmail.com</strong>. Теперь мы
                можем выслать вам напоминание пароля на эту электронную почту.
            </p>
            <a class=\"popup-form_send-button send-button button blue-bg ie-radius\" href=\"#\">Напомнить пароль</a>
        </div>
    </div>

    <div id=\"register-company\" class=\"register-company-form large-popup popup-block opacity-border large\">
        <div class=\"popup-content\">
            <div class=\"title-popup\">Ваша компания уже зарегистрирована?</div>
            <p class=\"text\">Выберите компанию, к которой присоединиться</p>
            <span class=\"close-popup icon-popup-close js-popup-closer clickable\"></span>

            <div class=\"companies-wrapper\">
                <div class=\"scroll-block js-scrollable\">
                    <table class=\"reg-companies\">
                        <tr class=\"row\">
                            <td class=\"company-name col\">
                                <span class=\"link is-gradiented\">Стальтsdfg sdhs dgh ghs dghsdfh wdgh xcgh dgh dghорг </span><span class=\"gray60-color is-gradiented\">Моск sdgh sdfgh sdfhg sdgh sdgh sgdh ва</span>
                            </td>
                            <td class=\"phone-text col\">
                                <p class=\"is-gradiented-bottom\">(495) 203-40-32 (495) 203-40-32 (495) 203-40-32 (495) 203-40-32 (495) 203-40-32 (495) 203-40-32 (495) 203-40-32 (495) 203-40-32</p>
                            </td>
                            <td class=\"choose col\">
                                <a class=\"choose-btn button ie-radius\" href=\"#\">Выбрать</a>
                            </td>
                        </tr>
                        <tr class=\"row\">
                            <td class=\"company-name col\">
                                <span class=\"link is-gradiented\">Стальторг </span>
                                <span class=\"gray60-color is-gradiented\">Москва</span>
                            </td>
                            <td class=\"phone-text col\">
                                <p class=\"is-gradiented-bottom\">(495) 203-40-32</p>
                            </td>
                            <td class=\"choose col\">
                                <a class=\"choose-btn button ie-radius\" href=\"#\">Выбрать</a>
                            </td>
                        </tr>
                        <tr class=\"row\">
                            <td class=\"company-name col\">
                                <span class=\"link is-gradiented\">Стальторг </span>
                                <span class=\"gray60-color is-gradiented\">Москва</span>
                            </td>
                            <td class=\"phone-text col\">
                                <p class=\"is-gradiented-bottom\">(495) 203-40-32</p>
                            </td>
                            <td class=\"choose col\">
                                <a class=\"choose-btn button ie-radius\" href=\"#\">Выбрать</a>
                            </td>
                        </tr>
                        <tr class=\"row\">
                            <td class=\"company-name col\">
                                <span class=\"link is-gradiented\">Стальторг </span>
                                <span class=\"gray60-color is-gradiented\">Москва</span>
                            </td>
                            <td class=\"phone-text col\">
                                <p class=\"is-gradiented-bottom\">(495) 203-40-32</p>
                            </td>
                            <td class=\"choose col\">
                                <a class=\"choose-btn button ie-radius\" href=\"#\">Выбрать</a>
                            </td>
                        </tr>
                        <tr class=\"row\">
                            <td class=\"company-name col\">
                                <span class=\"link is-gradiented\">Стальторг</span>
                                <span class=\"gray60-color is-gradiented\">Москва</span>
                            </td>
                            <td class=\"phone-text col\">
                                <p class=\"is-gradiented-bottom\">(495) 203-40-32</p>
                            </td>
                            <td class=\"choose col\">
                                <a class=\"choose-btn button ie-radius\" href=\"#\">Выбрать</a>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <a class=\"popup-form_send-button send-button button green-bg ie-radius\" href=\"#\">Нет, зарегистрировать
                новую</a>
        </div>
    </div>

    <div id=\"loading-cities\" class=\"drop-wrapper popover-block cities opacity-border\" style=\"top: 30%; left:45%;\">
        <div class=\"popover-content-wrapper\">
            <div class=\"loading-mask\">
                <div class=\"spinner\"></div>
            </div>
        </div>
    </div>

    <div id=\"cities\" class=\"drop-wrapper popover-block cities opacity-border\">
        <div class=\"popover-content-wrapper\">
            <div class=\"cities_top\">
                <ul class=\"dropdown menu-drop\">
                    <li class=\"drop-item\">
                        <span class=\"drop-link current is-gradiented\">Москва и Область</span>
                    </li>
                    <li class=\"drop-item first\">
                        <a class=\"drop-link\" href=\"#\">Все регионы</a>
                    </li>
                    <li class=\"drop-item\">
                        <a class=\"drop-link is-gradiented\" href=\"#\">Санкт-Петербург</a>
                    </li>
                    <li class=\"drop-item\">
                        <a class=\"drop-link is-gradiented\" href=\"#\">Екатеринбург</a>
                    </li>
                    <li class=\"drop-item\">
                        <a class=\"drop-link is-gradiented\" href=\"#\">Казань</a>
                    </li>
                </ul>
            </div>
            <div class=\"cities_bottom js-searchable-block\">
                <fieldset class=\"cities_search\">
                    <div class=\"search_field-wrapper\">
                        <input class=\"search_form-text form-text js-search-query ie-radius\" type=\"text\"
                               placeholder=\"Город или регион\"/>
                        <button type=\"submit\" class=\"icon-search-small search-button\"></button>
                    </div>
                </fieldset>
                <div class=\"js-scrollable\">
                    <ul class=\"cities_find-list\">
                        <li class=\"drop-item first js-searchable js-hidable-parent\"
                            data-search-show-children=\".drop-item\"
                            data-search-source=\"#region-1\">
                            <span class=\"drop-link\" id=\"region-1\">Краснодарский край</span>
                            <ul class=\"level-inside\">
                                <li class=\"drop-item js-searchable\" data-search-hide-parent=\".js-hidable-parent\">
                                    <a class=\"drop-link is-gradiented\" href=\"#\">Краснодар</a>
                                </li>
                                <li class=\"drop-item js-searchable\" data-search-hide-parent=\".js-hidable-parent\">
                                    <a class=\"drop-link is-gradiented\" href=\"#\">Сочи Сочи Сочи Сочи Сочи Сочи Сочи Сочи
                                        Сочи Сочи</a>
                                </li>
                                <li class=\"drop-item js-searchable\" data-search-hide-parent=\".js-hidable-parent\">
                                    <a class=\"drop-link is-gradiented\" href=\"#\">Новороссийск</a>
                                </li>
                                <li class=\"drop-item js-searchable\" data-search-hide-parent=\".js-hidable-parent\">
                                    <a class=\"drop-link is-gradiented\" href=\"#\">Анапа</a>
                                </li>
                            </ul>
                        </li>
                        <li class=\"drop-item first js-searchable js-hidable-parent\"
                            data-search-show-children=\".drop-item\"
                            data-search-source=\"#region-2\">
                            <span class=\"drop-link\" id=\"region-2\">Московская область</span>
                            <ul class=\"level-inside\">
                                <li class=\"drop-item js-searchable\" data-search-hide-parent=\".js-hidable-parent\">
                                    <a class=\"drop-link is-gradiented\" href=\"#\">Москва</a>
                                </li>
                                <li class=\"drop-item js-searchable\" data-search-hide-parent=\".js-hidable-parent\">
                                    <a class=\"drop-link is-gradiented\" href=\"#\">Мытищи</a>
                                </li>
                                <li class=\"drop-item js-searchable\" data-search-hide-parent=\".js-hidable-parent\">
                                    <a class=\"drop-link is-gradiented\" href=\"#\">Люберцы</a>
                                </li>
                                <li class=\"drop-item js-searchable\" data-search-hide-parent=\".js-hidable-parent\">
                                    <a class=\"drop-link is-gradiented\" href=\"#\">Одинцово</a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

    </div>

    <div id=\"photo\" class=\"photo popup-block opacity-border large\">
        <div class=\"img-content\">
            <img src=\"./markup/pic/big-prod.jpg\" alt=\"image description\"/>
        </div>
    </div>

    <div id=\"delete-product\" class=\"delete-product-block popup-block opacity-border large\">
        <div class=\"popup-content\">
            <div class=\"title-popup centered\">Удаление товара</div>
            <p class=\"info centered\">Товар будет безвозвратно удален</p>
            <span class=\"close-popup icon-popup-close js-popup-closer clickable\"></span>
            <ul class=\"buttons clearfix\">
                <li class=\"item float-left\">
                    <a class=\"cancel-btn button gray60-bg ie-radius\" href=\"#\">Отмена</a>
                </li>
                <li class=\"item float-left\">
                    <a class=\"delete-btn button red-bg ie-radius\" href=\"#\">Удалить</a>
                </li>
            </ul>
        </div>
    </div>

    <div id=\"overwrite-product\" class=\"overwrite-product-block popup-block opacity-border large\">
        <div class=\"popup-content\">
            <span class=\"close-popup icon-popup-close js-popup-closer clickable\"></span>

            <div class=\"title-popup centered\">Товар уже существует</div>
            <p class=\"info centered\">Обновить существующий товар или добавить новый?</p>
            <ul class=\"buttons clearfix\">
                <li class=\"item float-left\">
                    <a class=\"delete-btn button red-bg ie-radius\" href=\"#\">Обновить</a>
                </li>
                <li class=\"item float-left\">
                    <a class=\"cancel-btn button gray60-bg ie-radius\" href=\"#\">Отмена</a>
                </li>
            </ul>
        </div>
    </div>

    <div id=\"load-photo\" class=\"load-photo-block popup-block opacity-border large\">
        <div class=\"popup-content\">
            <div class=\"title-popup\">Загрузить фото</div>
            <span class=\"close-popup icon-popup-close js-popup-closer clickable\"></span>

            <form class=\"load-photo-form popup-form\" action=\"#\">
                <fieldset>
                    <div class=\"clearfix\">
                        <div class=\"photo-wrapper float-left\">
                            <div class=\"img is-bordered\">
                                <a class=\"pattern-big\" href=\"#\"></a>
                            </div>
                        </div>
                        <div class=\"data-wrapper float-left\">
                            <div class=\"field-wrap\">
                                <label class=\"file-upload\">
                                    <span class=\"file-btn button white-bg is-bordered error ie-radius\">Выбрать из файла...</span>
                                    <input type=\"file\"/>
                                    <span class=\"icon-error-color js-helper-opener\"
                                          data-text=\"Поле не должно быть пустым\"></span>
                                </label>
                            </div>
                            <div class=\"field-wrap\">
                                <textarea name=\"comment\" class=\"form-textarea ie-radius\"
                                          placeholder=\"Комментарий\"></textarea>
                            </div>
                            <div class=\"field-wrap\">
                                <input type=\"checkbox\" id=\"opt-photo\" class=\"js-styled-checkbox\"/>
                                <label for=\"opt-photo\">Оптимизировать изображение</label>
                            </div>
                        </div>
                    </div>
                    <div class=\"submit-wrapper\">
                        <input class=\"load-btn button blue-bg ie-radius\" type=\"submit\" value=\"Загрузить\"/>
                    </div>
                </fieldset>
            </form>
        </div>
    </div>

    <div id=\"loading-popup\" class=\"popup-block static-height opacity-border large\">
        <div class=\"popup-content\">
            <div class=\"loading-mask \">
                <div class=\"spinner\"></div>
            </div>
        </div>
    </div>

    <div id=\"add-product\" class=\"add-product-block popup-block opacity-border large\">
        <div class=\"popup-content\">
            <div class=\"title-popup\">Добавить товар</div>
            <span class=\"close-popup icon-popup-close js-popup-closer clickable\"></span>

            <form class=\"add-product-form popup-form\" action=\"#\">
                <fieldset>
                    <div class=\"clearfix\">
                        <div class=\"data-wrapper float-left\">
                            <div class=\"field-wrap\">
                                <input class=\"form-text ie-radius\" type=\"text\" placeholder=\"Название\"/>
                            </div>
                            <div class=\"field-wrap\">
                                <input class=\"form-text ie-radius\" type=\"text\" placeholder=\"Размер\"/>
                            </div>
                            <div class=\"group clearfix\">
                                <div class=\"product-price-wrapper field-wrap float-left\">
                                    <input class=\"product-price form-text ie-radius\" type=\"text\" placeholder=\"Цена\"/>
                                    <span class=\"suffix\"><span class=\"icon-rouble\"></span></span>
                                </div>
                                <div class=\"product-hundredweight-wrapper field-wrap float-left\">
                                    <select class=\"hundredweight form-select\">
                                        <option class=\"disabled\" value=\"-1\" disabled=\"disabled\">Тонна</option>
                                        <option value=\"0\">Тонна</option>
                                        <option value=\"1\">Центнер</option>
                                        <option value=\"2\">Килограмм</option>
                                        <option value=\"3\">Грамм</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class=\"photo-wrapper float-left\">
                            <label class=\"file-upload\">
                                <div class=\"img is-bordered\">
                                    <span class=\"no-photo file-added\"></span>
                                </div>
                                <input type=\"file\"/>
                            </label>
                        </div>
                    </div>
                    <div class=\"field-wrap\">
                        <textarea class=\"form-textarea ie-radius\" placeholder=\"Описание\"></textarea>
                    </div>
                    <div class=\"submit-wrapper\">
                        <input class=\"add-btn button blue-bg ie-radius\" type=\"submit\" value=\"Добавить\"/>
                    </div>
                </fieldset>
            </form>
        </div>
    </div>

    <div id=\"add-products\" class=\"add-products-block popup-block opacity-border large\">
        <div class=\"popup-content\">
            <div class=\"title-popup\">Загрузить товары</div>
            <span class=\"close-popup icon-popup-close js-popup-closer clickable\"></span>

            <form class=\"add-products-form popup-form\" action=\"#\">
                <fieldset>
                    <div class=\"item\">
                        <p>Воспользуйтесь нашим простым шаблоном для формирования списка товаров:</p>
                        <a class=\"file-btn button white-bg is-bordered ie-radius\" href=\"#\">Скачать шаблон</a>
                    </div>
                    <div class=\"item clearfix\">
                        <p>Загрузите ваш список товаров, созданный на основе шаблона</p>
                        <label class=\"file-upload float-left disabled\">
                            <span class=\"overflow items\"></span>
                            <span class=\"file-btn button white-bg is-bordered ie-radius\">Выбрать файл...</span>
                            <input type=\"file\" disabled=\"disabled\"/>
                        </label>
                    </div>
                    <ul class=\"radio-list\">
                        <li class=\"item clearfix\">
                            <input id=\"add\" type=\"radio\" name=\"radio-item\" class=\"radio bg-grey80 float-left\"/>
                            <label for=\"add\" class=\"float-left\">Добавить к существующему, обновить одинаковые позиции
                                (рекомендуется)</label>
                        </li>
                        <li class=\"item clearfix\">
                            <input id=\"del\" type=\"radio\" name=\"radio-item\" class=\"radio bg-grey80 float-left\"/>
                            <label for=\"del\" class=\"float-left\">Удалить существующий и закачать новый</label>
                        </li>
                        <li class=\"item field-wrap\">
                            <p>Или укажите ссылку на прайс-лист в формате YML</p>
                            <input type=\"text\" disabled=\"disabled\" class=\"form-text\" placeholder=\"Ссылка на файл\"/>
                        </li>
                    </ul>
                    <div class=\"submit-wrapper\">
                        <input class=\"add-btn button blue-bg ie-radius\" type=\"submit\" value=\"Загрузить\"/>
                    </div>
                </fieldset>
            </form>
        </div>
    </div>

    <div id=\"demand-answer-authenticated\" class=\"demand-answer-block popup-block opacity-border large\">
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

    <div id=\"add-support-request\" class=\"support-block popup-block opacity-border large\">
        <div class=\"popup-content\">
            <div class=\"title-popup\">Добавление завки</div>
            <span class=\"close-popup icon-popup-close js-popup-closer clickable\"></span>

            <form class=\"support-form form\" action=\"#\">
                <fieldset>
                    <div class=\"field-wrap\">
                        <input type=\"text\" class=\"form-text ie-radius\" placeholder=\"Проблема\"/>
                    </div>
                    <div class=\"field-wrap\">
                        <textarea class=\"form-textarea ie-radius\" placeholder=\"Подробно опишите проблему\"></textarea>
                    </div>
                    <div class=\"submit-wrapper\">
                        <input class=\"add-btn button blue-bg ie-radius\" type=\"submit\" value=\"Отправить\"/>
                    </div>
                </fieldset>
            </form>
        </div>
    </div>

    <div id=\"demand-answers\" class=\"demand-answers-block popup-block opacity-border large\">
        <div class=\"popup-content\">
            <div class=\"title-popup\">Ответы на потребность <strong class=\"demand-count red-color\">№45781</strong></div>
            <span class=\"close-popup icon-popup-close js-popup-closer clickable\"></span>

            <div class=\"answers js-scrollable\">
                <div class=\"item clearfix\">
                    <div class=\"left float-left\">
                        <div class=\"user-name\">
                            <a class=\"link\" href=\"#\">Семен Меркушев</a>
                        </div>
                        <div class=\"answer-add-date\">
                            <span class=\"date\">22 мар 2013 г.</span>
                        </div>
                        <div class=\"answer-text\">
                            <p>Обновились цены, теперь арматура по 19 000 <span class=\"icon-rouble\"></span> Условия те же.</p>
                        </div>
                    </div>
                    <div class=\"user-photo float-right\">
                        <a class=\"pattern-small\" href=\"#\">
                        </a>
                    </div>
                </div>
                <div class=\"item clearfix\">
                    <div class=\"left float-left\">
                        <div class=\"user-name\">
                            <a class=\"link\" href=\"#\">Семен Меркушев</a>
                        </div>
                        <div class=\"answer-add-date\">
                            <span class=\"date\">22 мар 2013 г.</span>
                        </div>
                        <div class=\"answer-text\">
                            <p>Обновились цены, теперь арматура по 19 000 <span class=\"icon-rouble\"></span> Условия те же.</p>
                        </div>
                    </div>
                    <div class=\"user-photo float-right\">
                        <a class=\"pattern-small\" href=\"#\">
                            <img src=\"./markup/pic/user-img-small.jpg\" alt=\"image description\"/>
                        </a>
                    </div>
                </div>
                <div class=\"item clearfix\">
                    <div class=\"left float-left\">
                        <div class=\"user-name\">
                            <a class=\"link\" href=\"#\">Семен Меркушев</a>
                        </div>
                        <div class=\"answer-add-date\">
                            <span class=\"date\">22 мар 2013 г.</span>
                        </div>
                        <div class=\"answer-text\">
                            <p>Обновились цены, теперь арматура по 19 000 <span class=\"icon-rouble\"></span> Условия те же.</p>
                        </div>
                    </div>
                    <div class=\"user-photo float-right\">
                        <a class=\"pattern-small\" href=\"#\">
                            <img src=\"./markup/pic/user-img-small.jpg\" alt=\"image description\"/>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id=\"choose-section\" class=\"choose-section drop-wrapper popover-block opacity-border\">
        <div class=\"heading\">
            <p class=\"title\"><strong>Выбор раздела</strong></p>
        </div>
        <div class=\"bottom\">
            <div class=\"js-searchable-block\">
                <fieldset>
                    <div class=\"search_field-wrapper\">
                        <input class=\"search_form-text form-text js-search-query ie-radius\" type=\"text\"
                               placeholder=\"Город или регион\"/>
                        <button type=\"submit\" class=\"icon-search-small search-button\"></button>
                    </div>
                </fieldset>
                <div class=\"sections js-scrollable\">
                    <ul class=\"find-list\">
                        <li class=\"drop-item js-searchable\">
                            <span class=\"drop-link current\">Сортовой прокат</span>
                            <ul class=\"level-inside\">
                                <li class=\"drop-item js-searchable\">
                                    <a class=\"drop-link\" href=\"#\">Арматура</a>
                                </li>
                                <li class=\"drop-item js-searchable\">
                                    <a class=\"drop-link\" href=\"#\">Балка</a>
                                </li>
                                <li class=\"drop-item js-searchable\">
                                    <a class=\"drop-link\" href=\"#\">Канатка</a>
                                    <ul class=\"level-inside\">
                                        <li class=\"drop-item js-searchable\">
                                            <a class=\"drop-link\" href=\"#\">Арматура</a>
                                        </li>
                                        <li class=\"drop-item js-searchable\">
                                            <a class=\"drop-link\" href=\"#\">Балка</a>
                                        </li>
                                    </ul>
                                </li>
                                <li class=\"drop-item js-searchable\">
                                    <a class=\"drop-link\" href=\"#\">Квадрат</a>
                                </li>
                                <li class=\"drop-item js-searchable\">
                                    <a class=\"drop-link\" href=\"#\">Арматура</a>
                                </li>
                                <li class=\"drop-item js-searchable\">
                                    <a class=\"drop-link\" href=\"#\">Балка</a>
                                </li>
                                <li class=\"drop-item js-searchable\">
                                    <a class=\"drop-link\" href=\"#\">Канатка</a>
                                </li>
                                <li class=\"drop-item js-searchable\">
                                    <a class=\"drop-link\" href=\"#\">Квадрат</a>
                                </li>
                            </ul>
                        </li>

                    </ul>
                </div>
            </div>
        </div>
    </div>


    <div id=\"choose-section-2\" class=\"choose-section non_recursive drop-wrapper popover-block opacity-border\">
        <div class=\"heading\">
            <p class=\"title\"><strong>Выбор раздела</strong></p>
        </div>
        <div class=\"bottom\">
            <div class=\"js-searchable-block\">
                <fieldset>
                    <div class=\"search_field-wrapper\">
                        <input class=\"search_form-text form-text js-search-query ie-radius\" type=\"text\"
                               placeholder=\"Город или регион\"/>
                        <button type=\"submit\" class=\"icon-search-small search-button\"></button>
                    </div>
                </fieldset>
                <div class=\"sections js-scrollable\">
                    <ul class=\"find-list\">
                        <li class=\"drop-item js-searchable\">
                            <span class=\"drop-link current\">Сортовой прокат</span>
                            <ul class=\"level-inside category-list-wrapper\">
                                <li class=\"drop-item js-searchable\">
                                    <div class=\"level1\">
                                        <a class=\"drop-link\" href=\"#\">Арматура</a>
                                    </div>
                                </li>

                                <li class=\"drop-item js-searchable\">
                                    <div class=\"level1\">
                                        <a class=\"drop-link\" href=\"#\">Балка</a>
                                    </div>
                                </li>
                                <li class=\"drop-item js-searchable\">
                                    <div class=\"level1\">
                                        <a class=\"drop-link\" href=\"#\">Канатка</a>
                                    </div>
                                </li>
                                <li class=\"drop-item js-searchable\">
                                    <div class=\"level2\">
                                        <a class=\"drop-link\" href=\"#\">Арматура</a>
                                    </div>
                                </li>
                                <li class=\"drop-item js-searchable\">
                                    <div class=\"level3\">
                                        <a class=\"drop-link\" href=\"#\">Балка</a>
                                    </div>
                                </li>

                                <li class=\"drop-item js-searchable\">
                                    <div class=\"level1\">
                                        <a class=\"drop-link\" href=\"#\">Арматура</a>
                                    </div>
                                </li>

                                <li class=\"drop-item js-searchable\">
                                    <div class=\"level1\">
                                        <a class=\"drop-link\" href=\"#\">Балка</a>
                                    </div>
                                </li>
                                <li class=\"drop-item js-searchable\">
                                    <div class=\"level1\">
                                        <a class=\"drop-link\" href=\"#\">Канатка</a>
                                    </div>
                                </li>
                            </ul>
                        </li>

                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div id=\"demand-subscription\" class=\"demand-subscription-block popup-block opacity-border large\">
        <div class=\"popup-content\">
            <div class=\"title-popup\">Подписка на потребности</div>
            <span class=\"close-popup icon-popup-close js-popup-closer clickable\"></span>

            <form class=\"demand-subscription-form\" action=\"#\">
                <fieldset>
                    <div class=\"field-wrap\">
                        <div class=\"loading-mask embed-mask\">
                            <div class=\"spinner\"></div>
                        </div>
                        <input type=\"text\" class=\"search-field form-text ie-radius with-tags\"
                               placeholder=\"Введите категорию продукции\"/>
                        <button type=\"submit\" class=\"icon-search-small search-button\"></button>
                        <ul class=\"tt-dropdown-menu tag-dropdownt list is-bordered\">
                            <li class=\"tt-suggestion item clickable\"><strong>Арм</strong>атура</li>
                            <li class=\"tt-suggestion item clickable\"><strong>Арм</strong>атура</li>
                            <li class=\"tt-suggestion item clickable\"><strong>Арм</strong>атура</li>
                            <li class=\"tt-suggestion item clickable\"><strong>Арм</strong>атура</li>
                            <li class=\"tt-suggestion item clickable\"><strong>Арм</strong>атура</li>
                        </ul>
                    </div>
                </fieldset>
                <div class=\"subscription-wrapper\">
                    <div class=\"scroll-block js-scrollable\">
                        <ul class=\"subscription-products\">
                            <li class=\"item clearfix\">
                                <div class=\"product-name float-left\">
                                    <span>Арматура АЗ</span>
                                </div>
                                <div class=\"delete float-right\">
                                    <a class=\"delete-btn button red-bg ie-radius\" href=\"#\">Удалить</a>
                                </div>
                            </li>
                            <li class=\"item clearfix is-invisible\">
                                <div class=\"items overflow\"></div>
                                <div class=\"product-name float-left\">
                                    <span>Арматура АЗ</span>
                                </div>
                                <div class=\"delete float-right\">
                                    <a class=\"delete-btn button red-bg ie-radius\" href=\"#\">Удалить</a>
                                </div>
                            </li>
                            <li class=\"item clearfix\">
                                <div class=\"product-name float-left\">
                                    <span>Арматура АЗ</span>
                                </div>
                                <div class=\"delete float-right\">
                                    <a class=\"delete-btn button red-bg ie-radius\" href=\"#\">Удалить</a>
                                </div>
                            </li>
                            <li class=\"item clearfix\">
                                <div class=\"product-name float-left\">
                                    <span>Арматура АЗ</span>
                                </div>
                                <div class=\"delete float-right\">
                                    <a class=\"delete-btn button red-bg ie-radius\" href=\"#\">Удалить</a>
                                </div>
                            </li>
                            <li class=\"item clearfix\">
                                <div class=\"product-name float-left\">
                                    <span>Арматура АЗ</span>
                                </div>
                                <div class=\"delete float-right\">
                                    <a class=\"delete-btn button red-bg ie-radius\" href=\"#\">Удалить</a>
                                </div>
                            </li>
                            <li class=\"item clearfix\">
                                <div class=\"product-name float-left\">
                                    <span>Арматура АЗ</span>
                                </div>
                                <div class=\"delete float-right\">
                                    <a class=\"delete-btn button red-bg ie-radius\" href=\"#\">Удалить</a>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </form>

        </div>
    </div>

    <div id=\"demand-subscription-category\" class=\"demand-subscription-block static-height popup-block opacity-border large\">
        <div class=\"popup-content\">
            <div class=\"title-popup\">Подписка на потребности</div>
            <span class=\"close-popup icon-popup-close js-popup-closer clickable\"></span>

            <form class=\"demand-subscription-form\" action=\"#\">
                <fieldset>
                    <div class=\"field-wrap\">
                        <div class=\"loading-mask embed-mask\">
                            <div class=\"spinner\"></div>
                        </div>
                        <input type=\"text\" class=\"search-field form-text ie-radius with-tags\"
                               placeholder=\"Категория\"/>
                        <button type=\"submit\" class=\"icon-search-small search-button\"></button>
                    </div>
                </fieldset>
                <div class=\"subscription-wrapper\">
                    <div class=\"scroll-block js-scrollable\">
                        <ul class=\"subscription-products\">
                            <li class=\"item\">

                                <label class=\"product-name clearfix clickable\">
                                    <span class=\"float-left\">Листовой и плоский прокат</span>
                                    <input type=\"checkbox\" class=\"js-styled-checkbox bg-grey float-right\"/>
                                </label>
                            </li>
                            <li class=\"item is-invisible\">
                                <div class=\"level2\">
                                    <label class=\"product-name clearfix clickable\">
                                        <span class=\"float-left\">Лист стальной, металический</span>
                                        <input type=\"checkbox\" class=\"js-styled-checkbox bg-grey float-right\"/>
                                    </label>
                                </div>
                            </li>
                            <li class=\"item\">
                                <div class=\"level3\">
                                    <label class=\"product-name clearfix clickable\">
                                        <span class=\"float-left\">Лист нержавеющий</span>
                                        <input type=\"checkbox\" class=\"js-styled-checkbox bg-grey float-right\"/>
                                    </label>
                                </div>
                            </li>
                            <li class=\"item\">
                                <div class=\"level3\">
                                    <label class=\"product-name clearfix clickable\">
                                        <span class=\"float-left\">Лист оцинкованный</span>
                                        <input type=\"checkbox\" class=\"js-styled-checkbox bg-grey float-right\"/>
                                    </label>
                                </div>
                            </li>
                            <li class=\"item\">
                                <div class=\"level3\">
                                    <label class=\"product-name clearfix clickable\">
                                        <span class=\"float-left\">Лист жаропрочный</span>
                                        <input type=\"checkbox\" class=\"js-styled-checkbox bg-grey float-right\"/>
                                    </label>
                                </div>
                            </li>
                            <li class=\"item\">
                                <div class=\"level2\">
                                    <label class=\"product-name clearfix clickable\">
                                        <span class=\"float-left\">Рулон</span>
                                        <input type=\"checkbox\" class=\"js-styled-checkbox bg-grey float-right\"/>
                                    </label>
                                </div>
                            </li>
                            <li class=\"item\">
                                <div class=\"level2\">
                                    <label class=\"product-name clearfix clickable\">
                                        <span class=\"float-left\">Фольга</span>
                                        <input type=\"checkbox\" class=\"js-styled-checkbox bg-grey float-right\"/>
                                    </label>
                                </div>
                            </li>
                            <li class=\"item\">
                                <div class=\"level2\">
                                    <label class=\"product-name clearfix clickable\">
                                        <span class=\"float-left\">Лента стальная</span>
                                        <input type=\"checkbox\" class=\"js-styled-checkbox bg-grey float-right\"/>
                                    </label>
                                </div>
                            </li>
                            <li class=\"item\">
                                <div class=\"level3\">
                                    <label class=\"product-name clearfix clickable\">
                                        <span class=\"float-left\">Лента стальная оцинкованная</span>
                                        <input type=\"checkbox\" class=\"js-styled-checkbox bg-grey float-right\"/>
                                    </label>
                                </div>
                            </li>
                            <li class=\"item\">
                                <div class=\"level3\">
                                    <label class=\"product-name clearfix clickable\">
                                        <span class=\"float-left\">Лента стальная жаропрочная</span>
                                        <input type=\"checkbox\" class=\"js-styled-checkbox bg-grey float-right\"/>
                                    </label>
                                </div>
                            </li>
                            <li class=\"item\">
                                <div class=\"level2\">
                                    <label class=\"product-name clearfix clickable\">
                                        <span class=\"float-left\">Гофролист</span>
                                        <input type=\"checkbox\" class=\"js-styled-checkbox bg-grey float-right\"/>
                                    </label>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class=\"submit-wrapper\">
                    <input type=\"submit\" class=\"save-btn button blue-bg ie-radius\" value=\"Сохранить\" />
                </div>
            </form>

        </div>
    </div>

    <div id=\"demand-subscription-advance\" class=\"demand-subscription-block popup-block opacity-border large\">
        <div class=\"popup-content\">
            <div class=\"title-popup\">Подписка на потребности</div>
            <span class=\"close-popup icon-popup-close js-popup-closer clickable\"></span>

            <form class=\"demand-subscription-form\" action=\"#\">
                <fieldset>
                    <div class=\"field-wrap\">
                        <input id=\"notice\" type=\"checkbox\" class=\"js-styled-checkbox bg-white\"/>
                        <label for=\"notice\">уведомлять на </label><a class=\"link\"
                                                                     href=\"#\"><strong>asdas@sadsda.ru</strong></a>

                        <div class=\"loading-mask inline\">
                            <div class=\"spinner\"></div>
                        </div>
                    </div>
                    <div class=\"field-wrap\">
                        <div class=\"loading-mask embed-mask\">
                            <div class=\"spinner\"></div>
                        </div>
                        <input type=\"text\" class=\"search-field form-text ie-radius with-tags\" placeholder=\"Категория\"/>
                        <button type=\"submit\" class=\"icon-search-small search-button\"></button>
                        <ul class=\"tt-dropdown-menu tag-dropdownt list is-bordered g-hidden\">
                            <li class=\"tt-suggestion item clickable\"><strong>Арм</strong>атура</li>
                            <li class=\"tt-suggestion item clickable\"><strong>Арм</strong>атура</li>
                            <li class=\"tt-suggestion item clickable\"><strong>Арм</strong>атура</li>
                            <li class=\"tt-suggestion item clickable\"><strong>Арм</strong>атура</li>
                            <li class=\"tt-suggestion item clickable\"><strong>Арм</strong>атура</li>
                        </ul>
                    </div>
                    <div class=\"field-wrap\">
                        <input class=\"form-text ie-radius with-tags\" type=\"text\" placeholder=\"Регион\"/>
                        <button type=\"submit\" class=\"icon-search-small search-button\"></button>
                        <ul class=\"tt-dropdown-menu tag-dropdownt list is-bordered\">
                            <li class=\"tt-suggestion item clickable\">
                                <strong>Мос</strong>ква
                                <p class=\"region\">Московская область</p>
                            </li>
                            <li class=\"tt-suggestion item clickable\">
                                <strong>Мос</strong>аленки
                                <p class=\"region\">Московская область</p>
                            </li>
                            <li class=\"tt-suggestion item clickable\">
                                <strong>Мос</strong>ква
                                <p class=\"region\">Московская область</p>
                            </li>

                        </ul>
                    </div>
                    <div class=\"filtered-block\">
                        <ul class=\"list\">
                            <li class=\"item ie-radius\">
                                <div class=\"overflow items\"></div>
                                <span class=\"product-text\">Москва</span>
                                <span class=\"icon-filter-del clickable\"></span>
                            </li>
                            <li class=\"item ie-radius\">
                                <div class=\"overflow items\"></div>
                                <span class=\"product-text\">Москва</span>
                                <span class=\"icon-filter-del clickable\"></span>
                            </li>
                            <li class=\"item ie-radius\">
                                <div class=\"overflow items\"></div>
                                <span class=\"product-text\">Москва</span>
                                <span class=\"icon-filter-del clickable\"></span>
                            </li>
                            <li class=\"item ie-radius\">
                                <div class=\"overflow items\"></div>
                                <span class=\"product-text\">Москва</span>
                                <span class=\"icon-filter-del clickable\"></span>
                            </li>
                            <li class=\"item ie-radius\">
                                <div class=\"overflow items\"></div>
                                <span class=\"product-text\">Москва</span>
                                <span class=\"icon-filter-del clickable\"></span>
                            </li>
                            <li class=\"item ie-radius\">
                                <span class=\"product-text\">Днепропетровск</span>
                                <span class=\"icon-filter-del clickable\"></span>
                            </li>
                        </ul>
                        <p class=\"empty-message\">Вы подписаны на все регионы</p>
                    </div>
                </fieldset>
                <div class=\"subscription-wrapper\">
                    <div class=\"table-container\">
                        <p class=\"empty-message table-cell\">Вы подписаны на все категории</p>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div id=\"confirmation-email\" class=\"confirmation-email popup-block opacity-border large\">
        <div class=\"popup-content\">
            <div class=\"title-popup\">Подтвердите почту</div>
            <span class=\"close-popup icon-popup-close js-popup-closer clickable\"></span>

            <form id=\"confirmation-email-form\" class=\"popup-form\" action=\"#\">
                <fieldset>
                    <p class=\"text\">Мы отправили вам письмо со ссылкой для подтверждения вашей электронной почты.
                        Перед тем как работать с информацией, пожалуйста, проверьте почту и откройте эту ссылку.</p>

                    <div class=\"submit-wrapper\">
                        <input class=\"send-button button blue-bg ie-radius\" type=\"submit\" value=\"Отправить еще раз\"/>
                    </div>
                </fieldset>
            </form>
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

    <div id=\"promotion-1\" class=\"promotion-popup popup-block opacity-border large\">
        <div class=\"popup-content\">
            <div class=\"title-popup\">Оплата рекомендованного продвижения</div>
            <span class=\"close-popup icon-popup-close js-popup-closer clickable\"></span>

            <form action=\"#\">
                <fieldset>
                    <ul class=\"list clearfix\">
                        <li class=\"item float-left\">
                            <div class=\"item-title\">
                                <input id=\"radio-1\" name=\"promotion\" type=\"radio\" class=\"radio bg-grey80\"/>
                                <label for=\"radio-1\">квартал</label>
                            </div>
                            <p class=\"price\">6000 <span class=\"icon-rouble\"></span></p>
                        </li>
                        <li class=\"item float-left\">
                            <div class=\"item-title\">
                                <input id=\"radio-2\" name=\"promotion\" type=\"radio\" class=\"radio bg-grey80\"/>
                                <label for=\"radio-2\">полугодие</label>
                            </div>
                            <p class=\"price\">10000 <span class=\"icon-rouble\"></span>
                                <span class=\"discount\">скидка 15%</span>
                            </p>
                        </li>
                        <li class=\"item float-left\">
                            <div class=\"item-title\">
                                <input id=\"radio-3\" name=\"promotion\" checked type=\"radio\" class=\"radio bg-grey80\"/>
                                <label for=\"radio-3\">год</label>
                            </div>
                            <p class=\"price\">
                                18000 <span class=\"icon-rouble\"></span>
                                <span class=\"discount\">скидка 25%</span>
                            </p>
                        </li>
                    </ul>
                    <div class=\"submit-wrapper\">
                        <input class=\"send-button button blue-bg ie-radius\" type=\"submit\" value=\"Оплатить\"/>
                    </div>
                </fieldset>
            </form>

        </div>
    </div>

    <div id=\"promo\" class=\"popup-block opacity-border large\">
        <div class=\"popup-content\">
            <div class=\"title-popup\">Промо-код</div>
            <span class=\"close-popup icon-popup-close js-popup-closer clickable\"></span>

            <form class=\"popup-form\" action=\"#\">
                <fieldset>
                    <div class=\"promo-group clearfix\">
                        <div class=\"promo-wrapper field-wrap float-left\">
                            <input type=\"text\" class=\"promo-field form-text ie-radius\" placeholder=\"Промокод\">
                            <span class=\"icon-help js-helper-opener\" data-text=\"Вы можете ввести промокод, он дает
                             право на пользование полным пакетом услуг в течении одного месяца\"></span>

                        </div>
                        <div class=\"promo-description-wrapper field-wrap float-left\">
                            <p class=\"promo-status green-color\">Промокод действителен и дает право на пользование полным пакетом услуг в течении одного месяца</p>
                            <p class=\"promo-status red-color\">Промокод действителен с 16 окт 2015 до 31 дек 2015</p>
                        </div>
                    </div>
                    <div class=\"submit-wrapper\">
                        <input class=\"send-button button blue-bg ie-radius\" type=\"submit\" value=\"Применить\"/>
                    </div>
                </fieldset>
            </form>
        </div>
    </div>

    <div class=\"photo popup-block opacity-border large\" id=\"announcement-preview\">
        <div class=\"popup-wrapper clearfix\">
            <div class=\"img-content float-left\">
                <img width=\"960\" height=\"90\" src=\"./markup/pic/banner.jpg\">
            </div>
            <div class=\"popup-content float-left\">
                <div class=\"announcement-information\">
                    <p class=\"info\">
                        <strong>Зона: </strong>Премиум
                    </p>
                    <p class=\"info\">
                        <strong>Размер: </strong>960х90
                    </p>
                    <p class=\"info\">
                        <strong>Дата активности: </strong> 01 сен 2015 - 30 дек 2015
                    </p>
                </div>
            </div>
        </div>

    </div>

    <div id=\"user-info\" class=\"popup-block opacity-border large\">
        <div class=\"popup-content\">
            <span class=\"close-popup icon-popup-close js-popup-closer clickable\"></span>

            <div class=\"user-info clearfix\">
                <div class=\"img-wrapper is-bordered float-left\">
                    <img src=\"./markup/pic/user.jpg\" alt=\"image description\" width=\"100\" height=\"100\">
                </div>
                <div class=\"name float-left\">admin</div>
            </div>
            <div class=\"interests\">
                <div class=\"block-title\"><strong>Интересы</strong></div>
                <p class=\"interest-item\">Части квартиры:
                        <a href=\"#\">спальная</a>,
                        <a href=\"#\">детская</a>,
                        <a href=\"#\">прихожая</a>,
                        <a href=\"#\">коридор</a>,
                        <a href=\"#\">туалет</a>,
                        <a href=\"#\">ванная</a>,
                        <a href=\"#\">лоджия</a>,
                        <a href=\"#\">балкон</a>,
                        <a href=\"#\">гостиная</a>,
                        <a href=\"#\">спортзал</a>,
                        <a href=\"#\">кухня</a>,
                        <a href=\"#\">кабинет</a>,
                        <a href=\"#\">сауна</a>
                </p>
                <p class=\"interest-item\">Коммуникации:
                    <a href=\"#\">вентиляция</a>,
                    <a href=\"#\">водоснабжение</a>,
                    <a href=\"#\">отопление</a>,
                    <a href=\"#\">газоснабжение</a>,
                    <a href=\"#\">электрика</a>,
                    <a href=\"#\">канализация</a>
                </p>
                <p class=\"interest-item\">Технические средства:
                    <a href=\"#\">кондиционирование</a>,
                    <a href=\"#\">домашний кинотеатр</a>,
                    <a href=\"#\">безопасность</a>,
                    <a href=\"#\">умный дом</a>

                </p>
            </div>
        </div>
    </div>

    <div id=\"user-info-2\" class=\"popup-block opacity-border large\">
        <div class=\"popup-content\">
            <span class=\"close-popup icon-popup-close js-popup-closer clickable\"></span>

            <div class=\"user-info clearfix\">
                <div class=\"img-wrapper is-bordered float-left\">
                    <div class=\"pattern-small\"></div>
                </div>
                <div class=\"name float-left\">admin</div>
            </div>
            <div class=\"interests\">
                <div class=\"block-title\"><strong>Интересы</strong></div>
                <p class=\"interest-item empty gray60-color centered\">
                    У пользователя нет интересов
                </p>
            </div>
        </div>
    </div>

    <div id=\"add-answer\" class=\"popup-block opacity-border large\">
        <div class=\"popup-content\">
            <span class=\"close-popup icon-popup-close js-popup-closer clickable\"></span>
            <div class=\"add-answer-block\">
                <div class=\"title\"><strong>Оставить комментарий</strong></div>
                <form action=\"#\" class=\"popup-form\">
                    <fieldset>
                        <div class=\"group clearfix\">
                            <div class=\"field-wrap float-left\">
                                <input type=\"text\" class=\"form-text\" placeholder=\"Ваше имя\">
                            </div>
                            <div class=\"field-wrap float-left\">
                                <input type=\"text\" class=\"form-text\" placeholder=\"Email\">
                            </div>
                        </div>
                        <div class=\"field-wrap\">
                            <textarea class=\"form-textarea\" placeholder=\"Ваш комментарий\"></textarea>
                        </div>
                        <div class=\"submit-wrapper\">
                            <input type=\"submit\" class=\"button blue-bg\" value=\"Добавить комментарий\">
                        </div>
                    </fieldset>
                </form>
            </div>
        </div>
    </div>


    <div id=\"add-content-topic\" class=\"add-content-entry-form large-popup popup-block opacity-border large\">
        <div class=\"popup-content\">
            <div class=\"title-popup\">Добавить публикацию</div>
            <span class=\"close-popup icon-popup-close clickable js-popup-closer\"></span>

            <form id=\"add-content-topic-form\" class=\"popup-form js-ajax-form-submit\" method=\"#\">
                <fieldset>
                    <div class=\"field-wrap\">
                        <input type=\"text\" class=\"form-text ie-radius\" placeholder=\"Заголовок\">
                    </div>
                    <div class=\"group clearfix\">
                        <div class=\"user-email-wrapper field-wrap float-left\">
                            <select class=\"form-select ie-radius\">
                                <option value=\"\" selected=\"selected\">Категория</option>
                                <option value=\"\">Квартира, Таунхаус</option>
                                <option value=\"\">Загородный дом</option>
                                <option value=\"\">Сад, строения</option>
                                <option value=\"\">спальная</option>
                                <option value=\"\">сауна</option>
                                <option value=\"\">кабинет</option>
                                <option value=\"\">кухня</option>
                            </select>

                        </div>

                        <div class=\"user-email-wrapper field-wrap float-left\">
                            <select class=\"form-select ie-radius\">
                                <option value=\"\" selected=\"selected\">Доп. категория</option>
                                <option value=\"\">Квартира, Таунхаус</option>
                                <option value=\"\">Загородный дом</option>
                                <option value=\"\">Сад, строения</option>
                                <option value=\"\">спальная</option>
                                <option value=\"\">сауна</option>
                                <option value=\"\">кабинет</option>
                            </select>
                        </div>
                    </div>
                    <div class=\"user-email-wrapper field-wrap\">
                        <select class=\"form-select ie-radius\">
                            <option value=\"\" selected=\"selected\">Раздел</option>
                            <option value=\"1\">Проекты и технологии</option>
                            <option value=\"2\">Отделочные материалы</option>
                            <option value=\"3\">Выполнение работ</option>
                            <option value=\"4\">Мебель, декор, аксессуары</option>
                        </select>
                    </div>
                    <div class=\"textarea-wrapper field-wrap\">
                        <label>Описание</label>
                         <textarea class=\"description form-textarea ie-radius\" placeholder=\"Описание\" data=\"editable\"></textarea>
                    </div>
                    <div class=\"textarea-wrapper field-wrap\">
                        <label>Краткое описание</label>
                        <textarea class=\"short-description form-textarea ie-radius\" placeholder=\"Краткое описание\" data=\"editable\"></textarea>
                    </div>

                    <div class=\"photo-wrapper field-wrap\">
                        <label class=\"file-upload\">
                            <span class=\"load-file-btn ico-upload\">Главное фото публикации</span>
                            <input type=\"file\">
                        </label>
                    </div>

                    <div class=\"tags-list-wrapper\">

                        <div class=\"field-wrap\">
                            <div class=\"tag-wrapper is-bordered ie-radius form-text\">
                                <div class=\"filtered-block\">
                                    <div id=\"tags-list-container\">

                                    </div>
                                </div>
                                <div class=\"loading-mask embed-mask for-high-input\">
                                    <div class=\"spinner\"></div>
                                </div>
                                <input type=\"text\" placeholder=\"Теги\" />

                            </div>

                        </div>
                    </div>

                    <div class=\"submit-wrapper\">
                        <input class=\"popup-form_send-button send-button button blue-bg ie-radius\" type=\"submit\" value=\"Добавить\">
                    </div>
                </fieldset>
            </form>
        </div>
    </div>
";
    }

    public function getTemplateName()
    {
        return "@markup/_popups.html.twig";
    }

    public function getDebugInfo()
    {
        return array (  519 => 493,  472 => 447,  26 => 2,  20 => 1,  380 => 172,  377 => 171,  371 => 168,  365 => 165,  359 => 159,  353 => 155,  347 => 153,  341 => 152,  337 => 154,  334 => 153,  331 => 152,  328 => 151,  316 => 148,  310 => 147,  304 => 145,  298 => 144,  293 => 141,  283 => 135,  280 => 134,  274 => 130,  270 => 168,  266 => 166,  264 => 165,  257 => 160,  255 => 159,  250 => 156,  247 => 155,  245 => 151,  242 => 150,  239 => 149,  236 => 148,  234 => 147,  231 => 146,  225 => 144,  222 => 143,  220 => 140,  217 => 139,  215 => 134,  210 => 131,  208 => 130,  187 => 103,  180 => 87,  178 => 86,  143 => 33,  140 => 32,  111 => 174,  106 => 170,  104 => 125,  81 => 105,  62 => 91,  60 => 32,  52 => 27,  50 => 9,  599 => 389,  596 => 388,  590 => 378,  546 => 336,  543 => 335,  537 => 334,  533 => 377,  530 => 335,  527 => 334,  524 => 333,  515 => 326,  512 => 325,  507 => 323,  505 => 322,  503 => 321,  501 => 320,  499 => 319,  497 => 318,  493 => 315,  490 => 314,  483 => 156,  324 => 160,  322 => 149,  318 => 153,  315 => 152,  294 => 132,  292 => 131,  290 => 140,  288 => 129,  286 => 128,  284 => 127,  278 => 122,  275 => 121,  233 => 78,  229 => 145,  226 => 75,  202 => 126,  199 => 125,  193 => 105,  185 => 42,  182 => 41,  177 => 47,  175 => 41,  149 => 17,  146 => 16,  142 => 433,  138 => 431,  136 => 388,  125 => 379,  122 => 378,  114 => 325,  112 => 314,  109 => 171,  107 => 152,  103 => 150,  100 => 121,  96 => 119,  94 => 118,  90 => 116,  88 => 75,  85 => 74,  82 => 52,  80 => 51,  76 => 103,  74 => 16,  66 => 12,  63 => 11,  58 => 8,  55 => 29,  48 => 4,  45 => 3,  482 => 430,  480 => 155,  478 => 428,  130 => 81,  127 => 80,  120 => 10,  117 => 9,  46 => 12,  44 => 7,  40 => 1,  37 => 4,  31 => 3,);
    }
}
