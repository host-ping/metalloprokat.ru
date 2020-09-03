Metal.Subscription = function ($scope, popups, $http, $collection) {
    var me = this;
    me.popups = popups;
    $scope.items = $collection.getInstance();
    $scope.loadingItems = true;
    $scope.formSubmitting = false;

    this.options = {
        loadItemsUrl: null,
        loadSubscribedItemsUrl: null,
        updateItemsUrl: null,
        updateDemandSubsribtionStatusUrl: null
    };

    $scope.demandSubscriptionAjaxFormOptions = {
        eventsPrefix: 'demandsubscription.'
    };

    $scope.$on('demandsubscription.ajaxformdone', function (e, data) {
        if (data.data.status !== 'success') {
            return;
        }
        //TODO: придумать что-то покрасивее, что бы не завязываться на дом-элемент попапа
        me.popups.close('#demands-subscriptions');
    });

    this.loadItems = function () {
        $http({
            method: 'GET',
            url: me.options.loadItemsUrl
        })
            .success(function (items) {
                $http({
                    method: 'GET',
                    url: me.options.loadSubscribedItemsUrl
                })
                    .success(function (subscribedItemsIds) {
                        $scope.items.addAll(items);
                        items = $scope.items.all();

                        for (var i = 0, n = items.length; i < n; i++) {
                            var item = items[i];
                            if (item.parentId) {
                                var parent = $scope.items.get(item.parentId);
                                if (!parent.children) {
                                    parent.children = [];
                                }
                                parent.children.push(item);
                            }
                        }

                        for (i = 0, n = subscribedItemsIds.length; i < n; i++) {
                            $scope.items.get(subscribedItemsIds[i]).subscribed = true;
                        }
                        $scope.loadingItems = false;
                    });
            });

    };

    this.toggleItemSubscription = function (item, checked) {
        item.subscribed = checked;
        function unCheckParent(item, checked) {
            if (item.parentId) {
                var parent = $scope.items.get(item.parentId);
                parent.subscribed = checked;
                if (parent.parentId) {
                    unCheckParent(parent, checked);
                }
            }
        }

        if (checked == false) {
            unCheckParent(item, checked);
        }

        function checkChild(item, checked) {
            if (item.children) {
                for (var i = 0, n = item.children.length; i < n; i++) {
                    item.children[i].subscribed = checked;
                    if (item.children[i].children) {
                        checkChild(item.children[i], checked);
                    }
                }
            }
        }

        checkChild(item, checked);
    };

    this.updateItems = function () {
        $scope.formSubmitting = true;
        var items = $scope.items.all();
        var itemsIds = [];
        for (var i = 0, n = items.length; i < n; i++) {
            if (items[i].subscribed) {
                itemsIds.push(items[i].id);
            }
        }

        $http({
            method: 'POST',
            url: me.options.updateItemsUrl,
            data: {
                items_ids: itemsIds
            }
        })
            .success(function () {
                $scope.formSubmitting = false;
            });
    };

    this.updateDemandSubscriptionStatus = function (newStatus) {
        $http({
            method: 'POST',
            url: me.options.updateDemandSubsribtionStatusUrl,
            data: {
                checked: newStatus
            }
        })
            .success(function () {
                $scope.subsctiptionStatus = $scope.demandSubscriptionStatuses[newStatus];
            });
    };
};
