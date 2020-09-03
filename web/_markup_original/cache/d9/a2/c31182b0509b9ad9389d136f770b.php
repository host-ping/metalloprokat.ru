<?php

/* @markup/ui/_components/datepicker.html.twig */
class __TwigTemplate_d9a2c31182b0509b9ad9389d136f770b extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        echo "<script id=\"calendar-template\" type=\"text/html\">
    <div class=\"heading white-bg\">
        <div class=\"date icon-calendar\"><%= selectedDate.format('D MMM YYYY') %></div>
    </div>
    <div class=\"calendar-wrapper white95-bg\">
        <div class=\"calendar-navigation clearfix\">
            <div class=\"month float-left\">
                <ul class=\"month-list\">
                    <li class=\"item\"><%= _.str.capitalize(month) %></li>
                </ul>
                <div class=\"nav-btn\">
                    <span class=\"prev btn clndr-previous-button\">prev</span>
                    <span class=\"next btn clndr-next-button\">next</span>
                </div>
            </div>
            <div class=\"year float-right\">
                <ul class=\"year-list\">
                    <li class=\"item\"><%= year %></li>
                </ul>
                <div class=\"nav-btn\">
                    <span class=\"prev btn clndr-previous-year-button\">prev</span>
                    <span class=\"next btn clndr-next-year-button\">next</span>
                </div>
            </div>
        </div>
        <table class=\"calendar\">
            <thead>
                <tr class=\"row\">
                    <% _.each(daysOfTheWeek, function(day) { %>
                        <th class=\"col\"><%= day %></th>
                    <% }); %>
                </tr>
            </thead>
            <tbody>
                <% for (var i = 0; i < numberOfRows; i++) { %>
                    <tr class=\"week row\">
                        <% for (var j = 0; j < 7; j++){ %>
                            <% var d = j + i * 7; %>
                            <% var dayTs = days[d].date.format('YYYY-MM-DD'); %>
                            <% var selectedDayTs = selectedDate.format('YYYY-MM-DD'); %>
                            <% var rowClass = ''; %>
                            <% if (j == 0) { %>
                                <% rowClass += ' first'; %>
                            <% } %>
                            <% if (j == 6) { %>
                                <% rowClass += ' last'; %>
                            <% } %>

                            <% if (!relatedDatepickerDate) { %>
                                <% if (dayTs == selectedDayTs) { %>
                                    <% rowClass += ' in-range'; %>
                                    <% rowClass += ' first-range'; %>
                                    <% rowClass += ' last-range'; %>
                                <% } %>
                            <% } else { %>
                                <% var relatedSelectedDayTs = relatedDatepickerDate.format('YYYY-MM-DD'); %>

                                <% if (relatedDatepickerMode == 'finish') { %>
                                    <% if (dayTs >= selectedDayTs && dayTs <= relatedSelectedDayTs) { %>
                                        <% rowClass += ' in-range'; %>

                                        <% if (dayTs == selectedDayTs) { %>
                                            <% rowClass += ' first-range'; %>
                                        <% } %>
                                        <% if (dayTs == relatedSelectedDayTs) { %>
                                            <% rowClass += ' last-range'; %>
                                        <% } %>
                                    <% } %>
                                <% } else { %>
                                    <% if (dayTs <= selectedDayTs && dayTs >= relatedSelectedDayTs) { %>
                                        <% rowClass += ' in-range'; %>

                                        <% if (dayTs == selectedDayTs) { %>
                                            <% rowClass += ' last-range'; %>
                                        <% } %>
                                        <% if (dayTs == relatedSelectedDayTs) { %>
                                            <% rowClass += ' first-range'; %>
                                        <% } %>
                                    <% } %>
                                <% } %>

                            <% } %>

                            <% if (days[d].classes.indexOf('today') > -1) { %>
                                <% rowClass += ' current'; %>
                            <% } %>

                            <% if (days[d].classes.indexOf('adjacent-month') > -1) { %>
                                <td class=\"col disabled <%= rowClass %>\">
                                    <%= days[d].day %>
                                </td>
                            <% } else { %>
                                <td class=\"col <%= rowClass %>\">
                                    <div class=\"elem\">
                                        <span class=\"link ie-radius <%= days[d].classes %>\"><%= days[d].day %></span>
                                    </div>
                                </td>
                            <% } %>
                        <% } %>
                    </tr>
                <% } %>
            </tbody>
        </table>
    </div>
</script>
";
    }

    public function getTemplateName()
    {
        return "@markup/ui/_components/datepicker.html.twig";
    }

    public function getDebugInfo()
    {
        return array (  19 => 1,  519 => 493,  472 => 447,  26 => 2,  20 => 1,  380 => 172,  377 => 171,  371 => 168,  365 => 165,  359 => 159,  353 => 155,  347 => 153,  341 => 152,  337 => 154,  334 => 153,  331 => 152,  328 => 151,  316 => 148,  310 => 147,  304 => 145,  298 => 144,  293 => 141,  283 => 135,  280 => 134,  274 => 130,  270 => 168,  266 => 166,  264 => 165,  257 => 160,  255 => 159,  250 => 156,  247 => 155,  245 => 151,  242 => 150,  239 => 149,  236 => 148,  234 => 147,  231 => 146,  225 => 144,  222 => 143,  220 => 140,  217 => 139,  215 => 134,  210 => 131,  208 => 130,  187 => 103,  180 => 87,  178 => 86,  143 => 33,  140 => 32,  111 => 174,  106 => 170,  104 => 125,  81 => 105,  62 => 91,  60 => 32,  52 => 27,  50 => 9,  599 => 389,  596 => 388,  590 => 378,  546 => 336,  543 => 335,  537 => 334,  533 => 377,  530 => 335,  527 => 334,  524 => 333,  515 => 326,  512 => 325,  507 => 323,  505 => 322,  503 => 321,  501 => 320,  499 => 319,  497 => 318,  493 => 315,  490 => 314,  483 => 156,  324 => 160,  322 => 149,  318 => 153,  315 => 152,  294 => 132,  292 => 131,  290 => 140,  288 => 129,  286 => 128,  284 => 127,  278 => 122,  275 => 121,  233 => 78,  229 => 145,  226 => 75,  202 => 126,  199 => 125,  193 => 105,  185 => 42,  182 => 41,  177 => 47,  175 => 41,  149 => 17,  146 => 16,  142 => 433,  138 => 431,  136 => 388,  125 => 379,  122 => 378,  114 => 325,  112 => 314,  109 => 171,  107 => 152,  103 => 150,  100 => 121,  96 => 119,  94 => 118,  90 => 116,  88 => 75,  85 => 74,  82 => 52,  80 => 51,  76 => 103,  74 => 16,  66 => 12,  63 => 11,  58 => 8,  55 => 29,  48 => 4,  45 => 3,  482 => 430,  480 => 155,  478 => 428,  130 => 81,  127 => 80,  120 => 10,  117 => 9,  46 => 12,  44 => 7,  40 => 1,  37 => 4,  31 => 3,);
    }
}
