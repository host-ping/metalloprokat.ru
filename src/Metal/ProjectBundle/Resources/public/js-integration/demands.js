$(document).ready(function() {

    $('body').delegate('.js-view-contacts', 'click', function(event) {
        event.preventDefault();
        var $el = $(event.currentTarget);
        var $popoverMask = $el.siblings('.drop-wrapper').find('.loading-mask');
        $popoverMask.removeClass('g-hidden');

        $.ajax({
            url: $el.data('view-url'),
            type: 'GET',
            success: function(data) {
                $($el.data('contact-content')).html(data);
                $el.removeClass('js-view-contacts');
                $popoverMask.addClass('g-hidden');
            }
        });
    });

    $('body').delegate('.js-calc-contacts', 'click', function(event) {
        event.preventDefault();
        var $el = $(event.currentTarget);

        var windowHeight = $(window).height();
        var $dropdown = $el.siblings('.contact');
        var elPos = $el.offset().top - $(window).scrollTop();
        var dropdownHeight = $dropdown.outerHeight(true);
        $el.removeClass('top-opener');
        $dropdown.removeClass('top-position');

        if (elPos + dropdownHeight + $el.height() + 20 > windowHeight) {   //FIXME: нужно как-то избавится от отступа в ЛК внизу страницы
            $dropdown.addClass('top-position');
            $el.addClass('top-opener');
        }

    });

    $('body').delegate('.js-toggle-favorite', 'click', function(event) {
        event.preventDefault();

        var $el = $(event.currentTarget);

        //if (!User.id) {
        //    Brouzie.Popups.openPopup($('#login'));
        //
        //    return;
        //}

        if (!User.allow_add_in_favorite) {
            Brouzie.Popups.openPopup($('#complete-package-favorites'));

            return;
        }

        $.ajax({
            url: $el.data('url'),
            type: 'POST',
            success: function(data) {
                $el
                    .add($el.siblings('.js-toggle-favorite'))
                    .toggleClass('g-hidden');
            }
        });
    });

    $('body').delegate('.js-toggle-archive', 'click', function(event) {
        event.preventDefault();

        var $el = $(event.currentTarget);
        $.ajax({
            url: $el.data('url'),
            type: 'POST',
            success: function(data) {
                if (data.subject === 'delete') {
                    $el.closest('.demand_item').addClass('g-hidden');
                } else {
                    var html = typeof data === 'object' ? data.html : data;
                    $el.closest('.demand_item').html(html);
                }
            }
        });
    });
});
