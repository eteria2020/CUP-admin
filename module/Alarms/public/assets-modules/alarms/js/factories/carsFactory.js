'use strict';

angular.module('SharengoCsApp').factory('carsFactory', function ($http, ENV) {
    var factory = {};

    factory.getCars = function () {
        return $http.get(ENV.apiEndpoint + 'cars');
    };

    factory.getTrip = function (car) {
        return $http.get(ENV.apiEndpoint + 'trips?limit=1&plate=' + car.plate);
    };

    factory.getLastClosedTrip = function (car) {
        return $http.get(ENV.apiEndpoint + 'trips?limit=1&plate=' + car.plate);
    };

    return factory;
});
