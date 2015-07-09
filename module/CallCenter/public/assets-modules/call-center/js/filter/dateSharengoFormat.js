'use strict';

angular.module('SharengoCsApp').filter('dateSharengoFormat', function dateTwist(){
    return function(date, format, zone){

        var formatDate;

        if (format !== undefined) {
            formatDate = format;
        } else {
            formatDate = 'DD-MM-YYYY HH:mm:ss';
        }

        return moment(date).format(formatDate);
    }
});