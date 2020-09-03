Metal.Reviews = function ($scope, popups, $http, $collection) {
    var me = this;

    $scope.reviews = $collection.getInstance();
    $scope.hasMore = false;
    $scope.page = 1;
    me.popups = popups;

    this.options = {
        loadMoreReviewsUrl: null
    };

    $scope.answerReviewAjaxFormOptions = {
        eventsPrefix: 'answerreview.'
    };

    $scope.$on('answerreview.ajaxformdone', function(e, data) {
        if (data.data.status !== 'success') {
            return;
        }

        var answeredReview = data.data.answeredReview;
        $scope.reviews.add(answeredReview);

        angular.forEach($scope.reviews.all(), function(review) {
            if (review && review.id == answeredReview.id) {
                review = answeredReview;
            }
        });

        //TODO: придумать что-то покрасивее, что бы не завязываться на дом-элемент попапа
        me.popups.close('#review-answer');

    });

    this.moderateReview = function (review) {
        review.isProcessing = true;

        $http({
            method: 'POST',
            url: review.moderateUrl
        })
            .success(function (data) {
                review.isModerated = true;
            });
    };

    this.deleteReview = function (review) {

        $http({
            method: 'POST',
            url: review.deleteUrl
        })
            .success(function (data) {
                review.isDeleted = true;
            });
    };

    this.loadMoreReviews = function() {
        $scope.reviewsLoading = true;
        $scope.page++;

        $http({
            url: me.options.loadMoreReviewsUrl,
            method: 'GET',
            params: {
                'page' : $scope.page
            }
        })
            .success(function(data) {
                $scope.reviews.addAll(data.reviews);
                $scope.hasMore = data.hasMore;
                $scope.reviewsLoading = false;
            });
    };
};
