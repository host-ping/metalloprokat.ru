Metal.Address = function ($scope, $http, $collection) {
    $scope.company = null;
    $scope.changingSlug = false;
    $scope.addressMsg = true;

    $scope.addressEditAjaxFormOptions = {
        eventsPrefix: 'addressedit.'
    };

    $scope.$on('addressedit.ajaxformbefore', function (e, data) {
        $scope.changingSlug = true;
        $scope.$apply();
    });

    $scope.$on('addressedit.ajaxformalways', function (e, data) {
        $scope.changingSlug = false;

        if (data.dataOrJqXHR.status === 'error') {
            $scope.addressMsg = false;
        } else {
            $scope.addressMsg = true;
            $scope.company = data.dataOrJqXHR.companyArray;
        }
    });

};
