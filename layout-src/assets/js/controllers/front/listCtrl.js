'use strict';
/**
 * List Controller
 */
app.controller('ListCtrl', [
    '$rootScope',
    '$scope',
    '$http',
    'SweetAlert',
    'securityService',
    '$filter',
    '$timeout',
    function (
        $rootScope,
        $scope,
        $http,
        SweetAlert,
        securityService,
        $filter,
        $timeout,
        $location
    ) {
        $scope.dataLoaded = false;
        $scope.amountMax = 0;
        $scope.filter = {};
        var requestData = {};
        var config = {
            headers: {
                'Content-Type': 'application/json;charset=utf-8;'
            }
        };

        $rootScope.homepage = true;
        $scope.advanceFilter = {};
        $rootScope.marker = [];
        $scope.units = [];
        $scope.currencies = [];
        $scope.types = [];
        $scope.flexibility = [];
        $scope.seniority = [];
        $scope.categories = [];
        $scope.projectArea = [];
        $scope.languages = [];

        /**
         * Load data for homepage
         */
        $scope.getData = function () {
            $http.get('/api/v2/front/categories/', requestData, config)
                .then(function (response) {
                    if (response.status === 200) {
                        $rootScope.categories = response.data;
                        $scope.dataLoaded = true;
                    } else {
                        $scope.responseDetails = "Data: " + response +
                            "<hr />status: " + status +
                            "<hr />headers: " + header +
                            "<hr />config: " + config;
                    }
                }, function (response, status, header, config) {
                    $scope.responseDetails = "Data: " + response +
                        "<hr />status: " + status +
                        "<hr />headers: " + header +
                        "<hr />config: " + config;
                });
        };

        // fix for reload page
        if ($location.path() === '/front/list') {
            $scope.getData();
        }
    }]);
