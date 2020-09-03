function updateHref()
{
    $('a[data-href]').each(function(i, el) {
        $(el)
            .attr('href', $(el).attr('data-href'))
            .removeAttr('data-href');
    });
}

(function ($) {

    $('document').ready(function () {
        $('select').styler();
        updateHref();

        $('.order-button-holder .order-button').hover(function () {
            $(this).addClass('hovered');
        }, function () {
            $(this).removeClass('hovered');
        });

        $('#city-tabs').easyTabs({defaultContent: 1});

        $('.tabs-content').scrollbar({
            //
        });

        // $('input, select').styler({
        //     'browseText': "Загрузить из файла"
        // });

        $(':input[placeholder]').placeholder({
            overrideSupport: true
        });


        function tooltipShower(event)
        {
            $(event.target).parent().find('.popup-error').addClass('g-visible');
        }
        function tooltipHider(event)
        {
            $(event.target).parent().find('.popup-error').removeClass('g-visible');
        }
        $('body')
            .delegate('.form-text', 'focus', tooltipShower)
            .delegate('.form-select', 'focus', tooltipShower)
            .delegate('.form-text', 'blur', tooltipHider)
            .delegate('.form-select', 'blur', tooltipHider)
        ;
        $('body')
            .delegate('.js-clickable', 'touchstart mousedown', function(event) {
                $(event.currentTarget).addClass('clicked');
            })
            .delegate('.js-clickable', 'touchstart mouseup mouseout', function(event) {
                $(event.currentTarget).removeClass('clicked');
            });

        $('.js-searchable-block').delegate('.js-search-query', 'keyup', function (event) {
            var q = $(event.currentTarget).val().toLowerCase();

            $(event.delegateTarget).find('.js-search-source').map(function(index, el) {
                var $el = $(el);
                $el.toggleClass('g-hidden', $el.text().toLowerCase().indexOf(q) === -1);

                if ($el.data('search-hide-parent')) {
                    var $parent = $el.parents($el.data('search-hide-parent'));
                    $parent.toggle($parent.find('.js-search-source').not('.g-hidden').length > 0);
                }
            });
        });


        $('body').delegate('.js-popover-opener', 'click', function (event) {
            event.preventDefault();

            var $el = $(event.currentTarget);
            var popoverSelector = $el.data('popover');
            var $popoverEl = $(popoverSelector);

            var xClick = event.pageX - $popoverEl.width() / 2;
            var yClick = $el.offset().top + $el.outerHeight();

            if (xClick < $el.offset().left) {
                xClick = $el.offset().left;
            }

            $popoverEl
                .css({
                    'left': xClick,
                    'top': yClick
                })
                .show();

            function popoverHider(e)
            {
                // clicked on popover and element not removed
                if (!$(e.target).parents(popoverSelector).length && $(e.target).parents('body').length) {
                    $popoverEl.hide();
                    $('body').unbind('click', popoverHider);
                }
            }

            $('body').bind('click', popoverHider);
        });

        $('body').delegate('.js-popover-opener', 'click', function (event) {
            event.preventDefault();

            $($(event.currentTarget).data('focus-element')).focus();
        });

        $('body').delegate('.js-add-new-product', 'click', function(event) {
            event.preventDefault();
            var $el = $(event.currentTarget);
            if ($el.hasClass('disabled')) {
                return;
            }
            var productRow = $('#new-product-row').html();
            var $container = $('#products-list-container');

            productRow = productRow.replace(/__name__/g, $container.find('.product-string').length);
            $container.find('.more-btn').hide();
            $container.append(productRow);
            $('select').styler();

            initializeCategoriesAutocomplete($container.find('.product:last :input'));

            $container.find('.product:last :input').focus();
        });


        $('body').delegate('.js-file', 'change', function (e) {
            var productRowHtml = $('#demand-form-new-file-template').html();
            var $container = $('#demand-form-files-container');

            productRowHtml = productRowHtml.replace(/__name__/g, $container.find('.js-file').length);
            $container.append(productRowHtml);

        });

        $('#header .logo')
            .mouseover(function () {
                $('.buble').removeClass('g-hidden');
            })
            .mouseout(function () {
                $('.buble').addClass('g-hidden');
            })
        ;

        $('.js-file').bind('change', function () {
            $('#products-list-container :input')
                .attr('disabled', 'disabled')
                .addClass('disabled')
                .trigger('refresh')
            ;
            $('.more-btn a').addClass('disabled');
        });
    });

    $(window).load(function() {
        var $el = $('.inside .product :input:first');
        $el
            .focus()
            .val($el.val())
        ;
    });

})(jQuery);

function initializeCategoriesAutocomplete($acEls)
{
    if (typeof $acEls === 'undefined') {
        $acEls = $('.product :input');
    }

    $acEls.each(function(i, el) {
        var ac = $(el)
            .autocomplete({
                search: function(event, ui) {
                    if (ac.xhr) {
                        ac.xhr.abort();
                    }
                },
                source: function(request, response) {
                    ac.xhr = $.get(initializeCategoriesAutocomplete.categoriesSuggestUrl, { q: request.term , source: 'metalspros'}, function(data) {
                        response(data);
                    });
                },
                minLength: 2,
                select: function(event, ui) {
                    $(el).parents('.product-string').find('.category-id').val(ui.item.id);
                }
            })
            .data('uiAutocomplete');

        ac._renderItem = function(ul, item) {
            item.value = item.title;

            return $('<li>')
                .data('category-id', item.id)
                .append($('<a>').text(item.title))
                .appendTo(ul);
        };
    });
}

function initializeCitiesAutocomplete()
{
    var autocomplete =
        $('#demand_cityTitle')
            .autocomplete({
                search: function(event, ui) {
                    //TODO: reset hidden city?
                    if (autocomplete.xhr) {
                        autocomplete.xhr.abort();
                    }
                },
                source: function(request, response) {
                    autocomplete.xhr = $.get(initializeCitiesAutocomplete.citiesSuggestUrl, { q: request.term }, function(data) {
                        response(data);
                    });
                },
                minLength: 2,
                select: function(event, ui) {
                    $('#demand_city').val(ui.item.id);
                }
            })
            .data('uiAutocomplete');

    autocomplete._renderItem = function(ul, item) {
        item.value = item.title;

        return $('<li>')
            .data('city-id', item.id)
            .append($('<a>').text(item.title))
            .appendTo(ul);
    };
}
