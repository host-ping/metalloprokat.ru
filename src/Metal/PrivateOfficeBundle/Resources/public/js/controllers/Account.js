Metal.Account = function($scope,  $http) {
    this.options = {
        deleteAvatarUrl: null
    };
    var me = this;

    $scope.avatar = null;
    $scope.loadingLogo = false;
    $scope.options = {autoUpload: true};

    $scope.$on('fileuploadstart', function(e, data) {
        $scope.loadingLogo = true;
    });

    $scope.$on('fileuploaddone', function(e, data) {
        $scope.avatar = data.result.avatar;
        $scope.loadingLogo = false;
    });

    this.setOptions = function(options) {
        this.options = options;
    };

    this.deleteAvatar = function() {
        $scope.loadingLogo = true;
        $scope.avatar = null;

        $http({
            method: 'POST',
            url: me.options.deleteAvatarUrl
        })
            .success(function (data) {
                $scope.loadingLogo = false;
            });
    };
};

