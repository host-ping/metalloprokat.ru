$(document).ready(function () {
    $('[data-encoded-content]').each(function (i, el) {
        var $el = $(el);
        var content = Base64.decode($el.data('encoded-content'));
        $el.html(content);
        $el.data('encoded-content', false);
    });
});
