Metal.Categories = function ($scope, popups, $http) {
    var me = this;

    $scope.formSubmitting = false;
    this.options = {
        saveItemsUrl: null
    };

    this.saveItems = function (categories) {
        $scope.formSubmitting = true;
        $http({
            method: 'POST',
            url: me.options.saveItemsUrl,
            data: {
                categories: categories
            }
        })
            .success(function () {
                $scope.formSubmitting = false;
            });
    };

    this.newSubItem = function (scope) {
        var category = scope.$modelValue;

        scope.expand();

        category.nodes.push({
            title: '',
            focused: true,
            nodes: []
        });
    };

    this.newItem = function () {
        $scope.categories.push({
            title: '',
            focused: true,
            nodes: []
        });
    };

    this.collapseAll = function () {
        $scope.$broadcast('angular-ui-tree:collapse-all');
    };

    this.expandAll = function () {
        $scope.$broadcast('angular-ui-tree:expand-all');
    };
};
