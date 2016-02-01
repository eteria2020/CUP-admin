'use strict';

angular.module('SharengoCsApp').factory('mapFactory', function ($http, ENV) {
    var factory = {};

    factory.getPolygon = function () {
        var parsedCol = new Array();
        parsedCol.push({
            id: id,
            path: [
                {
                    latitude: -87,
                    longitude: 120
                },
                {
                    latitude: -87,
                    longitude: -87
                },
                {
                    latitude: -87,
                    longitude: 0
                }
            ],
            stroke: false,
            editable: false,
            draggable: false,
            geodesic: false,
            visible: true,
            fill: {
                color: 0,
                opacity: 0
            }
        });
        var id = 0;

        angular.forEach(MilanoBorders.placemarks, function (idx, node) {

            var parsed_coords = new Array();
            var counter = 0;
            var lastLng;
            id++;
            angular.forEach(idx.coordinates, function (index, value) {

                counter++;
                if (counter == 1) {
                    lastLng = index;
                }
                if (counter == 2) {
                    parsed_coords.push({
                        latitude: index,
                        longitude: lastLng
                    });
                }
                if (counter == 3) {
                    counter = 0;
                }
            });

            parsedCol.push({
                id: id,
                path: parsed_coords,
                stroke: {
                    color: '#009ee0',
                    opacity: 0.8,
                    weight: 2
                },
                editable: false,
                draggable: false,
                geodesic: false,
                visible: true,
                fill: {
                    color: 0,
                    opacity: 0
                }
            });
        });
        return parsedCol;
    };



    return factory;
});
