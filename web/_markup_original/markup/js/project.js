$(document).ready(function($) {
    $('#answer').bind('popup.open', function(e) {
        e.$popup.find('.js-demand-id-injectable').text(e.$opener.data('demand-id'));
    });

    $('body')
        .delegate('.js-favorite-comment', 'click', function(e) {
            var $favBlock = $(e.currentTarget);
            if ($favBlock.data('submitting-favorite')) {
                return;
            }

            $favBlock.find('[data-favorite="text"]').addClass('g-hidden');
            $favBlock.find('[data-favorite="textarea"]').removeClass('g-hidden');

            $favBlock.find('.note').focus();
            $favBlock.addClass('focus');
            $favBlock.find('.js-favorite-comment-field').val($favBlock.find('.js-favorite-comment-text').text());
        })
        .delegate('.js-favorite-comment-field', 'blur', function(e) {
            var $el = $(e.currentTarget);
            var $favBlock = $el.parents('.js-favorite-comment');

            if ($favBlock.data('submitting-favorite')) {
                return;
            }

            var hasText = $favBlock.find('.js-favorite-comment-text').text().length > 0;

            $favBlock.toggleClass('focus', hasText);
            $favBlock.find('.js-favorite-comment-date').toggleClass('g-hidden', !hasText);
            $favBlock.find('[data-favorite="textarea"]').addClass('g-hidden');
            $favBlock.find('[data-favorite="text"]').removeClass('g-hidden');
        })
        .delegate('.js-favorite-comment-submit', 'click mousedown', function(e) {
            e.preventDefault();

            var $el = $(e.currentTarget);
            var $lMask = $el.siblings('.loading-mask');
            var $favBlock = $el.parents('.js-favorite-comment');

            $favBlock.data('submitting-favorite', true);
            $lMask.toggleClass('g-hidden');

            $form = $favBlock.parents('form');

            function callback(response) {
                var hasText = response.text.length > 0;
                $favBlock.toggleClass('focus', hasText);
                $favBlock.find('.js-favorite-comment-text').text(response.text);
                $favBlock.find('.js-favorite-comment-date')
                    .toggleClass('g-hidden', !hasText)
                    .text(response.date);
                $lMask.toggleClass('g-hidden');
                $favBlock.find('[data-favorite="textarea"]').addClass('g-hidden');
                $favBlock.find('[data-favorite="text"]').removeClass('g-hidden');
                $favBlock.data('submitting-favorite', false);
            }

            if (window.DEBUG || false) {
                window.setTimeout(function() {
                    var response = {
                        text: 'ololo',
                        date: '12 aug 2013'
                    };

                    callback(response);
                }, 1500);
            } else {
                $.ajax({
                    url: $form.attr('action'),
                    type: $form.attr('method') || 'POST',
                    data: $form.serializeArray(),
                    success: callback
                });
            }
        })
    ;
});
