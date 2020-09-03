(function (w, $) {
    MetalMaps.CompaniesMap = function (el, opts, companies, $toggles) {
        var self = this;
        ymaps.ready(function () {
            self.initialize(el, opts, companies, $toggles);
        });
    };

    MetalMaps.CompaniesMap.prototype.initialize = function (el, opts, companies, $toggles) {
        this.map = new ymaps.Map(el, {
            center: opts.center,
            zoom: opts.zoom,
            controls: [
                new ymaps.control.TypeSelector(),
                new ymaps.control.ZoomControl({options: {position: {left: 10, top: 10}}})
            ]
        }, {
            maxZoom: 21,
            minZoom: 4,
            suppressMapOpenBlock: true
        });
        var geoObjects = this.map.geoObjects;

        this.map.events.add('click', function (e) {
            var map = e.get('target');
            if (map.balloon) {
                map.balloon.close();
            }
        });

        var placemarksWithProducts = new ymaps.GeoObjectCollection();
        var placemarksWithoutProducts = new ymaps.GeoObjectCollection();

        this.placemarks = {};
        for (var i = 0, n = companies.length; i < n; i++) {
            var company = companies[i];
            var placemark = this.getCompanyPlacemark(company);
            if (company.has_products) {
                placemarksWithProducts.add(placemark);
            } else {
                placemarksWithoutProducts.add(placemark);
            }
        }

        $toggles.bind('click', function(e) {
            var $activeEl = $(e.currentTarget);

            $toggles
                .toggleClass('active')
                .toggleClass('clickable');

            if ($activeEl.data('filter') === 'allCompanies') {
                geoObjects.add(placemarksWithoutProducts);
            } else {
                geoObjects.remove(placemarksWithoutProducts);
            }
        });

        geoObjects.add(placemarksWithProducts);
        if ($toggles.filter('.active').data('filter') == 'allCompanies') {
            geoObjects.add(placemarksWithoutProducts);
        }
    };

    MetalMaps.CompaniesMap.prototype.getCompanyPlacemark = function (company) {
        var iconStyle = 'company-no-products';
        if (company.has_products) {
            iconStyle = 'company-products';
        }

        var options = MetalMaps.iconFactory(iconStyle);
        options.balloonLayout = MetalMaps.layoutFactory('companyBalloonLayout');
        options.balloonShadow = false;

        var placemark = new ymaps.Placemark(company.coord, {
            //hintContent: company.title,
            balloonContent: company.title,
            company: company
        }, options);

        this.placemarks[company.id] = placemark;

        return placemark;
    }

    function initializeSmallMap()
    {
        var $maps = $('.js-map-with-placemark');

        if (!$maps.length) {
            return;
        }
        ymaps.ready(function () {
            $maps.each(function (i, el) {
                var $el = $(el);

                if ($el.parents('.js-map-wrapper').is(':visible')) {
                    var coord = $el.data('placemark');

                    var map = new ymaps.Map(el, {
                        center: coord,
                        zoom: 15,
                        controls: [new ymaps.control.ZoomControl({options: {position: {left: 10, top: 10}, size: 'small'}})]
                    }, {
                        maxZoom: 21,
                        minZoom: 4,
                        suppressMapOpenBlock: true
                    });

                    if ($el.parent().hasClass('js-resizable-map')) {
                        $el.parent().resizable({
                            handles: 's',
                            minHeight: 100,
                            maxHeight: 1000,
                            stop: function () {
                                map.container.fitToViewport();
                            }
                        });
                    }

                    var placemark = new ymaps.Placemark(coord, {
                        hintContent: $el.data('placemark-title')
                    }, MetalMaps.iconFactory('company'));

                    map.geoObjects.add(placemark);
                    $el.attr('data-map-init', true);
                }

            });
        });
    }

    w.initializeSmallMap = initializeSmallMap;

})(window, jQuery);


$(document).ready(function () {
    initializeSmallMap();
    $('.js-block-switcher').bind('click', function(el) {
        var $el = $(el.currentTarget);
        var $mapEl = $($el.data('switch-block')).find('.js-map-with-placemark');

        if (!$mapEl.data('map-init')) {
            initializeSmallMap();
            $mapEl.attr('data-map-init', true);
        }
    });
});
