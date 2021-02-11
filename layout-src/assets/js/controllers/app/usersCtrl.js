'use strict';
/**
 * Users Controller
 */
app.controller('UsersCtrl', [
    '$rootScope',
    '$scope',
    '$http',
    'SweetAlert',
    'securityService',
    'uiGridConstants',
    '$filter',
    '$timeout',
    '$aside',
    '$location',
    'stringsAndDates',
    '$uibModalStack',
    function (
        $rootScope,
        $scope,
        $http,
        SweetAlert,
        securityService,
        uiGridConstants,
        $filter,
        $timeout,
        $aside,
        $location,
        stringsAndDates,
        $uibModalStack
    ) {
        $scope.showForm = false;
        $scope.users = {};
        $scope.usersForm = {};
        $scope.uniqueEmail = true;
        $scope.dataLoaded = false;
        $scope.nameTtoShort = false;
        $scope.surnameToShort = false;
        $scope.disableSave = true;
        $scope.invalidEmail = false;
        $scope.phoneFail = false;
        
        $scope.roles = [
            {
                id: 0,
                name: $filter('translate')('users.permissions.admin')
            },
            {
                id: 1,
                name: $filter('translate')('users.permissions.operator')
            }
        ];

        var requestData = {};
        var config = {
            headers : {
                'Content-Type': 'application/json;charset=utf-8;'
            }
        };

        $scope.getData = function() {
            $http.get('/api/v2/users', requestData, config)
                .then(function (response) {
                    if(response.status === 200) {
                        $scope.users = response.data.users;
                        $scope.gridOptions.data = $scope.users;
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
        if($location.path() === '/app/users') {
            $scope.getData();
        }

        $scope.highlightFilteredHeader = function( row, rowRenderIndex, col, colRenderIndex ) {
            if( col.filters[0].term ){
                return 'header-filtered';
            } else {
                return '';
            }
        };

        $scope.getFullName = function( grid, row) {
            return row.entity.name+' '+row.entity.surname;
        };

        $scope.getRoleName = function( grid, row) {
            if(row.entity.role === 0) {
                return $filter('translate')('users.permissions.admin');
            } else if (row.entity.role === 1) {
                return $filter('translate')('users.permissions.operator');
            } else {
                return '';
            }
        };

        $scope.gridOptions = {
            enableFiltering: true,
            enableRowHeaderSelection: false,
            enableRowSelection: true,
            enableSelectAll: true,
            enableHorizontalScrollbar: uiGridConstants.scrollbars.WHEN_NEEDED,
            enableVerticalScrollbar: uiGridConstants.scrollbars.WHEN_NEEDED,
            selectionRowHeaderWidth: 35,
            rowHeight: 40,
            showGridFooter:true,
            massUpdate: false,
            columnDefs: [
                {
                    field: 'name',
                    displayName: $filter('translate')('users.name'),
                    cellTemplate: '<div class="ui-grid-cell-contents">{{ grid.appScope.getFullName( grid, row) }}</div>',
                    enableColumnResizing: false,
                    headerCellClass: $scope.highlightFilteredHeader,
                    cellClass: "padding-left-20 padding-top-5"
                },
                {
                    field: 'username',
                    displayName: $filter('translate')('users.login'),
                    enableColumnResizing: false,
                    headerCellClass: $scope.highlightFilteredHeader,
                    cellClass: "padding-left-20 padding-top-5"
                },
                {
                    field: 'role',
                    displayName: $filter('translate')('users.role'),
                    cellTemplate: '<div class="ui-grid-cell-contents">{{ grid.appScope.getRoleName( grid, row) }}</div>',
                    enableColumnResizing: false,
                    headerCellClass: $scope.highlightFilteredHeader,
                    cellClass: "padding-left-20 padding-top-5"
                },
                {
                    field: 'id',
                    displayName: $filter('translate')('users.actions'),
                    enableColumnResizing: false,
                    cellTemplate: '<div class="ui-grid-cell-contents"><div class="btn-group" role="group" aria-label="">' +
                    '<a ng-click="grid.appScope.editUser(grid, row)" class="btn btn-xs btn-warning" title="'+$filter('translate')('common.grid.activity.edit')+'"><i class="glyphicon glyphicon-pencil"></i></a>' +
                    '<a ng-click="grid.appScope.resetPassword(grid, row)" class="btn btn-xs btn-primary" title="'+$filter('translate')('common.grid.activity.resetPassword')+'"><i class="glyphicon glyphicon-refresh"></i></a>' +
                    '<a ng-click="grid.appScope.deleteUser(grid, row)" class="btn btn-danger btn-xs" title="'+$filter('translate')('common.grid.activity.delete')+'"><i class="glyphicon glyphicon-trash"></i></a></div></div>',
                    headerCellClass: $scope.highlightFilteredHeader,
                    enableFiltering: false,
                    cellClass: "padding-left-20 padding-top-5"
                }
            ]
        };

        $scope.gridOptions.multiSelect = false;
        $scope.gridOptions.modifierKeysToMultiSelect = false;
        $scope.gridOptions.noUnselect = true;
        $scope.gridOptions.onRegisterApi = function( gridApi ) {
            $scope.gridApi = gridApi;
            gridApi.selection.on.rowSelectionChanged($scope, function(row){
                $scope.userRow = row.entity;
                $scope.showForm = true;
            });
        };

        $scope.toggleRowSelection = function() {
            $scope.gridApi.selection.clearSelectedRows();
            $scope.showForm = false;
        };

        $scope.getTableHeight = function(height) {
            var rowHeight = 40; // your row height
            var headerHeight = 40; // your header height
            var heightGrid = ($scope.gridOptions.data.length * rowHeight + headerHeight);

            if(heightGrid > height) {
                heightGrid = height;
            } else if(heightGrid < (height - 100)) {
                heightGrid = height - 100;
            }

            return {
                height: heightGrid + "px"
            };
        };

        $scope.deleteUser = function(grid, row) {
            SweetAlert.swal({
                title: $filter('translate')('common.sure'),
                text: $filter('translate')('common.delete')+" "+ row.entity.name,
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: $filter('translate')('common.yes'),
                cancelButtonText: $filter('translate')('common.no'),
                closeOnConfirm: true,
                closeOnCancel: true
            }, function (isConfirm) {
                if (isConfirm) {
                    $http.delete(
                        "/api/v2/users/"+row.entity.id,
                        {},
                        {'Content-Type': 'application/json; charset=UTF-8'}
                    ).then(function(dataFromServer) {
                        if(dataFromServer.data.success === false) {
                            $scope.sweetAlert(
                                $filter('translate')('common.failed'),
                                $filter('translate')('common.user.notDeleted'),
                                'warning',
                                '#007AFF'
                            );
                            $scope.dataLoaded = true;
                        } else {
                            $scope.sweetAlert(
                                $filter('translate')('common.success'),
                                $filter('translate')('common.record.delete'),
                                'success',
                                '#007AFF'
                            );
                            $scope.getData();
                        }
                    }, function(data, status, headers, config) {
                        $scope.usersForm.dataLoaded = true;
                        $scope.sweetAlert(
                            $filter('translate')('common.failed'),
                            $filter('translate')('common.user.notDeleted'),
                            'warning',
                            '#007AFF'
                        );
                    });
                }
            });
        };

        $scope.addUser = function () {
            $aside.open({
                templateUrl: 'addUser.html',
                scope: $scope,
                placement: 'right',
                size: 'm',
                backdrop: true,
                controller: function ($scope, $uibModalInstance) {
                    $scope.form = {title: $filter('translate')('users.add')};
                    $scope.passwordHide = false;
                    $scope.usersForm.enabled = true;
                    $scope.ok = function (e) {
                        $scope.dataLoaded = false;

                        // to do fill data object to send
                        var dataObject = {
                            username: $scope.usersForm.username,
                            password: $scope.usersForm.password,
                            role: $scope.usersForm.role,
                            phone: $scope.usersForm.phone,
                            name: $scope.usersForm.name,
                            surname: $scope.usersForm.surname,
                            enabled: $scope.usersForm.enabled
                        };

                        $http.post(
                            "/api/v2/users",
                            dataObject,
                            {'Content-Type': 'application/json; charset=UTF-8'}
                        ).then(function(dataFromServer) {
                            if(dataFromServer.data.success === false) {
                                $scope.sweetAlert(
                                    $filter('translate')('common.failed'),
                                    $filter('translate')('common.user.notSaved'),
                                    'success',
                                    '#007AFF'
                                );
                                $scope.dataLoaded = true;
                            } else {
                                $scope.getData();
                                $scope.usersForm = {};
                            }
                        }, function(data, status, headers, config) {
                            $scope.usersForm.dataLoaded = true;
                            $scope.sweetAlert(
                                $filter('translate')('common.failed'),
                                $filter('translate')('common.user.notSaved'),
                                'warning',
                                '#007AFF'
                            );
                        });
                        $scope.$parent.usersForm = {};
                        $scope.usersForm = {};
                        $uibModalInstance.close();
                        e.stopPropagation();
                    };
                    $scope.cancel = function (e) {
                        $scope.$parent.usersForm = {};
                        $scope.usersForm = {};
                        $uibModalInstance.dismiss();
                        e.stopPropagation();
                    };
                }
            });
        };

        $scope.editUser = function (grid, row) {
            $aside.open({
                templateUrl: 'addUser.html',
                scope: $scope,
                placement: 'right',
                size: 'm',
                backdrop: true,
                controller: function ($scope, $uibModalInstance) {
                    $scope.usersForm = row.entity;
                    $scope.form = {title: $scope.usersForm.name + ' ' + $scope.usersForm.surname};
                    $scope.passwordHide = true;
                    $scope.invalidEmail = true;
                    $scope.uniqueEmail = true;
                    $scope.disableSave = false;
                    $scope.ok = function (e) {
                        $scope.dataLoaded = false;

                        // to do fill data object to send
                        var dataObject = {
                            id: row.entity.id,
                            role: $scope.usersForm.role,
                            phone: $scope.usersForm.phone,
                            name: $scope.usersForm.name,
                            surname: $scope.usersForm.surname,
                            enabled: $scope.usersForm.enabled
                        };

                        $http.put(
                            "/api/v2/users",
                            dataObject,
                            {'Content-Type': 'application/json; charset=UTF-8'}
                        ).then(function(dataFromServer) {
                            if(dataFromServer.data.success === false) {
                                $scope.sweetAlert(
                                    $filter('translate')('common.failed'),
                                    $filter('translate')('common.user.notSaved'),
                                    'warning',
                                    '#007AFF'
                                );
                                $scope.dataLoaded = true;
                            } else {
                                $scope.sweetAlert(
                                    $filter('translate')('common.success'),
                                    $filter('translate')('common.record.updates'),
                                    'success',
                                    '#007AFF'
                                );
                                $scope.getData();
                                $scope.usersForm = {};
                            }
                        }, function(data, status, headers, config) {
                            $scope.usersForm.dataLoaded = true;
                            $scope.sweetAlert(
                                $filter('translate')('common.failed'),
                                $filter('translate')('common.user.notSaved'),
                                'warning',
                                '#007AFF'
                            );
                        });
                        $scope.$parent.usersForm = {};
                        $scope.usersForm = {};
                        $uibModalInstance.close();
                        e.stopPropagation();
                    };
                    $scope.cancel = function (e) {
                        $scope.$parent.usersForm = {};
                        $scope.usersForm = {};
                        $uibModalInstance.dismiss();
                        e.stopPropagation();
                    };
                }
            });
        };

        $scope.sweetAlert = function (title, text, type, color) {
            SweetAlert.swal({
                title: title,
                text: text,
                type: type,
                confirmButtonColor: color
            });
        };

        $scope.isUniqueEmail = function() {
            $scope.uniqueEmail = true;
            if(typeof $scope.users !== 'undefined' && typeof $scope.usersForm.username !== 'undefined') {
                angular.forEach($scope.users, function(user) {
                    if (user.username === $scope.usersForm.username) {
                        $scope.uniqueEmail = false;
                    }
                });

                if ($scope.uniqueEmail === true) {
                    $http.get('/api/v2/users/unique/'+$scope.usersForm.username, {}, config)
                        .then(function (response) {
                            if (response.status === 200) {
                                if (response.data.deleted === true) {
                                    $scope.reCreateUser(response.data.id);
                                }
                                $scope.uniqueEmail = response.data.unique;
                            } else {
                                $scope.responseDetails = "Data: " + response +
                                    "<hr />status: " + status +
                                    "<hr />config: " + config;
                            }
                    }, function (response, status, header, config) {
                        $scope.responseDetails = "Data: " + response +
                            "<hr />status: " + status +
                            "<hr />headers: " + header +
                            "<hr />config: " + config;
                    });
                } 
            }
        };

        $scope.reCreateUser = function(id) {
            SweetAlert.swal({
                title: $filter('translate')('common.sure'),
                text: $filter('translate')('common.recreate'),
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: $filter('translate')('common.yes'),
                cancelButtonText: $filter('translate')('common.no'),
                closeOnConfirm: true,
                closeOnCancel: true
            }, function (isConfirm) {
                if (isConfirm) {
                    $http.get(
                        "/api/v2/consultants/recreate/"+id,
                        {},
                        {'Content-Type': 'application/json; charset=UTF-8'}
                    ).then(function(dataFromServer) {
                        if(dataFromServer.data.success === false) {
                            $scope.sweetAlert(
                                $filter('translate')('common.failed'),
                                $filter('translate')('common.record.fail'),
                                'warning',
                                '#007AFF'
                            );
                        } else {
                            $scope.sweetAlert(
                                $filter('translate')('common.success'),
                                $filter('translate')('common.record.created'),
                                'success',
                                '#007AFF'
                            );
                            $scope.getData();
                            $uibModalStack.dismissAll();
                        }
                    }, function(data, status, headers, config) {
                        $scope.consultantForm.dataLoaded = true;
                        $scope.sweetAlert(
                            $filter('translate')('common.failed'),
                            $filter('translate')('common.record.fail'),
                            'warning',
                            '#007AFF'
                        );
                    });
                }
            });
        };
        
        $scope.$watch('usersForm', function() {
            if ($scope.nameToShort === false
                && $scope.surnameToShort === false
                && typeof $scope.usersForm.name !== 'undefined'
                && typeof $scope.usersForm.surname !== 'undefined'
                && typeof $scope.usersForm.username !== 'undefined'
                && typeof $scope.usersForm.password !== 'undefined'
                && typeof $scope.usersForm.role !== 'undefined'
                && $scope.usersForm.password.length > 4
                && $scope.usersForm.password === $scope.usersForm.passwordCheck
                && $scope.usersForm.username === $scope.usersForm.usernameCheck
                && $scope.uniqueEmail === true
                && $scope.invalidEmail === true) {
                    $scope.disableSave = false;
            } else {
                $scope.disableSave = true;
            }
            
            if(typeof $scope.usersForm.surname !== 'undefined') {
                if ($scope.usersForm.surname.length < 2) {
                    $scope.surnameToShort = $filter('translate')('common.minLength') + ' 2';
                } else {
                    $scope.surnameToShort = false;
                }
            }
            
            if(typeof $scope.usersForm.name !== 'undefined') {
                if ($scope.usersForm.name.length < 2) {
                    $scope.nameToShort = $filter('translate')('common.minLength') + ' 2';
                } else {
                    $scope.nameToShort = false;
                }
            }
                        
            if (stringsAndDates.validateEmail($scope.usersForm.username) === false) {
                $scope.invalidEmail = false;
            } else {
                $scope.invalidEmail = true;
            }
        }, true);

        $scope.resetPassword = function(grid, row) {
            $http.post('/api/v2/security/reset-password', {email: row.entity.username, role: row.entity.role})
                .then(function (data) {
                    if (data.data.success === true) {
                        $scope.sweetAlert(
                            $filter('translate')('common.success'),
                            $filter('translate')('login.passwordSend')+' '+row.entity.username,
                            'success',
                            '#007AFF'
                        );
                    } else {
                        $scope.sweetAlert(
                            $filter('translate')('common.failed'),
                            $filter('translate')('login.EMAIL_NOT_FOUND'),
                            'warning',
                            '#007AFF'
                        );
                    }
                },function (data, status, header, config) {
                    $scope.sweetAlert(
                        $filter('translate')('common.failed'),
                        $filter('translate')('login.technicalError'),
                        'warning',
                        '#007AFF'
                    );
                });
        };
    }]);
