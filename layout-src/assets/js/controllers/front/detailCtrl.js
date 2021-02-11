'use strict';
/**
 * Detail Controller
 */
app.controller('DetailCtrl', [
    '$rootScope',
    '$scope',
    '$http',
    '$state',
    '$stateParams',
    'SweetAlert',
    'securityService',
    '$filter',
    '$aside',
    '$location',
    'FileUploader',
    function ($rootScope, $scope, $http, $state, $stateParams, SweetAlert, securityService, $filter, $aside, $location, FileUploader) {
        $scope.id = $stateParams.id;
        $scope.detail = {};
        $scope.dataLoaded = false;
        $rootScope.homepage = false;

        var requestData = {};
        var config = {
            headers: {
                'Content-Type': 'application/json;charset=utf-8;'
            }
        };

        $scope.getData = function () {
            $http.get('/api/v2/front/cms/' + $scope.id, requestData, config)
                .then(function (response) {
                    if (response.status === 200) {
                        $scope.detail  = response.data.job;
                        if (typeof $scope.detail.notFound !== 'undefined' && $scope.detail.notFound === true) {
                            $location.path('/not-found');
                        }
                        $scope.dataLoaded = true;
                    }
                }, function (response, status, header, config) {
                    $scope.responseDetails = "Data: " + response +
                        "<hr />status: " + status +
                        "<hr />headers: " + header +
                        "<hr />config: " + config;
                });
        };

        // fix for reload page
        if ($location.path().indexOf('/detail/') !== -1) {
            $scope.getData();
        }

        $scope.sweetAlert = function (title, text, type, color) {
            SweetAlert.swal({
                title: title,
                text: text,
                type: type,
                confirmButtonColor: color
            });
        };
    }]);
