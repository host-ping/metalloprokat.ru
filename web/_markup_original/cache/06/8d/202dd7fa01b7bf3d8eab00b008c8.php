<?php

/* @markup/private/management/products.html.twig */
class __TwigTemplate_068d202dd7fa01b7bf3d8eab00b008c8 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = $this->env->loadTemplate("@markup/private/management/_managemet_layout.html.twig");

        $this->blocks = array(
            'title' => array($this, 'block_title'),
            'user_alert' => array($this, 'block_user_alert'),
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
        echo "Товары";
    }

    // line 3
    public function block_user_alert($context, array $blocks = array())
    {
        // line 4
        echo "    <div class=\"user-alert  clearfix\">
        <div class=\"wrap clearfix\">
            <div class=\"account-info float-right\">
                <span class=\"extend-btn button green-bg js-popup-opener\">Исправить</span>
            </div>

            <div class=\"info\">
                <p class=\"text\"><strong class=\"red-color\">5 товаров</strong> размещены в категории \"Прочее\". Измените их категорию, чтобы получить больше клиентов. </p>
            </div>

        </div>
    </div>
";
    }

    // line 20
    public function block_content($context, array $blocks = array())
    {
        // line 21
        echo "        <div id=\"content\" class=\"private-room-content content-right outline-right float-right\">
            ";
        // line 22
        $this->displayBlock('tabs', $context, $blocks);
        // line 26
        echo "            <div class=\"products-content-wrapper outline-right\">
                <div class=\"extend-package-info clearfix\">
                    <div class=\"info\">
                        <p class=\"text\">Если вы хотите, чтобы более 500 позиций были размещены в разделе поставщики, а так же
                            участвовали в рейтинге, подключите пакет <a href=\"\" class=\"link\">расширенный</a> или
                            <a href=\"#\" class=\"link\">полный</a> </p>
                    </div>
                </div>
                <div class=\"header-icons clearfix\">
                    <div class=\"items overflow g-hidden\"></div>
                    <div class=\"loading-mask g-hidden\">
                        <div class=\"spinner\"></div>
                    </div>
                    <div class=\"icons float-left\">
                        <ul class=\"icons-list clearfix\">
                            <li class=\"item float-left\">
                                <a class=\"add link js-popover-opener\" data-popover=\"#add-product-list\" href=\"#\"></a>
                                <div id=\"add-product-list\" class=\"drop-wrapper add-product-drop opacity-border\">
                                    <ul class=\"dropdown\">
                                        <li class=\"drop-item\">
                                            <a class=\"drop-link js-popup-opener\" data-popup=\"#add-product\" href=\"#\">Добавить товар</a>
                                        </li>
                                        <li class=\"drop-item\">
                                            <a class=\"drop-link js-popup-opener\" data-popup=\"#add-products\" href=\"#\">Загрузить товары</a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                            <li class=\"item float-left\">
                                <button class=\"revert link\" disabled=\"disabled\"></button>
                            </li>
                            <li class=\"item float-left\">
                                <a class=\"photo link js-popup-opener\" data-popup=\"#load-photo\" href=\"#\"></a>
                            </li>
                            <li class=\"item float-left\">
                                <form action=\"#\">
                                    <fieldset>
                                        <div class=\"autoupdate select-wrapper field-wrap\">
                                            <select class=\"form-select\">
                                                <option value=\"-1\"></option>
                                                <option value=\"0\">00:00</option>
                                                <option value=\"1\">01:00</option>
                                                <option value=\"2\">02:00</option>
                                                <option value=\"3\">03:00</option>
                                                <option value=\"4\">04:00</option>
                                                <option value=\"5\">05:00</option>
                                                <option value=\"6\">06:00</option>
                                                <option value=\"7\">07:00</option>
                                                <option value=\"8\">08:00</option>
                                                <option value=\"9\">09:00</option>
                                                <option value=\"10\">10:00</option>
                                                <option value=\"11\">11:00</option>
                                                <option value=\"12\">12:00</option>
                                                <option value=\"13\">13:00</option>
                                                <option value=\"14\">14:00</option>
                                                <option value=\"15\">15:00</option>
                                                <option value=\"16\">16:00</option>
                                                <option value=\"17\">17:00</option>
                                                <option value=\"18\">18:00</option>
                                                <option value=\"19\">19:00</option>
                                                <option value=\"20\">20:00</option>
                                                <option value=\"21\">21:00</option>
                                                <option value=\"22\">22:00</option>
                                                <option value=\"23\">23:00</option>
                                            </select>
                                            <span class=\"icon-help js-helper-opener\"
                                                  data-text=\"Выберите время, в которое ежедневно будет производиться автоматическое подтверждение актуальности цен\"
                                                  ></span>
                                        </div>

                                    </fieldset>
                                </form>
                            </li>
                        </ul>
                    </div>
                    <div class=\"float-right\">
                        <p class=\"last-update float-right\">Обновлено 12 марта 2013</p>

                        <div class=\"filials-list-wrapper float-left\">
                            <span class=\"filial icon-check black clickable js-popover-opener\" data-popover=\"#filials-list-filter\">Центральный офис</span>
                            <div id=\"filials-list-filter\" class=\"drop-wrapper filials-list-filter opacity-border\">
                                <ul class=\"dropdown\">
                                    <li class=\"drop-item\">
                                        <span class=\"drop-link current\">Центральный офис</span>
                                    </li>
                                    <li class=\"drop-item\">
                                        <a class=\"drop-link\" href=\"#\">Филиал 1</a>
                                    </li>
                                    <li class=\"drop-item\">
                                        <a class=\"drop-link\" href=\"#\">Филиал 2</a>
                                    </li>
                                    <li class=\"drop-item\">
                                        <a class=\"drop-link\" href=\"#\">Добавить</a>
                                    </li>
                                </ul>
                            </div>
                        </div>

                    </div>
                </div>

                <form class=\"product-management form js-private-product-container\" action=\"#\">
                    <div class=\"form-container clearfix\">
                        <div class=\"products-list float-left\">
                            <div class=\"heading clearfix\">
                                <div class=\"check-all block float-left\">
                                    <input type=\"checkbox\" class=\"js-styled-checkbox bg-grey\"/>
                                </div>
                                <div class=\"product-photo block float-left\">
                                    <a class=\"link icon-check black js-popover-opener\" data-popover=\"#photo-product-filter\" href=\"#\">все</a>
                                    <div id=\"photo-product-filter\" class=\"drop-wrapper photo-product-filter opacity-border\">
                                        <ul class=\"dropdown\">
                                            <li class=\"drop-item\">
                                                <a class=\"drop-link current\" href=\"#\">все</a>
                                            </li>
                                            <li class=\"drop-item\">
                                                <a class=\"drop-link\" href=\"#\">с фото</a>
                                            </li>
                                            <li class=\"drop-item\">
                                                <a class=\"drop-link\" href=\"#\">без фото</a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class=\"product-name block float-left\">
                                    <input type=\"text\" class=\"search form-text ie-radius js-popover-opener-focus\" data-popover=\"#categories\" placeholder=\"поиск по товарам\"/>
                                    <button type=\"submit\" class=\"icon-search-small search-button\"></button>
                                    <div id=\"categories\" class=\"choose-section drop-wrapper categories-filter opacity-border\">
                                        <div class=\"heading\">
                                            <div class=\"drop-item special\">
                                                <a href=\"#\" class=\"drop-link\">Спецпредложения</a>
                                            </div>
                                        </div>
                                        <div class=\"bottom\">
                                            <div class=\"sections js-scrollable\">
                                                <ul class=\"category-list\">
                                                    <li class=\"drop-item\">
                                                        <a href=\"#\" class=\"drop-link\">Сортовой прокат</a>
                                                        <ul class=\"level-inside\">
                                                            <li class=\"drop-item\">
                                                                <a class=\"drop-link\" href=\"#\">Арматура</a>
                                                            </li>
                                                            <li class=\"drop-item\">
                                                                <a class=\"drop-link\" href=\"#\">Балка</a>
                                                            </li>
                                                            <li class=\"drop-item\">
                                                                <a class=\"drop-link\" href=\"#\">Канатка</a>
                                                                <ul class=\"level-inside\">
                                                                    <li class=\"drop-item\">
                                                                        <a class=\"drop-link\" href=\"#\">Арматура</a>
                                                                    </li>
                                                                    <li class=\"drop-item\">
                                                                        <a class=\"drop-link\" href=\"#\">Балка</a>
                                                                    </li>
                                                                </ul>
                                                            </li>
                                                            <li class=\"drop-item\">
                                                                <a class=\"drop-link\" href=\"#\">Квадрат</a>
                                                            </li>
                                                            <li class=\"drop-item\">
                                                                <a class=\"drop-link\" href=\"#\">Арматура</a>
                                                            </li>
                                                            <li class=\"drop-item\">
                                                                <a class=\"drop-link\" href=\"#\">Балка</a>
                                                            </li>
                                                            <li class=\"drop-item\">
                                                                <a class=\"drop-link\" href=\"#\">Канатка</a>
                                                            </li>
                                                            <li class=\"drop-item\">
                                                                <a class=\"drop-link\" href=\"#\">Квадрат</a>
                                                            </li>
                                                        </ul>
                                                    </li>

                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class=\"product-price block float-left\">
                                    <a class=\"link icon-check black\" href=\"#\">по цене</a>
                                </div>
                                <div class=\"product-status block float-left\">
                                    <span class=\"link icon-check black clickable js-popover-opener\" data-popover=\"#status\">все</span>
                                    <div id=\"status\" class=\"drop-wrapper status-filter opacity-border\">
                                        <ul class=\"dropdown\">
                                            <li class=\"drop-item\">
                                                <a class=\"drop-link current\" href=\"#\">все</a>
                                            </li>
                                            <li class=\"drop-item\">
                                                <span class=\"drop-link icon-check-big complete\">промодерировано</span>
                                            </li>
                                            <li class=\"drop-item\">
                                                <span class=\"drop-link process icon-clock clickable\">не промодерировано</span>
                                            </li>
                                            <li class=\"drop-item\">
                                                <span class=\"drop-link icon-filter-del clickable\">не промодерировано</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class=\"product-management-list\">
                                <div class=\"list js-scrollable\">
                                    <div class=\"item\">
                                        ";
        // line 232
        echo "                                        <div class=\"fix-holder item-block clearfix\">
                                            <div class=\"check-all block\">
                                                <input type=\"checkbox\" class=\"checkbox js-styled-checkbox\"/>
                                            </div>
                                            <div class=\"product-photo block\">
                                                <div class=\"img is-bordered edited\">
                                                    <a class=\"pattern-small\" href=\"#\"></a>
                                                </div>
                                            </div>
                                            <div class=\"product-name block\">
                                                <strong class=\"product edited is-gradiented ie-radius\">Шестигранник калиброванный калиброванный</strong>
                                                <span class=\"category edited ie-radius\">Арматура</span>
                                                <div class=\"product-description-wrapper\">
                                                    <p class=\"product-description is-gradiented-bottom ie-radius edited ng-binding\">
                                                        Получите трубу 146х25 в максимально возможные короткие сроки. Труба 146х25 будет изготовлена из стали 20 по ГОСТ 8732-78 за 15-30 дней.
                                                    </p>
                                                </div>
                                            </div>
                                            <div class=\"product-size block\">
                                                <p class=\"size edited is-gradiented ie-radius\">20x30x1523</p>
                                                <p class=\"text edited ie-radius\">сп</p>
                                                <span class=\"icon-help js-helper-opener\"
                                                      data-text=\"Спецпредложение будет отображаться на главной странице вашего сайта\"
                                                      data-absolute-parent=\".products-list\"></span>
                                            </div>
                                            <div class=\"product-price block\">
                                                <p class=\"price red-color edited ie-radius\">
                                                    <strong>26 000 <span class=\"icon-rouble\"></span></strong>
                                                </p>
                                                <p class=\"vol edited ie-radius\">за тонну</p>
                                            </div>
                                            <div class=\"product-status block\">
                                                <span class=\"process icon-clock clickable product-status-link\"></span>
                                                <span class=\"icon-pencil clickable\"></span>
                                                <span class=\"delete-btn clickable g-hidden\">
                                                    <span class=\"icon-filter-del\"></span>
                                                </span>
                                            </div>
                                        </div>
                                        <div class=\"change-holder item-block clearfix g-hidden\">
                                            <div class=\"check-all block\"></div>
                                            <div class=\"product-photo block\">
                                                <div class=\"img is-bordered\">
                                                    <a class=\"pattern-small\" href=\"#\"></a>
                                                </div>
                                            </div>
                                            <div class=\"product-name block\">
                                                <input type=\"text\" class=\"product form-text ie-radius error\" placeholder=\"Шестигранник калибров...\"/>
                                                <span class=\"icon-error-color js-helper-opener\" data-absolute-parent=\".products-list\" data-text=\"Недопустимое значение\"></span>
                                                <a href=\"#\" class=\"category link js-popover-opener ie-radius\" data-popover=\"#choose-section\" data-left=\"0\" data-top=\"-20\">Арматура</a>
                                            </div>
                                            <div class=\"product-size block\">
                                                <input type=\"text\" placeholder=\"20x30\" class=\"size form-text ie-radius\"/>

                                            </div>
                                            <div class=\"product-price block\">
                                                <input class=\"price form-text red-color ie-radius error\" type=\"text\" placeholder=\"дог.\"/>
                                                <span class=\"icon-error-color js-helper-opener\" data-absolute-parent=\".products-list\" data-text=\"Недопустимое значение\"></span>

                                                <div class=\"price-for-wrapper\">
                                                    <label for=\"price-for\">от</label>
                                                    <input id=\"price-for\" type=\"checkbox\" class=\"checkbox bg-grey js-styled-checkbox\"/>
                                                </div>
                                                <div class=\"select-wrapper\">
                                                    <select id=\"product-volume\" class=\"vol form-select\">
                                                        <option value=\"0\">&nbsp;</option>
                                                        <option value=\"1\">за тонну</option>
                                                        <option value=\"2\">за кг</option>
                                                        <option value=\"3\">за ведро</option>
                                                    </select>
                                                    <span class=\"icon-error-color js-helper-opener\"
                                                          data-text=\"Спецпредложение будет отображаться на главной странице вашего сайта\"
                                                          data-absolute-parent=\".products-list\"></span>
                                                </div>

                                            </div>
                                            <div class=\"product-status block\">
                                                <button class=\"ok-btn\" type=\"submit\">ok</button>
                                            </div>
                                            <div class=\"product-position-wrapper\">
                                                <span class=\"showcase text\">
                                                    <input id=\"showcase-input\" type=\"checkbox\" class=\"checkbox bg-grey js-styled-checkbox\"/>
                                                    <label for=\"showcase-input\">сп</label>
                                                </span>
                                                <span class=\"icon-help js-helper-opener\"
                                                      data-text=\"Спецпредложение будет отображаться на главной странице вашего сайта\"
                                                      data-absolute-parent=\".products-list\"></span>
                                                <label>позиция</label>
                                                <input type=\"text\" class=\"form-text product-position\" value=\"1\">
                                            </div>
                                            <div class=\"product-description-wrapper\">
                                                <textarea class=\"product-description form-textarea ie-radius placeholder\" placeholder=\"Описание продукта\"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class=\"item\">
                                        <div class=\"fix-holder item-block clearfix \">
                                            <div class=\"check-all block\">
                                                <input type=\"checkbox\" class=\"checkbox js-styled-checkbox\"/>
                                            </div>
                                            <div class=\"product-photo block\">
                                                <div class=\"img is-bordered edited\">
                                                    <a class=\"pattern-small\" href=\"#\"></a>
                                                </div>
                                            </div>
                                            <div class=\"product-name block\">
                                                <strong class=\"product edited ie-radius\">Шестигранник калибров...</strong>
                                                <span class=\"category edited ie-radius\">Арматура</span>
                                                <div class=\"product-description-wrapper\">
                                                    <p class=\"product-description is-gradiented-bottom ie-radius edited ng-binding\">
                                                        Получите трубу 146х25 в максимально возможные короткие сроки. Труба 146х25 будет изготовлена из стали 20 по ГОСТ 8732-78 за 15-30 дней.
                                                    </p>
                                                </div>
                                            </div>
                                            <div class=\"product-size block\">
                                                <p class=\"size edited ie-radius\">20x30</p>
                                                <p class=\"text edited ie-radius\">сп</p>
                                                <span class=\"icon-help js-helper-opener\"
                                                      data-text=\"Спецпредложение будет отображаться на главной странице вашего сайта\"
                                                      data-absolute-parent=\".products-list\"></span>
                                            </div>
                                            <div class=\"product-price block\">
                                                <p class=\"price red-color edited ie-radius\">
                                                    <strong>26 000 <span class=\"icon-rouble\"></span></strong>
                                                </p>
                                                <p class=\"vol edited ie-radius\">за тонну</p>
                                            </div>
                                            <div class=\"product-status block\">
                                                <span class=\"process icon-clock clickable product-status-link\"></span>
                                                <span class=\"icon-pencil clickable\"></span>
                                                <span class=\"delete-btn clickable g-hidden\">
                                                    <span class=\"icon-filter-del\"></span>
                                                </span>
                                            </div>
                                        </div>
                                        <div class=\"change-holder item-block clearfix g-hidden\">
                                            <div class=\"check-all block\"></div>
                                            <div class=\"product-photo block\">
                                                <div class=\"img is-bordered\">
                                                    <a class=\"pattern-small\" href=\"#\"></a>
                                                </div>
                                            </div>
                                            <div class=\"product-name block\">
                                                <input type=\"text\" class=\"product form-text ie-radius\" placeholder=\"Шестигранник калибров...\"/>
                                                <a href=\"#\" class=\"category link js-popover-opener ie-radius\" data-popover=\"#choose-section\" data-left=\"0\" data-top=\"-20\">Арматура</a>
                                            </div>
                                            <div class=\"product-size block\">
                                                <input type=\"text\" placeholder=\"20x30\" class=\"size form-text ie-radius\"/>
                                                <p class=\"showcase text\">
                                                    <input id=\"showcase-input\" type=\"checkbox\" class=\"checkbox bg-grey js-styled-checkbox\"/>
                                                    <label for=\"showcase-input\">сп</label>
                                                </p>
                                                <span class=\"icon-help js-helper-opener\"
                                                      data-text=\"Спецпредложение будет отображаться на главной странице вашего сайта\"
                                                      data-absolute-parent=\".products-list\"></span>
                                            </div>
                                            <div class=\"product-price block\">
                                                <input class=\"price form-text red-color ie-radius\" type=\"text\" placeholder=\"26 000 руб.\"/>
                                                <select id=\"product-volume\" class=\"vol form-select\">
                                                    <option value=\"0\">&nbsp;</option>
                                                    <option value=\"1\">за тонну</option>
                                                    <option value=\"2\">за кг</option>
                                                    <option value=\"3\">за ведро</option>
                                                </select>
                                            </div>
                                            <div class=\"product-status block\">
                                                <button class=\"ok-btn\" type=\"submit\">ok</button>
                                            </div>
                                            <div class=\"product-description-wrapper\">
                                                <textarea class=\"product-description form-textarea ie-radius placeholder\" placeholder=\"Описание продукта\"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class=\"item\">
                                        <div class=\"fix-holder item-block clearfix\">
                                            <div class=\"check-all block\">
                                                <input type=\"checkbox\" class=\"checkbox js-styled-checkbox\"/>
                                            </div>
                                            <div class=\"product-photo block\">
                                                <div class=\"img is-bordered edited\">
                                                    <a class=\"pattern-small\" href=\"#\"></a>
                                                </div>
                                            </div>
                                            <div class=\"product-name block\">
                                                <strong class=\"product edited ie-radius\">Шестигранник калибров...</strong>
                                                <span class=\"category edited ie-radius\">Арматура</span>
                                                <div class=\"product-description-wrapper\">
                                                    <p class=\"product-description is-gradiented-bottom ie-radius edited ng-binding\">
                                                        Получите трубу 146х25 в максимально возможные короткие сроки. Труба 146х25 будет изготовлена из стали 20 по ГОСТ 8732-78 за 15-30 дней.
                                                    </p>
                                                </div>
                                            </div>
                                            <div class=\"product-size block\">
                                                <p class=\"size edited ie-radius\">20x30</p>
                                                <p class=\"text edited ie-radius\">сп</p>
                                                <span class=\"icon-help js-helper-opener\"
                                                      data-text=\"Спецпредложение будет отображаться на главной странице вашего сайта\"
                                                      data-absolute-parent=\".products-list\"></span>
                                            </div>
                                            <div class=\"product-price block\">
                                                <p class=\"price red-color edited ie-radius\">
                                                    <strong>26 000 <span class=\"icon-rouble\"></span></strong>
                                                </p>
                                                <p class=\"vol edited ie-radius\">за тонну</p>
                                            </div>
                                            <div class=\"product-status block\">
                                                <span class=\"complete icon-check-big clickable product-status-link\"></span>
                                                <span class=\"icon-pencil clickable\"></span>
                                                <span class=\"delete-btn clickable g-hidden\">
                                                    <span class=\"icon-filter-del\"></span>
                                                </span>
                                            </div>
                                        </div>
                                        <div class=\"change-holder item-block clearfix g-hidden\">
                                            <div class=\"check-all block\"></div>
                                            <div class=\"product-photo block\">
                                                <div class=\"img is-bordered\">
                                                    <a class=\"pattern-small\" href=\"#\"></a>
                                                </div>
                                            </div>
                                            <div class=\"product-name block\">
                                                <input type=\"text\" class=\"product form-text ie-radius\" placeholder=\"Шестигранник калибров...\"/>
                                                <a href=\"#\" class=\"category link js-popover-opener ie-radius\" data-popover=\"#choose-section\" data-left=\"0\" data-top=\"-20\">Арматура</a>
                                            </div>
                                            <div class=\"product-size block\">
                                                <input type=\"text\" placeholder=\"20x30\" class=\"size form-text ie-radius\"/>
                                                <p class=\"showcase text\">
                                                    <input id=\"showcase-input\" type=\"checkbox\" class=\"checkbox bg-grey js-styled-checkbox\"/>
                                                    <label for=\"showcase-input\">сп</label>
                                                </p>
                                                <span class=\"icon-help js-helper-opener\"
                                                      data-text=\"Спецпредложение будет отображаться на главной странице вашего сайта\"
                                                      data-absolute-parent=\".products-list\"></span>
                                            </div>
                                            <div class=\"product-price block\">
                                                <input class=\"price form-text red-color ie-radius\" type=\"text\" placeholder=\"26 000 руб.\"/>
                                                <select id=\"product-volume\" class=\"vol form-select\">
                                                    <option value=\"0\">&nbsp;</option>
                                                    <option value=\"1\" selected=\"selected\">за тонну</option>
                                                    <option value=\"2\">за кг</option>
                                                    <option value=\"3\">за ведро</option>
                                                </select>
                                            </div>
                                            <div class=\"product-status block\">
                                                <button class=\"ok-btn\" type=\"submit\">ok</button>
                                            </div>
                                            <div class=\"product-description-wrapper\">
                                                <textarea class=\"product-description form-textarea ie-radius placeholder\" placeholder=\"Описание продукта\"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class=\"item\">
                                        <div class=\"fix-holder item-block clearfix\">
                                            <div class=\"check-all block\">
                                                <input type=\"checkbox\" class=\"checkbox js-styled-checkbox\"/>
                                            </div>
                                            <div class=\"product-photo block\">
                                                <div class=\"img is-bordered edited\">
                                                    <a class=\"pattern-small\" href=\"#\"></a>
                                                </div>
                                            </div>
                                            <div class=\"product-name block\">
                                                <strong class=\"product edited ie-radius\">Шестигранник калибров...</strong>
                                                <span class=\"category edited ie-radius\">Арматура</span>
                                                <div class=\"product-description-wrapper\">
                                                    <p class=\"product-description is-gradiented-bottom ie-radius edited ng-binding\">
                                                        Получите трубу 146х25 в максимально возможные короткие сроки. Труба 146х25 будет изготовлена из стали 20 по ГОСТ 8732-78 за 15-30 дней.
                                                    </p>
                                                </div>
                                            </div>
                                            <div class=\"product-size block\">
                                                <p class=\"size edited ie-radius\">20x30</p>
                                                <p class=\"text edited ie-radius\">сп</p>
                                            </div>
                                            <div class=\"product-price block\">
                                                <p class=\"price red-color edited ie-radius\">
                                                    <strong>26 000 <span class=\"icon-rouble\"></span></strong>
                                                </p>
                                                <p class=\"vol edited ie-radius\">за тонну</p>
                                            </div>
                                            <div class=\"product-status block\">
                                                <span class=\"process icon-clock clickable product-status-link\"></span>
                                                <span class=\"icon-pencil clickable\"></span>
                                                <span class=\"delete-btn clickable g-hidden\">
                                                    <span class=\"icon-filter-del\"></span>
                                                </span>
                                            </div>
                                        </div>
                                        <div class=\"change-holder item-block clearfix g-hidden\">
                                            <div class=\"check-all block\"></div>
                                            <div class=\"product-photo block\">
                                                <div class=\"img is-bordered\">
                                                    <a class=\"pattern-small\" href=\"#\"></a>
                                                </div>
                                            </div>
                                            <div class=\"product-name block\">
                                                <input type=\"text\" class=\"product form-text ie-radius\" placeholder=\"Шестигранник калибров...\"/>
                                                <a href=\"#\" class=\"category link js-popover-opener ie-radius\" data-popover=\"#choose-section\" data-left=\"0\" data-top=\"-20\">Арматура</a>
                                            </div>
                                            <div class=\"product-size block\">
                                                <input type=\"text\" placeholder=\"20x30\" class=\"size form-text ie-radius\"/>
                                            </div>
                                            <div class=\"product-price block\">
                                                <input class=\"price form-text red-color ie-radius\" type=\"text\" placeholder=\"26 000 руб.\"/>
                                                <select id=\"product-volume\" class=\"vol form-select\">
                                                    <option value=\"0\">&nbsp;</option>
                                                    <option value=\"1\">за тонну</option>
                                                    <option value=\"2\">за кг</option>
                                                    <option value=\"3\">за ведро</option>
                                                </select>
                                            </div>
                                            <div class=\"product-status block\">
                                                <button class=\"ok-btn\" type=\"submit\">ok</button>
                                            </div>
                                            <div class=\"product-description-wrapper\">
                                                <textarea class=\"product-description form-textarea ie-radius placeholder\" placeholder=\"Описание продукта\"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class=\"item\">
                                        <div class=\"fix-holder item-block clearfix\">
                                            <div class=\"check-all block\">
                                                <input type=\"checkbox\" class=\"checkbox js-styled-checkbox\"/>
                                            </div>
                                            <div class=\"product-photo block\">
                                                <div class=\"img is-bordered edited\">
                                                    <a class=\"pattern-small\" href=\"#\"></a>
                                                </div>
                                            </div>
                                            <div class=\"product-name block\">
                                                <strong class=\"product edited ie-radius\">Шестигранник калибров...</strong>
                                                <span class=\"category edited ie-radius\">Арматура</span>
                                                <div class=\"product-description-wrapper\">
                                                    <p class=\"product-description is-gradiented-bottom ie-radius edited ng-binding\">
                                                        Получите трубу 146х25 в максимально возможные короткие сроки. Труба 146х25 будет изготовлена из стали 20 по ГОСТ 8732-78 за 15-30 дней.
                                                    </p>
                                                </div>
                                            </div>
                                            <div class=\"product-size block\">
                                                <p class=\"size edited ie-radius\">20x30</p>
                                            </div>
                                            <div class=\"product-price block\">
                                                <p class=\"price red-color edited ie-radius\">
                                                    <strong>26 000 <span class=\"icon-rouble\"></span></strong>
                                                </p>
                                                <p class=\"vol edited ie-radius\">за тонну</p>
                                            </div>
                                            <div class=\"product-status block\">
                                                <span class=\"process icon-clock clickable product-status-link\"></span>
                                                <span class=\"delete-btn clickable g-hidden\">
                                                    <span class=\"icon-filter-del\"></span>
                                                </span>
                                            </div>
                                        </div>
                                        <div class=\"change-holder item-block clearfix g-hidden\">
                                            <div class=\"check-all block\"></div>
                                            <div class=\"product-photo block\">
                                                <div class=\"img is-bordered\">
                                                    <a class=\"pattern-small\" href=\"#\"></a>
                                                </div>
                                            </div>
                                            <div class=\"product-name block\">
                                                <input type=\"text\" class=\"product form-text ie-radius\" placeholder=\"Шестигранник калибров...\"/>
                                                <a href=\"#\" class=\"category link js-popover-opener ie-radius\" data-popover=\"#choose-section\" data-left=\"0\" data-top=\"-20\">Арматура</a>
                                            </div>
                                            <div class=\"product-size block\">
                                                <input type=\"text\" placeholder=\"20x30\" class=\"size form-text ie-radius\"/>
                                            </div>
                                            <div class=\"product-price block\">
                                                <input class=\"price form-text red-color ie-radius\" type=\"text\" placeholder=\"26 000 руб.\"/>
                                                <select id=\"product-volume\" class=\"vol form-select\">
                                                    <option value=\"0\">&nbsp;</option>
                                                    <option value=\"1\">за тонну</option>
                                                    <option value=\"2\">за кг</option>
                                                    <option value=\"3\">за ведро</option>
                                                </select>
                                            </div>
                                            <div class=\"product-status block\">
                                                <button class=\"ok-btn\" type=\"submit\">ok</button>
                                            </div>
                                            <div class=\"product-description-wrapper\">
                                                <textarea class=\"product-description form-textarea ie-radius placeholder\" placeholder=\"Описание продукта\"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class=\"item\">
                                        <div class=\"fix-holder item-block clearfix\">
                                            <div class=\"check-all block\">
                                                <input type=\"checkbox\" class=\"checkbox js-styled-checkbox\"/>
                                            </div>
                                            <div class=\"product-photo block\">
                                                <div class=\"img is-bordered edited\">
                                                    <a class=\"pattern-small\" href=\"#\"></a>
                                                </div>
                                            </div>
                                            <div class=\"product-name block\">
                                                <strong class=\"product edited ie-radius\">Шестигранник калибров...</strong>
                                                <span class=\"category edited ie-radius\">Арматура</span>
                                                <div class=\"product-description-wrapper\">
                                                    <p class=\"product-description is-gradiented-bottom ie-radius edited ng-binding\">
                                                        Получите трубу 146х25 в максимально возможные короткие сроки. Труба 146х25 будет изготовлена из стали 20 по ГОСТ 8732-78 за 15-30 дней.
                                                    </p>
                                                </div>
                                            </div>
                                            <div class=\"product-size block\">
                                                <p class=\"size edited ie-radius\">20x30</p>
                                            </div>
                                            <div class=\"product-price block\">
                                                <p class=\"price red-color edited ie-radius\">
                                                    <strong>26 000 <span class=\"icon-rouble\"></span></strong>
                                                </p>
                                                <p class=\"vol edited ie-radius\">за тонну</p>
                                            </div>
                                            <div class=\"product-status block\">
                                                <span class=\"process icon-clock clickable product-status-link\"></span>
                                                <span class=\"delete-btn clickable g-hidden\">
                                                    <span class=\"icon-filter-del\"></span>
                                                </span>
                                            </div>
                                        </div>
                                        <div class=\"change-holder item-block clearfix g-hidden\">
                                            <div class=\"check-all block\"></div>
                                            <div class=\"product-photo block\">
                                                <div class=\"img is-bordered\">
                                                    <a class=\"pattern-small\" href=\"#\"></a>
                                                </div>
                                            </div>
                                            <div class=\"product-name block\">
                                                <input type=\"text\" class=\"product form-text ie-radius\" placeholder=\"Шестигранник калибров...\"/>
                                                <a href=\"#\" class=\"category link js-popover-opener ie-radius\" data-popover=\"#choose-section\" data-left=\"0\" data-top=\"-20\">Арматура</a>
                                            </div>
                                            <div class=\"product-size block\">
                                                <input type=\"text\" placeholder=\"20x30\" class=\"size form-text ie-radius\"/>
                                            </div>
                                            <div class=\"product-price block\">
                                                <input class=\"price form-text red-color ie-radius\" type=\"text\" placeholder=\"26 000 руб.\"/>
                                                <select id=\"product-volume\" class=\"vol form-select\">
                                                    <option value=\"0\">&nbsp;</option>
                                                    <option value=\"1\">за тонну</option>
                                                    <option value=\"2\">за кг</option>
                                                    <option value=\"3\">за ведро</option>
                                                </select>
                                            </div>
                                            <div class=\"product-status block\">
                                                <button class=\"ok-btn\" type=\"submit\">ok</button>
                                            </div>
                                            <div class=\"product-description-wrapper\">
                                                <textarea class=\"product-description form-textarea ie-radius placeholder\" placeholder=\"Описание продукта\"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class=\"item\">
                                        <div class=\"fix-holder item-block clearfix\">
                                            <div class=\"check-all block\">
                                                <input type=\"checkbox\" class=\"checkbox js-styled-checkbox\"/>
                                            </div>
                                            <div class=\"product-photo block\">
                                                <div class=\"img is-bordered edited\">
                                                    <a class=\"pattern-small\" href=\"#\"></a>
                                                </div>
                                            </div>
                                            <div class=\"product-name block\">
                                                <strong class=\"product edited ie-radius\">Шестигранник калибров...</strong>
                                                <span class=\"category edited ie-radius\">Арматура</span>
                                                <div class=\"product-description-wrapper\">
                                                    <p class=\"product-description is-gradiented-bottom ie-radius edited ng-binding\">
                                                        Получите трубу 146х25 в максимально возможные короткие сроки. Труба 146х25 будет изготовлена из стали 20 по ГОСТ 8732-78 за 15-30 дней.
                                                    </p>
                                                </div>
                                            </div>
                                            <div class=\"product-size block\">
                                                <p class=\"size edited ie-radius\">20x30</p>
                                            </div>
                                            <div class=\"product-price block\">
                                                <p class=\"price red-color edited ie-radius\">
                                                    <strong>26 000 <span class=\"icon-rouble\"></span></strong>
                                                </p>
                                                <p class=\"vol edited ie-radius\">за тонну</p>
                                            </div>
                                            <div class=\"product-status block\">
                                                <span class=\"process icon-clock clickable product-status-link\"></span>
                                                <span class=\"delete-btn clickable g-hidden\">
                                                    <span class=\"icon-filter-del\"></span>
                                                </span>
                                            </div>
                                        </div>
                                        <div class=\"change-holder item-block clearfix g-hidden\">
                                            <div class=\"check-all block\"></div>
                                            <div class=\"product-photo block\">
                                                <div class=\"img is-bordered\">
                                                    <a class=\"pattern-small\" href=\"#\"></a>
                                                </div>
                                            </div>
                                            <div class=\"product-name block\">
                                                <input type=\"text\" class=\"product form-text ie-radius\" placeholder=\"Шестигранник калибров...\"/>
                                                <a href=\"#\" class=\"category link js-popover-opener ie-radius\" data-popover=\"#choose-section\" data-left=\"0\" data-top=\"-20\">Арматура</a>
                                            </div>
                                            <div class=\"product-size block\">
                                                <input type=\"text\" placeholder=\"20x30\" class=\"size form-text ie-radius\"/>
                                            </div>
                                            <div class=\"product-price block\">
                                                <input class=\"price form-text red-color ie-radius\" type=\"text\" placeholder=\"26 000 руб.\"/>
                                                <select id=\"product-volume\" class=\"vol form-select\">
                                                    <option value=\"0\">&nbsp;</option>
                                                    <option value=\"1\">за тонну</option>
                                                    <option value=\"2\">за кг</option>
                                                    <option value=\"3\">за ведро</option>
                                                </select>
                                            </div>
                                            <div class=\"product-status block\">
                                                <button class=\"ok-btn\" type=\"submit\">ok</button>
                                            </div>
                                            <div class=\"product-description-wrapper\">
                                                <textarea class=\"product-description form-textarea ie-radius placeholder\" placeholder=\"Описание продукта\"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class=\"item\">
                                        <div class=\"fix-holder item-block clearfix\">
                                            <div class=\"check-all block\">
                                                <input type=\"checkbox\" class=\"checkbox js-styled-checkbox\"/>
                                            </div>
                                            <div class=\"product-photo block\">
                                                <div class=\"img is-bordered edited\">
                                                    <a class=\"pattern-small\" href=\"#\"></a>
                                                </div>
                                            </div>
                                            <div class=\"product-name block\">
                                                <strong class=\"product edited ie-radius\">Шестигранник калибров...</strong>
                                                <span class=\"category edited ie-radius\">Арматура</span>
                                                <div class=\"product-description-wrapper\">
                                                    <p class=\"product-description is-gradiented-bottom ie-radius edited ng-binding\">
                                                        Получите трубу 146х25 в максимально возможные короткие сроки. Труба 146х25 будет изготовлена из стали 20 по ГОСТ 8732-78 за 15-30 дней.
                                                    </p>
                                                </div>
                                            </div>
                                            <div class=\"product-size block\">
                                                <p class=\"size edited ie-radius\">20x30</p>
                                            </div>
                                            <div class=\"product-price block\">
                                                <p class=\"price red-color edited ie-radius\">
                                                    <strong>26 000 <span class=\"icon-rouble\"></span></strong>
                                                </p>
                                                <p class=\"vol edited ie-radius\">за тонну</p>
                                            </div>
                                            <div class=\"product-status block\">
                                                <span class=\"process icon-clock clickable product-status-link\"></span>
                                                <span class=\"delete-btn clickable g-hidden\">
                                                    <span class=\"icon-filter-del\"></span>
                                                </span>
                                            </div>
                                        </div>
                                        <div class=\"change-holder item-block clearfix g-hidden\">
                                            <div class=\"check-all block\"></div>
                                            <div class=\"product-photo block\">
                                                <div class=\"img is-bordered\">
                                                    <a class=\"pattern-small\" href=\"#\"></a>
                                                </div>
                                            </div>
                                            <div class=\"product-name block\">
                                                <input type=\"text\" class=\"product form-text ie-radius\" placeholder=\"Шестигранник калибров...\"/>
                                                <a href=\"#\" class=\"category link js-popover-opener ie-radius\" data-popover=\"#choose-section\" data-left=\"0\" data-top=\"-20\">Арматура</a>
                                            </div>
                                            <div class=\"product-size block\">
                                                <input type=\"text\" placeholder=\"20x30\" class=\"size form-text ie-radius\"/>
                                            </div>
                                            <div class=\"product-price block\">
                                                <input class=\"price form-text red-color ie-radius\" type=\"text\" placeholder=\"26 000 руб.\"/>
                                                <select id=\"product-volume\" class=\"vol form-select\">
                                                    <option value=\"0\">&nbsp;</option>
                                                    <option value=\"1\">за тонну</option>
                                                    <option value=\"2\">за кг</option>
                                                    <option value=\"3\">за ведро</option>
                                                </select>
                                            </div>
                                            <div class=\"product-status block\">
                                                <button class=\"ok-btn\" type=\"submit\">ok</button>
                                            </div>
                                            <div class=\"product-description-wrapper\">
                                                <textarea class=\"product-description form-textarea ie-radius placeholder\" placeholder=\"Описание продукта\"></textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <div class=\"loading-mask-wrapper\">
                                        <div class=\"loading-mask big\">
                                            <div class=\"spinner\"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class=\"photo-list float-left\">
                            <div class=\"heading\">
                                <div class=\"search-photo block\">
                                    <input type=\"text\" class=\"search form-text ie-radius\" placeholder=\"поиск по фото\"/>
                                    <button type=\"submit\" class=\"icon-search-small search-button\"></button>
                                </div>
                            </div>
                            <div class=\"photos\">
                                <div class=\"photo-container js-scrollable\">
                                    <div class=\"clearfix\">
                                        <div class=\"img is-bordered item float-left\">
                                            <a class=\"pattern-small\" href=\"#\">
                                                <img src=\"./markup/pic/small-img.jpg\" alt=\"image description\"/>
                                            </a>
                                        </div>
                                        <div class=\"img is-bordered item float-left\">
                                            <a class=\"pattern-small\" href=\"#\"></a>
                                        </div>
                                        <div class=\"img is-bordered item float-left\">
                                            <a class=\"pattern-small\" href=\"#\"></a>
                                        </div>
                                        <div class=\"img is-bordered item float-left\">
                                            <a class=\"pattern-small\" href=\"#\"></a>
                                        </div>
                                        <div class=\"special-title float-left empty-text\">
                                            <p class=\"text\"></p>
                                        </div>
                                        <div class=\"img is-bordered item float-left\">
                                            <a class=\"pattern-small\" href=\"#\"></a>
                                        </div>
                                        <div class=\"img is-bordered item current float-left\">
                                            <a class=\"pattern-small\" href=\"#\">
                                                <img src=\"./markup/pic/small-img.jpg\" alt=\"image description\"/>
                                            </a>
                                        </div>
                                        <div class=\"img is-bordered item float-left\">
                                            <a class=\"pattern-small\" href=\"#\"></a>
                                        </div>
                                        <div class=\"img is-bordered item float-left\">
                                            <a class=\"pattern-small\" href=\"#\"></a>
                                        </div>
                                        <div class=\"img is-bordered item float-left\">
                                            <a class=\"pattern-small\" href=\"#\"></a>
                                        </div>
                                        <div class=\"img is-bordered item float-left\">
                                            <a class=\"pattern-small\" href=\"#\"></a>
                                        </div>
                                        <div class=\"img is-bordered item float-left\">
                                            <a class=\"pattern-small\" href=\"#\"></a>
                                        </div>
                                        <div class=\"img is-bordered item float-left\">
                                            <a class=\"pattern-small\" href=\"#\"></a>
                                        </div>
                                        <div class=\"img is-bordered item float-left\">
                                            <a class=\"pattern-small\" href=\"#\"></a>
                                        </div>
                                        <div class=\"img is-bordered item float-left\">
                                            <a class=\"pattern-small\" href=\"#\"></a>
                                        </div>
                                        <div class=\"img is-bordered item float-left\">
                                            <a class=\"pattern-small\" href=\"#\"></a>
                                        </div>
                                        <div class=\"img is-bordered item float-left\">
                                            <a class=\"pattern-small\" href=\"#\"></a>
                                        </div>
                                        <div class=\"img is-bordered item float-left\">
                                            <a class=\"pattern-small\" href=\"#\"></a>
                                        </div>
                                        <div class=\"img is-bordered item float-left\">
                                            <a class=\"pattern-small\" href=\"#\"></a>
                                        </div>
                                        <div class=\"img is-bordered item float-left\">
                                            <a class=\"pattern-small\" href=\"#\"></a>
                                        </div>
                                        <div class=\"img is-bordered item float-left\">
                                            <a class=\"pattern-small\" href=\"#\"></a>
                                        </div>
                                        <div class=\"img is-bordered item float-left\">
                                            <a class=\"pattern-small\" href=\"#\"></a>
                                        </div>
                                        <div class=\"img is-bordered item float-left\">
                                            <a class=\"pattern-small\" href=\"#\"></a>
                                        </div>
                                        <div class=\"img is-bordered item float-left\">
                                            <a class=\"pattern-small\" href=\"#\"></a>
                                        </div>
                                        <div class=\"img is-bordered item float-left\">
                                            <a class=\"pattern-small\" href=\"#\"></a>
                                        </div>
                                        <div class=\"img is-bordered item float-left\">
                                            <a class=\"pattern-small\" href=\"#\"></a>
                                        </div>
                                        <div class=\"img is-bordered item float-left\">
                                            <a class=\"pattern-small\" href=\"#\"></a>
                                        </div>
                                        <div class=\"img is-bordered item float-left\">
                                            <a class=\"pattern-small\" href=\"#\"></a>
                                        </div>
                                    </div>


                                    <div class=\"submit-wrapper\">
                                        <span class=\"see-more button\">Показать еще...</span>
                                    </div>
                                </div>
                                <div class=\"bottom-shadow\"></div>
                            </div>
                            <div class=\"big-photo\">
                                <div class=\"img-wrapper is-bordered\">
                                    <a class=\"pattern-big\" href=\"#\">
                                        <img src=\"./markup/pic/p-logo3.jpg\" alt=\"image description\"/>
                                    </a>
                                    <div class=\"photo-btns-wrapper\">
                                        <div class=\"add-photo-wrapper float-left\">
                                            <label class=\"file-upload with-icon js-popup-opener\">
                                                <span class=\"add-photo-link ico-upload\"></span>
                                            </label>

                                        </div>
                                        <span class=\"del-btn ico-delete-btn float-left clickable ng-scope\"></span>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class=\"bottom-shadow\"></div>
                    </div>
                    <fieldset class=\"bottom-block clearfix\">
                        <p class=\"choose-products float-left\">Выбрано 18 товаров из 2832</p>
                        <ul class=\"float-right\">
                            <li class=\"delete-btn item float-left\">
                                <button type=\"submit\" class=\"delete button red-bg js-popup-opener ie-radius\" data-popup=\"#delete-product\">Удалить</button>
                            </li>
                            <li class=\"replace-btn item float-left\">
                                <button type=\"submit\" class=\"replace button white-bg is-bordered js-popover-opener ie-radius\" data-popover=\"#choose-section\" data-left=\"0\" data-top=\"250\">Перенести в раздел</button>
                            </li>
                            <li class=\"change-btn item float-left\">
                                <button type=\"button\" class=\"replace button white-bg is-bordered js-popover-opener ie-radius\" data-popover=\"#change-special\">Изменить СП</button>
                                <div id=\"change-special\" class=\"change-special drop-wrapper popover-block opacity-border\">
                                    <ul class=\"dropdown\">
                                        <li class=\"drop-item\">
                                            <a href=\"#\" class=\"drop-link\">Сделать спецпредложением</a>
                                        </li>
                                        <li class=\"drop-item\">
                                            <a href=\"#\" class=\"drop-link\">Убрать спецпредложение</a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                            <li class=\"photo-btn-wrapper item float-left\">
                                <button type=\"submit\" class=\"photo-btn button white-bg is-bordered clearfix ie-radius\">
                                    <span class=\"text float-left\">Связать с фото</span>
                                    <span class=\"photo-ico float-right\"></span>
                                </button>
                            </li>
                        </ul>
                        <ul class=\"float-right g-hidden\">
                            <li class=\"delete-btn item float-left\">
                                <button disabled=\"disabled\" type=\"submit\" class=\"delete button red-bg js-popup-opener ie-radius\" data-popup=\"#delete-product\">Удалить</button>
                            </li>
                            <li class=\"replace-btn item float-left\">
                                <button disabled=\"disabled\" type=\"submit\" class=\"replace button white-bg is-bordered js-popover-opener ie-radius\" data-popover=\"#choose-section\">Перенести в раздел</button>
                            </li>
                            <li class=\"photo-btn-wrapper item float-left\">
                                <button type=\"submit\" class=\"photo-btn button white-bg is-bordered clearfix ie-radius\" disabled=\"disabled\">
                                    <span class=\"text float-left\">Связать с фото</span>
                                    <span class=\"photo-ico float-right\"></span>
                                </button>
                            </li>
                        </ul>
                    </fieldset>
                </form>
                ";
        // line 997
        echo "                ";
        // line 998
        echo "                    ";
        // line 999
        echo "                ";
        // line 1000
        echo "            </div>
        </div>


    ";
    }

    // line 22
    public function block_tabs($context, array $blocks = array())
    {
        // line 23
        echo "                ";
        $context["activeTab"] = "products";
        // line 24
        echo "                ";
        $this->displayParentBlock("tabs", $context, $blocks);
        echo "
            ";
    }

    public function getTemplateName()
    {
        return "@markup/private/management/products.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  1057 => 24,  1054 => 23,  1051 => 22,  1043 => 1000,  1041 => 999,  1039 => 998,  1037 => 997,  271 => 232,  64 => 26,  62 => 22,  59 => 21,  56 => 20,  40 => 4,  37 => 3,  31 => 2,);
    }
}
