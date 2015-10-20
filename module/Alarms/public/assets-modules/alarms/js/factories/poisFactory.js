'use strict';

angular.module('SharengoCsApp').factory('poisFactory', function ($http, ENV) {
    var factory = {};

    factory.getPois = function () {
        return $http.get(ENV.apiEndpoint + 'pois');
    };
    
    return factory;
});
