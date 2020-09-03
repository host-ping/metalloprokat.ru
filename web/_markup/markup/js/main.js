(function($){
    $.fn.center = function () {
        this.css("margin", 0);
        this.css("top", (($(window).height() - this.outerHeight()) / 2) + $(window).scrollTop() + "px");
        this.css("left", (($(window).width() - this.outerWidth()) / 2) + $(window).scrollLeft() + "px");
        return this;
    };

    $('document').ready(function() {
        $.reject({
            reject: {
                msie7:true,
                firefox2:true
            },
            browserInfo: {
                gcf: {
                    text:'Google Chrome Frame',
                    url:'http://google.com/chromeframe',
                    allow: {
                        all: false,
                        msie7: true,
                        firefox2:true
                    }
                }
            }
        });

        initializeForms();
        initializeScroll();
        updatePopoverPosition();
        updatePrivateProductContainerHeight();
        updateContentWideHeight();
        showCompanyPhones();
        updatePrivateOfficeLeftMenuHeight();
        initializeBackgroundRedirect();
        horizontalScroll();
        setSpecialBorderBottom();
		openItem();
		OpenBox({
			wrap: '#nav',
			link: '.toogle-menu',
			box: '.menu-holder',
			openClass: 'open'
		});
		OpenBox({
			wrap: '#nav .menu li',
			link: '.arrow-menu',
			box: '.drop',
			openClass: 'open'
		});
		OpenBox({
			wrap: '#nav',
			link: '.nav-overlay',
			box: '.menu-holder',
			openClass: 'open'
		});
		runSelectize();

        $('.popup-block').center();

        $('.js-slider-range-block').each(function(i, el) {
            var $el = $(el);
            var $sliderEl = $el.find('.js-slider-range');
            var $minInput = $el.find('.js-slider-range-min:input');
            var $maxInput = $el.find('.js-slider-range-max:input');
            var $amountEl = $el.find('.js-slider-range-amount');

            $sliderEl.slider({
                min: parseInt($minInput.data('initial-value')),
                max: parseInt($maxInput.data('initial-value')),
                step: 100,
                range: true,
                values: [ parseInt($minInput.val()), parseInt($maxInput.val())],
                slide: function (event, ui) {
                    $amountEl.text(ui.values[0] + ' — ' + ui.values[1]);
                },
                stop: function (event, ui) {
                    if (ui.values[0] != $minInput.val()) {
                        $minInput.val(ui.values[0]).trigger('change');
                    }

                    if (ui.values[1] != $maxInput.val()) {
                        $maxInput.val(ui.values[1]).trigger('change');
                    }
                }
            });

            $amountEl.text($minInput.val() + ' — ' + $maxInput.val());
        });

        Brouzie.Behaviors.initialize();
        Brouzie.Popups.initialize();
        //Brouzie.Toltips.initialize();
        Brouzie.Popovers.initialize();
        Brouzie.Popovers.Plugins.BlockSwitcher.initialize();
        Brouzie.ExpandableMenu.initialize();
        Brouzie.Tabs.initialize();

//        $('u-menu:visible').parents('.login-user').addClass('active');

        // Tooltip opener
        function tooltipOpener(event) {
            var $el = $(event.currentTarget);
            if ($el.data('tooltip')) {
                var elem = $($el.data('tooltip'));
                var $tooltip = $el.parents('.holder').find('.tooltip');
                elem.removeClass('g-hidden');
                var pos = $el.position();
            } else {
                $el.prepend('<div class="tooltip report-link with-bullet"></div>');
                var $tooltip = $el.find('.tooltip');
            }

            if ($el.data('tooltipClass')) {
                $tooltip.addClass($el.data('tooltipClass'));
            }

            if ($el.data('tooltipTitle')) {
                var title = $el.data('tooltipTitle');
                $tooltip.text(title);
            }


            $tooltip.append('<i class="chat-bubble-arrow-border"></i><span class="chat-bubble-arrow"></span>');
            var pWidth = $el.outerWidth(false);
            var bulletWidth = $tooltip.find('i').outerWidth(true);
            var bulletPosition = (pWidth / 2) - (bulletWidth / 2);
            if ($el.data('tooltipClass')) {
                $tooltip.find('i').css({
                    'right': bulletPosition
                });
                $('.chat-bubble-arrow-border').css({
                    'right': bulletPosition
                });
                $('.chat-bubble-arrow').css({
                    'right': bulletPosition + 1
                });
            } else {
                if (pos) {
                    $('.chat-bubble-arrow-border').css({
                        'left': pos.left + bulletPosition
                    });
                    $('.chat-bubble-arrow').css({
                        'left': pos.left + bulletPosition + 1
                    });
                } else {

                    $('.chat-bubble-arrow-border').css({
                        'left': bulletPosition
                    });
                    $('.chat-bubble-arrow').css({
                        'left': bulletPosition + 1
                    });
                }
            }
        }

        function tooltipHider(event) {
            var $el = $(event.currentTarget);
            if ($el.data('tooltip')) {
                var elem = $($el.data('tooltip'));
                elem.addClass('g-hidden');
                elem.find('.chat-bubble-arrow-border').remove();
                elem.find('.chat-bubble-arrow').remove();
            } else {
                $(this).find('.tooltip').remove();
            }
        }

        $('body').delegate('.js-tooltip-opener', 'mouseover', tooltipOpener);
        $('body').delegate('.js-tooltip-opener', 'mouseout', tooltipHider);

        // Help opener
        $('body').delegate('.js-helper-opener', 'mouseover', function(e){
            var $el = $(e.currentTarget);
            var text = $el.data('text');

            if ($el.data('absoluteParent')) {
                var $parentElem = $($el.data('absoluteParent'));
                $parentElem.prepend('<div class="helper opacity-border">' +
                    '<div class="content"></div>' +
                    '</div>');
                $('.helper').find('.content').html(text);
                $('.helper').append('<span class="b"></span>');


                var icoLeftPosition = $el.offset().left - ($el.width() - $('.helper .b').width() / 2);
                var icoTopPosition = $el.offset().top - $('.helper').outerHeight() - $('.helper .b').height();
                $('.helper').offset({top: icoTopPosition, left: icoLeftPosition});
                $('.helper').css({'bottom': 'auto'});

            } else {
                $el.prepend('<div class="helper opacity-border">' +
                    '<div class="content"></div>' +
                    '</div>');
                $('.helper').find('.content').html(text);
                $('.helper').append('<span class="b"></span>');


            }
        });

        $('body').delegate('.js-helper-opener', 'mouseout', function(e){
            $('.helper').remove();
        });

        // список категорий, "показать все"
        $('.js-categories-expand').bind('expandable.toggled', function() {
            updateFiltersBlockHeight();
            // trigger resize for updating fixed blocks
            $(window).resize();
        });

        $('.js-show-button-on-change').bind('change', function(e) {
            var $button = $('.js-show-all');
            if ($button.is(':visible')) {
                return;
            }
            $button.removeClass('g-hidden');
            updateFiltersBlockHeight();
            isSidebarInWindow();
            // trigger resize for updating fixed blocks
            $(window).resize();
        });

        function updateContentScrollableHeight()
        {
            if (Modernizr.touch) {
                return;
            }

            var $contentScrollable = $('.content-scrollable');
            if (!$contentScrollable.length) {
                return
            }
            var blockHeight = $(window).height()
                - $contentScrollable.offset().top - 20;

            $contentScrollable.css({
                'height': blockHeight + 'px'
            });
        }

        $(window).load(function () {
            horizontalScroll();
            if ($('.js-fixed').length && !Modernizr.touch) {
                $('.js-fixed').scrollToFixed();
            }

            if ($('.js-fixed-side-banner').length && !Modernizr.touch) {
                $('.js-fixed-side-banner').scrollToFixed({
                    offsets: false,
                    zIndex: 100,
                    dontSetWidth: true,
                    marginTop: function() {
                        var $searchBlock = $('#search-fixed');
                        if(!$searchBlock.length) {
                            return 0;
                        }

                        return $searchBlock.outerHeight(true);

                    },
                    limit: function() {
                        return $('#footer').offset().top - $('.js-fixed-side-banner').outerHeight(true);
                    },
                    preAbsolute: function () {
                        $(this).parent().css({
                            height: $(this).outerHeight(true)+'px'
                        });
                    }
                });
            }
            if ($('body').hasClass('no-scroll')){
                $('html').addClass('no-scroll');
            }

            if ($('.js-fixed-filters').length && !Modernizr.touch) {
                $('.js-fixed-filters').scrollToFixed({
                    spacerClass: 'sidebar-fixed-spacer',
                    offsets: false,
                    zIndex: 100,
                    dontSetWidth: true,
                    marginTop: function (e) {
                        return $('#search-fixed').outerHeight(true);
                    },
                    limit: function () {
                        var $scrollStopper = $('#footer');

                        return $scrollStopper.offset().top - $('.js-fixed-filters').outerHeight(true);
                    },
                    preAbsolute: function () {
                        $(this).parent().css({
                            height: $(this).outerHeight(true)+'px'
                        });
                    }
                });
            }

            if ($('.js-fixed-minisite-menu').length && !Modernizr.touch) {
                $('.js-fixed-minisite-menu').scrollToFixed({
                    marginTop: function () {
                        var marginTop = $(window).height() - $('.js-fixed-minisite-menu').outerHeight(true);
                        if (marginTop >= 0) {
                            return 0;
                        }
                        return marginTop;
                    },
                    limit: function () {
                        var $scrollStopper = $('#footer');

                        return $scrollStopper.offset().top - $('.js-fixed-minisite-menu').outerHeight(true);
                    }
                });
            }

            if ($('.js-company-info-fixed').length && !Modernizr.touch) {
                $('.js-company-info-fixed').scrollToFixed({
                    offsets: false,
                    zIndex: 100,
                    marginTop: function () {
                        return $('#search-fixed').outerHeight(true);
                    },
                    limit: function () {
                        var $scrollStopper = $('#footer');

                        return $scrollStopper.offset().top - $('.js-company-info-fixed').outerHeight(true);
                    },
                    preAbsolute: function () {
                        $(this).parent().css({
                            height: $(this).outerHeight(true)+'px'
                        });
                    }
                });
            }
            updateContentScrollableHeight();
        });

        $('.js-dropdown-opener').each(function (i, el) {
            var $el = $(el);
            var $dd = $($el.data('display-dropdown'));

            $el
                .bind('mouseenter', function (event) {
                    $dd.css({
                        top: $el.position().top,
                        marginTop: '-15px'
                    });

                    $dd.show();
                    $el.addClass('hover');
                    if (($el.offset().top + $dd.outerHeight() - $(window).scrollTop()) > $(window).height()) {
                        $dd.css('margin-top', '-' + ($dd.height() - 20) + 'px');
                    }

                    $el.parents('.js-scrollable').find('.scroll-element').addClass('g-hidden');
                })
                .bind('mouseleave', function (event) {
                    var $related = $(event.relatedTarget);

                    if ($related[0] !== $dd[0]) {
                        $dd.hide();
                        $el.removeClass('hover');
                        $el.parents('.js-scrollable').find('.scroll-element').removeClass('g-hidden');
                    }
                });

            $dd.on('mouseleave', function () {
                $dd.hide();
                $el.removeClass('hover');
                $el.parents('.js-scrollable').find('.scroll-element').removeClass('g-hidden');
            });
        });

        $(window).bind('resize', function() {
            // to prevent wrong height calculation
            window.setTimeout(function () {
                updateFiltersBlockHeight();
                updateCompanyContactBlockHeight();
                updateContentScrollableHeight();
                updatePrivateProductContainerHeight();
                initializeScroll();
                setShowButtonPosition();
                setSpecialBorderBottom();
            }, 100)
        });
        $(window).load(function(){
            updateFiltersBlockHeight();
            updateCompanyContactBlockHeight();
            updateContentScrollableHeight();
            updateContentWideHeight();
            setShowButtonPosition();
        });

        $('.product-management-list').delegate('.item-block', 'mouseenter', function(e){
            var el = $(e.currentTarget);
            el.find('.product-status').find('.product-status-link').addClass('g-hidden');
            el.find('.product-status').find('.delete-btn').removeClass('g-hidden');
        });
        $('.product-management-list').delegate('.item-block', 'mouseleave', function(e){
            var el = $(e.currentTarget);
            el.find('.product-status').find('.product-status-link').removeClass('g-hidden');
            el.find('.product-status').find('.delete-btn').addClass('g-hidden');
        });

        function setThemeColor()
        {
            var $activeEl = $('.js-theme:checked');
            var $backgroundColorEl = $('.js-theme-background:checked');

            var hoverColor = $backgroundColorEl.data('hoverColor');
            var placeholderColor = $backgroundColorEl.data('placeholder');
            var mainBg = $backgroundColorEl.data('bgColor');

            var css = "";

            $.each($activeEl, function(i, obj){
                css += "." + $(obj).data('name') + "{ background:" + obj.value + ";}"
            });
            css += ".r-info{background:"+ hoverColor +"}";
            css += ".bg{border-color:"+ mainBg +"}";
            css += ".main-background{background:"+ mainBg +"}";
            css += "line{stroke:"+ placeholderColor +"}";
            css += ".placeholder{background:"+ placeholderColor +"}";

            $("#themes").html(css);

        }

        $('.js-theme').click(function(){
            setThemeColor();
        });
        setThemeColor();

        moment.lang('ru');

        $('.js-datepicker').each(function(i, el) {
            // pre-initialize some values
            var $el = $(el);
            var config = $el.data('datepicker-config');

            $el.data('datepicker.selectedDate', moment(config.selectedDate));
        });

        $('.js-datepicker').each(function(i, el) {
            var $el = $(el);
            var config = $el.data('datepicker-config');

            function getDaysOfWeek() {
                var daysOfTheWeek = [];
                for (var i = 0; i < 7; i++) {
                    daysOfTheWeek.push(moment().weekday(i).format('dd'));
                }

                return daysOfTheWeek;
            }

            var compiledTemplate = _.template($('#calendar-template').html());

            function render(data) {
                data.selectedDate = $el.data('datepicker.selectedDate');
                data.relatedDatepickerDate = null;
                if (config.relatedDatepicker) {
                    data.relatedDatepickerDate = $(config.relatedDatepicker).data('datepicker.selectedDate');
                    data.relatedDatepickerMode = config.relatedDatepickerMode;
                }

                return compiledTemplate(data);
            }

            var clndr = $el.clndr({
                daysOfTheWeek: getDaysOfWeek(),
                startWithMonth: $el.data('datepicker.selectedDate'),
                render: render,
                clickEvents: {
                    click: function(target) {
                        var selectedDate = target.date;
                        $el.data('datepicker.selectedDate', selectedDate);

                        _.each(config.targets, function(target) {
                            var val = selectedDate.format(target.format);

                            switch (target.target) {
                                case 'url':
                                    var queryString = {};
                                    document.location.search.replace(
                                        new RegExp("([^?=&]+)(=([^&]*))?", "g"),
                                        function ($0, $1, $2, $3) {
                                            queryString[$1] = decodeURIComponent($3);
                                        }
                                    );
                                    queryString[target.query] = val;
                                    document.location.search = $.param(queryString);
                                    break;

                                default:
                                    $(target.target).val(val);
                            }
                        });

                        clndr.render();
                    }
                }
            });
        });

        $('body').delegate('.js-toggle-button', 'change', function(e){
            $el = $(e.currentTarget);
            $el.parents('.js-toggle-block').find('.active').removeClass('active');
            $el.parent().addClass('active');
        });

//        collapseBreadcrumbs($('.js-collapsable-breadcrumbs'));
    });

    function showCompanyPhones()
    {
        $('.js-hide-phone').append("<span class='see show-phone js-disabled-hidden'><i class='brace'>(</i><span class='link clickable js-show-phone'>показать</span><i class='brace'>)</i></span>");
        $('body').delegate('.js-show-phone', 'click', function(event) {
            $el = $(event.currentTarget);
            $el.parents('.show-phone').find('.brace').hide();

            //если телефонов нет, то скрывать нужно
            $el.parents('.js-hide-phone').append("<span class='more-phone-opener link clickable js-popover-opener' data-popover='#more-phones-company-1'>Больше телефонов</span>");

            $el.hide();

            $phone = $el.parents('.js-hide-phone').find('.curr-phone');
            $phone.removeClass('is-gradiented');
            $phone.css({
                'whiteSpace' : 'normal',
                'width' : 'auto'
            })
        });
    }

    function updatePopoverPosition()
    {
        var parentEl = $('.js-popover-centered');
        var calculatedWidth = parentEl.width();
        var popoverWidth = parentEl.find('.drop-wrapper').width();
        var popoverPosition = calculatedWidth/2 - popoverWidth/2;

        parentEl.find('.drop-wrapper').css({
            'left': popoverPosition + 'px'
        });
    }

    function setShowButtonPosition()
    {
        if (isMobile()) {
            return;
        }

        var $button = $('.js-show-all');
        if (!$button.length) {
            return;
        }

        if (isSidebarInWindow()){
            $button.css({
                position: 'relative',
                bottom: 'auto',
                left: 'auto'
            });
        } else {
            var bottom = $(window).height() - ($('#footer').offset().top - $(window).scrollTop());

            $button.css({
                position: 'fixed',
                bottom: bottom > 0 ? bottom + 'px' : 0,
                left: $button.parent().offset().left, // fix for webkit
                zIndex: '105px'
            });
        }
    }

    function isSidebarInWindow()
    {
        var scrollTop = $(window).scrollTop();
        var windowHeight = $(window).height();
        var $button = $('.js-show-all:visible');
        var $el = $('.filters');

        return ($el.height() + $el.offset().top + $button.outerHeight(true)) < (scrollTop + windowHeight);

    }

    function updateFiltersBlockHeight()
    {
        if (isMobile()) {
            return;
        }

        var calculatedHeight = $(window).height() - $('#search-fixed').outerHeight(true);
        var $button = $('.js-show-all');
        if ($button.is(':visible')) {
            calculatedHeight -= $button.outerHeight();
        }

        var contentHeight = $('#content').outerHeight(true);

        var realHeight = $('.js-scrollable-filters .filters').outerHeight(true);
        var height = Math.min(calculatedHeight, realHeight, contentHeight);

        $('.js-scrollable-filters').css({
            maxHeight: height+'px',
            height: height+'px'
        });


    }

    function updateCompanyContactBlockHeight()
    {
        if (isMobile()) {
            return;
        }

        var calculatedHeight = $(window).height() - $('#search-fixed').outerHeight(true);

        $('.js-scrollable-contacts').css({
            maxHeight: calculatedHeight+'px'
        });
    }

    function updatePrivateOfficeLeftMenuHeight()
    {
        if (isMobile()) {
            return;
        }

        var calculatedHeight = $(window).height() - $('#header').outerHeight(true);

        $('.js-calc-height-private-menu').css({
            maxHeight: calculatedHeight+'px'
        });
    }

    function updatePrivateProductContainerHeight()
    {
        if (isMobile()) {
            return;
        }

        var windowHeight = $(window).height();
        var $container = $('.js-private-product-container');

        if (!$container.length) {
            return;
        }

        var $buttonsBlock = $container.find('.bottom-block');
        var $productsBlock = $container.find('.list');
        var $photosBlock = $container.find('.photo-container');
        var headingHeight = $container.find('.heading').outerHeight(true);
        var $bigPhotoBlock = $container.find('.photo-list').find('.big-photo');
        var bigPhotoBlockHeight = $bigPhotoBlock.length ? $bigPhotoBlock.outerHeight(true) : 0;

        var calculateContainerHeight = windowHeight - ($buttonsBlock.outerHeight(true) + $container.offset().top);
        var calculateProductsBlock = calculateContainerHeight - headingHeight;
        var calculatePhotoBlock = calculateContainerHeight - (headingHeight + bigPhotoBlockHeight);
        $productsBlock.css({
            'height': calculateProductsBlock + 'px'
        });

        if ($photosBlock.length) {
          $photosBlock.css({
            'height': calculatePhotoBlock + 'px',
            'minHeight': '50px'
          });

          var rightBlockHeight = bigPhotoBlockHeight + $photosBlock.outerHeight(true);
        } else {
          rightBlockHeight = 0;
        }

        if (calculateProductsBlock < rightBlockHeight) {
            $productsBlock.css({
                'height': rightBlockHeight + 'px'
            });
        }
    }

    function initializeScroll()
    {
        if (Modernizr.touch) {
            return;
        }

        $('.js-scrollable').scrollbar({
            type: 'simple',
            disableBodyScroll: true
        });


    }

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

    function updateContentWideHeight()
    {
        if ($('.js-calc-height').length && !Modernizr.touch) {
            var contentHeight = $('.js-calc-height').outerHeight(true);
            var wrapperHeight = $('.wrapper').outerHeight(true);
            var windowHeight = $(window).height();

            if (contentHeight < windowHeight) {
                $('.js-calc-height').css({
                    'minHeight': wrapperHeight + 'px'
                });

                $('.no-favorites.js-calc-height').css({
                    'height': wrapperHeight + 'px'
                });

            }

        }

        return false;

    }

    function initializeBackgroundRedirect()
    {
        if (Modernizr.touch) {
            return;
        }
        var $container = $('.container');

        if (!$container.data('redirect-url')) {
            return;
        }

        var $insider = $('.inside-container');
        $insider
            .bind('mousemove', function(e) {
                if (e.target === e.currentTarget) {
                    $insider.css({cursor: 'pointer'});
                } else {
                    $insider.css({cursor: 'auto'});
                }
            })
            .bind('click', function(e) {
                if (e.target === e.currentTarget) {
                    if ($container.data('new-window')) {
                        window.open($container.data('redirect-url'));
                    } else {
                        document.location.href = $container.data('redirect-url');
                    }
                }
            });
    }

    function horizontalScroll()
    {
        if ($(window).width() > 1024) {
            return;
        }

        var scrollLeft = $('#main').offset().left;
        $(window).scrollTop(0).scrollLeft(scrollLeft);
    }

    $(document).bind('contenchanged', function(e) {
        initializeForms(e.$el);
    });

    $(window).scroll(function () {
        setShowButtonPosition();
    });

    function isMobile()
    {
        return screen.width < 768 && Modernizr.touch;
    }

    function setSpecialBorderBottom() {
        var $specialTitle = $('.js-special-title');

        if (!$specialTitle.length) {
            return;
        }
        var index = 3;
        if ($(window).width() < 1238) {
            index = 1;
        }
        var $prevEl = $specialTitle.prev('.view-product');
        $prevEl.siblings('.view-product').removeClass('special-border-bottom');
        var i = 1;
        do {
            $prevEl.addClass('special-border-bottom');

            $prevEl = $prevEl.prev('.view-product');
            i += 1;
        } while (index >= i) ;
    }
	function openItem(){
	    $('.wrap-open').each(function () {
	        var hold = $(this);
	        var link = hold.find('.button-close , .button-open');
	        var box = hold.find('.box-open ');
	
	        link.on('click', function (e) {
	            e.preventDefault();
	            if (!hold.hasClass('open')) {
	                hold.addClass('open');
	                box.slideUp(0).slideDown(400);
	            }
	            else {
	                box.slideUp(400, function () {
	                    hold.removeClass('open');
	                });
	            }
	        })
	    })
	}
	function OpenBox(obj){
		$(obj.wrap).each(function(){
			var hold = $(this);
			var link = hold.find(obj.link);
			var box = hold.find(obj.box);
			var w = obj.w;
			var close = hold.find(obj.close);
			
			link.click(function(){
				$(obj.wrap).not(hold).removeClass(obj.openClass);
				if (!hold.hasClass(obj.openClass)) {
					hold.addClass(obj.openClass);
				}
				else {
					hold.removeClass(obj.openClass);
				}
				return false;
			});
			
			hold.hover(function(){
				$(this).addClass('hovering');
			}, function(){
				$(this).removeClass('hovering');
			});
			
			$("body").click(function(){
				if (!hold.hasClass('hovering')) {
					hold.removeClass(obj.openClass);
				}
			});
			close.click(function(){
				hold.removeClass(obj.openClass);
				
				return false;
			});
		});
	}
	function runSelectize(){
		$(function(){
			/*$('.popup-form').submit(function(e){
				e.preventDefault();
			});*/
			
			$('.form-select').selectize({
				create: true
			});
		})
	};

})(jQuery);



(function (w, $)
{
    function collapseBreadcrumbs($breadcrumbsBlocks)
    {
        function isShouldBeCollapsed($breadcrumbs)
        {
            var actualWidth = 0;
            var allowedWidth = $breadcrumbs.width();

            var reserveSelector;
            if (reserveSelector = $breadcrumbs.data('collapsable-breadcrumbs-reserve')) {
                $(reserveSelector).each(function(i, el) {
                    allowedWidth -= $(el).outerWidth(true);
                });
            }

            $breadcrumbs.children().each(function(i, el) {
                actualWidth += $(el).outerWidth(true);
            });

            return actualWidth > allowedWidth && $breadcrumbs.data('collapsable.collapsedItemsCount') < 1;
        }

        function collapseItemsIfNeeded($breadcrumbs, $ignoredItem)
        {
            var $collapsableItems = $breadcrumbs.find('.js-collapsable-item').not($ignoredItem);

            $collapsableItems.sort(function(a, b) {
                return $(b).data('collapsable-breadcrumb-priority') - $(a).data('collapsable-breadcrumb-priority');
            });

            $collapsableItems.each(function(i, item) {
                var $item = $(item);

                if (isShouldBeCollapsed($breadcrumbs)) {
                    $item.data('collapsed.isCollapsed', true);
                    $item.html('...');
                    $item.attr('title', $item.data('collapsed.originalContent'));
                    $item.addClass('collapsed');
                    $breadcrumbs.data('collapsable.collapsedItemsCount', $breadcrumbs.data('collapsable.collapsedItemsCount') + 1);
                }
            });
        }

        function processBreadcrumbsBlock($breadcrumbs)
        {
            $breadcrumbs.data('collapsable.collapsedItemsCount', 0);
            var $collapsableItems = $breadcrumbs.find('.js-collapsable-item');

            $collapsableItems.each(function(i, item) {
                var $item = $(item);

                $item.data('collapsed.originalContent', $item.text());
                $item.data('collapsed.originalContentHtml', $item.html());
                $item.data('collapsed.originalTitle', $item.attr('title'));

                $item.bind('click', function(e) {
                    if (!$item.data('collapsed.isCollapsed')) {
                        return;
                    }

                    $item.data('collapsed.isCollapsed', false);
                    $item.html($item.data('collapsed.originalContentHtml'));
                    $item.attr('title', $item.data('collapsed.originalTitle') || '');
                    $item.removeClass('collapsed');
                    $breadcrumbs.data('collapsable.collapsedItemsCount', $breadcrumbs.data('collapsable.collapsedItemsCount') - 1);

                    e.preventDefault();
                    e.stopImmediatePropagation();

                    collapseItemsIfNeeded($breadcrumbs, $item);
                });
            });

            collapseItemsIfNeeded($breadcrumbs);
        }

        $breadcrumbsBlocks.each(function(i, el) {
            processBreadcrumbsBlock($(el));
        });
    }

    w.collapseBreadcrumbs = collapseBreadcrumbs;
})(window, jQuery);
