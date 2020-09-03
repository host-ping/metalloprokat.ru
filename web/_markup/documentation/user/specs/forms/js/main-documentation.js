(function($){

    $('document').ready(function() {

        initializeForms();
    });


    function initializeForms($context)
    {
        $context = $context || null;

        $('input, select', $context)
            .not(':file')
            .not('[styled-checkbox], [styled-select]')
            .not('.not-styling')
            .styler({selectSearch: false});

        $(':input[placeholder]', $context).placeholderEnhanced({normalize: false});
    }

})(jQuery);

