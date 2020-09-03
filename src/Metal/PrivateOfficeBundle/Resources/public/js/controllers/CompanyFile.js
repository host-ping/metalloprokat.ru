Metal.CompanyFile = function ($scope, $http, $collection) {

    this.icons = {};
    var me = this;

    $scope.documents = $collection.getInstance();
    $scope.editableDocument = null;
    $scope.globalOperationProcessing = false;

    $scope.documentAddAjaxFormOptions = {
        eventsPrefix: 'companyfileadd.'
    };
    $scope.documentEditAjaxFormOptions = {
        eventsPrefix: 'companyfileedit.'
    };

    $scope.$on('companyfileedit.ajaxformbefore', function (e) {
        $scope.globalOperationProcessing = true;
        $scope.$apply();
    });

    $scope.$on('companyfileedit.ajaxformalways', function (e, data) {
        $scope.globalOperationProcessing = false;

        if (data.dataOrJqXHR.status === 'success') {
            $scope.editableDocument = null;
            $scope.documents.add(data.dataOrJqXHR.document);
            //TODO: добавить updatedAt
        }
    });

    $scope.$on('companyfileadd.ajaxformdone', function (e, data) {
        if (data.data.status !== 'success') {
            return;
        }

        var companyFile = data.data.document;
        companyFile.iconUrl = me.icons[companyFile.extension]

        $scope.documents.add(companyFile);
    });

    this.initializeDocuments = function (companyFiles) {
        for (var i = 0, n = companyFiles.length; i < n; i++) {
            companyFiles[i].iconUrl = this.icons[companyFiles[i].extension]
        }

        $scope.documents.addAll(companyFiles);
    };

    this.editDocument = function (document) {
        $scope.editableDocument = angular.copy(document);
    };

    this.deleteCompanyFile = function (companyFile) {
        $scope.globalOperationProcessing = true;
        $http({
            method: 'POST',
            url: companyFile.deleteUrl,
            data: {
                companyFile: companyFile.id
            }
        })
            .success(function (data) {
                $scope.documents.remove(companyFile);
                $scope.globalOperationProcessing = false;
            });
    };
};




