app.directive('preventEnter', function() {
    return {
        link: function (scope, element, attrs) {
            element.keypress(function (e) {
                if (e.keyCode == 13) {
                    e.preventDefault();
                    return;
                }
            });
        }
    }
});