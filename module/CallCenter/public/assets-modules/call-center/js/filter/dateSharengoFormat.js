'use strict';

angular.module('SharengoCsApp').filter('dateSharengoFormat', function dateTwist(){
    return function(date, format){

        var formatDate;

        if (format !== undefined) {
            formatDate = format;
        } else {
            formatDate = 'DD-MM-YYYY HH:m:ss';
        }
        return moment(date).format(formatDate);
    }
});