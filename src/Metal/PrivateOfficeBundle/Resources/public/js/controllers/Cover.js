Metal.Cover = function($scope, $http, $collection) {

    var me = this;

    $scope.activeCover = null;
    $scope.coverOptions = {
        autoUpload: true
    };
    $scope.coverFormOptions = {
        eventsPrefix: 'cover.'
    };
    $scope.minisiteCovers = $collection.getInstance();
    $scope.loadingCover = false;
    $scope.globalLoadingMask = false;

    $scope.$on('cover.ajaxformbefore', function(e, data) {
        $scope.loadingCover = true;
    });

    $scope.$on('cover.ajaxformdone', function(e, data) {
        if (data.data.errors) {
            alert(data.data.errors[data.paramName[0]][0]);
            $scope.loadingCover = false;
            return false;
        }

        $scope.minisiteCovers.add(data.data.cover);
        $scope.loadingCover = false;
    });

    this.setActiveCover = function(cover) {
        $scope.globalLoadingMask = true;
        $scope.activeCover = cover;

        $http({
            method: 'POST',
            url: cover.applyCoverUrl
        })
            .success(function (data) {
                $scope.globalLoadingMask = false;
            });
    };
};

