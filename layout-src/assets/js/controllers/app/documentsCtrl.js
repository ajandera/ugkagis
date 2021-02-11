'use strict';
/**
 * Documents Controller
 */
app.controller('DocumentsCtrl', [
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
    'FileUploader',
    function ($rootScope, $scope, $http, SweetAlert, securityService, uiGridConstants, $filter, $timeout, $aside, $location, FileUploader) {
        $scope.showForm = false;
        $scope.documents = {};
        $scope.dataLoaded = false;

        var requestData = {};
        var config = {
            headers : {
                'Content-Type': 'application/json;charset=utf-8;'
            }
        };

        $scope.getData = function() {
            $http.get('/api/v2/documents', requestData, config)
                .then(function (response) {
                    if(response.status === 200) {
                        $scope.documents = response.data.documents;
                        $scope.gridOptions.data = $scope.documents;
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
        if($location.path() === '/app/documents') {
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
                    cellClass: "padding-left-20 padding-top-5"
                },
                {
                    field: 'description',
                    displayName: $filter('translate')('categories.description'),
                    enableColumnResizing: false,
                    width: "40%",
                    headerCellClass: $scope.highlightFilteredHeader,
                    cellClass: "padding-left-10 padding-top-5"
                },
                {
                    field: 'id',
                    displayName: $filter('translate')('users.actions'),
                    width: "10%",
                    enableColumnResizing: false,
                    cellTemplate: '<div class="ui-grid-cell-contents"><div class="btn-group" role="group" aria-label="">' +
                    '<a ng-click="grid.appScope.downloadDocument(grid, row)" class="btn btn-xs btn-primary" title="'+$filter('translate')('categories.sebTree')+'"><i class="glyphicon glyphicon-download"></i></a>' +
                    '<a ng-click="grid.appScope.editDocument(grid, row)" class="btn btn-warning btn-xs" title="'+$filter('translate')('categories.edit')+'"><i class="glyphicon glyphicon-pencil"></i></a>' +
                    '<a ng-click="grid.appScope.deleteDocument(grid, row)" class="btn btn-danger btn-xs" title="'+$filter('translate')('categories.delete')+'"><i class="glyphicon glyphicon-trash"></i></a></div></div>',
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

        $scope.deleteDocument = function(grid, row) {
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
                        "/api/v2/documents/"+row.entity.id,
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

        $scope.downloadDocument = function (grid, row) {
            window.open(
                row.entity.src,
                '_blank'
            );
        };

        $scope.sweetAlert = function (title, text, type, color) {
            SweetAlert.swal({
                title: title,
                text: text,
                type: type,
                confirmButtonColor: color
            });
        };

        var uploader = $scope.uploader = new FileUploader({
            url: '/api/v2/documents/uploader'
        });

        // FILTERS

        // a sync filter
        uploader.filters.push({
            name: 'syncFilter',
            fn: function(item /*{File|FileLikeObject}*/, options) {
                console.log('syncFilter');
                return this.queue.length < 10;
            }
        });

        // an async filter
        uploader.filters.push({
            name: 'asyncFilter',
            fn: function(item /*{File|FileLikeObject}*/, options, deferred) {
                console.log('asyncFilter');
                setTimeout(deferred.resolve, 1e3);
            }
        });

        // an max size filter
        uploader.filters.push({
            name: 'enforceMaxFileSize',
            fn: function (item) {
                return item.size <= 5242880; // 5 MiB to bytes
            }
        });

        // CALLBACKS
        uploader.onCompleteAll = function() {
            $scope.getData();
        };

        $scope.editDocument = function (grid, row) {
            $aside.open({
                templateUrl: 'editDocument.html',
                scope: $scope,
                placement: 'right',
                size: 'm',
                backdrop: true,
                controller: function ($scope, $uibModalInstance) {
                    $scope.documentForm = row.entity;
                    $scope.form = { title: $scope.documentForm.name };

                    $scope.ok = function (e) {
                        $scope.dataLoaded = false;

                        // to do fill data object to send
                        var dataObject = {
                            id: row.entity.id,
                            name: $scope.documentForm.name
                        };

                        $http.put(
                            "/api/v2/documents/rename",
                            dataObject,
                            {'Content-Type': 'application/json; charset=UTF-8'}
                        ).then(function(dataFromServer) {
                            if(dataFromServer.data.success === false) {
                                $scope.sweetAlert(
                                    $filter('translate')('common.failed'),
                                    $filter('translate')('common.common.fail'),
                                    'success',
                                    '#007AFF'
                                );
                            } else {
                                $scope.getData();
                            }
                            $scope.dataLoaded = true;
                        }, function(data, status, headers, config) {
                            $scope.dataLoaded = true;
                            $scope.sweetAlert(
                                $filter('translate')('common.failed'),
                                $filter('translate')('common.common.fail'),
                                'warning',
                                '#007AFF'
                            );
                        });
                        $scope.currencyForm = {};
                        $uibModalInstance.close();
                        e.stopPropagation();
                    };
                    $scope.cancel = function (e) {
                        $scope.currencyForm = {};
                        $uibModalInstance.dismiss();
                        e.stopPropagation();
                    };
                }
            });
        };
    }]);