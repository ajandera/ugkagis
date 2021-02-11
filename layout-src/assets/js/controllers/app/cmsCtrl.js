'use strict';
/**
 * Cms Controller
 */
app.controller('CmsCtrl', [
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
        $scope.seniority = {};
        $scope.seniorityForm = {};
        $scope.dataLoaded = false;

        var requestData = {};
        var config = {
            headers : {
                'Content-Type': 'application/json;charset=utf-8;'
            }
        };

        $scope.getData = function() {
            $http.get('/api/v2/seniority', requestData, config)
                .then(function (response) {
                    if(response.status === 200) {
                        $scope.seniority = response.data.seniority;
                        $scope.gridOptions.data = $scope.seniority;
                        $scope.dataLoaded = true;
                    } else {
                        $scope.responseDetails = "Data: " + response +
                            "<hr />status: " + status +
                            "<hr />config: " + config;
                    }
                }, function (response, status, header, config) {
                    $scope.responseDetails = "Data: " + response +
                        "<hr />status: " + status +
                        "<hr />config: " + config;
                });
        };

        // fix for reload page
        if($location.path() === '/app/seniority') {
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
                    displayName: $filter('translate')('seniority.name'),
                    enableColumnResizing: false,
                    width: "40%",
                    headerCellClass: $scope.highlightFilteredHeader,
                    cellClass: "padding-left-20 padding-top-5"
                },
                {
                    field: 'description',
                    displayName: $filter('translate')('seniority.description'),
                    enableColumnResizing: false,
                    width: "50%",
                    headerCellClass: $scope.highlightFilteredHeader,
                    cellClass: "padding-left-20 padding-top-5"
                },
                {
                    field: 'id',
                    displayName: $filter('translate')('users.actions'),
                    enableColumnResizing: false,
                    width: "10%",
                    cellTemplate: '<div class="ui-grid-cell-contents"><div class="btn-group" role="group" aria-label="">' +
                    '<a ng-click="grid.appScope.editSeniority(grid, row)" class="btn btn-xs btn-warning" title="'+$filter('translate')('common.grid.activity.edit')+'"><i class="glyphicon glyphicon-pencil"></i></a>' +
                    '<a ng-click="grid.appScope.deleteSeniority(grid, row)" class="btn btn-danger btn-xs" title="'+$filter('translate')('common.grid.activity.delete')+'"><i class="glyphicon glyphicon-trash"></i></a></div></div>',
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
                $scope.seniorityRow = row.entity;
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

        $scope.deleteSeniority = function(grid, row) {
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
                        "/api/v2/seniority/"+row.entity.id,
                        {},
                        {'Content-Type': 'application/json; charset=UTF-8'}
                    ).then(function(dataFromServer) {
                        if(dataFromServer.data.success === false) {
                            $scope.sweetAlert(
                                $filter('translate')('common.failed'),
                                $filter('translate')('common.record.fail'),
                                'success',
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

        $scope.addSeniority = function () {
            $aside.open({
                templateUrl: 'addSeniority.html',
                scope: $scope,
                placement: 'right',
                size: 'm',
                backdrop: true,
                controller: function ($scope, $uibModalInstance) {
                    $scope.form = {title: $filter('translate')('seniority.add')};
                    $scope.ok = function (e) {
                        $scope.dataLoaded = false;

                        //validate submit form
                        if($scope.validateSubmit($scope.seniorityForm) === false) {
                            return;
                        }

                        // to do fill data object to send
                        var dataObject = {
                            name: $scope.seniorityForm.name,
                            description: $scope.seniorityForm.description
                        };

                        $http.post(
                            "/api/v2/seniority",
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
                        $scope.seniorityForm = {};
                        $uibModalInstance.close();
                        e.stopPropagation();
                    };
                    $scope.cancel = function (e) {
                        $scope.seniorityForm = {};
                        $uibModalInstance.dismiss();
                        e.stopPropagation();
                    };
                }
            });
        };

        $scope.editSeniority = function (grid, row) {
            $aside.open({
                templateUrl: 'addSeniority.html',
                scope: $scope,
                placement: 'right',
                size: 'm',
                backdrop: true,
                controller: function ($scope, $uibModalInstance) {
                    $scope.seniorityForm = row.entity;
                    $scope.form = {title: $scope.seniorityForm.name};
                    $scope.ok = function (e) {
                        $scope.dataLoaded = false;

                        //validate submit form
                        if($scope.validateSubmit($scope.seniorityForm) === false) {
                            return;
                        }

                        // to do fill data object to send
                        var dataObject = {
                            id: row.entity.id,
                            name: $scope.seniorityForm.name,
                            description: $scope.seniorityForm.description
                        };

                        $http.put(
                            "/api/v2/seniority",
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
                        $scope.seniorityForm = {};
                        $uibModalInstance.close();
                        e.stopPropagation();
                    };
                    $scope.cancel = function (e) {
                        $scope.seniorityForm = {};
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
                requiredFilled.property.push('Short');
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
