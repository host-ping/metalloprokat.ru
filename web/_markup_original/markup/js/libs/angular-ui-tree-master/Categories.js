Metal.Categories = function ($scope, $timeout) {
    var me = this;
    $scope.formSubmitting = false;


    this.saveItems = function (categories) {
        $scope.formSubmitting = true;

        $timeout(callAtTimeout, 5000);
    };

    function callAtTimeout() {
        $scope.formSubmitting = false;
    }

    this.remove = function (scope) {
        scope.remove();
    };

    this.newSubItem = function (scope) {
        var nodeTitle = window.prompt('Название категории');
        var nodeData = scope.$modelValue;

        if (!nodeTitle) {
            return;
        }

        nodeData.nodes.push({
            title: nodeTitle,
            nodes: []
        });
    };

    this.newItem = function () {
        var nodeTitle = window.prompt('Название категории');

        if (nodeTitle == null) {
            return;
        }
        $scope.categories.push({
            id: $scope.categories.length + 1,
            title: nodeTitle,
            parentId: null,
            nodes: []
        });
    };

    this.visible = function (item) {
        return !($scope.query && $scope.query.length > 0 && item.title.indexOf($scope.query) == -1);

    };

    this.findNodes = function () {

    };

    $scope.categories = [{
        'id': 1,
        'title': 'node1',
        'nodes': [
            {
                'id': 11,
                'title': 'node1.1',
                'nodes': [
                    {
                        'id': 111,
                        'title': 'node1.1.1',
                        'nodes': []
                    }
                ]
            },
            {
                'id': 12,
                'title': 'node1.2',
                'nodes': []
            }
        ]
    }, {
        'id': 2,
        'title': 'node2',
        'nodrop': true, // An arbitrary property to check in custom template for nodrop-enabled
        'nodes': [
            {
                'id': 21,
                'title': 'node2.1',
                'nodes': []
            },
            {
                'id': 22,
                'title': 'node2.2',
                'nodes': []
            }
        ]
    }, {
        'id': 3,
        'title': 'node3',
        'nodes': [
            {
                'id': 31,
                'title': 'node3.1',
                'nodes': []
            }
        ]
    }];
};

