/* jshint nomen:false */
/* global define, angular */

(function (factory) {
    'use strict';
    if (typeof define === 'function' && define.amd) {
        // Register as an anonymous AMD module:
        define([
            'jquery',
            'angular',
            'brouzie.popups'
        ], factory);
    } else {
        factory(jQuery, angular, Brouzie.Popups);
    }
}(function ($, angular, popups) {
    'use strict';

    angular.module('brouzie.popups', [])
        .provider('popups', function () {
//            https://github.com/likeastore/ngDialog#api
//            https://github.com/ocombe/ocModal

            this.$get = [
                '$compile',
                '$rootScope',
                '$timeout',
                function ($compile, $rootScope, $timeout) {
                    var publicMethods = {
                        open: function (config, $opener) {
                            $opener = $($opener);
                            var defaultConfig = {
                                popup: null, // selector to dom element with popup
//                        tmpl: null, // selector to dom element with popup template
                                scope: null,
                                data: null
                            };
                            config = angular.extend(defaultConfig, config);

                            var $popup;
                            if (config.popup) {
                                $popup = $(config.popup);
                            } else {
                                throw new Error('Expected popup option.');
                            }

                            var tmpl = $popup.data('popup-template');
                            var $tmpl;
                            if (tmpl) {
                                $tmpl = $(tmpl);
                                $popup.html($tmpl.html());
                            }

                            var scope;
                            if (angular.isObject(config.scope)) {
                                scope = config.scope.$new()
                            } else if ($opener.length && config.scope !== false) {
                                scope = $opener.scope().$new();
                            } else {
                                scope = $rootScope.$new();
                            }

                            //TODO: add support of data from opener via data-popup-data attr
                            if (angular.isObject(config.data)) {
                                angular.extend(scope, angular.copy(config.data));
                            }

                            $timeout(function () {
                                $compile($popup)(scope);

                                popups.openPopup($popup, $opener);
                            });
                        },
                        close: function ($popup) {
                            $popup = $($popup);

                            popups.closePopup($popup);
                        }
                    };

                    return publicMethods;
//                    return {
//                        defaults: $config
//                    };
                }
            ];
        })
        .directive('popupOpener', ['$parse', 'popups', function ($parse, popups) {
            return {
                restrict: 'A',
                link: function (scope, element, attrs) {
                    element.on('click', function (event) {
                        popups.open({'popup': attrs.popupOpener}, event.currentTarget)
                    });

                }
            };
        }])
    ;
}));
