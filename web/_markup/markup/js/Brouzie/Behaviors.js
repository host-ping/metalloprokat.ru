(function (w, $) {
    function initialize(customOptions)
    {
        var options = {
            hoverableSelector: '.js-hoverable',
            clickableSelector: '.js-clickable',
            toggableBlockSelector: '.js-togglable-block',
            toggableChildBlockSelector: '.js-togglable-child-block',
            expandableSelector: '.js-expandable'
        };
        $.extend(options, customOptions || {});

        $('body')
            .delegate(options.hoverableSelector, 'hover', function (event) {
                var $el = $(event.currentTarget);

                $el.addClass('hovered');
            })
            .delegate(options.hoverableSelector, 'blur', function (event) {
                var $el = $(event.currentTarget);

                $el.removeClass('hovered');
            })

            .delegate(options.clickableSelector, 'touchstart mousedown', function (event) {
                var $el = $(event.currentTarget);

                $el.removeClass('clicked');
            })
            .delegate(options.clickableSelector, 'touchstart mouseup mouseout', function (event) {
                var $el = $(event.currentTarget);

                $el.removeClass('clicked');
            })

            .delegate(options.toggableBlockSelector, 'mouseover mouseout', function (event) {
                var $el = $(event.currentTarget);

                $el
                    .add($el.siblings(options.toggableBlockSelector))
                    .toggleClass('g-hidden');
            })
            .delegate(options.toggableChildBlockSelector, 'mouseover mouseout', function (event) {
                var $el = $(event.currentTarget);

                $el
                    .find($el.data('toggable-child'))
                    .toggleClass('g-hidden');

            })

            .delegate('.js-search-query', 'keyup', function (event) {
                var q = $(event.currentTarget).val().toLowerCase();
                var $searchableBlock = $(event.currentTarget).parents('.js-searchable-block');

                $searchableBlock.find('.js-searchable').map(function(index, el) {
                    var $el = $(el);
                    var txt = $el.data('search-source') ? $($el.data('search-source')).text() : $el.text();

                    $el.data('searchable-matched', txt.toLowerCase().indexOf(q) !== -1);
                    $el.data('searchable-visible', null);
                });

                $searchableBlock.find('.js-searchable[data-search-show-children]').each(function(i, el) {
                    var $el = $(el);
                    var displayEl = $el.data('searchable-matched');

                    if (displayEl) {
                        $el
                            .find($el.data('search-show-children'))
                            .data('searchable-visible', true)
                        ;
                    }
                    $el.data('searchable-visible', displayEl);
                });

                $searchableBlock.find('.js-searchable[data-search-hide-parent]').each(function(i, el) {
                    var $el = $(el);
                    var $parent = $el.parents($el.data('search-hide-parent'));
                    var displayEl = $el.data('searchable-matched') || $el.data('searchable-visible');
                    var displayParentEl = displayEl || $parent.data('searchable-visible');

                    $el.data('searchable-visible', displayEl);
                    $parent.data('searchable-visible', displayParentEl);
                });

                $searchableBlock.find('.js-searchable').each(function(i, el) {
                    var $el = $(el);
                    var displayEl = $el.data('searchable-visible');
                    if (displayEl === null) {
                        displayEl = $el.data('searchable-matched');
                    }

                    $el.toggleClass('g-hidden', !displayEl);
                });

                //TODO: документировать
                //TODO: возможность добавлять класс к сматченым строкам
                //TODO: индексирование
            })

            .delegate(options.expandableSelector, 'click', function(event) {
                event.preventDefault();

                var $el = $(event.currentTarget);
                $el
                    .add($el.siblings(options.expandableSelector))
                    .toggleClass('g-hidden');

                var section = $el.data('expandable');

                var $items = $('[data-expandable-section="'+section+'"]');
                $items.toggleClass('g-hidden');

                $el.trigger('expandable.toggled');
            })
        ;
    }

    w.Brouzie = w.Brouzie || {};
    Brouzie.Behaviors = {
        initialize: initialize
    };

})(window, jQuery);
