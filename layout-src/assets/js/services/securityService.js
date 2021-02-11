/**
 * This file is part of the ugkagis
 * @author Aleš Jandera <ales.jander@gmail.com>
 */

app.service('securityService', function ($rootScope, $location, $http, $window) {

    var that = this;
    var rootScope = $rootScope;

    /**
     * Whether user has permission to perform given functionality in currently selected hotel
     * @param functionality name of functionality
     * @returns {boolean}
     */
    this.isPermitted = function(functionality) {
        if(rootScope.securityInfo === null || angular.equals({}, rootScope.securityInfo)) {
            $location.path('/jobs/list');
        } else {
            if(typeof rootScope.securityInfo === 'undefined') {
                $location.path('/home');
            } else {
                if (functionality === '' || rootScope.securityInfo.role === 'admin') {
                    return true;
                } else {
                    return that.evaluatePermission(functionality, rootScope.securityInfo.role);
                }
            }
        }

        return false;
    };

    this.evaluatePermission = function (functionality, role) {
        if(functionality === 'admin' && role > 0) {
            return false;
        } else {
            return true;
        }
    };

    /**
     * Verify that user has access allowed for given functionality and if not, redirect him to Dashboard
     * @param functionality name
     */
    this.verifyAccessAllowed = function(functionality) {
        var permission = that.isPermitted(functionality);
        if(permission === false) {
            $location.path('/app/home');
        }
    };

    /**
     * Verify that user has access to save the form.
     * @param functionality name
     */
    this.verifySaveAllowed = function(functionality) {
        return that.isPermitted(functionality);
    };
    
    this.encrypt = function(string) {
        // Encrypt String using CryptoJS AES
        var encrypted = CryptoJS.AES.encrypt(string, 'dhkashdajk13221wqe');
        return encrypted.toString();
    };

    this.decrypt = function(encrypted) {
        // Decrypt String using CryptoJS AES 
        var decrypted = CryptoJS.AES.decrypt(encrypted, 'dhkashdajk13221wqe');
        return decrypted.toString(CryptoJS.enc.Utf8);
    };

    this.getSecurityInfo = function() {
        var loginStorage = $window.localStorage.getItem('login');
        if (loginStorage === null) {
            $http.get('/api/v2/security/profile', {})
                .success(function (data, status, headers, config) {
                    $rootScope.securityInfo = data.user;
                    if($rootScope.securityInfo === null
                        || $rootScope.securityInfo.length === 0
                        || $rootScope.securityInfo.role > 1
                    ) {
                        $rootScope.isLoggedIn = false;
                        $location.path('/home');
                    } else {
                        var encrypted = that.encrypt(JSON.stringify($rootScope.securityInfo));
                        $window.localStorage.setItem('login', encrypted);
                        $rootScope.isLoggedIn = true;
                        $rootScope.isPermitted = that.isPermitted; // function pointer so that nav.html can access it
                    }
                })
                .error(function (data, status, header, config) {
                    console.log("Error - can't get securityInfo");
                });
        } else {
            $rootScope.securityInfo = JSON.parse(that.decrypt(loginStorage));
            if ($rootScope.securityInfo === null || $rootScope.securityInfo.role > 1) {
                $rootScope.isLoggedIn = false;
            } else {
                $rootScope.isLoggedIn = true;
                $rootScope.isPermitted = that.isPermitted;
            }
        }
    };
});
