$(document).ready(function() {

    $('.private-room-content').delegate('.js-process-btn', 'click', function (event) {
        event.preventDefault();

        var $element = $(event.currentTarget);
        var $lMask = $element.siblings('.loading-mask');
        var $processedText = $element.siblings('.text');
        $lMask.removeClass('g-hidden');

        $.ajax({
            url: $element.data('url'),
            type: 'POST',
            success: function (data) {
                $element.addClass('g-hidden');
                $lMask.addClass('g-hidden');
                $processedText.removeClass('g-hidden');
                $element.siblings('.text').find('.js-processed-date').html(data.date);
            }
        });

    });

    $('.js-filter-check').click(function (event) {
        var $el = $(event.currentTarget);
        $el.parents('.js-filter-form').submit();
    });

    $('.private-room-content').delegate('.js-approved-employee', 'click', function(event){
        event.preventDefault();

        var $el = $(event.currentTarget);

        if (confirm('Подтвердить сотрудника ?')) {
            $.ajax({
                url: $el.data('url'),
                type: 'POST',
                success: function(){
                    window.location.reload(true);
                }
            });
        }
    });

    $('.private-room-content').delegate('.js-edit-employee', 'click', function (e) {
        $el = $(e.currentTarget);

        var $loadingMask = $el.siblings('.loading-mask');

        $loadingMask.removeClass('g-hidden');
        var $url = $el.data('load-url');
        $.ajax({
            url: $url,
            type: 'GET',
            success: function (data) {
                $loadingMask.addClass('g-hidden');
                $('#load-employee-popup-container').html($.trim(data.html));
                Brouzie.Popups.openPopup($('#edit-employee-popup'));
            },
            error: function (jqXHR, textStatus, errorThrown) {
                $loadingMask.addClass('g-hidden');
            }
        });
    });

    $('.private-room-content').delegate('.js-del-item', 'click', function(event){
        event.preventDefault();

        var $el = $(event.currentTarget);
        var $itemShadow = $el.siblings('.overflow');

        var kindDelEl = "Удалить отзыв?";
        if ($el.data('kind') == 'filial') {
            kindDelEl = "Удалить филиал?";
        } else if ($el.data('kind') == 'employee') {
            kindDelEl = "Удалить сотрудника?";
        }

        if (confirm(kindDelEl)) {
            $.ajax({
                url: $el.data('url'),
                type: 'POST',
                success: function(){
                    if ($el.data('kind') == 'filial') {
                        window.location.reload(true);
                    } else if ($el.data('kind') == 'employee') {
                        $itemShadow.removeClass('g-hidden');
                        $el.addClass('items').addClass('overflow');
                        $el.siblings('.js-edit-employee').addClass('g-hidden');
                        $el.siblings('.js-approved-employee').addClass('g-hidden');
                        $el.addClass('g-hidden');
                    } else {
                        $itemShadow.removeClass('g-hidden');
                    }
                }
            });
        }
    });

    $('body').delegate('.js-del-element', 'click', function (event) {
        event.preventDefault();

        var $el = $(event.currentTarget);

        $el.parents('.js-changed-element').remove();
    });

    $('.management-content-wrapper').delegate('.js-show-filial', 'click', function(event){
        var $el = $(event.currentTarget);

        $.ajax({
            url: $el.data('url'),
            type: 'POST',
            success: function(data) {
                var $newEl = $($.trim(data.html));
                $('.management-content-wrapper').html($newEl);
                includeAjax($newEl);
            }
        });

    });

    $('.management-content-wrapper').delegate('.js-add-phone-row', 'click', function(event) {
        event.preventDefault();

        var $el = $(event.currentTarget);
        var tmpl = $('#new-phone-row').html();
        var $container = $('#phone-list-container');

        var index = $container.data('items-index');
        index++;
        $container.data('items-index', index);

        tmpl = tmpl.replace(/__name__/g, index);
        $container.find('.js-phone-row-controls').addClass('g-hidden');
        $container.append(tmpl);

        $container.find('.js-phone-row:last .phone').focus();
    });

    $('.management-content-wrapper').delegate('.js-del-phone-row', 'click', function(event) {
        event.preventDefault();

        var $el = $(event.currentTarget);
        var $container = $('#phone-list-container');
        $el.parents('.js-phone-row').remove();

        $container.find('.js-phone-row-controls:last').removeClass('g-hidden');
    });

    $('.management-content-wrapper').delegate('.js-add-site-row', 'click', function(event) {
        event.preventDefault();

        var $el = $(event.currentTarget);
        var tmpl = $('#new-site-list-row').html();
        var $container = $('#site-list-container');

        var index = $container.data('items-index');
        index++;
        $container.data('items-index', index);

        tmpl = tmpl.replace(/__name__/g, index);
        $container.find('.js-site-row-controls').addClass('g-hidden');
        $container.append(tmpl);

        $container.find('.js-site-row:last .site').focus();
    });

    $('.management-content-wrapper').delegate('.js-del-site-row', 'click', function(event) {
        event.preventDefault();

        var $el = $(event.currentTarget);
        var $container = $('#site-list-container');
        $el.parents('.js-site-row').remove();

        $container.find('.js-site-row-controls:last').removeClass('g-hidden');
    });
});
