'use strict';
/**
 * Main Front Controller
 */
app.controller('MainFrontCtrl', [
    '$rootScope',
    '$scope',
    '$state',
    '$translate',
    '$localStorage',
    '$window',
    '$document',
    '$timeout',
    '$http',
    'securityService',
    '$filter',
    '$location',
    '$aside',
    '$anchorScroll',
    function ($rootScope,
              $scope,
              $state,
              $translate,
              $localStorage,
              $window,
              $document,
              $timeout,
              $http,
              securityService,
              $filter,
              $location,
              $aside,
              $anchorScroll
    ) {
        //remove filter if set
        window.onbeforeunload = function() {
            $window.localStorage.removeItem('filter');
        };

        $scope.goTo = function (hash) {
            $location.hash(hash);
            $anchorScroll();
        };

        $scope.forgotEmail = null;
        $scope.registerValidate = true;
        $scope.uniqueEmail = true;
        $scope.signUp = {};
        
        $scope.show = false;
        var config = {
            headers : {
                'Content-Type': 'application/json;charset=utf-8;'
            }
        };

        $http.get('/api/v2/front/settings', {})
            .success(function (data, status, headers, config) {
                if (data.settings !== null) {
                    $rootScope.settings = data.settings;
                } else {
                    console.log("Error - can't get settings");
                }
            })
            .error(function (data, status, header, config) {
                console.log("Error - can't get settings");
            });

        $scope.logIn = function () {
            $scope.openedModel = $aside.open({
                templateUrl: 'logIn.html',
                scope: $scope,
                placement: 'right',
                size: 's',
                backdrop: true,
                controller: function ($scope, $uibModalInstance) {
                    $scope.signIn = {};
                    $scope.disableLoggin = false;
                    $scope.disableLogginMessage = false;
                    $scope.ok = function (e) {
                        $window.localStorage.removeItem('login');
                        $scope.loginFormDataLoading = true;

                        var data = {
                            username: $scope.signIn.username,
                            password: $scope.signIn.password,
                            role: $scope.signIn.role
                        };

                        $http.post('/api/v2/security/login', data, {'Content-Type': 'application/json; charset=UTF-8'})
                            .success(function (data, status, headers, config) {
                                if (typeof data.error === 'undefined') {
                                    var encrypted;
                                    if ( data.user.role === 0 || data.user.role === 1) {
                                        //is admin or operator
                                        $rootScope.securityInfo = data.user;
                                        encrypted = securityService.encrypt(JSON.stringify($rootScope.securityInfo));
                                        $window.localStorage.setItem('login', encrypted);
                                        $rootScope.isLoggedIn = true;
                                        $rootScope.isPermitted = securityService.isPermitted;
                                        $rootScope.role = data.user.role;
                                        $location.path('/app/dashboard');
                                    } else if (data.user.role === 2 || data.user.role === 3) {
                                        $rootScope.securityInfo = data.user;
                                        encrypted = securityService.encrypt(JSON.stringify($rootScope.securityInfo));
                                        $window.localStorage.setItem('login', encrypted);
                                        $rootScope.isLoggedIn = true;
                                        $rootScope.isFrontPermitted = securityService.isFrontPermitted;
                                        $rootScope.role = data.user.role;

                                        if (data.user.name.length <= 2) {
                                            $location.path('/account/personal-info');
                                        } else {
                                            $uibModalInstance.dismiss();
                                            e.stopPropagation();
                                        }

                                    } else {
                                        $scope.loginFormDataLoading = false;
                                        $scope.flashMessage = {
                                            class: 'danger',
                                            text: 'Unsupported role of user.'
                                        };
                                    }
                                } else {
                                    $scope.loginFormDataLoading = false;
                                    $scope.flashMessage = {
                                        class: 'danger',
                                        text: data.error
                                    };
                                }
                            })
                            .error(function (data, status, header, config) {
                                $scope.loginFormDataLoading = false;
                            });
                    };
                    $scope.cancel = function (e) {
                        $uibModalInstance.dismiss();
                        e.stopPropagation();
                    };

                    $scope.checkRole = function() {
                        $scope.disableLoggin = true;
                        $http.get('/api/v2/front/role/'+$scope.signIn.username, {}, {'Content-Type': 'application/json; charset=UTF-8'})
                            .success(function (data, status, headers, config) {
                                if (data.user !== null) {
                                    if (data.user.enabled === true) {
                                        $scope.signIn.role = data.user.role;
                                        $scope.disableLoggin = false;
                                        $scope.disableLogginMessage = false;
                                        return;
                                    } else {
                                        if (data.user.role === false) {
                                            $scope.disableLoggin = true;
                                            $scope.disableLogginMessage = false;
                                        } else {
                                            $scope.disableLoggin = true;
                                            $scope.disableLogginMessage = true;
                                        }
                                        return;
                                    }
                                }
                            })
                            .error(function (data, status, header, config) {
                                $scope.loginFormDataLoading = false;
                            });
                    }
                }
            });
        };

        $scope.isUniqueEmail = function() {
            $scope.uniqueEmail = true;
            $http.get('/api/v2/front/unique/' + $scope.signUp.username, {}, config)
                .then(function (response) {
                    if (response.status === 200) {
                        $scope.uniqueEmail = response.data.unique;
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
            $scope.validateSignUp();
        };

        $scope.restorePassword = function (event) {
            $scope.openedModel = $aside.open({
                templateUrl: 'restore.html',
                scope: $scope,
                placement: 'right',
                size: 's',
                backdrop: true,
                controller: function ($scope, $uibModalInstance) {

                    $scope.ok = function (e) {
                        var data = {
                            token: $scope.signIn.username,
                            password: $scope.signIn.password
                        };

                        $http.post('/api/v2/security/login', data, {'Content-Type': 'application/json; charset=UTF-8'})
                            .success(function (data, status, headers, config) {
                                if (data.user !== null
                                    && typeof data.user.username !== 'undefined'
                                    && data.user.username === $scope.signIn.username
                                ) {
                                    $rootScope.securityInfo = data.user;
                                    $rootScope.isLoggedIn = true;
                                    $uibModalInstance.dismiss();
                                    e.stopPropagation();
                                } else {
                                    $scope.loginFormDataLoading = false;
                                    $scope.flashMessage = {
                                        class: 'danger',
                                        text: data.error
                                    };
                                }
                            })
                            .error(function (data, status, header, config) {
                                $scope.loginFormDataLoading = false;
                            });
                    };
                    $scope.cancel = function (e) {
                        $uibModalInstance.dismiss();
                        e.stopPropagation();
                    };
                }
            });
        };

        $scope.showForgot = function() {
            $scope.show = $scope.show  === false ? true : false;
        };

        $scope.sendForgotPassword = function(email) {
            if (email === '' || email === null) {
                $scope.newEmpty = { border: "1px solid red" };
                return;
            } else {
                $scope.newEmpty = '';
            }

            var data = {
                email: email,
                role: [0,1,2,3]
            };

            $http.post('/api/v2/security/reset-password', data, config)
                .then(function (data) {
                    if (data.data.success === true) {
                        $scope.flashMessage = {
                                class: 'success',
                                text: $filter('translate')('login.passwordSend')+' '+email
                            };
                    } else {
                        $scope.flashMessage = {
                            class: 'danger',
                            text: $filter('translate')('login.EMAIL_NOT_FOUND')
                        };
                    }
                },function (data, status, header, config) {
                    $scope.flashMessage = {
                        class: 'danger',
                        text: $filter('translate')('login.technicalError')
                    };
                });
        };
}]);
