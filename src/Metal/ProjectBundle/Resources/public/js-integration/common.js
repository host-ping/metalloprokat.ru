function updateHref()
{
    $('a[data-href]').each(function(i, el) {
        $(el)
            .attr('href', $(el).attr('data-href'))
            .removeAttr('data-href');
    });
}

var displayedCompaniesIds = {};
var hideCompanyPhones = {};
var sameCompanyCounter = 0;

function hidePhones() {
    $('.js-phone').not('[data-hidden-phone]').each(function (i, el) {
        var $el = $(el);
        var companyId = $el.data('phone-of-company');

        if ($el.html().length >= 40) {
            hideCompanyPhones[companyId] = true;
        }
            $el.attr('data-hidden-phone', true);

        if (!displayedCompaniesIds[companyId] || hideCompanyPhones[companyId]) {
            $el.addClass('is-gradiented');
        }

        if (hideCompanyPhones[companyId] && !$el.data('minisite-phone')) {
                $el.after("<span class='see show-phone js-show-phone js-popover-opener' data-popover='#more-phones-"+ companyId +'-'+sameCompanyCounter+"'><i class='brace'>(</i><span class='link clickable'>показать тел.</span><i class='brace'>)</i></span>");
                $el.parent().after("<div id='more-phones-"+ companyId +'-'+sameCompanyCounter+"' class='more-phones drop-wrapper opacity-border'><div class='dropdown'>"+ $el.html() +"</div></div>");
                sameCompanyCounter++;
        } else if (!displayedCompaniesIds[companyId]) {
            $el
                .after("<span class='see show-phone js-show-phone'><i class='brace'>(</i><span class='link clickable'>показать тел.</span><i class='brace'>)</i></span>");

        }
    });
}

function initializePhoneHandler() {
    $('body').delegate('.js-show-phone', 'click', function (event) {
        event.preventDefault();

        var $el = $(event.currentTarget);
        var $dataEl = $el.siblings('.js-phone');
        var companyId = $dataEl.data('phone-of-company');

        var $els = $('.js-phone[data-phone-of-company="' + companyId + '"]');
        if (!hideCompanyPhones[companyId] || $dataEl.data('minisite-phone')) {
            $els.removeClass('is-gradiented');
            $els.siblings('.see').remove();
        }

        if (!displayedCompaniesIds[companyId]) {
            $.ajax({
                url: $dataEl.data('url'),
                type: 'POST',
                data: {
                    'source': $dataEl.data('source'),
                    'object_id': $dataEl.data('object-id'),
                    'object_kind': $dataEl.data('object-kind'),
                    'category_id': $dataEl.data('category-id') ? $dataEl.data('category-id') : null
                },
                success: function (data) {
                }
            });
            displayedCompaniesIds[companyId] = true;
        }
    });
}

function scrollToElement($container) {
    $(window).scrollTop($container.offset().top - $('.search-block').height() - 5);
}

$(document).ready(function() {
    updateHref();
    hidePhones();
    initializePhoneHandler();

    var supportsHtml5History = !!(window.history && window.history.pushState);

    $(window).bind('popstate', function (e) {
        if (!supportsHtml5History) {
            //console.log('Browser not support html5 history.');
            return;
        }

        var state = e.originalEvent.state;

        // console.log('popstate', state.url, $(state.el), $(state.container));

        loadPagePart(state.url, state.el);
    });

    function loadPagePart(url, el) {
        var referer = document.location.href;
        var $loadingMask = $(el).siblings('.loading-mask');

        $loadingMask.removeClass('g-hidden');

        $(window).scrollTop($loadingMask.offset().top - ($(window).height() - $loadingMask.outerHeight(true)) / 2);

        $.ajax({
            url: url,
            type: 'GET',
            success: function (data) {
                $.each(data, function (key, value) {
                    var $container = $('[data-replacement-zone="' + key + '"]');
                    var cb = $container.data('replacement-callback');

                    switch ($container.data('replacement-mode')) {
                        case 'text':
                            $container.text($.trim(value));
                            break;

                        default:
                            var $newElem = $($.trim(value));
                            $container.html($newElem);
                            initializePagePart($newElem);
                            break;
                    }

                    if (cb) {
                        cb = window[cb];
                        cb($container);
                    }
                });

                $(window).trigger('pagechange', {referer: referer});

                $loadingMask.addClass('g-hidden');
            },
            error: function (jqXHR, textStatus, errorThrown) {
                //FIXME: проверить этот кейс, когда с сервера возвращается ошибка
                document.location.href = url;
                $loadingMask.addClass('g-hidden');
            }
        });
    }

    $('body').delegate('.js-pagination', 'click', function(event) {
        if (!supportsHtml5History) {
            //console.log('Browser not support html5 history.');
            return;
        }

        event.preventDefault();

        var $el = $(event.currentTarget);
        //TODO: если на странице несколько пагинаций - нужно сделать что-то типа data-region и по нему выбирать
        //TODO: неплохо было бы подменять также другие блоки, которые зависят от страницы

        var url = $el.attr('href');
        var el = '.js-pagination';
        var state;

        if (!history.state) {
            // http://stackoverflow.com/questions/13633278/event-state-of-history-object-in-html-5-is-null
            // http://stackoverflow.com/questions/11092736/window-onpopstate-event-state-null
            state = {
                'url': document.location.href,
                'el': el
            };
            // console.log('initial', state);

            history.replaceState(state, document.title, document.location.href);
        }

        loadPagePart(url, el);

        state = {
            'url': url,
            'el': el
        };
        // console.log('add entry', state);

        history.pushState(state, $el.text(), url);
    });

    function initializePagePart($newElement) {
        includeAjax($newElement);
        updateHref();
        processAnnouncements();
        hidePhones();
        initializeScroll();
    }

    $('body').delegate('.js-load-more', 'click', function(event) {
        event.preventDefault();
        var $el = $(event.currentTarget);
        $el.siblings('.loading-mask')
            .removeClass('g-hidden');

        $.ajax({
            url: $el.attr('href'),
            type: 'GET',
            success: function(data) {
                var html = typeof data === 'object' ? data.html : data;
                var $newEl = $($.trim(html));
                $($el.data('load-more-replace')).replaceWith($newEl);

                initializePagePart($newEl);
            }
        });
    });

    $('body').delegate('.js-replace-load-more', 'click', function (event) {
        //TODO: объединить с .js-load-more
        event.preventDefault();
        var $el = $(event.currentTarget);
        var $loadingMask = $($el.data('loading-mask'));
        $loadingMask.removeClass('g-hidden');

        $.ajax({
            url: $el.data('url'),
            type: 'POST',
            data: $el.data('send-data'),
            success: function (data) {
                var html = typeof data === 'object' ? data.html : data;
                var $newEl = $($.trim(html));
                $($el.data('load-more-replace')).replaceWith($newEl);

                initializeForms($($el.data('load-more-replace')));
                $loadingMask.addClass('g-hidden');
                updateHref();
                updateFiltersBlockHeight();
                $(window).trigger('resize');
            }
        });
    });

    // each(function(i, el) {el.reset()});

    processAnnouncements();

    $('body').delegate('.js-ajax-form-submit', 'submit', function(event) {
        event.preventDefault();

        var $form = $(event.currentTarget);

        if ($form.data('submit')) {
            return;
        }
        $form.data('submit', true);

        resetFormErrors($form);

        var $button = $form.find(':submit');
        var $lMask = $button.siblings('.loading-mask');
        if (!$lMask.length) {
            $lMask = $button.after('<div class="loading-mask"><div class="spinner"></div></div>').siblings('.loading-mask');
        }
        $lMask.removeClass('g-hidden');

        if (!$form.data('brouzieAjaxform')) {
            $form.fileupload({autoUpload: false, replaceFileInput: false});

            $form.ajaxform()
                .on('ajaxformdone', function(e, eventData) {
                    var data = eventData.data;

                    $lMask.addClass('g-hidden');
                    $form.data('submit', false);
                    if (data.errors) {
                        highlightFormErrors($form, data.errors);
                        $form.trigger('response.error', data);
                    } else {
                        if (data.message) {
                            alert(data.message);
                            if ($form.data('success') === 'show-alert-and-close-popup') {
                                Brouzie.Popups.closePopup($form.parents('.popup-block'));
                            }
                        } else if ($form.data('success') === 'reload') {
                            location.reload();
                        } else if (data.data) {
                            bindDataToDomNew($form, data.data);
                        } else if ($form.data('success') === 'close-popup') {
                            Brouzie.Popups.closePopup($form.parents('.popup-block'));
                        } else if ($form.data('success') === 'redirect') {
                            document.location.href = data.redirect_to ? data.redirect_to : $form.data('redirect-location');
                        } else if ($form.data('success') === 'close-popup-and-show-message') {
                            Brouzie.Popups.closePopup($form.parents('.popup-block'));
                            $('#msg').removeClass('g-hidden');
                        }
                        else {
                            // console.log('no bind, no alert')
                        }

                        $form.trigger('response.success', data);
                    }
                });
        }

        $form.ajaxform('submit');
    });

    $('body').delegate('.js-preload-items-count-on-change:input', 'change', function(e) {
        var $inputEl = $(e.currentTarget);
        var $form = $inputEl.parents('form');
        var $buttonBlock = $('.js-show-all');
        var $button = $buttonBlock.find('.show-btn');
        var $lMask = $buttonBlock.find('.loading-mask');
        var url = $form.data('count-url');
        var separator = url.indexOf('?') === -1 ? '?' : '&';

        var attributes = $form.find(':input[name^="attribute"]').serialize();
        var values = $form.find(':input:not([name^="attribute"])').serialize();

        if (values.length) {
            url += separator + values;
        }

        var prevXhr;
        if (prevXhr = $form.data('prev-xhr')) {
            prevXhr.abort();
        }

        $lMask.removeClass('g-hidden');

        prevXhr = $.ajax({
            url: url,
            data: attributes,
            type: 'POST',
            success: function(data) {
                $button.attr('href', data.url);
                $buttonBlock.find('.js-items-count').text(data.countFormatted);
                $lMask.addClass('g-hidden');
            },
            complete: function() {
                $lMask.addClass('g-hidden');
            }
        });
        $form.data('prev-xhr', prevXhr);

        if (!$buttonBlock.is(':visible')) {
            $buttonBlock.removeClass('g-hidden');
            updateFiltersBlockHeight();
            // trigger resize for updating fixed blocks
            $(window).trigger('resize');
        }
    });

    $('body').delegate('.js-print-page', 'click', function () {
        // хак для Оперы
        window.setTimeout(function() {
            window.print();
            return false;
        }, 500);
    });

    $('.js-movable-element').each(function(i, el) {
        var $el = $(el);
        $el.appendTo($el.data('move-to'));
    });
});

//TODO: use reader interface
function bindDataToDomNew($el, reader)
{
    $el.find('[data-bind-value]').each(function(i, el) {
        var $targetEl = $(el);

        $targetEl.val(reader[$targetEl.data('bind-value')]);
        $targetEl.trigger('refresh');
    });

    $el.find('[data-bind-attr]').each(function(i, el) {
        var $targetEl = $(el);
        var config = $targetEl.data('bind-attr').split(':');

        $targetEl.attr(config[0], reader[config[1]]);
    });

    $el.find('[data-bind-text]').each(function(i, el) {
        var $targetEl = $(el);

        $targetEl.text(reader[$targetEl.data('bind-text')]);
    });
}

function clearBindDataInfo($el)
{
    $el.find('[data-bind-value]').removeAttr('data-bind-value');
    $el.find('[data-bind-attr]').removeAttr('data-bind-attr');
    $el.find('[data-bind-text]').removeAttr('data-bind-text');
}

function bindDataToDom($el, $srcEl)
{
    $el.find('[data-bind-value]').each(function(i, el) {
        var $targetEl = $(el);

        $targetEl.val($srcEl.data($targetEl.data('bind-value')));
        $targetEl.trigger('refresh');

    });

    $el.find('[data-bind-attr]').each(function(i, el) {
        var $targetEl = $(el);
        var config = $targetEl.data('bind-attr').split(':');

        $targetEl.attr(config[0], $srcEl.data(config[1]));
    });

    $el.find('[data-bind-text]').each(function(i, el) {
        var $targetEl = $(el);

        $targetEl.text($srcEl.data($targetEl.data('bind-text')));
    });
}

$(document).bind('popup.beforeopen', function(e) {
    var $popup = e.$popup;
    var tmpl = $popup.data('popup-template');
    if (!tmpl) {
        return;
    }

    var $tmpl = $(tmpl);
    if ($tmpl.attr('type') === 'text/ng-template') {
        return;
    }
    $popup.html($tmpl.html());

    $popup.trigger($.Event('contenchanged', {$el: $popup}));
});

$(document).bind('popup.open', function(e) {
    if (e.$opener) {
        bindDataToDom(e.$popup, e.$opener);
    }
});

function resetFormErrors($form) {
    $form.find(':input').removeClass('error');
    //TODO: add support radio
    $form.find('.jq-selectbox__select, .jq-selectbox__checkbox').removeClass('error');
    $form.find('.icon-error-color').remove();
}

function highlightFormErrors($form, errors) {
    if (!errors) {
        return;
    }

    for (var fieldName in errors) {
        var errorMsg = errors[fieldName][0];
        var $el = $form.find(':input[name="' + fieldName + '"]').not('[type="hidden"]');
        var $absoluteParent = $el.data('absoluteParent') || null;

        if (!$el.length) {
            // пока нет возможности менять property path делаем тупо alert
            alert(errorMsg);

            return;
        }

        if ($el.is('select')) {
            var $styledSelect = $el.siblings('.jq-selectbox__select');

            $styledSelect.addClass('error');
            $el.addClass('error');

            $styledSelect.closest('.jq-selectbox')
                .after($('<span></span>')
                    .addClass('icon-error-color js-helper-opener')
                    .data('text', errorMsg)
            );
            if ($absoluteParent) {
                $el.parent().siblings('.icon-error-color')
                    .attr('data-absolute-parent', $absoluteParent);
            }

            //TODO: add support of radios
            //TODO: add support of input type file
        } else if ($el.is(':checkbox')) {
            var $styledCheckbox = $el.closest('.jq-checkbox');
            var $lastEl = $styledCheckbox.siblings(':last');
            if (!$lastEl.length) {
                $lastEl = $styledCheckbox;
            }

            $styledCheckbox.addClass('error');
            $el.addClass('error');

            $lastEl
                .after($('<span></span>')
                    .addClass('icon-error-color js-helper-opener')
                    .data('text', errorMsg)
            )
            ;
        } else {
            $el
                .addClass('error')
                .after($('<span></span>')
                    .addClass('icon-error-color js-helper-opener')
                    .data('text', errorMsg)
            )
            ;
        }

        if ($absoluteParent) {
            $el.siblings('.icon-error-color')
                .attr('data-absolute-parent', $absoluteParent);
        }
    }

    var $body = $('body');
    var $errorEl = $form.find('.icon-error-color:first');

    if (!$errorEl.length) {
        return;
    }

    var $errorInput = $form.find('.error:first');
    var scrollTop = parseInt($errorEl.offset().top);
    var $scrollable = $('#content').find('.js-scrollable');

    if ($scrollable.length) {
        if (!isInWindow($scrollable, $errorInput)) {
            $scrollable.scrollTop(scrollTop - 70);
        }
    } else {
        if (!isInWindow($body, $errorInput)) {
            $body.scrollTop(scrollTop - 70);
        }
    }

    $errorEl.trigger('mouseover');
}

function isInWindow($scrollableEl, $errorInput)
{
    var windowHeight = $(window).height();
    var scrollTop = $scrollableEl.scrollTop();

    return scrollTop <= $errorInput.offset().top - 70 && ($errorInput.height() + $errorInput.offset().top ) < (scrollTop + windowHeight)
}

function processAnnouncements()
{
    var announcementsOptions = [];
    $('.js-announcement').each(function(i, el) {
        var $announcement = $(el);
        if ($announcement.data('announcement-processed')) {
            return;
        }
        $announcement.data('announcement-processed', true);
        announcementsOptions.push($announcement.data('announcement-options'));
    });

    if (!announcementsOptions.length) {
        return;
    }

    $.ajax({
        url: processAnnouncements.loadAnnouncementsUrl,
        data: {query: announcementsOptions},
        type: 'POST',
        success: function (data) {
            var ieFlash;
            try {
                ieFlash = (window.ActiveXObject && (new ActiveXObject("ShockwaveFlash.ShockwaveFlash")) !== false)
            } catch (err) {
                ieFlash = false;
            }
            var isFlashInstalled = ((typeof navigator.plugins != "undefined" && typeof navigator.plugins["Shockwave Flash"] == "object") || ieFlash);

            var zones = [];
            for (var i = 0, n = data.length; i < n; i++) {
                var announcement = data[i];
                zones.push(announcement.zoneSlug);
                var resizableClass = '';
                var announcementWidth = announcement.width + 'px';

                if (announcement.isResizable || announcement.isHtml || announcement.isZip) {
                    resizableClass = 'resizable-announcement';
                    announcementWidth = 100 + '%';
                }

                if (announcement.isBackground && !Modernizr.touch) {
                    $('.js-layout-announcement')
                        .attr('data-redirect-url', announcement.url)
                        .css("background", "url("+announcement.imageUrl+")");
                    if (announcement.fallbackImageUrl) {
                        $('.js-secondary-announcement').css("background", "url("+announcement.fallbackImageUrl+")");
                    }

                    initializeBackgroundRedirect();
                } else {
                    var html = '';
                    var imageUrl;

                    if (announcement.isFlash) {
                        if (isFlashInstalled) {
                            html = '<object type="application/x-shockwave-flash" data="'+ announcement.imageUrl +'" width="'+ announcementWidth +'" height="' + announcement.height + '" style="position: absolute; z-index: 1;">' +
                            '<param name="movie" value="' + announcement.imageUrl + '"/>' +
                            '<param name="quality" value="high" />' +
                            '<param name="wmode" value="transparent" />' +
                            '<embed src="' + announcement.imageUrl + '" quality="high" wmode="transparent" width="'+ announcementWidth +'" height="' + announcement.height + '">' +
                            '</embed></object><a href="' + announcement.url + '" target="_blank" rel="noopener noreferrer" style="position: absolute; z-index: 2; width: '+ announcementWidth +'; height: ' + announcement.height + 'px; top:0; left:0;"></a>';
                        } else {
                            imageUrl = announcement.fallbackImageUrl;
                        }
                    } else if (announcement.isHtml || announcement.isZip) {
                        html = '<iframe src="' + announcement.imageUrl + '" width="'+ announcementWidth +'" height="' + announcement.height + '" scrolling="no">' +
                        '</iframe><a href="' + announcement.url + '" target="_blank" rel="noopener noreferrer" style="position: absolute; z-index: 2; width: '+ announcementWidth +'; height: ' + announcement.height + 'px; top:0; left:0;"></a>';
                    } else {
                        imageUrl = announcement.imageUrl;
                    }

                    if (!html && imageUrl) {
                        var width = announcement.width;
                        var styleDisplay = 'inline';
                        if (announcement.isResizable) {
                            width = '100%';
                            styleDisplay = 'block';
                        }
                        resizableClass = 'resizable-announcement';
                        html = '<a href="' + announcement.url + '" target="_blank" rel="noopener noreferrer"><img src="' + imageUrl + '" width="'+ width +'" height="' + announcement.height + '" style="display: '+ styleDisplay +';" /></a>'
                    }

                    var $targetEl = $('#' + announcement.elementId);

                    if (html) {
                        $targetEl
                            .html(html)
                            .addClass('has-announcement '+resizableClass+' ')
                            .css({width: announcementWidth, height: announcement.height + 'px'})
                        ;
                    }

                    if (announcement.addOnAnnouncement) {
                        $('.title-callback').remove();
                    }

                }
            }
            $(window).trigger('resize');

            // удаляем div для верхних баннеров, если их нет
            var headAnnouncementsSlug = ["head-side-1", "head-side-2", "head-center"];

            if (_.intersection(zones, headAnnouncementsSlug).length == 0) {
                $(".head-announcements-wrapper").remove();
            }

            // удаляем div для vip баннеров, если их нет
            var vipAnnouncementsSlug = ["main-sub-1", "main-sub-2", "main-sub-3"];

            if (_.intersection(zones, vipAnnouncementsSlug).length == 0) {
                $("#vip-announcements").remove();
                $("#vip-announcements-demands").remove();
            }

            //$('.js-announcement').not('.has-announcement').remove();
        }
    });
}

