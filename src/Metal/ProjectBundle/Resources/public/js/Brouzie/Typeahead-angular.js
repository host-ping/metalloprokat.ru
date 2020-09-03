/**
 * @ngdoc directive
 * @name ng.directive:typeahead
 * @restrict ECA
 *
 * @param {string=} typeaheadPrefetchUrl
 * @param {string=} typeaheadRemoteUrl
 * @param {string=} typeaheadLoading
 * @param {string=} typeaheadOnSelect
 * @param {string=} typeaheadThumbprint
 * @param {expression=} typeaheadModel
 * @param {expression=} typeaheadRemoteUrlCallback
 * @param {string=} typeaheadSuggestionTemplateUrl
 */
angular.module('brouzie.typeahead', [])
    .directive('typeahead', ['$http', '$templateCache', '$compile', '$parse', function ($http, $templateCache, $compile, $parse) {
        return {
            link: function (originalScope, element, attrs) {
                if (!attrs.typeaheadPrefetchUrl && !attrs.typeaheadRemoteUrl) {
                    throw new Error('Expected "typeaheadRemoteUrl" or "typeaheadPrefetchUrl" attribute.');
                }

                var $el = angular.element(element);
                var isLoadingSetter = $parse(attrs.typeaheadLoading).assign || angular.noop;
                var onSelectCallback = $parse(attrs.typeaheadOnSelect);
                var thumbprint = attrs.typeaheadThumbprint || '';
                var $setModelValue = $parse(attrs.typeaheadModel).assign;

                var tplContent = '<p ng-bind="item.title"></p>';
                if (attrs.typeaheadSuggestionTemplateUrl) {
                    var tplUrl = $parse(attrs.typeaheadSuggestionTemplateUrl)(originalScope.$parent);
                    $http.get(tplUrl, {cache: $templateCache}).success(function (response) {
                        tplContent = response.trim();
                    });
                }

                var bloodhoundOptions = {
                    datumTokenizer: function(s) { return s.title.split(/(\s+|\/)/); },
                    queryTokenizer: function(s) { return s.split(/\s+/); },
                    limit: 100
                };

                var previousXhr;

                if (attrs.typeaheadPrefetchUrl) {
                    bloodhoundOptions.prefetch = {
                        url: attrs.typeaheadPrefetchUrl,
                        thumbprint: thumbprint,
                        ajax: {
                            beforeSend: function (xhr) {
                                isLoadingSetter(originalScope, true);
                            },
                            complete: function () {
                                isLoadingSetter(originalScope, false);
                                originalScope.$apply();
                            }
                        }
                    };
                } else {
                    bloodhoundOptions.remote = {
                        url: attrs.typeaheadRemoteUrl,
                        wildcard: '__QUERY__',
                        ajax: {
                            beforeSend: function (xhr) {
                                isLoadingSetter(originalScope, true);
                                originalScope.$apply();

                                if (previousXhr) {
                                    previousXhr.abort();
                                }
                                previousXhr = xhr;
                            },
                            success: function () {
                                previousXhr = null;
                                isLoadingSetter(originalScope, false);
                                originalScope.$apply();
                            }
                        }
                    };

                    if (attrs.typeaheadRemoteUrlCallback) {
                        var fn = $parse(attrs.typeaheadRemoteUrlCallback);

                        bloodhoundOptions.remote.replace = function (url, query) {
                            return fn(originalScope, {url: url, query: query});
                        };
                    }
                }

                var suggestSource = new Bloodhound(bloodhoundOptions);
                suggestSource.initialize();

                $el.typeahead({
                    minLength: 2,
                    autoselect: true,
                    hint: false,
                    highlight: true
                }, {
                    source: suggestSource.ttAdapter(),
                    displayKey: 'title',
                    templates: {
                        empty: '<div class="empty-message">Результаты не найдены</div>',
                        suggestion: function (item) {
                            var tplScope = originalScope.$new();
                            tplScope.item = item;

                            var html = $compile(tplContent)(tplScope);
                            tplScope.$apply();

                            return html;
                        }
                    }
                })
                    .bind('typeahead:selected', function (e, item) {
                        if (attrs.typeaheadClearOnSelect) {
                            $el.typeahead('val', '');
                        }
                        $setModelValue(originalScope, item);
                        onSelectCallback(originalScope, {$event: e, $item: item});
                        originalScope.$apply();
                    });
            }
        }
    }]);
