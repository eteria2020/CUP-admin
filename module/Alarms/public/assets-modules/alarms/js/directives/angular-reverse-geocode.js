angular.module('SharengoCsApp')
    .directive('reverseGeocode', function (uiGmapIsReady, $http) {
        return {
            restrict: 'E',
            scope: {
                lat: '=',
                lng: '='
            },
            link: function (scope, element, attrs) {
                uiGmapIsReady.promise().then(function () {
                    element.text('Indirizzo non trovato');
                    scope.$watch('lng', function () {
                        if (scope.lat !== undefined && scope.lng !== undefined) {
                            $http({
                                method: 'GET',
                                url: 'https://maps.sharengo.it/reverse.php?format=json&zoom=18&addressdetails=1&lon=' + scope.lng + '&lat=' + scope.lat
                            }).then(function successCallback(response) {
                                element.text(response.data.display_name);
                            }, function errorCallback(response) {

                            });
                        }
                    });
                });
            }
        };
    });