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

(function (w, $) {
    function initializeLineChart($el, xAsis, data) {
        $el.highcharts({
            chart: {
                style: {
                    fontFamily: 'Open Sans'
                }
            },
            xAxis: {
                categories: _.keys(xAsis),
                min: 1,
                max: 15,
                labels: {
                    formatter: function () {
                        return xAsis[this.value];
                    },
                    autoRotation: []
                }
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
            scrollbar: {
                enabled: true
            },
            legend: {
                width: 720,
                itemWidth: 220,
                itemStyle: {
                    fontWeight: 'normal',
                    fontFamily: 'Open Sans',
                    fontSize: '12px'

                }
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
                }],
                useHTML: true,
                borderColor: '#c6c6c6',
                borderRadius: 4,
                style: {
                    fontSize: '11px',
                    padding: '8px'
                }
            },
            yAxis: {
                opposite: true,
                title: '',
                min: 0
            },
            series: data

        });
    }

    function initializePieChart($el, data) {
        $el.highcharts({
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
                shared: true,
                useHTML: true,
                borderColor: '#c6c6c6',
                borderRadius: 4,
                style: {
                    fontSize: '11px',
                    padding: '8px'
                }//,
                //formatter: function () {
                //    for (var i = 0, n = this.points.length)
                //    return '<b>' + this.points.name + '</b>: ' + this.y;
                //}
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
            series: data
        });
    }

    function mouseEnterHandler(e) {
        var chart = $('.js-chart-wrapper:visible .graph-container').highcharts();
        var $el = $(e.currentTarget);
        var i = $el.index() - 1;
        var points = [];

        for (var j = 0, n = chart.series.length; j < n; j++) {
            chart.series[j].data[i].setState('hover');
            points.push(chart.series[j].points[i]);
        }
        chart.tooltip.refresh(points);
    }

    function mouseLeaveHandler(e) {
        var chart = $('.js-chart-wrapper:visible .graph-container').highcharts();
        var $el = $(e.currentTarget);
        var i = $el.index() - 1;

        for (var j = 0, n = chart.series.length; j < n; j++) {
            chart.series[j].data[i].setState();
        }
        chart.tooltip.hide();
    }

    function initHoverOnTableChart() {
        var $tableRow = $('.graph-info .row').not('.total');
        var $toggleContainer = $('.toggle-wrapper');

        if ($toggleContainer.is(':visible')) {
            $tableRow
                .bind('mouseenter', mouseEnterHandler)
                .bind('mouseleave', mouseLeaveHandler);
        } else {
            $tableRow
                .unbind('mouseenter', mouseEnterHandler)
                .unbind('mouseleave', mouseLeaveHandler);
        }
    }

    w.initializeLineChart = initializeLineChart;
    w.initializePieChart = initializePieChart;

    $(document).ready(function () {
        initHoverOnTableChart();

        var $currentChartWrapper = $('.js-chart-wrapper:visible');
        $('.js-pie-table-head .row .col:eq(1)').addClass('current-col');
        $('.js-stats-table-body .row').each(function (i, e) {
            $('.js-stats-table-body .row:eq(' + i + ') .col:eq(1)').addClass('current-col');
        });

        var firstChartContainer = $('.js-chart-wrapper:first').find('.graph-container');
        if (window.chartData) {
            initializePieChart(firstChartContainer, window.chartData[0]);
            firstChartContainer.data('chart-initialized', true);
        }

        $('.js-chart-shower').bind('click', function (e) {
            var $el = $(e.currentTarget);
            var key = $el.data('col-index');
            var $chartEl = $('#pie-chart-' + key);

            $('.js-chart-shower').removeClass('current');
            $el.addClass('current');

            if (!$chartEl.data('chart-initialized')) {
                initializePieChart($chartEl, window.chartData[key]);
                $chartEl.data('chart-initialized', true);
            }

            var $chartWrapper = $($el.data('chart-show'));

            $currentChartWrapper.addClass('g-hidden');
            $('.js-chart-current-field').text($el.html());
            $chartWrapper.removeClass('g-hidden');

            $('.js-pie-table-head .row .col').removeClass('current-col');
            $('.js-stats-table-body .row .col').removeClass('current-col');
            var colIndex = $el.data('col-index') + 1;

            $('.js-pie-table-head .row .col:eq(' + colIndex + ')').addClass('current-col');
            $('.js-stats-table-body .row').each(function (i, e) {
                $('.js-stats-table-body .row:eq(' + i + ') .col:eq(' + colIndex + ')').addClass('current-col');
            });

            $currentChartWrapper = $chartWrapper;
        });

        $('.js-toggle-chart').bind('click', function (el) {
            var $toggleContainer = $('.toggle-wrapper');

            var $el = $(el.currentTarget);
            var $toggles = $el.add($el.siblings('.js-toggle-chart'));

            $toggles.toggleClass('g-hidden');
            $toggleContainer.toggleClass('g-hidden');
            Cookies.set('charts_state', $el.data('toggle-save'));

            initializeScroll();
            $(window).resize();
            initHoverOnTableChart();
        });
    });

})(window, jQuery);
