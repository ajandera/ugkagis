'use strict';
/**
 * controllers used for the profile page
 */

app.controller('ProfileCtrl', [
    "$scope",
    "$rootScope",
    "$http",
    '$aside',
    'SweetAlert',
    '$filter',
    '$location',
    'securityService',
    '$window',
    function($scope, $rootScope, $http, $aside, SweetAlert, $filter, $location, securityService, $window) {
        $scope.loader = true;
        $scope.phoneFail = false;
        $scope.personal = {};

        var config = {
            headers : {
                'Content-Type': 'application/json;charset=utf-8;'
            }
        };

        $scope.getData = function() {
            $http.get('/api/v2/users/detail/'+$rootScope.securityInfo.id, {}, config)
                .then(function (response) {
                    if(response.status === 200) {
                        var data = response.data.detail;

                        $scope.personal = {
                            id: data.id,
                            username: data.username,
                            phone: data.phone,
                            name: data.name,
                            surname: data.surname,
                            signature: data.signature
                        };
                        $scope.loader = false;
                    }
                }, function (response, status, header, config) {
                    $scope.responseDetails = "Data: " + response +
                        "<hr />status: " + status +
                        "<hr />headers: " + header +
                        "<hr />config: " + config;
                });
        };

        $scope.getData();

        $scope.savePersonalInfo = function() {
            $scope.loader = true;
            var requestData = {
                id: $scope.personal.id,
                username: $scope.personal.username,
                phone: $scope.personal.phone,
                name: $scope.personal.name,
                surname: $scope.personal.surname,
                enabled: true
            };

            $http.put('/api/v2/users', requestData, config)
                .then(function (response) {
                    if (response.status === 200 && response.data.response !== null) {
                        $scope.flashMessage = {
                            class: 'success',
                            text: $filter('translate')('front.personal.saved')
                        };
                        $scope.getData();
                        $rootScope.securityInfo.name = $scope.personal.name + ' ' + $scope.personal.surname;
                        var encrypted = securityService.encrypt(JSON.stringify($rootScope.securityInfo));
                        $window.localStorage.setItem('login', encrypted);
                    } else {
                        $scope.flashMessage = {
                            class: 'danger',
                            text: $filter('translate')('front.personal.error')
                        };
                        $scope.loader = false;
                    }
                }, function (response, status, header, config) {
                    $scope.responseDetails = "Data: " + response +
                        "<hr />status: " + status +
                        "<hr />headers: " + header +
                        "<hr />config: " + config;
                });
        };

        $scope.changePassword = function() {
        if($scope.personal.password !== $scope.personal.passwordCheck) {
            $scope.flashMessage = {
                class: 'danger',
                text: $filter('translate')('front.personal.passwordNotSame')
            };
            return;
        } else {
            delete $scope.flashMessage;
        }

        $scope.loader = true;
        var requestData = {
            id: $rootScope.securityInfo.id,
            password: $scope.personal.password
        };

        $http.post('/api/v2/security/change-password', requestData, config)
            .then(function (response) {
                if (response.status === 201) {
                    $scope.flashMessage = {
                        class: 'success',
                        text: $filter('translate')('front.personal.passwordSaved')
                    };
                    delete $scope.personal.password;
                    delete $scope.personal.passwordCheck;
                    $scope.loader = false;
                } else {
                    $scope.flashMessage = {
                        class: 'danger',
                        text: $filter('translate')('front.personal.error')
                    };
                    $scope.loader = false;
                }
            }, function (response, status, header, config) {
                $scope.responseDetails = "Data: " + response +
                "<hr />status: " + status +
                "<hr />headers: " + header +
                "<hr />config: " + config;
            });
        };
    }
]);
