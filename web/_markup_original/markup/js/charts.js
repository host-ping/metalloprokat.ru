(function (Highcharts) {
    Highcharts.wrap(Highcharts.seriesTypes.pie.prototype, 'render', function (proceed) {

        var chart = this.chart,
            center = this.center || (this.yAxis && this.yAxis.center),
            titleOption = this.options.title,
            box;

        proceed.call(this);

        if (center && titleOption) {
            box = {
                x: chart.plotLeft + center[0] - 0.5 * center[2],
                y: chart.plotTop + center[1] - 0.5 * center[2],
                width: center[2],
                height: center[2]
            };
            if (!this.title) {
                this.title = this.chart.renderer.label(titleOption.text)
                    .css(titleOption.style)
                    .add()
                    .align(titleOption, null, box);
            } else {
                this.title.align(titleOption, null, box);
            }
        }
    });

}(Highcharts));

(function ($) {
    $(function () {
        if ($('#line-chart').length) {
            var chart = new Highcharts.Chart({
                chart: {
                    renderTo: 'line-chart'
                },
                rangeSelector: {
                    enabled: false
                },
                buttons: {
                    enabled: false
                },
                exporting: {
                    enabled: false
                },
                navigator: {
                    enabled: false
                },
                scrollbar: {
                    enabled: false
                },
                legend: {
                    enabled: false
                },
                plotOptions: {
                    series: {
                        marker: {
                            enabled: false
                        }
                    }
                },
                colors: [
                    '#e35341',
                    '#d07a17',
                    '#9c8117',
                    '#6a9011',
                    '#00984b',
                    '#189293',
                    '#488ac0',
                    '#9973bf',
                    '#be6aa3',
                    '#848484'
                ],
                title: {
                    text: ''
                },
                tooltip: {
                    shared: true,
                    crosshairs: [{
                        width: 1,
                        color: '#c6c6c6'

                    }]

                },
                xAxis: {
                    categories: ['янв 2014', 'фев 2014', 'мар 2014', 'апр 2014', 'май 2014', 'июн 2014', 'июл 2014',
                        'авг 2014', 'сент 2014', 'окт 2014', 'ноя 2014', 'дек 2014']
                },
                yAxis: {
                    opposite: true,
                    title: '',
                    min: 0
                },
                series: [
                    {
                        name: 'Просмотры товаров',
                        data: [5, 8, 3, 50, 501, 23, 15, 48, 78, 360],
                        type: 'spline',
                        marker: {
                            symbol: 'circle'
                        }
                    },
                    {
                        name: 'Переходы на сайт',
                        data: [65, 78, 300, 55, 510, 2, 45, 7, 22, 44],
                        type: 'spline',
                        marker: {
                            symbol: 'circle'
                        }
                    },
                    {
                        name: 'Просмотры телефонов',
                        data: [52, 18, 37, 50, 1, 30, 100, 40, 8, 6],
                        type: 'spline',
                        marker: {
                            symbol: 'circle'
                        }
                    },
                    {
                        name: 'Заявки пришло',
                        data: [75, 18, 13, 10, 1, 3, 5, 8, 8, 6],
                        type: 'spline',
                        marker: {
                            symbol: 'circle'
                        }
                    },
                    {
                        name: 'Заявки обработано',
                        data: [5, 80, 3, 50, 51, 23, 150, 48, 78, 36],
                        type: 'spline',
                        marker: {
                            symbol: 'circle'
                        }
                    },
                    {
                        name: 'Обратные звонки пришло',
                        data: [100, 180, 301, 150, 451, 230, 150, 480, 8, 316],
                        type: 'spline',
                        marker: {
                            symbol: 'circle'
                        }
                    },
                    {
                        name: 'Обратные звонки обработано',
                        data: [545, 80, 300, 570, 571, 273, 175, 478, 778, 376],
                        type: 'spline',
                        marker: {
                            symbol: 'circle'
                        }
                    },
                    {
                        name: 'Отзывы',
                        data: [15, 877, 34, 5, 510, 203, 105, 408, 708, 306],
                        type: 'spline',
                        marker: {
                            symbol: 'circle'
                        }
                    },
                    {
                        name: 'Жалобы пришло',
                        data: [577, 85, 374, 508, 541, 23, 150, 487, 758, 346],
                        type: 'spline',
                        marker: {
                            symbol: 'circle'
                        }
                    },
                    {
                        name: 'Жалобы обработано',
                        data: [155, 178, 413, 500, 541, 23, 15, 418, 78, 36],
                        type: 'spline',
                        marker: {
                            symbol: 'circle'
                        }
                    }
                ]
            });
        }

        if ($('#pie-chart').length) {
            var chart2 = $('#pie-chart').highcharts({
                chart: {
                    plotBackgroundColor: null,
                    plotBorderWidth: null,
                    plotShadow: false,
                    width: 742
                },
                title: {
                    text: ''
                },
                colors: [
                    '#e35341',
                    '#d07a17',
                    '#9c8117',
                    '#6a9011',
                    '#00984b',
                    '#189293',
                    '#488ac0',
                    '#9973bf',
                    '#be6aa3',
                    '#848484'
                ],
                legend: {
                    width: 742,
                    itemWidth: 247,
                    maxHeight: 60,
                    itemStyle: {
                        fontWeight: 'normal',
                        fontFamily: 'Open Sans',
                        fontSize: '12px'

                    }
                },
                tooltip: {
                    formatter: function() {
                        return '<b>'+ this.point.name +'</b>: '+ this.y;
                    }
                },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        dataLabels: {
                            enabled: false
                        },
                        showInLegend: true
                    }
                },
                series: [
                    {
                    type: 'pie',
                    name: 'Всего',
                    title: {
                        text: 'Обратн. звонки пришло',
                        verticalAlign: 'top',
                        y: -30,
                        align: 'center',
                        //x: -55
                    },
                    size: 180,
                    center: [200, 98],
                    data: [
                        ['Москва',   2],
                        ['Кострома', 12],
                        ['С.-Петербург', 26],
                        ['Ступино', 10],
                        ['Самара', 15],
                        ['Екатеринбург', 26.8]

                    ]
                },
                {
                    type: 'pie',
                    name: 'Всего',
                    title: {
                        text: 'Обратн. звонки обр.',
                        verticalAlign: 'top',
                        y: -30,
                        align: 'center',
                        x: -55
                    },
                    size: 180,
                    center: [450, 98],
                    data: [
                        ['Москва',   12],
                        ['Кострома', 11],
                        ['С.-Петербург', 35],
                        ['Ступино', 15],
                        ['Самара', 5],
                        ['Екатеринбург', 4]

                    ]
                }
                ]
            });
        }


        var $tableRow = $('.graph-info .row');

        $tableRow.mouseenter(function (e) {
            var $el = $(e.currentTarget);
            var i = $el.index();
            var points = [];
            for (var j = 0; j < chart.series.length; j++) {
                chart.series[j].data[i].setState('hover');
                points.push(chart.series[j].points[i]);
            }
            chart.tooltip.refresh(points);
        });

        $tableRow.mouseleave(function (e) {
            var $el = $(e.currentTarget);
            var i = $el.index();
            for (var j = 0; j < chart.series.length; j++) {
                chart.series[j].data[i].setState();
            }
            chart.tooltip.hide();
        });
    });
})(jQuery);
