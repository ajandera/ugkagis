app.directive('searchInput', ['$timeout','$interval', '$http', '$q', function($timeout, $interval, $http, $q) {
    return {
        restrict: 'E',
        templateUrl: 'assets/views/searchInput.tpl.html',
        scope: {
            inputModel: '='
        },
        link: function (scope, elem, attrs, ctrl) {
            scope.timer = $interval(function () {
                if (typeof scope.inputModel !== 'undefined') {
                    scope.searchString = angular.copy(scope.inputModel.name);
                    scope.stop();
                }
            }, 100);

            scope.search = function() {
                scope.stop();
                if (scope.searchString.length < 2) {
                    return;
                }

                $http.get(
                    '/api/v2/partners/name/'+scope.searchString,
                    {},
                    {
                        headers : {
                            'Content-Type': 'application/json;charset=utf-8;'
                        }
                    }
                ).then(function (response) {
                        if(response.status === 200) {
                            scope.inputModel = response.data.partners;
                        }
                    }, function (response, status, header, config) {
                        $scope.responseDetails = "Data: " + response +
                            "<hr />status: " + status +
                            "<hr />headers: " + header +
                            "<hr />config: " + config;
                    });
            };

            scope.stop = function() {
                $interval.cancel(scope.timer);
            };

            scope.select = function (id, name) {
                scope.stop();
                scope.inputModel = {id: id, name: name};
                scope.searchString = name;
            };
        }
    }
}]);
