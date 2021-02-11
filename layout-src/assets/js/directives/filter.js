/*
* AngularJS directive for TreeView
* @author Aleš Jandera <ajandera@ajandera.cz>
*/
app.directive('tree-view', ['$timeout','$interval', function($timeout, Sinterval) {
    var template = 'assets/views/treeView.tpl.html';
    var data = {
        restrict: 'E',
        replace: true,
        scope: {
            model: '@',
            output: '@'
        }
    };

    data.link = function (scope, elem, attrs, ctrl) {
        scope.model = JSON.parse(attrs.model);
        scope.output = parseInt(attrs.output);
    };

    data.templateUrl = function (elem, attrs) {
        return template;
    };

    return data;
}]);
