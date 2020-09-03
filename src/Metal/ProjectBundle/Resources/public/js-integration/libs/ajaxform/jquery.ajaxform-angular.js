/* jshint nomen:false */
/* global define, angular */

(function (factory) {
    'use strict';
    if (typeof define === 'function' && define.amd) {
        // Register as an anonymous AMD module:
        define([
            'jquery',
            'angular'
        ], factory);
    } else {
        factory(jQuery, angular);
    }
}(function ($, angular) {
    'use strict';

    angular.module('brouzie.ajaxform', [])
        .provider('ajaxForm', function () {
            var $config;

            $config = this.defaults = {
                before: function (e) {
                    // nothing to do here yet
                },
                done: function (e, data) {
                    var scope = $(this).ajaxform('option', 'scope');

                    scope.$apply(function () {
                        scope.ajaxFormDone = true;
                        scope.ajaxFormResponse = data.data;
                        //TODO: use option for this?
                        angular.extend(scope, data.data);
                    });
                },
                fail: function (e, data) {
                    var scope = $(this).ajaxform('option', 'scope');

                    scope.$apply(function () {
                        scope.ajaxFormFail = true;
                    });
                },
                always: function (e, data) {
                    var scope = $(this).ajaxform('option', 'scope');

                    scope.$apply(function () {
                        scope.ajaxFormSubmitting = false;
                        scope.ajaxFormSubmitted = true;
                    });
                }
            };
            this.$get = [
                function () {
                    return {
                        defaults: $config
                    };
                }
            ];
        })

        .controller('AjaxFormController', [
            '$scope', '$element', '$attrs', 'ajaxForm',
            function ($scope, $element, $attrs, ajaxForm) {
                $element.on('submit', function(e) {
                    e.preventDefault();

                    $scope.ajaxFormSubmit();
                });

                $scope.ajaxFormSubmit = function () {
                    if ($scope.ajaxFormSubmitting) {
                        return;
                    }
                    //TODO: обдумать, может быть есть более правильный вариант
                    //делаем фунцию лёгкой, без $scope.$apply, а вызов apply переносим выше
                    if ($scope.$root.$$phase != '$apply' && $scope.$root.$$phase != '$digest') {
                        $scope.$apply(function() {
                            resetScope();
                            $scope.ajaxFormSubmitting = true;
                        });
                    } else {
                        resetScope();
                        $scope.ajaxFormSubmitting = true;
                    }

                    $element.ajaxform('submit');
                };

                function resetScope() {
                    $scope.ajaxFormSubmitting = false;
                    $scope.ajaxFormSubmitted = false;
                    $scope.ajaxFormDone = false;
                    $scope.ajaxFormFail = false;
                    $scope.ajaxFormResponse = null;
                }
                resetScope();

                $element
                    .ajaxform(angular.extend(
                        {scope: $scope},
                        ajaxForm.defaults
                    ))
                    .on([
                        'ajaxformbefore',
                        'ajaxformdone',
                        'ajaxformfail',
                        'ajaxformalways'
                    ].join(' '), function (e, data) {
                        if ($scope.$emit(e.type, data).defaultPrevented) {
                            e.preventDefault();
                        }

                        var eventsPrefix = $element.ajaxform('option', 'eventsPrefix');
                        if (eventsPrefix) {
                            if ($scope.$emit(eventsPrefix + e.type, data).defaultPrevented) {
                                e.preventDefault();
                            }
                        }
                    })
                ;

                // Observe option changes:
                $scope.$watch(
                    $attrs.ajaxForm,
                    function (newOptions) {
                        if (newOptions) {
                            $element.ajaxform('option', newOptions);
                        }
                    }
                );
            }
        ])

        .directive('ajaxForm', function () {
            return {
                controller: 'AjaxFormController',
                scope: true
            };
        })
    ;
}));
