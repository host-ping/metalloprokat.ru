Metal.Company = function ($scope) {
    $scope.$watch('category', function (item) {
        processItem(item, $('#category-list-container'), $('#new-category-row'));
    });

};

Metal.CompanyLogo = function ($scope, $http, popups) {
    this.options = {
        deleteLogoUrl: null
    };

    var me = this;

    $scope.logo = null;
    $scope.loadingLogo = false;
    $scope.options = {
        autoUpload: false
    };
    $scope.companyLogoAjaxFormOptions = {
        eventsPrefix: 'companylogo.'
    };

    $scope.$on('companylogo.ajaxformbefore', function (e, data) {
        $scope.loadingLogo = true;
        $scope.$apply();
    });

    $scope.$on('companylogo.ajaxformdone', function (e, data) {
        $scope.logo = data.data.logo;
        $scope.loadingLogo = false;

        //TODO: придумать что-то покрасивее, что бы не завязываться на дом-элемент попапа
        me.popups.close('#upload-company-logo-popup');
    });

    this.setOptions = function (options) {
        this.options = options;
    };

    this.popups = popups;

    this.deleteLogo = function () {
        $scope.loadingLogo = true;
        $scope.logo = null;

        $http({
            method: 'POST',
            url: me.options.deleteLogoUrl
        })
        .success(function (data) {
            $scope.loadingLogo = false;
        });
    };
};
