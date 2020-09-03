$(document).ready(function () {
    $('img[rel=popover]').popover({
        html: true,
        trigger: 'hover',
        placement: 'bottom',
        content: function(){return '<img src="'+$(this).data('img') + '" />';}
    });

    $('.js-hide').click(function(){
        $('.js-tree-label').each(function(i, el){
            var $el = $(el);
            var $list = $($el.data('collapse-child'));
            $list.addClass('hidden');
            $el.removeClass('item-expanded');
            $el.addClass('item-collapsed');
        });

    });

    $('.js-show').click(function(){
        $('.js-tree-label').each(function(i, el){
            var $el = $(el);
            var $list = $($el.data('collapse-child'));
            $list.removeClass('hidden');
            $el.removeClass('item-collapsed');
            $el.addClass('item-expanded');
        });

    });

    $('body').delegate('.js-tree-label', 'click', function (event) {
        event.preventDefault();
        var $el = $(event.currentTarget);
        var $list = $($el.data('collapse-child'));
        $list.toggleClass('hidden');

        $el.toggleClass('item-collapsed');
        $el.toggleClass('item-expanded');
    });

});
