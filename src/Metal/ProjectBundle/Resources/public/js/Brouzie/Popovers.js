(function (w, $) {
    function PopoverHider() {
        //TODO: use $popoverEl arg
        this.hider = function(event)
        {
            var $popoverEl = event.data.$popoverEl;
            if (!$popoverEl.find(event.target).length // not clicked on popover
                && $(event.target).parents('body').length // and popover element not removed from dom
                && $popoverEl.data('popover.opener').get(0) !== event.target // and not clicked on focus opener
            ) {
                closePopover($popoverEl);
            }
        }
    }

    function openPopover($popoverEl, $opener)
    {
        if ($popoverEl.is(':visible')) {
            return;
        }

        $opener.addClass('active');

        var popoverPaddingTop = parseInt($popoverEl.css('padding-top')) + parseInt($popoverEl.find('.dropdown').css('padding-top') || 0);
        var popoverPaddingLeft = parseInt($popoverEl.css('padding-left')) + parseInt($popoverEl.find('.dropdown').css('padding-left') || 0);

        var isCentered = $opener.data('centered');
        var zIndex = $opener.data('index') || 999;

        var diffLeft = $opener.offset().left + ($opener.data('offset-left') || 0);
        var diffTop = $opener.offset().top + ($opener.data('offset-top') || 0);
        var leftPos, topPos;

        if ($opener.data('different-position')) {
            leftPos = diffLeft - popoverPaddingLeft;
            topPos = diffTop - popoverPaddingTop;
        }

        if ($opener.data('officesOpener')) {
            diffLeft = $opener.position().left;
            diffTop = $opener.position().top;
            leftPos = diffLeft - popoverPaddingLeft;
            topPos = diffTop - popoverPaddingTop;
        }
        $popoverEl.data('popover.opener', $opener);

        if (isCentered) {
            $popoverEl.center();
        } else {
            $popoverEl.css({
                left: leftPos,
                top: topPos
            });
        }

        $popoverEl.css({
            zIndex: zIndex
        });

        $popoverEl.show();
        var hider = (new PopoverHider()).hider;
        $popoverEl.data('popover.hider', hider);

        $('body').bind('click', {$popoverEl: $popoverEl}, hider);
    }

    function closePopover($popoverEl)
    {
        var $opener = $popoverEl.data('popover.opener');
        var hider = $popoverEl.data('popover.hider');

        $popoverEl.hide();
        $opener.removeClass('active');
        if (hider) {
            $('body').unbind('click', hider);
        }

        $popoverEl.data('popover.opener', null);
        $popoverEl.data('popover.hider', null);
    }

    function popoverOpenListener(event)
    {
        var $el = $(event.currentTarget);
        var popoverSelector = $el.data('popover');
        var $popoverEl = $(popoverSelector);

        openPopover($popoverEl, $el);
    }

    function initialize(customOptions)
    {
        var options = {
            openerSelector: '.js-popover-opener',
            openerOnFocusSelector: '.js-popover-opener-focus',
            selfCloserSelector: '.js-popover-self-closer'
        };
        $.extend(options, customOptions || {});

        $('body')
            .delegate(options.openerSelector, 'click', function (event) {
                event.preventDefault();
                popoverOpenListener(event);
            })
            .delegate(options.openerOnFocusSelector, 'focus', function (event) {
                popoverOpenListener(event);
            })
            .delegate(options.openerOnFocusSelector, 'blur', function (event) {
                //closePopover($(this).data('popover.opener'));
            })
            .delegate(options.selfCloserSelector, 'click', function (event) {
                event.preventDefault();
                var $el = $(event.currentTarget);

                var $popoverEl = $el.parents().filter(function () {
                    return $(this).data('popover.opener');
                });

                closePopover($popoverEl);
            });
    }

    w.Brouzie = w.Brouzie || {};
    Brouzie.Popovers = {
        initialize: initialize,
        openPopover: openPopover,
        closePopover: closePopover,
        Plugins: {}
    };

})(window, jQuery);

(function (w, $) {

    function initialize(customOptions)
    {
        var options = {
            switcherSelector: '.js-block-switcher'
        };
        $.extend(options, customOptions || {});

        $('body').delegate(options.switcherSelector, 'click', function(event) {
            event.preventDefault();

            var $el = $(event.currentTarget);
            var $popover = $el.parents('.js-popover');
            var $switchableBlocks = $($popover.data('switchable-block-set'));
            var $switchers = $popover.find('.js-block-switcher');

            $switchableBlocks.addClass('g-hidden');
            $($el.data('switch-block')).removeClass('g-hidden');

            $switchers.removeClass('current');
            $el.addClass('current');

//            $popover.data('popover.opener').text($el.text());

            Brouzie.Popovers.closePopover($popover)
        });
    }

    Brouzie.Popovers.Plugins.BlockSwitcher = {
        initialize: initialize
    }
})(window, jQuery);
