(function () {
    var balloonLayoutCfg = {
        template: '#maps__companies__baloon',
        methods: {
            build: function () {
                this.constructor.superclass.build.call(this);

                this._$element = $('.map-balloon', this.getParentElement());
                this._$element.each(function (i, el) {
                    hidePhones();
                    var $el = $(el);
                    var elHeight = $el.height();
                    var $arrowEl = $el.find('.arrow');

                    $el.css({
                        left: -($arrowEl.outerWidth()) + 'px',
                        top: -(elHeight + $arrowEl.height()) + 'px'
                    });
                });
            },
            clear: function () {
                this.constructor.superclass.clear.call(this);
            },
            onSublayoutSizeChange: function () {
                MetalMaps.layouts.companyBalloonLayout.superclass.onSublayoutSizeChange.apply(this, arguments);

                if (!this._isElement(this._$element)) {
                    return;
                }

                this.events.fire('shapechange');
            },
            getShape: function () {
                var self = MetalMaps.layouts.companyBalloonLayout;

                if (!this._isElement(this._$element)) {
                    return self.superclass.getShape.call(this);
                }

                var position = this._$element.position();

                return new ymaps.shape.Rectangle(new ymaps.geometry.pixel.Rectangle([
                    [position.left, position.top],
                    [
                        position.left + this._$element[0].offsetWidth,
                        position.top + this._$element[0].offsetHeight + this._$element.find('.arrow')[0].offsetHeight
                    ]
                ]));
            },

            _isElement: function (element) {
                return element && element[0] && element.find('.arrow');
            }
        }
    };

    MetalMaps.layoutsCfgs.companyBalloonLayout = balloonLayoutCfg;
})();
