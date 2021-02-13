'use strict';
/**
 * New admin login controller
*/
app.controller('LoginCtrl', [
    '$scope', 
    '$http',
    '$filter',
    'SweetAlert',
    '$rootScope',
    '$location',
    '$window',
    function ($scope, $http, $filter, SweetAlert, $rootScope, $location, $window) {

    var vm = this;
    $scope.newEmpty = '';
    $scope.showEmailWrong = false;
    $scope.loginFormDataLoading = false;
    var marginTop = (window.innerHeight - parseInt('600')) / parseInt('2');
    if (marginTop < 0) {
        marginTop = 0
    }
    $scope.marginTop = marginTop+'px';
    
    if($location.path() === '/login' 
        || $location.path() === '/forgot'
        || $location.path() === '/signin'
        || $location.path() === '/restore'
    ) {
        if ($rootScope.isLoggedIn === true) {
            $location.path('/app/home');
        }
    }

    $scope.sweetAlert = function (title, text, type, color) {
        SweetAlert.swal({
            title: title,
            text: text,
            type: type,
            confirmButtonColor: color
        });
    };

    var config = {
        headers : {
            'Content-Type': 'application/json;charset=utf-8;'
        }
    };

    $scope.login = function() {
        $window.localStorage.removeItem('login');
        $scope.loginFormDataLoading = true;
        var data = {
            username: $scope.username,
            password: $scope.password,
            role: 0
        };

        $http.post('/api/v2/security/login', data, config)
            .success(function (data, status, headers, config) {
                if (data.user == null) {
                    $scope.loginFormDataLoading = false;
                    $scope.sweetAlert(
                        $filter('translate')('common.failed'),
                        data.error,
                        'warning',
                        '#007AFF'
                    );
                } else {
                    $rootScope.securityInfo = data.user;
                    $rootScope.isLoggedIn = true;
                    $location.path('/app/categories');
                }
            })
            .error(function (data, status, header, config) {
                $scope.loginFormDataLoading = false;
                $scope.sweetAlert(
                    $filter('translate')('common.failed'),
                    $filter('translate')('login.AUTH_ERROR'),
                    'warning',
                    '#007AFF'
                );
            });

    };

    $scope.resetPassword = function() {
        $scope.loginFormDataLoading = true;
        if($scope.email === '' || $scope.email === null) {
            $scope.newEmpty = {border: "1px solid red"};
            $scope.loginFormDataLoading = false;
            return;
        } else {
            $scope.newEmpty = '';
        }
        
        var data = {
            email: $scope.email,
            role: [0, 1]
        };

        $http.post('/api/v2/security/reset-password', data, config)
            .then(function (data) {
                if (data.data.success === true) {
                    $scope.loginFormDataLoading = false;
                    $scope.sweetAlert(
                        $filter('translate')('common.success'),
                        $filter('translate')('login.passwordSend')+' '+$scope.email,
                        'success',
                        '#007AFF'
                    );
                } else {
                    $scope.loginFormDataLoading = false;
                    $scope.sweetAlert(
                        $filter('translate')('common.failed'),
                        $filter('translate')('login.EMAIL_NOT_FOUND'),
                        'warning',
                        '#007AFF'
                    );
                }
            },function (data, status, header, config) {
                $scope.loginFormDataLoading = false;
                $scope.showEmailWrong = true;
                $scope.sweetAlert(
                    $filter('translate')('common.failed'),
                    $filter('translate')('login.technicalError'),
                    'warning',
                    '#007AFF'
                );
            });
    };
    
    $scope.restorePassword = function() {
        $scope.loader = true;
        var requestData = {
            token: $location.search().token,
            password: $scope.password
        };

        $http.post('/api/v2/security/restore-password', requestData, config)
            .then(function (response) {
                if (response.status === 201) {
                    $scope.sweetAlert(
                            $filter('translate')('common.success'),
                            $filter('translate')('front.personal.passwordSaved'),
                            'success',
                            '#007AFF'
                        );
                    delete $scope.password;
                    delete $scope.passwordCheck;
                    $scope.loader = false;
                } else {
                    $scope.sweetAlert(
                            $filter('translate')('common.failed'),
                            $filter('translate')('login.technicalError'),
                            'warning',
                            '#007AFF'
                        );
                    $scope.loader = false;
                }
            }, function (response, status, header, config) {
                $scope.responseDetails = "Data: " + response +
                "<hr />status: " + status +
                "<hr />headers: " + header +
                "<hr />config: " + config;
            });
    };
    
    $scope.checkToken = function() {
        $http.post('/api/v2/security/check-token', { token: $location.search().token}, {'Content-Type': 'application/json;charset=utf-8;'})
            .success(function (data, status, headers, config) {
                if (data.success !== true) {
                    $location.path('/home');
                }
            })
            .error(function (data, status, header, config) {

            });
    };

}]);
