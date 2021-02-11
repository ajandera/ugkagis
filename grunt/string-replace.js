module.exports = {
    layout: {
        files: {
            'layout-src/master/_config.constant.js': 'layout-src/assets/js/config.constant.js'
        },
        options: {
            replacements: [{
                pattern: /\.\.\//g,
                replacement: ''
            }]
        }
    }
};

