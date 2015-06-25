angular.module('SharengoCsApp')
    .directive('reverseGeocode', function (uiGmapIsReady) {
        return {
            restrict: 'E',
            scope: {
                lat: '=',
                lng: '='
            },
            link: function (scope, element, attrs) {
                uiGmapIsReady.promise().then(function () {
                    var geocoder = new google.maps.Geocoder();
                    scope.$watch('lng', function () {
                        if (scope.lat !== undefined && scope.lng !== undefined) {
                            var latlng = new google.maps.LatLng(scope.lat, scope.lng);
                            geocoder.geocode({
                                'latLng': latlng,
                                'language': 'it'
                            }, function (results, status) {
                                if (status === google.maps.GeocoderStatus.OK) {
                                    if (results[1]) {
                                        element.text(results[0].formatted_address);
                                    } else {
                                        element.text('Indirizzo non trovato');
                                    }
                                } else {
                                    element.text('Indirizzo non trovato');
                                }
                            });
                        }
                    });
                });
            }
        };
    });