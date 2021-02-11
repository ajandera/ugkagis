'use strict';
/**
 * Languages Controller
 */
app.controller('LanguagesCtrl', [
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
        $scope.languages = {};
        $scope.languagesForm = {};
        $scope.uniqueEmail = true;
        $scope.dataLoaded = false;

        var requestData = {};
        var config = {
            headers : {
                'Content-Type': 'application/json;charset=utf-8;'
            }
        };

        $scope.getData = function() {
            $http.get('/api/v2/languages', requestData, config)
                .then(function (response) {
                    if(response.status === 200) {
                        $scope.languages = response.data.languages;
                        $scope.gridOptions.data = $scope.languages;
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
        if($location.path() === '/app/languages') {
            $scope.getData();
        }

        $scope.highlightFilteredHeader = function( row, rowRenderIndex, col, colRenderIndex ) {
            if( col.filters[0].term ){
                return 'header-filtered';
            } else {
                return '';
            }
        };

        $scope.isDefault = function( grid, row) {
            if(row.entity.defaults === 1) {
                return '<a ng-click="setDefault(false, entity.row.id)" class="btn btn-xs btn-success"><i class="glyphicon glyphicon-ok"></i></a>';
            } else {
                return '<a ng-click="setDefault(true, entity.row.id)" class="btn btn-xs btn-danger"><i class="glyphicon glyphicon-remove"></i></a>';
            }
        };

        $scope.setDefault = function(set, id) {

        };

        $scope.isVisible = function( grid, row) {
            if(row.entity.visible === 1) {
                return '<a ng-click="setVisible(false, entity.row.id)" class="btn btn-xs btn-success"><i class="glyphicon glyphicon-ok"></i></a>';
            } else {
                return '<a ng-click="setVisible(true, entity.row.id)" class="btn btn-xs btn-danger"><i class="glyphicon glyphicon-remove"></i></a>';
            }
        };

        $scope.setVisible = function(set, id) {

        };

        $scope.gridOptions = {
            enableFiltering: true,
            enableRowHeaderSelection: false,
            enableRowSelection: true,
            enableSelectAll: false,
            enableHorizontalScrollbar: uiGridConstants.scrollbars.WHEN_NEEDED,
            enableVerticalScrollbar: uiGridConstants.scrollbars.WHEN_NEEDED,
            selectionRowHeaderWidth: 35,
            rowHeight: 40,
            showGridFooter:true,
            massUpdate: false,
            columnDefs: [
                {
                    field: 'name',
                    displayName: $filter('translate')('languages.name'),
                    enableColumnResizing: false,
                    headerCellClass: $scope.highlightFilteredHeader,
                    cellClass: "padding-left-20 padding-top-5"
                },
                {
                    field: 'code',
                    displayName: $filter('translate')('languages.code'),
                    enableColumnResizing: false,
                    headerCellClass: $scope.highlightFilteredHeader,
                    cellClass: "padding-left-20 padding-top-5"
                },
                {
                    field: 'translation_code',
                    displayName: $filter('translate')('languages.translationCode'),
                    enableColumnResizing: false,
                    headerCellClass: $scope.highlightFilteredHeader,
                    cellClass: "padding-left-20 padding-top-5"
                },
                {
                    field: 'defaults',
                    displayName: $filter('translate')('languages.defaults'),
                    cellTemplate: '<div class="ui-grid-cell-contents" ng-bind-html="grid.appScope.isDefault( grid, row)"></div>',
                    enableColumnResizing: false,
                    headerCellClass: $scope.highlightFilteredHeader,
                    cellClass: "padding-left-20 padding-top-5"
                },
                {
                    field: 'visible',
                    displayName: $filter('translate')('languages.visible'),
                    cellTemplate: '<div class="ui-grid-cell-contents" ng-bind-html="grid.appScope.isVisible( grid, row)"></div>',
                    enableColumnResizing: false,
                    headerCellClass: $scope.highlightFilteredHeader,
                    cellClass: "padding-left-20 padding-top-5"
                },
                {
                    field: 'id',
                    displayName: $filter('translate')('users.actions'),
                    enableColumnResizing: false,
                    cellTemplate: '<div class="ui-grid-cell-contents"><div class="btn-group" role="group" aria-label="">' +
                    '<a ng-click="grid.appScope.editLanguages(grid, row)" class="btn btn-xs btn-warning" title="'+$filter('translate')('common.grid.activity.edit')+'"><i class="glyphicon glyphicon-pencil"></i></a>' +
                    '<a ng-click="grid.appScope.deleteLanguages(grid, row)" class="btn btn-danger btn-xs" title="'+$filter('translate')('common.grid.activity.delete')+'"><i class="glyphicon glyphicon-trash"></i></a></div></div>',
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
                $scope.languageRow = row.entity;
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

        $scope.deleteLanguages = function(grid, row) {
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
                        "/api/v2/languages/"+row.entity.id,
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

        $scope.addLanguage = function () {
            $aside.open({
                templateUrl: 'addLanguage.html',
                scope: $scope,
                placement: 'right',
                size: 'm',
                backdrop: true,
                controller: function ($scope, $uibModalInstance) {
                    $scope.form = {title: $filter('translate')('languages.add')};
                    $scope.ok = function (e) {
                        $scope.dataLoaded = false;

                        //validate submit form
                        if($scope.validateSubmit($scope.languagesForm) === false) {
                            return;
                        }

                        // to do fill data object to send
                        var dataObject = {
                            name: $scope.languagesForm.name,
                            code: $scope.languagesForm.code,
                            translationCode: $scope.languagesForm.translation_code,
                            defaults: $scope.languagesForm.defaults,
                            visible: $scope.languagesForm.visible
                        };

                        $http.post(
                            "/api/v2/languages",
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
                        $scope.languagesForm = {};
                        $uibModalInstance.close();
                        e.stopPropagation();
                    };
                    $scope.cancel = function (e) {
                        $scope.languagesForm = {};
                        $uibModalInstance.dismiss();
                        e.stopPropagation();
                    };
                }
            });
        };

        $scope.editLanguages = function (grid, row) {
            $aside.open({
                templateUrl: 'addLanguage.html',
                scope: $scope,
                placement: 'right',
                size: 'm',
                backdrop: true,
                controller: function ($scope, $uibModalInstance) {
                    $scope.languagesForm = row.entity;
                    $scope.form = {title: $scope.languagesForm.name};
                    $scope.ok = function (e) {
                        $scope.dataLoaded = false;

                        //validate submit form
                        if($scope.validateSubmit($scope.languagesForm) === false) {
                            return;
                        }

                        // to do fill data object to send
                        var dataObject = {
                            id: row.entity.id,
                            name: $scope.languagesForm.name,
                            code: $scope.languagesForm.code,
                            translationCode: $scope.languagesForm.translation_code,
                            defaults: $scope.languagesForm.defaults,
                            visible: $scope.languagesForm.visible
                        };

                        $http.put(
                            "/api/v2/languages",
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
                        $scope.languagesForm = {};
                        $uibModalInstance.close();
                        e.stopPropagation();
                    };
                    $scope.cancel = function (e) {
                        $scope.languagesForm = {};
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

            if(form.code === null
                || typeof form.code === 'undefined'
                || form.code === '') {
                requiredFilled.property.push('Code');
                requiredFilled.status = false;
            }

            if(form.translation_code === null
                || typeof form.translation_code === 'undefined'
                || form.translation_code === '') {
                requiredFilled.property.push('Translation Code');
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