function includeAjax($newEl) {
    var injector = angular.element(document.body).injector();
    $newEl.each(function (i, el) {
        injector.invoke(function ($compile) {
            var scope = angular.element(el).scope();
            scope.$apply(function () {
                $compile(el)(scope);
            });
        });
    });
}

function processItem(item, $container, $tmpl) {
    if (!item || !item.id) {
        return;
    }

    var tmpl = $tmpl.html();
    tmpl = tmpl.replace(/__name__/g, $container.find('.js-changed-element').length);

    $container.append(tmpl);

    var $row = $container.find('.js-changed-element:last');

    bindDataToDomNew($row, item);
    clearBindDataInfo($row);
}

angular.module('metalCommon', [])
    .filter('filesize', function () {
        var units = [
            'bytes',
            'KB',
            'MB',
            'GB',
            'TB',
            'PB'
        ];

        return function (bytes, precision) {
            if (isNaN(parseFloat(bytes)) || !isFinite(bytes)) {
                return '?';
            }
            //TODO: нужно учитывать правила форматирования текущей локали и неплохо бы переводить рамеры
            precision = precision || 2;

            var unit = 0;

            while (bytes >= 1024) {
                bytes /= 1024;
                unit++;
            }

            return bytes.toFixed(+precision) + ' ' + units[unit];
        };
    })
    .directive('initialize', function () {
        return {
            compile: function compile(temaplateElement, templateAttrs) {
                return {
                    pre: function (scope, element, attrs) {
                        scope.$eval(attrs.initialize);
                    }
                }
            },
            priority: 800,
            restrict: 'A'
        }
    })
    .directive('styledCheckbox', ['$parse', function ($parse) {
        return {
            restrict: 'A',
            require: '?ngModel',
            link: function (scope, element, attrs, ngModel) {
                element.styler();

                element.on('change', function (event) {
                    attrs.$set('checked', element[0].checked);
                    if (attrs.ngClick) {
                        var fn = $parse(attrs.ngClick);

                        scope.$apply(function () {
                            //TODO: тут есть неконсистенция: мы мы вешаемся на change а обработчик ожидает click
                            fn(scope, {$event: event});
                        });
                    }

                    if (ngModel) {
                        scope.$apply(function () {
                            ngModel.$setViewValue(element[0].checked);
                        });
                    }
                });

                scope.$watch(attrs.ngChecked, function (value, oldValue) {
                    window.setTimeout(function () {
                        element.trigger('refresh');
                    }, 1);
                });
            }
        };
    }])
    .directive('styledSelect', ['$parse', function ($parse) {
        return {
            restrict: 'A',
            require: '?ngModel',
            link: function (scope, element, attrs, ngModel) {
                element.styler({selectSearch: false});

                scope.$watch('checked', function () {
                    element.trigger('refresh');
                });
            }
        };
    }])
    .directive('jsScrollable', function () {
        return {
            restrict: 'A',
            link: function (scope, element) {
                element.scrollbar({
                    type: 'simple',
                    disableBodyScroll: true
                });
            }
        }
    })
    .filter('flattenTreeFilter', ['$filter', function ($filter) {
        return function (items, q) {
            var enableParents = function (item) {
                var parentId = item.parentId;

                if (parentId) {
                    hashMap[parentId].__visible = true;
                    enableParents(hashMap[parentId]);
                }
            };

            var hashMap = {};
            var i, n = items.length;
            var result = [];

            for (i = 0; i < n; i++) {
                hashMap[items[i].id] = items[i];
                items[i].__visible = false;
            }

            var filteredItems = $filter('filter')(items, {title: q});

            for (i = 0; i < filteredItems.length; i++) {
                var item = filteredItems[i];
                item.__visible = true;

                if (item.__visible) {
                    enableParents(item);
                }
            }
            // console.dir(hashMap);

            for (i = 0; i < n; i++) {
                item = items[i];
                if (item.__visible) {
                    result.push(item);
                }
                delete item.__visible;
            }

            // console.log(filteredItems);

            return result;
        };
    }]);

(function() {
    'use strict';
    angular
        .module('focus-if', [])
        .directive('focusIf', focusIf);

    focusIf.$inject = ['$timeout'];

    function focusIf($timeout) {
        function link($scope, $element, $attrs) {
            var dom = $element[0];
            if ($attrs.focusIf) {
                $scope.$watch($attrs.focusIf, focus);
            } else {
                focus(true);
            }
            function focus(condition) {
                if (condition) {
                    $timeout(function() {
                        dom.focus();
                    }, $scope.$eval($attrs.focusDelay) || 0);
                }
            }
        }
        return {
            restrict: 'A',
            link: link
        };
    }
})();

if (typeof fileuploadAvailable === 'undefined') {
    var fileuploadAvailable = false;
}

var ngModules = ['gettext', 'ngCollection', 'metalCommon', 'brouzie.popups', 'brouzie.typeahead', 'pasvaz.bindonce', 'initialValue', 'ui.tree', 'vs-repeat', 'focus-if'];
if (typeof fileuploadAvailable !== 'undefined' && fileuploadAvailable) {
    ngModules.push('brouzie.ajaxform');
    ngModules.push('blueimp.fileupload');
}

var ngApp = angular.module('metalApp', ngModules);
ngApp.config(function ($interpolateProvider) {
    $interpolateProvider.startSymbol('<%=');
    $interpolateProvider.endSymbol('%>');
});

ngApp.config(['$httpProvider', function ($httpProvider) {
    $httpProvider.defaults.headers.common["X-Requested-With"] = 'XMLHttpRequest';
}]);

//ngApp.config(['$locationProvider', function ($locationProvider) {
//    $locationProvider.html5Mode({
//        enabled: true,
//        requireBase: false,
//        rewriteLinks: false
//    });
//}]);

// disable angular routing for preventing conflicts with html5 history api
ngApp
    .config(['$provide', function ($provide) {
        $provide.decorator('$browser', ['$delegate', function ($delegate) {
            $delegate.onUrlChange = function () {
            };

            $delegate.url = function () {
                return '';
            };

            return $delegate;
        }]);
    }]);

if (fileuploadAvailable) {
    // https://github.com/angular/angular.js/issues/1375
    // https://github.com/uor/angular-file/blob/master/src/angular-file.js

    ngApp.config(function (fileUploadProvider) {
        var orig = fileUploadProvider.defaults.add;
        fileUploadProvider.defaults.add = function (e, data) {
            orig.call(fileUploadProvider, e, data);

            var scope = data.scope,
                filesCopy = [];
            angular.forEach(data.files, function (file) {
                filesCopy.push(file);
            });

            var model = data.fileInput.attr('file-model');
            var multiple = data.fileInput.attr('multiple');

            if (model) {
                scope.$apply(function () {
                    scope[model] = multiple ? filesCopy : filesCopy[0];
                });
            }
        };
    });

    ngApp.config(function (ajaxFormProvider) {
        var origBefore = ajaxFormProvider.defaults.before;
        ajaxFormProvider.defaults.before = function (e, data) {
            origBefore.call(this, e, data);

            var $form = $(e.target);
            resetFormErrors($form);
        };

        var origDone = ajaxFormProvider.defaults.done;
        ajaxFormProvider.defaults.done = function (e, data) {
            origDone.call(this, e, data);

            var $form = $(e.target);
            if (data.data && data.data.errors) {
                highlightFormErrors($form, data.data.errors);
            }
        };
    });
}

ngApp.run(function (gettextCatalog) {
    gettextCatalog.currentLanguage = 'ru';
    gettextCatalog.setStrings('ru', {
        selected_products_n: ['Выбран <%= selectedProductsIds.length|number %> товар', 'Выбрано <%= selectedProductsIds.length|number %> товара', 'Выбрано <%= selectedProductsIds.length|number %> товаров'],
        total_products_n: ['Всего <%= allProductsIds.length|number %> товар', 'Всего <%= allProductsIds.length|number %> товара', 'Всего <%= allProductsIds.length|number %> товаров']
    });
});

var Metal = {};

Metal.Core = function ($scope, popups) {
    this.popups = popups;

    this.confirm = function (caption, onSuccess, onError) {
        function processHandler(handler) {
            if (handler) {
                if (!angular.isArray(handler)) {
                    // fn -> [fn]
                    handler = [handler];
                }

                // fn -> fn, [args]
                if (!angular.isDefined(handler[1])) {
                    handler[1] = [];
                }

                if (angular.isArray(handler[1])) {
                    // [fn, [args]] -> [[fn, [args]]]
                    handler = [handler];
                }

                for (var i = 0, n = handler.length; i < n; i++) {
                    //TODO: preserve the scope
                    var fn = handler[i][0], args = handler[i][1];
                    fn.apply(fn, args);
                }
            }
        }

        if (confirm(caption)) {
            processHandler(onSuccess);
        } else {
            processHandler(onError);
        }
    };
};

Metal.Search = function ($scope, $rootScope, $sce) {
    $scope.$watch('q', function (item) {
        if (item) {
            document.location = item.url;
        }
    });

    this.buildUrl = function (url, query) {
        return url.replace('__QUERY__', encodeURIComponent(query)) + '&searchTerritory=' + $scope.searchTerritoryString;
    };

    this.initialize = function (activeTerritory) {
        $rootScope.searchTerritory = activeTerritory;
        $rootScope.searchTerritoryString = activeTerritory.kind + '-' + activeTerritory.id;
        $rootScope.searchUrl = $sce.trustAsResourceUrl(activeTerritory.url);
    }
};

Metal.Wizzard = function ($scope, $filter, $timeout) {

    var prevFilterValue = {};
    $scope.$watchCollection('filter', function (current, prev) {
        var diff = array_diff_assoc(current, prevFilterValue);

        _.each(diff, function (val, key) {
            // скрываем все значения атрибутов для атрибута
            var attrs = $scope.attrs[key];
            for (var i = 0, n = attrs.length; i < n; i++) {
                attrs[i].visible = false;
            }

            // показываем только те, которые попали под критерий фильтрации
            var filteredAttrs = $filter('filter')(attrs, {label: val});
            for (i = 0, n = filteredAttrs.length; i < n; i++) {
                filteredAttrs[i].visible = true;
            }
            // сбрасываем состояние чекбокса "отметить все" когда обнуляем фильтр
            if (!val) {
                $scope.checkAll[key] = false;
            }
        });

        prevFilterValue = angular.copy(current);
    });

    this.toggleCheckboxes = function (fieldName, checked) {
        var attrs = $scope.attrs[fieldName];
        for (var i = 0, n = attrs.length; i < n; i++) {
            if (attrs[i].visible) {
                attrs[i].checked = checked;
            }
        }
    }
};

function array_diff_assoc(arr1) {
    //  discuss at: http://phpjs.org/functions/array_diff_assoc/
    // original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // bugfixed by: 0m3r
    //  revised by: Brett Zamir (http://brett-zamir.me)
    //   example 1: array_diff_assoc({0: 'Kevin', 1: 'van', 2: 'Zonneveld'}, {0: 'Kevin', 4: 'van', 5: 'Zonneveld'});
    //   returns 1: {1: 'van', 2: 'Zonneveld'}

    var retArr = {},
        argl = arguments.length,
        k1 = '',
        i = 1,
        k = '',
        arr = {};

    arr1keys: for (k1 in arr1) {
        for (i = 1; i < argl; i++) {
            arr = arguments[i];
            for (k in arr) {
                if (arr[k] === arr1[k1] && k === k1) {
                    // If it reaches here, it was found in at least one array, so try next value
                    continue arr1keys;
                }
            }
            retArr[k1] = arr1[k1];
        }
    }

    return retArr;
}
