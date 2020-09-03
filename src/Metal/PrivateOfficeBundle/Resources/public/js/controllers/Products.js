Metal.Products = function ($scope, $http, popups, $collection, $timeout, $q) {

    $scope.allProductsIds = [];
    $scope.pendingProductsIds = [];

    $scope.products = $collection.getInstance();
    $scope.selectedProductsIds = [];
    $scope.companyPhotos = $collection.getInstance();
    $scope.commonPhotos = $collection.getInstance();
    $scope.selectedPhoto = null;
    $scope.editableProduct = null;
    $scope.expandedProduct = null;
    $scope.globalOperationProcessing = false;
    $scope.productsLoading = false;
    $scope.pageForPhoto = 0;
    $scope.perPage = 20;
    $scope.productsLoadingConfig = {};
    $scope.filials = $collection.getInstance();
    $scope.filial = {};
    $scope.scheduledActualizationFormSubmitting = false;
    $scope.categories = [];

    $scope.productPhotoAjaxFormOptions = {
        eventsPrefix: 'productsphoto.'
    };
    $scope.productAddAjaxFormOptions = {
        eventsPrefix: 'productadd.'
    };
    $scope.productEditAjaxFormOptions = {
        eventsPrefix: 'productedit.'
    };
    $scope.selectTimeAjaxFormOptions = {
        eventsPrefix: 'selecttime.'
    };
    $scope.productsImportAjaxFormOptions = {
        eventsPrefix: 'productsimport.'
    };

    this.options = {
        loadMoreProductsUrl: null,
        loadMoreCompanyPhotosUrl: null,
        loadProductsIdsUrl: null,
        deleteProductsUrl: null,
        disconnectPhotoFromProductUrl: null,
        moveProductsToCategoryUrl: null,
        changeOfferStatusUrl: null,
        actualizeProductsUrl: null,
        loadCategoriesUrl: null,
        forCustomCategories: null
    };

    var me = this;
    var productsLoadingTimeout = null;
    var companyPhotosLoadingTimeout = null;
    var productsLoadingXHR = null;
    var skipFilterByTitle = false;

    this.selectedAllProducts = false;
    this.popups = popups;

    this.setAllProductsIds = function (productsIds) {
        $scope.allProductsIds = angular.copy(productsIds);
    };

    this.selectPhoto = function (companyPhoto) {
        $scope.selectedPhoto = companyPhoto;
    };

    this.editProduct = function (product) {
        // если попали сюда со странички переноса товаров по кастомных категориях - запрет на редактирование товаров
        if (me.options.forCustomCategories) {
            return;
        }
        $scope.editableProduct = angular.copy(product);
        $scope.expandedProduct = null;
    };

    this.expandProduct = function (product) {
        $scope.expandedProduct = angular.copy(product);
    };

    $scope.$on('productsphoto.ajaxformdone', function (e, data) {
        if (data.data.status !== 'success') {
            return;
        }

        var uploadedPhoto = data.data.uploadedPhoto;
        $scope.companyPhotos.add(uploadedPhoto);

        angular.forEach($scope.products.all(), function (product) {
            if (product.photo && product.photo.id == uploadedPhoto.id) {
                product.photo = uploadedPhoto;
            }
        });

        //TODO: придумать что-то покрасивее, что бы не завязываться на дом-элемент попапа
        me.popups.close('#upload-product-image-popup');
    });

    $scope.$on('productadd.ajaxformdone', function (e, data) {
        if (data.data.status !== 'success') {
            return;
        }

        var uploadedPhoto = data.data.uploadedPhoto;
        if (uploadedPhoto) {
            $scope.companyPhotos.add(uploadedPhoto);
        }

        me.reloadProducts();

        //TODO: придумать что-то покрасивее, что бы не завязываться на дом-элемент попапа
        me.popups.close('#add-product-popup');
    });

    $scope.$on('productsimport.ajaxformdone', function (e, data) {
        if (data.data.errors) {
            return;
        }

        $('#errors-load-products').html($.trim(data.data.html));
        //TODO: придумать что-то покрасивее, что бы не завязываться на дом-элемент попапа
        me.popups.close('#add-products-popup');
        me.popups.open({popup: '#errors-load-products'});
        //TODO: переделать это на директиву angular
        $('#errors-load-products .js-scrollable').scrollbar({
            type: 'simple',
            disableBodyScroll: true
        });

        me.reloadProducts();

        //TODO: придумать что-то покрасивее, что бы не завязываться на дом-элемент попапа
        me.popups.close('#add-products-popup');
    });

    $scope.$on('productedit.ajaxformbefore', function (e) {
        $scope.globalOperationProcessing = true;
        $scope.$apply();
    });

    $scope.$on('productedit.ajaxformalways', function (e, data) {
        $scope.globalOperationProcessing = false;

        // здесь используется `always` вместо `done` потому что `always` вызывается позже.
        // После того, как мы обнулим `editableProduct` форма удалится из DOM, соответственно дальнейшие обработчики
        // не смогут корректно выполниться. Именно поэтому мы не вызываем `$scope.$apply()` в конце этого метода
        // достаточно его вызова в стандартном обработчике для `always`

        if (data.dataOrJqXHR.status === 'success') {
            $scope.editableProduct = null;
            $scope.expandedProduct = null;
            $scope.products.add(data.dataOrJqXHR.product);
            $scope.productsUpdatedAt = data.dataOrJqXHR.productsUpdatedAt;
        }
    });

    $scope.$on('selecttime.ajaxformbefore', function (e) {
        $scope.scheduledActualizationFormSubmitting = true;
    });

    $scope.$on('selecttime.ajaxformalways', function (e, data) {
        $scope.scheduledActualizationFormSubmitting = false;
    });

    $scope.$watch('filterByTitle', function (newValue, oldValue) {
        if (typeof newValue === 'undefined' || oldValue == newValue) {
            return;
        }

        if (skipFilterByTitle) {
            skipFilterByTitle = false;

            return;
        }

        //TODO: use debounce/throttle
        if (productsLoadingTimeout) {
            $timeout.cancel(productsLoadingTimeout);
        }

        productsLoadingTimeout = $timeout(function () {
            me.reloadProducts();
            productsLoadingTimeout = null;
        }, 500);
    });

    $scope.$watch('photosSearch', function (newValue, oldValue) {
        if (typeof newValue === 'undefined' || oldValue == newValue) {
            return;
        }

        //TODO: use debounce/throttle
        if (companyPhotosLoadingTimeout) {
            $timeout.cancel(companyPhotosLoadingTimeout);
        }

        companyPhotosLoadingTimeout = $timeout(function () {
            me.reloadCompanyPhotos();
            companyPhotosLoadingTimeout = null;
        }, 500);
    });

    this.loadMoreProducts = function () {
        $scope.page++;
        this.loadProducts();
    };

    this.loadProducts = function () {
        $scope.productsLoading = true;

        var productsIds = [];
        for (var offset = $scope.perPage * ($scope.page - 1), i = offset, n = $scope.allProductsIds.length; i < (offset + $scope.perPage) && i < n; i++) {
            productsIds.push($scope.allProductsIds[i]);
        }

        $http({
            url: me.options.loadMoreProductsUrl,
            method: 'GET',
            params: {
                'productsIds[]': productsIds
            }
        })
            .success(function (data) {
                $scope.products.addAll(data.products);
                $scope.productsLoading = false;
                updatePrivateProductContainerHeight();
            });
    };

    this.reloadProducts = function () {
        $scope.productsLoading = true;
        $scope.page = 1;
        $scope.products.removeAll();

        if (productsLoadingXHR) {
            productsLoadingXHR.resolve();
        }

        productsLoadingXHR = $q.defer();

        var filters = {
            'title': $scope.filterByTitle,
            'special_offer': $scope.specialOffer ? 1 : 0,
            'hot_offer': $scope.hotOffer ? 1 : 0,
            'uncategorized': $scope.uncategorized ? 1 : 0,
            'filial_id': $scope.filial.id,
            'status_filter': $scope.productsLoadingConfig.filtersStatuses.query,
            'photo_filter': $scope.productsLoadingConfig.filtersPhotos.query,
        };


        if ($scope.filterByCategory) {
            if ($scope.filterByCategory.isCustomCategory) {
                filters['custom_category_id'] = $scope.filterByCategory.id;
            } else {
                filters['category_id'] = $scope.filterByCategory.id;
            }
        }

        $http({
            url: me.options.loadProductsIdsUrl,
            method: 'GET',
            timeout: productsLoadingXHR.promise,
            params: {
                filters: filters,
                sort: $scope.productsLoadingConfig.orderBy.query,
            }
        })
            .success(function (data) {
                $scope.allProductsIds = data.productsIds;
                $scope.pendingProductsIds = data.pendingProductsIds;
                me.loadProducts();
                $scope.globalOperationProcessing = false;
                productsLoadingXHR = null;
            });
    };

    this.reloadCompanyPhotos = function () {
        $scope.companyPhotos.removeAll();
        $scope.pageForPhoto = 0;

        this.loadMoreCompanyPhotos();
    };

    this.loadMoreCompanyPhotos = function () {
        $scope.photosLoading = true;
        $scope.pageForPhoto++;

        $http({
            url: me.options.loadMoreCompanyPhotosUrl,
            method: 'GET',
            params: {
                'page': $scope.pageForPhoto > 1 ? $scope.pageForPhoto : null,
                'photosSearch': $scope.photosSearch
            }
        })
            .success(function (data) {
                $scope.companyPhotos.addAll(data.companyPhotos);
                $scope.hasMore = data.hasMore;
                $scope.photosLoading = false;
            });
    };

    this.deleteProduct = function (productId) {
        $scope.globalOperationProcessing = true;
        $http({
            url: me.options.deleteProductsUrl,
            method: 'POST',
            data: {
                products: [productId]
            }
        })
            .success(function (data) {
                var idx = $scope.selectedProductsIds.indexOf(productId);
                if (idx > -1) {
                    $scope.selectedProductsIds.splice(idx, 1);
                }

                $scope.products.remove($scope.products.get(productId));

                idx = $scope.allProductsIds.indexOf(productId);
                $scope.allProductsIds.splice(idx, 1);
                $scope.globalOperationProcessing = false;
                $scope.productsUpdatedAt = data.productsUpdatedAt;
            });
    };

    this.deleteProducts = function () {
        $scope.globalOperationProcessing = true;
        $http({
            url: me.options.deleteProductsUrl,
            method: 'POST',
            data: {
                products: $scope.selectedProductsIds
            }
        })
            .success(function (data) {
                angular.forEach($scope.selectedProductsIds, function (productId) {

                    var idx = $scope.allProductsIds.indexOf(productId);
                    $scope.allProductsIds.splice(idx, 1);

                    $scope.products.remove($scope.products.get(productId));
                });
                $scope.selectedProductsIds = [];
                $scope.globalOperationProcessing = false;
                $scope.productsUpdatedAt = data.productsUpdatedAt;
            });
    };

    this.exportProducts = function (url) {
        $scope.globalOperationProcessing = true;
        $http({
            url: url,
            method: 'POST',
            data: {
                products: $scope.allProductsIds
            }
        })
            .success(function (data) {
                $('body').append($('<iframe></iframe>').attr('src', data.url).hide());
                $scope.globalOperationProcessing = false;
            });
    };


    this.toggleProductSelection = function (productId) {
        var idx = $scope.selectedProductsIds.indexOf(productId);
        if (idx > -1) {
            $scope.selectedProductsIds.splice(idx, 1);
        } else {
            $scope.selectedProductsIds.push(productId);
        }
        this.selectedAllProducts = $scope.selectedProductsIds.length == $scope.allProductsIds.length;
    };

    this.toggleAllProductsSelection = function (check) {
        this.selectedAllProducts = check;
        if (this.selectedAllProducts) {
            $scope.selectedProductsIds = angular.copy(_.difference($scope.allProductsIds, $scope.pendingProductsIds));
        } else {
            $scope.selectedProductsIds = [];
        }
    };

    this.deleteSelectedPhoto = function () {
        $scope.globalOperationProcessing = true;
        $http({
            url: $scope.selectedPhoto.deleteUrl,
            method: 'POST'
        })
            .success(function () {
                $scope.companyPhotos.remove($scope.companyPhotos.get($scope.selectedPhoto.id));

                angular.forEach($scope.products.all(), function (product) {
                    if (product.photo && product.photo.id == $scope.selectedPhoto.id) {
                        delete product.photo;
                    }
                });

                $scope.selectedPhoto = null;
                $scope.globalOperationProcessing = false;
            });
    };

    this.connectProductsWithPhoto = function () {
        $scope.globalOperationProcessing = true;
        $http({
            url: $scope.selectedPhoto.connectProductsUrl,
            method: 'POST',
            data: {
                products: $scope.selectedProductsIds,
                photo_id: $scope.selectedPhoto.id
            }
        })
            .success(function (data) {
                $scope.globalOperationProcessing = false;
                var companyPhoto = $scope.companyPhotos.get($scope.selectedPhoto.id);

                angular.forEach($scope.selectedProductsIds, function (productId) {
                    var product = $scope.products.get(productId);
                    if (product) {
                        product.photo = companyPhoto;
                    }
                });
                $scope.productsUpdatedAt = data.productsUpdatedAt;
                $scope.selectedProductsIds = [];
                $scope.selectedPhoto = null;
            });
    };

    this.disconnectPhotoFromProduct = function (productId) {
        $scope.globalOperationProcessing = true;
        $http({
            url: me.options.disconnectPhotoFromProductUrl,
            method: 'POST',
            data: {
                product: productId
            }
        })
            .success(function (data) {
                var product = $scope.products.get(productId);
                delete product.photo;

                $scope.globalOperationProcessing = false;
                $scope.productsUpdatedAt = data.productsUpdatedAt;
            });
    };

    this.moveProductsToCategory = function (categoryId) {
        //TODO: передавать сюда не айди категории а именно категорию, соответственно не отдавать ее с сервера
        $scope.globalOperationProcessing = true;

        $http({
            url: me.options.moveProductsToCategoryUrl,
            method: 'POST',
            data: {
                products: $scope.selectedProductsIds,
                category_id: categoryId
            }
        })
            .success(function (data) {
                angular.forEach($scope.selectedProductsIds, function (productId) {
                    var product = $scope.products.get(productId);
                    if (me.options.forCustomCategories) {
                        product.customCategory = data.customCategory;
                    } else {
                        product.category = data.category;
                    }
                });

                $scope.globalOperationProcessing = false;
                $scope.productsUpdatedAt = data.productsUpdatedAt;
                $scope.selectedProductsIds = [];
            });
    };

    this.changeProductsOfferStatus = function (fieldName, status) {
        $scope.globalOperationProcessing = true;

        $http({
            url: me.options.changeOfferStatusUrl,
            method: 'POST',
            data: {
                products: $scope.selectedProductsIds,
                status: status,
                fieldName: fieldName
            }
        })
            .success(function (data) {
                angular.forEach($scope.selectedProductsIds, function (productId) {
                    var product = $scope.products.get(productId);
                    product[fieldName] = status;
                });

                $scope.globalOperationProcessing = false;
                $scope.productsUpdatedAt = data.productsUpdatedAt;
                $scope.selectedProductsIds = [];
            });
    };

    this.moveProductToCategory = function (categoryId, categoryTitle) {
        if (!$scope.editableProduct.category) {
            $scope.editableProduct.category = {};
        }
        $scope.editableProduct.category.id = categoryId;
        $scope.editableProduct.category.title = categoryTitle;
    };

    this.actualizeProducts = function () {
        $scope.globalOperationProcessing = true;
        $http({
            url: me.options.actualizeProductsUrl,
            method: 'POST'
        })
            .success(function (data) {
                $scope.globalOperationProcessing = false;
                $scope.productsUpdatedAt = data.productsUpdatedAt;
            });
    };

    this.setFilial = function (filial) {
        $scope.filial = filial;
        me.loadCategories();
        me.reloadProducts();
    };

    this.loadCategories = function () {
        $http({
            url: me.options.loadCategoriesUrl,
            method: 'GET',
            params: {
                filialId: $scope.filial.id
            }
        })
            .success(function (data) {
                $scope.categories = data.categories;
            });
    };

    this.setFilterByCategory = function (category) {
        $scope.filterByCategory = category;
        skipFilterByTitle = true;
        $scope.filterByTitle = category.title;
        me.reloadProducts();
        $scope.filterByCategory = null;
    }
};


//SettingsController1.prototype.addContact = function() {
//    this.contacts.push({type: 'email', value: 'yourname@example.org'});
//};

