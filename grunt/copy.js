module.exports = {
    layout: {
        files: [
            {expand: true, src: "**", cwd: 'bower_components/bootstrap/fonts', dest: "layout/assets/fonts"},
            {expand: true, src: "**", cwd: 'bower_components/font-awesome/fonts', dest: "layout/assets/fonts"},
            {expand: true, src: "**", cwd: 'bower_components/themify-icons/fonts', dest: "layout/assets/css/fonts"},
            {expand: true, src: "**", cwd: 'bower_components/slick-carousel/slick/fonts', dest: "layout/assets/css/fonts"},
            {expand: true, src: "**", cwd: 'bower_components/flag-icon-css/flags', dest: "layout/assets/flags"},
            {expand: true, src: "**", cwd: 'layout-src/templates',     dest: "layout/templates"},
            {expand: true, src: "**", cwd: 'layout-src/assets/api',     dest: "layout/assets/api"},
            {expand: true, src: "**", cwd: 'layout-src/assets/i18n',    dest: "layout/assets/i18n"},
            {expand: true, src: "**", cwd: 'layout-src/assets/images',     dest: "layout/assets/images"},
            {expand: true, src: "**", cwd: 'layout-src/assets/files',     dest: "layout/assets/files"},
            {expand: true, src: "**", cwd: 'layout-src/assets/js/config',      dest: "layout/assets/js/config"},
            {expand: true, src: "**", cwd: 'layout-src/assets/js/directives',      dest: "layout/assets/js/directives"},
            {expand: true, src: "**", cwd: 'layout-src/assets/js/controllers',      dest: "layout/assets/js/controllers"},
            {expand: true, src: "**", cwd: 'layout-src/assets/js/filters',      dest: "layout/assets/js/filters"},
            {expand: true, src: "**", cwd: 'layout-src/assets/js/services',      dest: "layout/assets/js/services"},
            {expand: true, src: "**", cwd: 'layout-src/assets/views',     dest: "layout/assets/views"},
            {expand: true, src: "**", cwd: 'layout-src/assets/css/themes',     dest: "layout/assets/css/themes"},
            {src: 'bower_components/slick-carousel/slick/ajax-loader.gif', dest : 'layout/assets/css/ajax-loader.gif'},
            {src: 'layout-src/master/_index.min.html', dest : 'layout/index.html'},
            {src: 'layout-src/favicon.ico', dest : 'layout/favicon.ico'},
            {src: 'bower_components/angular-ui-grid/ui-grid.ttf', dest : 'layout/assets/css/ui-grid.ttf'},
            {src: 'bower_components/angular-ui-grid/ui-grid.woff', dest : 'layout/assets/css/ui-grid.woff'},
            {src: 'bower_components/summernote/dist/font/summernote.eot', dest : 'layout/assets/css/font/summernote.eot'},
            {src: 'bower_components/summernote/dist/font/summernote.ttf', dest : 'layout/assets/css/font/summernote.ttf'},
            {src: 'bower_components/summernote/dist/font/summernote.woff', dest : 'layout/assets/css/font/summernote.woff'}
        ]
    }
};
