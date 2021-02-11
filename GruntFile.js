module.exports = function(grunt) {
    var gtx = require('gruntfile-gtx').wrap(grunt);

    gtx.loadAuto();

    var gruntConfig = require('./grunt');
    gruntConfig.package = require('./package.json');

    gtx.config(gruntConfig);

    // We need our bower components in order to develop
    gtx.alias('build:layout', ['clean:layout', 'compass:layout', 'copy:layout', 'string-replace:layout', 'concat:layout', 'cssmin:layout', 'uglify:layout']);
    //gtx.alias('release', ['bower-install-simple', 'build:dev', 'bump-commit']);

    gtx.finalise();
};
