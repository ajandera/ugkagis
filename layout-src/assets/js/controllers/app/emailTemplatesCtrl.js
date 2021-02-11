'use strict';
/**
 * Email Templates Controller
 */
app.controller('EmailTemplatesCtrl', [
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
        $scope.emailTemplates = {};
        $scope.emailTemplatesForm = {
            type: 0
        };
        $scope.uniqueEmail = true;
        $scope.dataLoaded = false;
        $scope.availableVariables = false;
        $scope.events = [
            {
                id: 0,
                name: "No Event",
                variables: false
            },
            {
                id: 1,
                name: "Forgot password",
                variables: '%link%'
            },
            {
                id: 2,
                name: "Register consultant",
                variables: '%name%, %date%, %username%'
            },
            {
                id: 3,
                name: "Register partner",
                variables: '%companyName%, %companyId%, %contactName%, %date%, %contactUsername%'
            },
            {
                id: 4,
                name: "Notify user is available",
                variables: '%notifyBlock%'
            },
            {
                id: 5,
                name: "Notify work is available",
                variables: '%notifyBlock%'
            },
            {
                id: 8,
                name: "Newsletter",
                variables: '%infoBlock%'
            },
            {
                id: 9,
                name: "Reaction",
                variables: '%infoBlock%'
            }
        ];

        var requestData = {};
        var config = {
            headers : {
                'Content-Type': 'application/json;charset=utf-8;'
            }
        };

        $scope.getData = function() {
            $http.get('/api/v2/email-templates', requestData, config)
                .then(function (response) {
                    if(response.status === 200) {
                        $scope.emailTemplates = response.data.templates;
                        $scope.gridOptions.data = $scope.emailTemplates;
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
        if ($location.path() === '/app/email-templates') {
            $scope.getData();
        }

        $scope.highlightFilteredHeader = function( row, rowRenderIndex, col, colRenderIndex ) {
            if ( col.filters[0].term ){
                return 'header-filtered';
            } else {
                return '';
            }
        };
        
        $scope.replaceEvent = function(grid, row) {
            var type =  $filter('filter')($scope.events, {'id': row.entity.type});
            return (type[0] !== 'undefined') ? type[0].name : '';
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
                    displayName: $filter('translate')('emailTemplates.name'),
                    enableColumnResizing: false,
                    width: "45%",
                    headerCellClass: $scope.highlightFilteredHeader,
                    cellClass: "padding-left-20 padding-top-5"
                },
                {
                    field: 'type',
                    displayName: $filter('translate')('emailTemplates.type'),
                    enableColumnResizing: false,
                    width: "45%",
                    cellTemplate: '<div class="ui-grid-cell-contents">{{ grid.appScope.replaceEvent(grid, row) }} </div>',
                    headerCellClass: $scope.highlightFilteredHeader,
                    cellClass: "padding-left-20 padding-top-5"
                },
                {
                    field: 'id',
                    displayName: $filter('translate')('users.actions'),
                    enableColumnResizing: false,
                    width: "10%",
                    cellTemplate: '<div class="ui-grid-cell-contents"><div class="btn-group" role="group" aria-label="">' +
                    '<a ng-click="grid.appScope.editEmailTemplates(grid, row)" class="btn btn-xs btn-warning" title="'+$filter('translate')('common.grid.activity.edit')+'"><i class="glyphicon glyphicon-pencil"></i></a>' +
                    '<a ng-click="grid.appScope.deleteEmailTemplates(grid, row)" class="btn btn-danger btn-xs" title="'+$filter('translate')('common.grid.activity.delete')+'"><i class="glyphicon glyphicon-trash"></i></a></div></div>',
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
                $scope.emailTemplatesRow = row.entity;
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

        $scope.deleteEmailTemplates = function(grid, row) {
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
                        "/api/v2/email-templates/"+row.entity.id,
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

        $scope.addEmailTemplates = function () {
            $aside.open({
                templateUrl: 'addEmailTemplates.html',
                scope: $scope,
                placement: 'right',
                size: 'm',
                backdrop: true,
                controller: function ($scope, $uibModalInstance) {
                    $scope.form = {title: $filter('translate')('emailTemplates.add')};
                    $scope.ok = function (e) {
                        $scope.dataLoaded = false;

                        //validate submit form
                        if($scope.validateSubmit($scope.emailTemplatesForm) === false) {
                            return;
                        }

                        // to do fill data object to send
                        var dataObject = {
                            name: $scope.emailTemplatesForm.name,
                            subject: $scope.emailTemplatesForm.subject,
                            body: $scope.emailTemplatesForm.body,
                            type: $scope.emailTemplatesForm.type
                        };

                        $http.post(
                            "/api/v2/email-templates",
                            dataObject,
                            {'Content-Type': 'application/json; charset=UTF-8'}
                        ).then(function(dataFromServer) {
                            if(dataFromServer.data.success === false) {
                                $scope.sweetAlert(
                                    $filter('translate')('common.failed'),
                                    $filter('translate')('common.record.notSaved'),
                                    'success',
                                    '#007AFF'
                                );
                                $scope.dataLoaded = true;
                            } else {
                                $scope.getData();
                            }
                        }, function(data, status, headers, config) {
                            $scope.dataLoaded = true;
                            $scope.sweetAlert(
                                $filter('translate')('common.failed'),
                                $filter('translate')('common.record.notSaved'),
                                'warning',
                                '#007AFF'
                            );
                        });
                        $scope.emailTemplatesForm = {};
                        $scope.$parent.emailTemplatesForm = {};
                        $uibModalInstance.close();
                        e.stopPropagation();
                    };
                    $scope.cancel = function (e) {
                        $scope.emailTemplatesForm = {};
                        $scope.$parent.emailTemplatesForm = {};
                        $uibModalInstance.dismiss();
                        e.stopPropagation();
                    };
                }
            });
        };

        $scope.editEmailTemplates = function (grid, row) {
            $aside.open({
                templateUrl: 'addEmailTemplates.html',
                scope: $scope,
                placement: 'right',
                size: 'm',
                backdrop: true,
                controller: function ($scope, $uibModalInstance) {
                    $scope.emailTemplatesForm = row.entity;
                    $scope.checkVariables(row.entity.type);
                    $scope.form = {title: $scope.emailTemplatesForm.name};
                    $scope.ok = function (e) {
                        $scope.dataLoaded = false;

                        //validate submit form
                        if($scope.validateSubmit($scope.emailTemplatesForm) === false) {
                            return;
                        }

                        // to do fill data object to send
                        var dataObject = {
                            id: row.entity.id,
                            name: $scope.emailTemplatesForm.name,
                            subject: $scope.emailTemplatesForm.subject,
                            body: $scope.emailTemplatesForm.body,
                            type: $scope.emailTemplatesForm.type
                        };

                        $http.put(
                            "/api/v2/email-templates",
                            dataObject,
                            {'Content-Type': 'application/json; charset=UTF-8'}
                        ).then(function(dataFromServer) {
                            if(dataFromServer.data.success === false) {
                                $scope.sweetAlert(
                                    $filter('translate')('common.failed'),
                                    $filter('translate')('common.record.notSaved'),
                                    'success',
                                    '#007AFF'
                                );
                                $scope.dataLoaded = true;
                            } else {
                                $scope.getData();
                            }
                        }, function(data, status, headers, config) {
                            $scope.dataLoaded = true;
                            $scope.sweetAlert(
                                $filter('translate')('common.failed'),
                                $filter('translate')('common.record.notSaved'),
                                'warning',
                                '#007AFF'
                            );
                        });
                        $scope.emailTemplatesForm = {};
                        $uibModalInstance.close();
                        e.stopPropagation();
                    };
                    $scope.cancel = function (e) {
                        $scope.emailTemplatesForm = {};
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
            //check if all ratePlansForm have required fileds
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

            if(form.type === null
                || typeof form.type === 'undefined'
                || form.type === '') {
                requiredFilled.property.push('Event');
                requiredFilled.status = false;
            }

            if((form.subject === null
                || typeof form.subject === 'undefined'
                || form.subject === '') && (form.type !== 6 && form.type !== 7)) {
                requiredFilled.property.push('Subject');
                requiredFilled.status = false;
            }

            if(form.body === null
                || typeof form.body === 'undefined'
                || form.body === '') {
                requiredFilled.property.push('Body');
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
        };
        
        $scope.checkVariables = function(type) {
            angular.forEach($scope.events, function(e) {
                if (e.id === type) {
                    $scope.availableVariables = e.variables;
                }
            });
        };
    }]);
