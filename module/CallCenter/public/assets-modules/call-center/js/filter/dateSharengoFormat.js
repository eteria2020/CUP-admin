'use strict';

angular.module('SharengoCsApp').filter('dateSharengoFormat', function dateTwist(){
    return function(date, format, zone){

        var formatDate,zoneOffset;

        if (format !== undefined) {
            formatDate = format;
        } else {
            formatDate = 'DD-MM-YYYY HH:mm:ss';
        }

        if (zone !== undefined) {
        	zoneOffset = zone; 
        }else{
            zoneOffset = '+02:00';
        } 

        return moment.utc(date).utcOffset(zoneOffset).format(formatDate);
    }
});