/**
 * This file is part of the ugkagis
 * @author Aleš Jandera <ales.jander@gmail.com>
 */

app.service('stringsAndDates', function () {

    this.nextMonth = function(date) {
         return new Date(date.setMonth(date.getMonth() + 1, 1));
    },

    this.dateObjectToString = function(date) {
        var n = this.monthTo2(date.getMonth());
        return date.getFullYear() + '-' + n + '-' +("0" + date.getDate()).slice(-2);
    },

    this.inventoryDateCorrection = function(date) {
        var d = date.date.split('-');
        return d[0]+'-'+d[1]+'-'+("0" + d[2]).slice(-2);
    },

    this.monthTo2 = function(index) {
        var monthTo2 = [];
        monthTo2[0] = "01";
        monthTo2[1] = "02";
        monthTo2[2] = "03";
        monthTo2[3] = "04";
        monthTo2[4] = "05";
        monthTo2[5] = "06";
        monthTo2[6] = "07";
        monthTo2[7] = "08";
        monthTo2[8] = "09";
        monthTo2[9] = "10";
        monthTo2[10] = "11";
        monthTo2[11] = "12";
        return monthTo2[index];
    },

    this.accentMap = {
        'á':'a',
        'é':'e',
        'í':'i',
        'ó':'o',
        'ú':'u',
        'š':'s',
        'č':'c',
        'ř':'r',
        'ž':'z',
        'ý':'y',
        'ě':'e',
        'ň':'n',
        'ä':'a',
        'ü':'u',
        'Á':'A',
        'É':'E',
        'Í':'I',
        'Ó':'O',
        'Ú':'U',
        'Š':'S',
        'Č':'C',
        'Ř':'R',
        'Ž':'Z',
        'Ý':'Y',
        'Ě':'E',
        'Ň':'N',
        'Ä':'A',
        'Ü':'U'
    },

    this.accentFold = function(s) {
        if (!s) { return ''; }
        var ret = '';
        for (var i = 0; i < s.length; i++) {
            ret += this.accentMap[s.charAt(i)] || s.charAt(i);
        }
        return ret;
    };
    
    this.validateEmail = function(email) {
        var pattern = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return pattern.test(email);
    };
    
    this.hasNumbers = function(string) {
        var pattern = /\d/g;
        return pattern.test(string);
    };
    
    this.onlyNumbers = function(number) {
        var pattern = /^\d+$/;
        return pattern.test(number.replace("+", ""));
    }
});
