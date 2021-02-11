'use strict';

/**
 * Config constant
 */
app.constant('APP_MEDIAQUERY', {
    'desktopXL': 1200,
    'desktop': 992,
    'tablet': 768,
    'mobile': 480
});
app.constant('JS_REQUIRES', {
    //*** Scripts
    scripts: {
        //*** Javascript Plugins
        'd3': 'bower_components/d3/d3.min.js',

        //*** jQuery Plugins
        'chartjs': 'bower_components/chartjs/dist/Chart.min.js', // Chart.js 1.x is in chartjs/Chart.min.js, Chart.js 2.x in chartjs/dist/Chart.min.js
        'ckeditor-plugin': 'bower_components/ckeditor/ckeditor.js',
        'jquery-nestable-plugin': ['bower_components/jquery-nestable/jquery.nestable.js'],
        'touchspin-plugin': ['bower_components/bootstrap-touchspin/dist/jquery.bootstrap-touchspin.min.js', 'bower_components/bootstrap-touchspin/dist/jquery.bootstrap-touchspin.min.css'],
        'jquery-appear-plugin': ['bower_components/jquery-appear/build/jquery.appear.min.js'],
        'spectrum-plugin': ['bower_components/spectrum/spectrum.js', 'bower_components/spectrum/spectrum.css'],
        'jquery-ui': ['bower_components/jquery-ui/jquery-ui.min.js'],

        //*** Controllers
        'usersCtrl': ['assets/js/controllers/app/usersCtrl.js'],
        'categoriesCtrl': ['assets/js/controllers/app/categoriesCtrl.js'],
        'emailTemplateCtrl': ['assets/js/controllers/app/emailTemplatesCtrl.js'],
        'languagesCtrl': ['assets/js/controllers/app/languagesCtrl.js'],
        'loginCtrl': ['assets/js/controllers/login/loginCtrl.js'],
        'mainCtrl': ['assets/js/controllers/mainCtrl.js'],
        'cmsCtrl': ['assets/js/controllers/app/cmsCtrl.js'],
        'listCtrl': ['assets/js/controllers/front/listCtrl.js'],
        'detailCtrl': ['assets/js/controllers/front/detailCtrl.js'],
        'mainFrontCtrl': ['assets/js/controllers/front/mainCtrl.js'],
        'documentsCtrl': ['assets/js/controllers/app/documentsCtrl.js'],
        'profileCtrl': ['assets/js/controllers/app/profileCtrl.js'],

        //*** Services
        'securityService': 'assets/js/services/securityService.js',
        'stringAndDates': 'assets/js/services/stringAndDates.js'

    },
    //*** angularJS Modules
    modules: [{
        name: 'toaster',
        files: ['bower_components/AngularJS-Toaster/toaster.js', 'bower_components/AngularJS-Toaster/toaster.css']
    }, {
        name: 'angularBootstrapNavTree',
        files: ['bower_components/angular-bootstrap-nav-tree/dist/abn_tree_directive.js', 'bower_components/angular-bootstrap-nav-tree/dist/abn_tree.css']
    }, {
        name: 'ngTable',
        files: ['bower_components/ng-table/dist/ng-table.min.js', 'bower_components/ng-table/dist/ng-table.min.css']
    }, {
        name: 'ui.mask',
        files: ['bower_components/angular-ui-utils/mask.min.js']
    }, {
        name: 'ngImgCrop',
        files: ['bower_components/ngImgCrop/compile/minified/ng-img-crop.js', 'bower_components/ngImgCrop/compile/minified/ng-img-crop.css']
    }, {
        name: 'angularFileUpload',
        files: ['bower_components/angular-file-upload/angular-file-upload.min.js']
    }, {
        name: 'monospaced.elastic',
        files: ['bower_components/angular-elastic/elastic.js']
    }, {
        name: 'ngMap',
        files: ['bower_components/ngmap/build/scripts/ng-map.min.js']
    }, {
        name: 'chart.js',
        files: ['bower_components/angular-chart.js-1.0.0/dist/angular-chart.min.js']
    }, {
        name: 'flow',
        files: ['bower_components/ng-flow/dist/ng-flow-standalone.min.js']
    }, {
        name: 'ckeditor',
        files: ['bower_components/angular-ckeditor/angular-ckeditor.min.js']
    }, {
        name: 'mwl.calendar',
        files: ['bower_components/angular-bootstrap-calendar/dist/js/angular-bootstrap-calendar-tpls.js', 'bower_components/angular-bootstrap-calendar/dist/css/angular-bootstrap-calendar.min.css', 'assets/js/config/config-calendar.js']
    }, {
        name: 'ng-nestable',
        files: ['bower_components/ng-nestable/src/angular-nestable.js']
    }, {
        name: 'ngNotify',
        files: ['bower_components/ng-notify/dist/ng-notify.min.js', 'bower_components/ng-notify/dist/ng-notify.min.css']
    }, {
        name: 'xeditable',
        files: ['bower_components/angular-xeditable/dist/js/xeditable.min.js', 'bower_components/angular-xeditable/dist/css/xeditable.css', 'assets/js/config/config-xeditable.js']
    }, {
        name: 'checklist-model',
        files: ['bower_components/checklist-model/checklist-model.js']
    }, {
        name: 'ui.knob',
        files: ['bower_components/ng-knob/dist/ng-knob.min.js']
    }, {
        name: 'ngAppear',
        files: ['bower_components/angular-appear/build/angular-appear.min.js']
    }, {
        name: 'countTo',
        files: ['bower_components/angular-count-to-0.1.1/dist/angular-filter-count-to.min.js']
    }, {
        name: 'angularSpectrumColorpicker',
        files: ['bower_components/angular-spectrum-colorpicker/dist/angular-spectrum-colorpicker.min.js']
    }, {
        name: 'sortable',
        files: ['bower_components/angular-ui-sortable/sortable.min.js']
    }]
});
