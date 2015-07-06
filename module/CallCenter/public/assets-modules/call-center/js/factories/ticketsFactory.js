'use strict';

angular.module('SharengoCsApp').factory('ticketsFactory', function ($http, ENV) {
    var factory = {};

    factory.submitRequest = function (data) {
        return $http.post(ENV.apiEndpoint + 'tickets', data);
    };

    factory.getLastCarTickets = function (car) {
        return $http.get(ENV.apiEndpoint + 'tickets?' +
            'plate=' + car.vettura_targa + '&limit=3');
    };

    factory.getLastUserTickets = function (user) {
        return $http.get(ENV.apiEndpoint + 'tickets?' +
            'user=' + user.id + '&limit=3');
    };

    factory.getTypeCall = function (type) {

        switch(type) {

            case 1:
                return 'Istruzioni utilizzo';
                break;

            case 2:
                return 'Guasto auto';
                break;

            case 3:
                return 'Altro';
                break;
        }
    };

    return factory;
});
