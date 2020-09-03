(function (w, $) {
    function openPopup($popupEl, $opener)
    {
        var $overflow = $('<div class="overflow"></div>');
        $('body').append($overflow);

        if (!$popupEl.data('popup-non-closable')) {
            $overflow.bind('click', function(event) {
                closePopup($popupEl);
            });
        }

        if ($opener && $opener.length && $opener.data('ajax-content')) {
            var $popupCover = $('#popup-cover');
            $popupEl.trigger($.Event('popup.beforeload', {$opener: $opener, $popup: $popupEl, $overflow: $overflow}));

            $popupCover
                .show()
                .center();

            $.ajax({
                url: $opener.data('ajax-content'),
                type: 'POST',
                success: function(data) {
                    var html = typeof data == 'object' ? data.html : data;
                    var $newEl = $($.trim(html));
                    $popupEl.html($newEl);
                    $popupCover.hide();

                    $popupEl.trigger($.Event('popup.beforeopen', {$opener: $opener, $popup: $popupEl, $overflow: $overflow}));

                    $popupEl
                        .show()
                        .center();

                    includeAjax($newEl);
                }
            });
        } else {
            $popupEl.trigger($.Event('popup.beforeopen', {$opener: $opener, $popup: $popupEl, $overflow: $overflow}));

            $popupEl
                .show();

            if (!Modernizr.touch) {
                $popupEl.center();

            }
        }

        $popupEl.data('popup.overflow', $overflow);

        $popupEl.trigger($.Event('popup.open', {$opener: $opener, $popup: $popupEl, $overflow: $overflow}));
    }

    function closePopup($popupEl)
    {
        var $overflow = $popupEl.data('popup.overflow');

        $popupEl.hide();

        $overflow.remove();
        $popupEl.trigger($.Event('popup.close', {$popup: $popupEl}));
    }

    function initialize(customOptions)
    {
        var options = {
            openerSelector: '.js-popup-opener',
            closerSelector: '.js-popup-closer',
            popupSelector:  '.popup-block'
        };
        $.extend(options, customOptions || {});

        $('body').delegate(options.closerSelector, 'click', function (event) {
            event.preventDefault();
            //TODO: add support of data-attribute popup

            var $el = $(event.currentTarget);

            closePopup($el.parents(options.popupSelector));
        });

        $('body').delegate(options.openerSelector, 'click', function (event) {
            event.preventDefault();

            var $el = $(event.currentTarget);
            var popupSelector = $el.data('popup');
            var $popupEl = $(popupSelector);

            openPopup($popupEl, $el);
        });
    }

    w.Brouzie = w.Brouzie || {};
    Brouzie.Popups = {
        initialize: initialize,
        openPopup: openPopup,
        closePopup: closePopup
    };

})(window, jQuery);
