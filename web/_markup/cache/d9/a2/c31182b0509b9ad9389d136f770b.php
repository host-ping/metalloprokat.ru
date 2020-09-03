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
        return array (  19 => 1,  518 => 492,  471 => 446,  26 => 2,  20 => 1,  387 => 177,  384 => 176,  378 => 173,  372 => 170,  366 => 164,  360 => 160,  354 => 158,  348 => 157,  344 => 159,  341 => 158,  338 => 157,  335 => 156,  329 => 154,  323 => 153,  305 => 149,  300 => 146,  297 => 145,  290 => 140,  287 => 139,  281 => 135,  277 => 173,  273 => 171,  271 => 170,  264 => 165,  262 => 164,  257 => 161,  254 => 160,  252 => 156,  243 => 153,  241 => 152,  238 => 151,  236 => 150,  232 => 149,  229 => 148,  227 => 145,  224 => 144,  222 => 139,  217 => 136,  215 => 135,  209 => 131,  206 => 130,  200 => 110,  194 => 108,  187 => 92,  185 => 91,  148 => 36,  145 => 35,  120 => 8,  117 => 7,  108 => 179,  106 => 176,  103 => 175,  78 => 110,  73 => 108,  59 => 96,  57 => 35,  50 => 30,  48 => 7,  40 => 1,  392 => 226,  389 => 225,  383 => 215,  339 => 173,  336 => 172,  330 => 171,  324 => 170,  320 => 214,  317 => 152,  314 => 171,  311 => 150,  308 => 169,  302 => 165,  299 => 164,  249 => 155,  246 => 154,  216 => 86,  213 => 85,  170 => 44,  167 => 43,  159 => 34,  156 => 33,  152 => 39,  150 => 33,  136 => 21,  133 => 20,  129 => 270,  125 => 268,  123 => 225,  112 => 216,  109 => 215,  107 => 169,  104 => 168,  101 => 130,  99 => 116,  94 => 113,  92 => 85,  89 => 84,  87 => 43,  82 => 40,  80 => 20,  72 => 16,  69 => 15,  63 => 13,  58 => 10,  55 => 9,  46 => 4,  43 => 3,  31 => 4,  28 => 3,);
    }
}
