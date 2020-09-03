(function (w, $) {
    function initialize(customOptions)
    {
        var options = {
            itemSelector: '.js-tabs'
        };
        $.extend(options, customOptions || {});

        var $el = $(options.itemSelector);
        var $tabsList = $el.parent();
        var totalWidth = 0;
        var calculatedWidth = 0;
        $el.each(function (i) {
            totalWidth += parseInt($el.eq(i).outerWidth(true));
        });

        if (totalWidth > $tabsList.width()) {
            if ($el.length > 2) {
                calculatedWidth = ($tabsList.width() - parseInt($el.eq(0).outerWidth(true)) - (parseInt($el.css('padding-left')) - parseInt($el.css('padding-right'))) - 4) / 2;

                $el.each(function (i, e) {
                    var $currentEl = $(e);
                    var textWidth = $currentEl.outerWidth(true) - $currentEl.find('.count').outerWidth(true);

                    if (calculatedWidth < textWidth) {
                        $el.not(':eq(0)').css({
                            'width': calculatedWidth + 'px'
                        });
                        $el.not(':eq(0)').find('.link').addClass('is-gradiented');
                    }


                });
            } else if ($el.length == 2) {
                calculatedWidth = $tabsList.width() / 2;

                $el.each(function (i, e) {
                    var $currentEl = $(e);
                    var textWidth = $currentEl.outerWidth(true) - $currentEl.find('.count').outerWidth(true);

                    if (calculatedWidth < textWidth) {
                        $el.eq(i).css({
                            'width': calculatedWidth + 'px'
                        })
                            .find('.link').addClass('is-gradiented');
                    }

                });
            }
        }



        $('body').delegate(options.itemSelector, 'click', function (event) {
            event.preventDefault();

            var $tab = $(event.currentTarget);
            var $activeTab = $tab
                .siblings(options.itemSelector)
                .filter('.active');

            var $tabContent = $($tab.data('tab'));
            var $activeTabContent = $($activeTab.data('tab'));

            $tab
                .addClass('active')
            ;

            $activeTab
                .removeClass('active')
            ;

            $tabContent.removeClass('g-hidden');
            $activeTabContent.addClass('g-hidden');
        });
    }

    w.Brouzie = w.Brouzie || {};
    Brouzie.Tabs = {
        initialize: initialize
    };

})(window, jQuery);
