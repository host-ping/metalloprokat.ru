(function (w, $) {
    MetalMaps.CompaniesMap = function (el, opts, companies) {
        var self = this;
        ymaps.ready(function () {
            self.initialize(el, opts, companies);
        });
    };

    MetalMaps.CompaniesMap.prototype.initialize = function (el, opts, companies) {
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

        geoObjects.add(placemarksWithProducts);

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

})(window, jQuery);

