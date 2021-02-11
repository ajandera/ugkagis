app.directive('chart', ['$http', 'graphOptions', function($http, graphOptions) {
    var template = 'assets/views/chart.tpl.html';
    var data = {
        restrict: 'E',
        replace: true,
        scope: {
            type: '@',
            scale: '@',
            point: '@',
            scales: '@',
            modes: '@',
            index: '@',
            max: '@'
        }
    };

    data.link = function (scope, elem, attrs, ctrl) {
        scope.index = attrs.index;
        scope.graphLoaded = false;
        scope.dataNotLoaded = false;
        scope.insideGraph = true;
        scope.labels = [];
        scope.series = [];
        scope.data = [];
        scope.name = attrs.type;
        scope.scalesObj = {};
        scope.modesObj = {};
        var i = 0;
        var c = 0;
        angular.forEach(scope.$root.graphTypesList, function(g) {
            if(g.name == attrs.type) {
                angular.forEach(g.scales, function(s) {
                    scope.scalesObj[i] = s;
                    i++;
                });

                angular.forEach(g.modes, function(m) {
                    scope.modesObj[c] = m;
                    c++;
                });
            }
        });
        scope.graphType = attrs.point;
        scope.graphScale = attrs.scale;
        scope.scaleDisabled = false;


        // Chart.js Options
        scope.options = graphOptions.getOptions(attrs.type);

        scope.loadData = function () {

            var requestData = {
                hotelId: scope.$root.selectedHotelId,
                type: attrs.type,
                scale: scope.graphScale
            };
            var config = {
                headers : {
                    'Content-Type': 'application/json;charset=utf-8;'
                }
            };

            $http.post('/new-admin/stats/load', requestData, config)
                .then(function (response) {
                    if(response.status == 200 && response.data.success) {
                        scope.labels = response.data.graph.xLabels;
                        scope.series = response.data.graph.seriesLabels;
                        scope.data = response.data.graph.series;
                        angular.forEach(scope.data, function(data) {
                            if(data !== '') {
                                scope.dataList = data;
                            }
                        });
                        scope.graphLoaded = true;
                        scope.insideGraph = true;
                        scope.scaleDisabled = false;
                    } else {
                        scope.graphLoaded = true;
                        scope.insideGraph = true;
                        scope.dataNotLoaded = true;
                        console.log("problem with graph response");
                        console.log(response);
                    }
                }, function (response, status, header, config) {
                    scope.graphLoaded = true;
                    scope.dataNotLoaded = true;
                    scope.responseDetails = "Data: " + response +
                    "<hr />status: " + status +
                    "<hr />headers: " + header +
                    "<hr />config: " + config;
                    console.log("problem with graph response");
                });

        };

        scope.loadData();

        scope.setClass = function(index) {
            if(index == 0 ) {
                return 'list-group-item list-group-item-info';
            } else {
                return 'list-group-item list-group-item-default';
            }

        };

        scope.changeScale = function(scale) {
            scope.$root.graphs[scope.index].scale = scale;
            scope.graphScale = scale;
        };

        scope.changeType = function(type) {
            scope.$root.graphs[scope.index].mode = type;
            scope.graphType = type;
        };

        scope.$watch('graphScale', function(next, current) {
            if(scope.scaleDisabled == false) {
                scope.insideGraph = false;
                scope.scaleDisabled = true;
                //fix for very quickly change
                if (angular.equals(next, current)) {
                    scope.insideGraph = true;
                    return; // simply skip that
                }

                //load graph when scale is changed
                if (current !== next) {
                    scope.loadData();
                }
            }
        }, true);

        scope.dismiss = function() {
            angular.forEach(scope.$root.graphs, function(g) {
                if(g.type == attrs.type) {
                    var index = scope.$root.graphs.indexOf(g);
                    scope.$root.graphs.splice(index, 1);
                    angular.forEach(scope.$root.graphTypesList, function(g) {
                        if(g.name == attrs.type) {
                            scope.$root.graphTypes.push(g);
                        }
                    });
                }
            });
        };

        scope.moveGraph = function (actualIndex, type) {
            if(type == 'left') {
                var newIndex = actualIndex - parseInt(1);
            } else if(type == 'right') {
                newIndex = parseInt(actualIndex) + parseInt(1);
            } else if(type == 'down') {
                newIndex = parseInt(actualIndex) + parseInt(2);
            } else if(type == 'top') {
                newIndex = parseInt(actualIndex) - parseInt(2);
            }

            var objFromMove = scope.$root.graphs[actualIndex];
            var objToMove = scope.$root.graphs[newIndex];
            scope.$root.graphs[newIndex] = objFromMove;
            scope.$root.graphs[actualIndex] = objToMove;
        };

        scope.isOds = function(index) {
            return index  % 2;
        };

        scope.isDown = function(index) {
            var max = parseInt(attrs.max) - parseInt(2);
            return parseInt(index) < max;
        };

        scope.isLast = function(index) {
            if (index == (attrs.max - parseInt(1))) {
                return true
            } else {
                return false;
            }
        };
    };

    data.templateUrl = function (elem, attrs) {
        return template;
    };

    return data;
}]);