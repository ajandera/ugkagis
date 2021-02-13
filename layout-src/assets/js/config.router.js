'use strict';

/**
 * Config for the router
 */
app.config([
    '$stateProvider',
    '$urlRouterProvider',
    '$controllerProvider',
    '$compileProvider',
    '$filterProvider',
    '$provide',
    '$ocLazyLoadProvider',
    'JS_REQUIRES',
    '$locationProvider',
    function ($stateProvider,
              $urlRouterProvider,
              $controllerProvider,
              $compileProvider,
              $filterProvider,
              $provide,
              $ocLazyLoadProvider,
              jsRequires) {

        app.controller = $controllerProvider.register;
        app.directive = $compileProvider.directive;
        app.filter = $filterProvider.register;
        app.factory = $provide.factory;
        app.service = $provide.service;
        app.constant = $provide.constant;
        app.value = $provide.value;

        // LAZY MODULES

        $ocLazyLoadProvider.config({
            debug: false,
            events: true,
            modules: jsRequires.modules
        });

        // APPLICATION ROUTES
        // -----------------------------------
        // For any unmatched url, redirect to /jobs/list
        $urlRouterProvider.otherwise("/login/signin");

        // Set up the states
        $stateProvider.state('app', {
            url: "/app",
            templateUrl: "assets/views/app/app.html",
            resolve: loadSequence('chartjs', 'chart.js', 'loginCtrl'),
            abstract: true
        }).state('app.categories', {
            url: "/categories",
            templateUrl: "assets/views/app/categories.html",
            title: 'Categories',
            resolve: loadSequence('categoriesCtrl', 'securityService'),
            ncyBreadcrumb: {
                label: 'Categories'
            }
        }).state('app.documents', {
            url: "/documents",
            templateUrl: "assets/views/app/documents.html",
            title: 'Documents',
            resolve: loadSequence('documentsCtrl', 'securityService'),
            ncyBreadcrumb: {
                label: 'Documents'
            }
        }).state('app.profile', {
            url: "/profile",
            templateUrl: "assets/views/app/profile.html",
            title: 'Profile',
            resolve: loadSequence('profileCtrl', 'securityService'),
            ncyBreadcrumb: {
                label: 'Profile'
            }
        }).state('app.emailTemplates', {
            url: "/email-templates",
            templateUrl: "assets/views/app/emailTemplates.html",
            title: 'Inventory',
            resolve: loadSequence('emailTemplateCtrl', 'securityService'),
            ncyBreadcrumb: {
                label: 'Email Templates'
            }
        }).state('app.languages', {
            url: "/languages",
            templateUrl: "assets/views/app/languages.html",
            resolve: loadSequence('languagesCtrl', 'securityService'),
            title: 'Languages',
            ncyBreadcrumb: {
                label: 'Languages'
            }
        }).state('app.cms', {
            url: "/cms",
            templateUrl: "assets/views/app/cms.html",
            resolve: loadSequence('cmsCtrl', 'securityService'),
            title: 'Cms',
            ncyBreadcrumb: {
                label: 'Cms'
            }
        }).state('app.users', {
            url: "/users",
            templateUrl: "assets/views/app/users.html",
            resolve: loadSequence('usersCtrl', 'securityService'),
            title: 'Users',
            ncyBreadcrumb: {
                label: 'Users'
            }
        }).state('error', {
            url: '/error',
            template: '<div ui-view class="fade-in-up"></div>'
        }).state('error.404', {
            url: '/404',
            templateUrl: "assets/views/utility_404.html"
        }).state('error.500', {
            url: '/500',
            templateUrl: "assets/views/utility_500.html"
        }).state('login', {// Login restore password
                url: '/login',
                template: '<div ui-view class="fade-in-right-big smooth"></div>',
                abstract: true
        }).state('login.restore', {
            url: '/restore',
            templateUrl: "assets/views/login/login_restore.html",
            resolve: loadSequence('loginCtrl')
        }).state('login.signin', {
            url: '/signin',
            templateUrl: "assets/views/login/sign.html",
            resolve: loadSequence('loginCtrl')
        }).state('front', {// Front module route
            url: '/home',
            templateUrl: "assets/views/front/layout.html",
            resolve: loadSequence('jquery-appear-plugin', 'ngAppear', 'countTo', 'mainFrontCtrl'),
            abstract: true
        }).state('front.list', {
            url: '/list',
            title: 'List',
            templateUrl: "assets/views/front/list.html",
            resolve: loadSequence('listCtrl')
        }).state('front.detail', {
            url: '/:id',
            templateUrl: "assets/views/front/detail.html",
            resolve: loadSequence('detailCtrl')
        }).state('front.notFound', {
            url: '/not-found',
            title: '404 Not Found',
            templateUrl: "assets/views/utility_404.html"
        });

        // Generates a resolve object previously configured in constant.JS_REQUIRES (config.constant.js)
        function loadSequence() {
            var _args = arguments;
            return {
                deps: ['$ocLazyLoad', '$q',
                    function ($ocLL, $q) {
                        var promise = $q.when(1);
                        for (var i = 0, len = _args.length; i < len; i++) {
                            promise = promiseThen(_args[i]);
                        }
                        return promise;

                        function promiseThen(_arg) {
                            if (typeof _arg === 'function')
                                return promise.then(_arg);
                            else
                                return promise.then(function () {
                                    var nowLoad = requiredData(_arg);
                                    if (!nowLoad)
                                        return $.error('Route resolve: Bad resource name [' + _arg + ']');
                                    return $ocLL.load(nowLoad);
                                });
                        }

                        function requiredData(name) {
                            if (jsRequires.modules)
                                for (var m in jsRequires.modules)
                                    if (jsRequires.modules[m].name && jsRequires.modules[m].name === name)
                                        return jsRequires.modules[m];
                            return jsRequires.scripts && jsRequires.scripts[name];
                        }
                    }]
            };
        }
    }]);
