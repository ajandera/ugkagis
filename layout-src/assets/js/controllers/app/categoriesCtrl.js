'use strict';
/**
 * Categories Controller
 */
app.controller('CategoriesCtrl', [
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
    function ($rootScope, $scope, $http, SweetAlert, securityService, uiGridConstants, $filter, $timeout, $aside, $location) {
        $scope.showForm = false;
        $scope.categories = {};
        $scope.categoriesForm = {};
        $scope.dataLoaded = false;

        var requestData = {};
        var config = {
            headers : {
                'Content-Type': 'application/json;charset=utf-8;'
            }
        };

        $scope.getData = function() {
            $http.get('/api/v2/categories/', requestData, config)
                .then(function (response) {
                    if(response.status === 200) {
                        $scope.categories = response.data.categories;
                        $scope.gridOptions.data = $scope.categories;
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
        if($location.path() === '/app/categories') {
            $scope.getData();
        }

        $scope.highlightFilteredHeader = function( row, rowRenderIndex, col, colRenderIndex ) {
            if( col.filters[0].term ){
                return 'header-filtered';
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
                    displayName: $filter('translate')('categories.name'),
                    enableColumnResizing: false,
                    headerCellClass: $scope.highlightFilteredHeader,
                    width: "40%",
                    cellClass: "padding-left-10 padding-top-5"
                },
                {
                    field: 'description',
                    displayName: $filter('translate')('categories.description'),
                    enableColumnResizing: false,
                    width: "50%",
                    headerCellClass: $scope.highlightFilteredHeader,
                    cellClass: "padding-left-10 padding-top-5"
                },
                {
                    field: 'id',
                    displayName: $filter('translate')('users.actions'),
                    width: "10%",
                    enableColumnResizing: false,
                    cellTemplate: '<div class="ui-grid-cell-contents"><div class="btn-group" role="group" aria-label="">' +
                    '<a ng-click="grid.appScope.editCategories(grid, row)" class="btn btn-xs btn-warning" title="'+$filter('translate')('common.grid.activity.edit')+'"><i class="glyphicon glyphicon-pencil"></i></a>' +
                    '<a ng-click="grid.appScope.deleteCategories(grid, row)" class="btn btn-danger btn-xs" title="'+$filter('translate')('common.grid.activity.delete')+'"><i class="glyphicon glyphicon-trash"></i></a></div></div>',
                    headerCellClass: $scope.highlightFilteredHeader,
                    enableFiltering: false,
                    cellClass: "padding-left-10 padding-top-5"
                }
            ]
        };

        $scope.gridOptions.multiSelect = false;
        $scope.gridOptions.modifierKeysToMultiSelect = false;
        $scope.gridOptions.noUnselect = true;
        $scope.gridOptions.onRegisterApi = function( gridApi ) {
            $scope.gridApi = gridApi;
            gridApi.selection.on.rowSelectionChanged($scope, function(row){
                $scope.categoriesRow = row.entity;
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

        $scope.deleteCategories = function(grid, row) {
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
                        "/api/v2/categories/"+row.entity.id,
                        {'Content-Type': 'application/json; charset=UTF-8'}
                    ).then(function(dataFromServer) {
                        if(dataFromServer.data.success === false) {
                            $scope.sweetAlert(
                                $filter('translate')('common.failed'),
                                $filter('translate')('common.record.fail'),
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
                            $filter('translate')('common.record.fail'),
                            'warning',
                            '#007AFF'
                        );
                    });
                }
            });
        };

        $scope.addCategories = function () {
            $aside.open({
                templateUrl: 'addCategories.html',
                scope: $scope,
                placement: 'right',
                size: 'm',
                backdrop: true,
                controller: function ($scope, $uibModalInstance) {
                    $scope.form = {title: $filter('translate')('categories.add')};
                    $scope.ok = function (e) {
                        $scope.dataLoaded = false;

                        //validate submit form
                        if($scope.validateSubmit($scope.categoriesForm) === false) {
                            return;
                        }

                        // to do fill data object to send
                        var dataObject = {
                            name: $scope.categoriesForm.name,
                            description: $scope.categoriesForm.description ? $scope.categoriesForm.description : '',
                            parent: $scope.parent
                        };

                        $http.post(
                            "/api/v2/categories",
                            dataObject,
                            {'Content-Type': 'application/json; charset=UTF-8'}
                        ).then(function(dataFromServer) {
                            if(dataFromServer.data.success === false) {
                                $scope.sweetAlert(
                                    $filter('translate')('common.failed'),
                                    $filter('translate')('common.language.notSaved'),
                                    'success',
                                    '#007AFF'
                                );
                                $scope.dataLoaded = true;
                            } else {
                                $scope.getData();
                            }
                        }, function(data, status, headers, config) {
                            $scope.usersForm.dataLoaded = true;
                            $scope.sweetAlert(
                                $filter('translate')('common.failed'),
                                $filter('translate')('common.language.notSaved'),
                                'warning',
                                '#007AFF'
                            );
                        });
                        $scope.categoriesForm = {};
                        $uibModalInstance.close();
                        e.stopPropagation();
                    };
                    $scope.cancel = function (e) {
                        $scope.categoriesForm = {};
                        $uibModalInstance.dismiss();
                        e.stopPropagation();
                    };
                }
            });
        };

        $scope.editCategories = function (grid, row) {
            $aside.open({
                templateUrl: 'addCategories.html',
                scope: $scope,
                placement: 'right',
                size: 'm',
                backdrop: true,
                controller: function ($scope, $uibModalInstance) {
                    $scope.categoriesForm = row.entity;
                    $scope.form = {title: $scope.categoriesForm.name};
                    $scope.ok = function (e) {
                        $scope.dataLoaded = false;

                        //validate submit form
                        if($scope.validateSubmit($scope.categoriesForm) === false) {
                            return;
                        }

                        // to do fill data object to send
                        var dataObject = {
                            id: row.entity.id,
                            name: $scope.categoriesForm.name,
                            description: $scope.categoriesForm.description,
                            parent: $scope.parent
                        };

                        $http.put(
                            "/api/v2/categories",
                            dataObject,
                            {'Content-Type': 'application/json; charset=UTF-8'}
                        ).then(function(dataFromServer) {
                            if(dataFromServer.data.success === false) {
                                $scope.sweetAlert(
                                    $filter('translate')('common.failed'),
                                    $filter('translate')('common.language.notSaved'),
                                    'success',
                                    '#007AFF'
                                );
                                $scope.dataLoaded = true;
                            } else {
                                $scope.getData();
                            }
                        }, function(data, status, headers, config) {
                            $scope.usersForm.dataLoaded = true;
                            $scope.sweetAlert(
                                $filter('translate')('common.failed'),
                                $filter('translate')('common.language.notSaved'),
                                'warning',
                                '#007AFF'
                            );
                        });
                        $scope.categoriesForm = {};
                        $uibModalInstance.close();
                        e.stopPropagation();
                    };
                    $scope.cancel = function (e) {
                        $scope.categoriesForm = {};
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

        $scope.validateSubmit = function(form) {

            var requiredFilled = {
                'status': true,
                'property': []
            };

            if(form.name === null
                || typeof form.name === 'undefined'
                || form.name === '') {
                requiredFilled.property.push('Name');
                requiredFilled.status = false;
            }

            if(form.description === null
                || typeof form.description === 'undefined'
                || form.description === '') {
                requiredFilled.property.push('Description');
                requiredFilled.status = false;
            }

            if(requiredFilled.status === false) {
                var fields = '';
                angular.forEach(requiredFilled.property, function(field) {
                    fields += field+', ';
                });

                $scope.sweetAlert(
                    $filter('translate')('common.requiredFields'),
                    fields,
                    'warning',
                    '#007AFF'
                );
                return false;
            } else {
                return true;
            }
        }
    }]);
