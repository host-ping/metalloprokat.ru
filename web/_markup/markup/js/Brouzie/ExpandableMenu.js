(function (w, $) {
    function initialize(customOptions)
    {
        var options = {
            itemSelector: '.js-expandable-menu-item',
            expanderSelector: '.js-expandable-menu-expander'
        };
        $.extend(options, customOptions || {});

        $('body').delegate(options.expanderSelector, 'click', function (event) {
            var $expander = $(event.currentTarget);
            var $el = $expander.parents(options.itemSelector);
            var $expandedEl = $el
                .siblings(options.itemSelector)
                .filter('.expanded');
            var $childrenEl = $el.find($el.data('expandable-menu-children'));
            var $expandedChildrenEl = $expandedEl.find($el.data('expandable-menu-children'));

            if (!$el.hasClass('expanded')) {
                event.preventDefault();
            }

            $el
                .removeClass('collapsed')
                .addClass('expanded')
            ;

            $expandedEl
                .removeClass('expanded')
                .addClass('collapsed')
            ;

            $childrenEl.removeClass('g-hidden');
            $expandedChildrenEl.addClass('g-hidden');
        });
    }

    w.Brouzie = w.Brouzie || {};
    Brouzie.ExpandableMenu = {
        initialize: initialize
    };

})(window, jQuery);
