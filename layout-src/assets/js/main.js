var app = angular.module('app', ['ugkagis']);
app.run(['$rootScope', '$state', '$stateParams',
function ($rootScope, $state, $stateParams) {

    // Attach Fastclick for eliminating the 300ms delay between a physical tap and the firing of a click event on mobile browsers
    FastClick.attach(document.body);

    // Set some reference to access them from any scope
    $rootScope.$state = $state;
    $rootScope.$stateParams = $stateParams;

    // GLOBAL APP SCOPE
    // set below basic information
    $rootScope.app = {
        name: 'UGKAGIS', // name of your project
        author: 'Aleš Jandera', // author's name or company name
        description: 'Ústav geodézie, kartografie a geografických informačných systémov (ÚGKaGIS) je vedecko–pedagogické pracovisko zabezpečujúce výchovu absolventov spôsobilých pre plnenie úloh rezortu geodézie, kartografie a katastra nehnuteľností.', // brief description
        version: '1.0', // current version
        year: ((new Date()).getFullYear()), // automatic current year (for copyright information)
        isMobile: (function () {// true if the browser is a mobile device
            var check = false;
            if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
                check = true;
            };
            return check;
        })(),
        defaultLayout: {
            isNavbarFixed: true, //true if you want to initialize the template with fixed header
            isSidebarFixed: true, // true if you want to initialize the template with fixed sidebar
            isSidebarClosed: false, // true if you want to initialize the template with closed sidebar
            isFooterFixed: false, // true if you want to initialize the template with fixed footer
            isBoxedPage: false, // true if you want to initialize the template with boxed layout
            theme: 'lyt2-theme-1', // indicate the theme chosen for your project
            logo: 'assets/images/logo.png', // relative path of the project logo
            logoCollapsed: 'assets/images/logo-collapsed.png' // relative path of the collapsed logo
        },
        layout: ''
    };
    $rootScope.app.layout = angular.copy($rootScope.app.defaultLayout);
}]);
// translate config
app.config(['$translateProvider',
function ($translateProvider) {

    // prefix and suffix information  is required to specify a pattern
    // You can simply use the static-files loader with this pattern:
    $translateProvider.useStaticFilesLoader({
        prefix: 'assets/i18n/',
        suffix: '.json'
    });

    // Since you've now registered more then one translation table, angular-translate has to know which one to use.
    // This is where preferredLanguage(langKey) comes in.
    $translateProvider.preferredLanguage('en');

    // Store the language in the local storage
    $translateProvider.useLocalStorage();

    // Enable sanitize
    $translateProvider.useSanitizeValueStrategy('sanitize');

}]);
// Angular-Loading-Bar
// configuration
app.config(['cfpLoadingBarProvider',
function (cfpLoadingBarProvider) {
    cfpLoadingBarProvider.includeBar = true;
    cfpLoadingBarProvider.includeSpinner = false;

}]);
// Angular-breadcrumb
// configuration
app.config(function ($breadcrumbProvider) {
    $breadcrumbProvider.setOptions({
        template: '<ul class="breadcrumb"><li><a ui-sref="app.dashboard"><i class="fa fa-home margin-right-5 text-large text-dark"></i>Home</a></li><li ng-repeat="step in steps">{{step.ncyBreadcrumbLabel}}</li></ul>'
    });
});
// ng-storage
//set a prefix to avoid overwriting any local storage variables
app.config(['$localStorageProvider',
    function ($localStorageProvider) {
        $localStorageProvider.setKeyPrefix('PacketLtr2');
    }]);
//filter to convert html to plain text
app.filter('htmlToPlaintext', function () {
      return function (text) {
          return String(text).replace(/<[^>]+>/gm, '');
      };
  }
);
//Custom UI Bootstrap Calendar Popup Template
app.run(["$templateCache", function ($templateCache) {
    $templateCache.put("uib/template/datepicker/popup.html",
            "<ul class=\"dropdown-menu clip-datepicker\"  ng-style=\"{display: (isOpen && 'block') || 'none', top: position.top+'px', left: position.left+'px'}\" ng-keydown=\"keydown($event)\">\n" +
            "	<li ng-transclude></li>\n" +
            "	<li ng-if=\"showButtonBar\" style=\"padding:10px 9px 2px\">\n" +
            "		<span class=\"btn-group pull-left\">\n" +
            "			<button type=\"button\" class=\"btn btn-sm btn-primary btn-o\" ng-click=\"select('today')\">{{ getText('current') }}</button>\n" +
            "			<button type=\"button\" class=\"btn btn-sm btn-primary btn-o\" ng-click=\"select(null)\">{{ getText('clear') }}</button>\n" +
            "		</span>\n" +
            "		<button type=\"button\" class=\"btn btn-sm btn-primary pull-right\" ng-click=\"close()\">{{ getText('close') }}</button>\n" +
            "	</li>\n" +
            "</ul>\n" +
        "");
}]);
