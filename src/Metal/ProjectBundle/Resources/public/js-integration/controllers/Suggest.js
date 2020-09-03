Metal.Suggest = function ($scope, $http, $q) {

    this.options = {
        suggestUrl: null
    };

    var me = this;
    var suggestLoadingXHR;

    // typeahead-loading doesn't work properly with request aborts
    $scope.loadingSuggestions = false;

    this.getSuggestions = function (query) {
        $scope.loadingSuggestions = true;
        //TODO: remove boilerplate
        if (suggestLoadingXHR) {
            suggestLoadingXHR.resolve();
        }

        suggestLoadingXHR = $q.defer();

        //TODO: use debounce/throttle
        return $http.get(me.options.suggestUrl, {
            timeout: suggestLoadingXHR.promise,
            params: {
                query: query
            }
        }).then(function (result) {
            console.log(result);
            suggestLoadingXHR = null;
            $scope.loadingSuggestions = false;
            var items = [];
            angular.forEach(result.data, function (item) {
                items.push(item);
            });

            return items;
        });
    };
};

