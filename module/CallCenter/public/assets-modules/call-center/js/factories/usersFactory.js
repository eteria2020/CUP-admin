'use strict';

angular.module('SharengoCsApp').factory('usersFactory', function ($http, ENV) {
    var factory = {};

    factory.getUsers = function (phone, name, surname, card) {
        var query = 'limit=10';

        if (phone) {
            query += '&phone=' + phone;
        }
        if (name) {
            query += '&name=' + name;
        }
        if (surname) {
            query += '&surname=' + surname;
        }
        if (card) {
            query += '&card_code=' + card;
        }

        return $http.get(ENV.apiEndpoint + 'users?' + query);
    };

    factory.getTrip = function (user) {
        return $http.get(ENV.apiEndpoint + 'trips?limit=1&customer=' + user.id);
    };

    factory.getLastTrips = function (user) {
        return $http.get(ENV.apiEndpoint + 'trips?limit=5&customer=' + user.id);
    };

    return factory;
});
