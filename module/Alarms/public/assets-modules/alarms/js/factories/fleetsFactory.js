'use strict';

angular.module('SharengoCsApp').factory('fleetsFactory', function ($http, ENV) {
    var factory = {};

    factory.getFleets = function () {
        return $http.get(ENV.apiEndpoint + 'fleets');
    };

    return factory;
});
