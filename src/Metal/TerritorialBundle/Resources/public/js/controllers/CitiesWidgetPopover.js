ngApp.factory('citiesWidgetPopoverLoader', function () {
    var popoversLoaders = {};
    var service = {};

    service.registerLoader = function (popoverName, loader) {
        return popoversLoaders[popoverName] = loader;
    };

    service.load = function (popoverName) {
        return popoversLoaders[popoverName]();
    };

    return service;
});

Metal.CitiesWidgetPopoverOpener = function (citiesWidgetPopoverLoader) {
    this.loadPopover = function (popoverName) {
        citiesWidgetPopoverLoader.load(popoverName);
    };
};

Metal.CitiesWidgetPopover = function ($scope, $http, citiesWidgetPopoverLoader) {
    var originalRegions = [];

    $scope.regionsLoaded = false;
    $scope.preferredCities = [];
    $scope.filteredRegions = [];
    $scope.activeTerritory = null;
    $scope.countryTerritory = null;

    this.initialize = function (popoverName, loadingUrl, loadingOptions) {
        citiesWidgetPopoverLoader.registerLoader(popoverName, function () {
            if ($scope.regionsLoaded) {
                return;
            }

            $http({
                method: 'post',
                url: loadingUrl,
                data: loadingOptions
            }).success(function (response) {
                originalRegions = response.regions;
                $scope.preferredCities = response.preferredCities;
                $scope.activeTerritory = response.activeTerritory;
                $scope.countryTerritory = response.countryTerritory;
                $scope.filteredRegions = angular.copy(originalRegions);
                $scope.regionsLoaded = true;
            });
        });
    };

    $scope.$watch('territorialTitle', function () {
        if (!$scope.territorialTitle) {
            $scope.filteredRegions = angular.copy(originalRegions);

            return;
        }

        var criteria = $scope.territorialTitle.toLowerCase();
        var regions = angular.copy(originalRegions);
        $scope.filteredRegions = [];

        for (var i = 0, n = regions.length; i < n; i++) {
            var region = regions[i];
            if (region.title.toLowerCase().indexOf(criteria) > -1) {
                $scope.filteredRegions.push(region);
            } else {
                var cities = [];
                var primaryCity, primaryCityShouldBeAdded = false;
                for (var j = 0, m = region.cities.length; j < m; j++) {
                    var city = region.cities[j];

                    if (city.isAdministrativeCenter) {
                        primaryCity = city;
                    }

                    if (city.title.toLowerCase().indexOf(criteria) > -1) {
                        cities.push(city);
                        if (!city.url) {
                            primaryCityShouldBeAdded = true;
                        }
                    }
                }
                if (primaryCityShouldBeAdded && cities.indexOf(primaryCity) === -1) {
                    cities.push(primaryCity);
                }
                if (cities.length) {
                    region.cities = cities;
                    $scope.filteredRegions.push(region);
                }
            }
        }
    });

    this.handleSelection = function (territoryObj, kind) {
        document.location.href = territoryObj.url;
    }
};

Metal.CitiesWidgetSearchPopover = function ($scope, $rootScope, $controller, $sce) {
    angular.extend(this, $controller('Metal.CitiesWidgetPopover', {$scope: $scope}));

    this.handleSelection = function (territoryObj, kind) {
        $scope.activeTerritory = territoryObj;
        $rootScope.searchTerritory = territoryObj;
        $rootScope.searchTerritoryString = kind + '-' + territoryObj.id;
        $rootScope.searchUrl = $sce.trustAsResourceUrl(territoryObj.url);
    }
};
