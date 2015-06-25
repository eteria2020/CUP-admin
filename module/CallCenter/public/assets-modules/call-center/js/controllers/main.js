'use strict';

angular.module('SharengoCsApp').controller('SharengoCsController', function (
    $scope,
    $interpolate,
    carsFactory,
    usersFactory,
    //mapFactory,
    ticketsFactory,
    uiGmapGoogleMapApi,
    uiGmapIsReady,
    $timeout
) {
    var infoBox;
    //necessary to avoid errors with network delay
    $scope.cars = [];
    $scope.mapLoader = true;

    var searchByCar = false;
    var searchByCustomer = false;

    $scope.accordionStatus = {
        researchOpen: true,
        hideCustomerData: true,
        customerDataOpen: true,
        hideCustomerSelect: true,
        customerSelectOpen: true,
        hideCarData: true,
        carDataOpen: true,
        hideTripData: true,
        tripDataOpen: true,
        hideLastTripData: true,
        lastTripDataOpen: true,
        hideRequest: true,
        requestOpen: false,
        showFormTicket: false,
        showSuccessMessageTicket: false,
        showNoResult: false
    };
    $scope.search = {};

    //$scope.polygons = mapFactory.getPolygon();

    var geocoder, map, marker;
    uiGmapIsReady.promise().then(function (maps) {
        map = maps[0].map;
    });

    var addMarker = function (lat, lon) {
        var myLatlng = new google.maps.LatLng(lat, lon);
        if (marker) {
            marker.setMap(null);
        }
        marker = new google.maps.Marker({
            position: myLatlng,
            map: map
        });
    }

    $scope.searchAddress = {
        address: '',
        number: '',
        city: 'Milano',
        search: function () {
            $scope.mapLoader = true;
            if (!geocoder) {
                geocoder = new google.maps.Geocoder();
            }
            var address = $scope.searchAddress.number+', '+$scope.searchAddress.address+', '+$scope.searchAddress.city
            geocoder.geocode( { 'address': address}, function(results, status) {
                if (status == google.maps.GeocoderStatus.OK) {
                    $scope.mapOptions.center = {
                        latitude: results[0].geometry.location.lat(),
                        longitude: results[0].geometry.location.lng() + 0.00010
                    }
                    $scope.mapOptions.zoom = 14;
                    addMarker($scope.mapOptions.center.latitude, $scope.mapOptions.center.longitude);
                } else {
                    alert('Geocode was not successful for the following reason: ' + status);
                }
                $scope.mapLoader = false;
                $scope.$digest();
            });
        }
    }

    $scope.loadCars = function () {
        $scope.mapLoader = true;
        return carsFactory.getCars().success(function (cars) {
            cars = cars.data;
            cars.forEach(function (car) {
                car.id = car.plate; //Date.parse(car.vettura_id.replace(' ', 'T'));
               /* car.latitude = car.vettura_lat;
                car.longitude = car.vettura_lon;*/
                car.carIcon = "/assets-modules/call-center/images/marker-s-blue.png";
                car.iconSelected = "/assets-modules/call-center/images/marker-s-blue-selected.png";
                if(car.status!='operative'){
                    car.carIcon = "/assets-modules/call-center/images/marker-s-red.png";
                    car.iconSelected = "/assets-modules/call-center/images/marker-s-red-selected.png";
                }else if(car.busy){
                    car.carIcon = "/assets-modules/call-center/images/marker-s-yellow.png";
                    car.iconSelected = "/assets-modules/call-center/images/marker-s-yellow-selected.png";
                }else if(car.reservation){
                    car.carIcon = "/assets-modules/call-center/images/marker-s-black.png";
                    car.iconSelected = "/assets-modules/call-center/images/marker-s-black-selected.png";
                }
            });
            $scope.cars = cars;
            $scope.mapLoader = false;
        });
    };

    $scope.loadCars();

    $scope.zoom = function (center, zoom) {
        $scope.mapOptions.center = {
            latitude: center.latitude,
            longitude: center.longitude
        };
        $scope.mapOptions.zoom = zoom;
    };

    $scope.onCarSelection = function (car) {
        $scope.cars.forEach(function (carl) {
            if (carl.plate === car.plate) {
                carl.carIcon = car.iconSelected;
            } else {
                carl.carIcon = carl.carIcon;
            }
        });

        /*ticketsFactory.getLastCarTickets(car.plate).success(function (tickets) {

            tickets.forEach(function (ticket) {
                ticket.tipo_chiamata = ticketsFactory.getTypeCall(ticket.tipo_chiamata);
            });

            car.lastTickets = tickets;
        });*/

        $scope.car = car;
        $scope.zoom({
            latitude: car.latitude,
            longitude: parseFloat(car.longitude) + 0.00010
        }, 21);

    };

    //TODO: avoid data repetition, bettermove then out of here
    $scope.zoomOut = function () {
        /* 
        $scope.cars.forEach(function (carl) {
            carl.carIcon = carl.icon;
        });
        */

        $scope.zoom({
            latitude: 45.46,
            longitude: 9.25
        }, 12);

        if (infoBox) {
            infoBox.close();
        }
    };

    $scope.onCustomerSelection = function (customer) {
               /* ticketsFactory.getLastUserTickets(customer).success(function (tickets) {

            tickets.forEach(function (ticket) {
                ticket.tipo_chiamata = ticketsFactory.getTypeCall(ticket.tipo_chiamata);
            });

            customer.lastTickets = tickets;
        });*/
        usersFactory.getLastTrips(customer).success(function (trips) {
            customer.lastTrips = trips.data;
        });
        $scope.customer = customer;
    };

    function searchCar(value, unit) {
        
        var cars,
            car;

        if (value !== undefined) {
            $scope.accordionStatus.hideCustomerSelect = true;

            $scope.loadCars().then(function () {
                cars = $scope.cars.filter(function (car) {
                    if (unit) {
                        return parseInt(car.label,10)===parseInt(value, 10);
                    }

                    return car.plate.toUpperCase() === value.toUpperCase();
                });

                if (cars && cars.length === 1) {
                    car = cars[0];
                }

                if (car) {
                    clearSearch();
                    searchByCar = true;
                    searchByCustomer = false;
                    $scope.accordionStatus.showNoResult = false;
                    $scope.accordionStatus.hideRequest = false;
                    $scope.accordionStatus.researchOpen = false;

                    $scope.onCarSelection(car);

                    $scope.accordionStatus.hideCarData = false;
                    carsFactory.getTrip(car).success(function (trip) {
                        $scope.trip = trip.trip;
                        $scope.accordionStatus.hideTripData = false;
                        $scope.onCustomerSelection(trip.data[0].customer);
                        $scope.accordionStatus.hideCustomerData = true;
                    }).error(function () {
                        $scope.accordionStatus.hideTripData = true;
                        $scope.accordionStatus.hideCustomerData = true;
                    });
                    carsFactory.getLastClosedTrip(car).success(function (trip) {
                        $scope.lastTrip = trip.data[0].trip;
                        $scope.lastTripUser = trip.data[0].customer;
                        $scope.accordionStatus.hideLastTripData = false;
                    }).error(function () {
                        $scope.accordionStatus.hideLastTripData = true;
                    });
                } else {

                    $scope.accordionStatus.showNoResult = true;

                    if (unit) {
                        $scope.noResult = 'un altro n° unità';
                    } else {
                        $scope.noResult = 'un\'altra targa';
                    }

                    $scope.zoomOut();
                    $scope.accordionStatus.hideCarData = true;
                    $scope.accordionStatus.hideTripData = true;
                    $scope.accordionStatus.hideCustomerData = true;
                    $scope.accordionStatus.hideLastTripData = true;
                    $scope.accordionStatus.hideRequest = true;
                }
            });
        }
    }

    $scope.plateChange = function (value, unit) {
                searchCar(value, unit);
    };

    function clearSearch() {
        $scope.search.plate = '';
        $scope.search.unit = '';
        $scope.search.mobile = '';
        $scope.search.name = '';
        $scope.search.surname = '';
        $scope.search.card = '';
    }

    $scope.userChange = function (phone, name, surname, card) {
        var customers;
        usersFactory.getUsers(phone, name, surname, card).then(function (response) {
            customers = response.data.data;
            if (customers.length === 1) {
                $scope.selectCustomer(customers[0]);
                $scope.accordionStatus.showNoResultByCustomer = false;
                clearSearch();
            } else if (customers.length >= 1) {
                $scope.customers = customers;
                $scope.accordionStatus.hideCustomerSelect = false;
                $scope.accordionStatus.hideCarData = true;
                $scope.accordionStatus.hideTripData = true;
                $scope.accordionStatus.hideCustomerData = true;
                $scope.accordionStatus.hideLastTripData = true;
                $scope.accordionStatus.hideRequest = true;
                $scope.accordionStatus.showNoResultByCustomer = false;
                $scope.zoomOut();
                clearSearch();
            } else {
                $scope.accordionStatus.showNoResultByCustomer = true;
                $scope.customers = [];
                $scope.accordionStatus.hideCustomerSelect = true;
                $scope.accordionStatus.hideCarData = true;
                $scope.accordionStatus.hideTripData = true;
                $scope.accordionStatus.hideCustomerData = true;
                $scope.accordionStatus.hideLastTripData = true;
                $scope.accordionStatus.hideRequest = true;
                $scope.zoomOut();
            }
        });
    };

    $scope.selectCustomer = function (customer) {
        $scope.onCustomerSelection(customer);
        $scope.accordionStatus.researchOpen = false;
        $scope.accordionStatus.hideCustomerSelect = true;
        $scope.accordionStatus.hideCustomerData = false;
        $scope.accordionStatus.hideRequest = false;
        searchByCar = false;
        searchByCustomer = true;
                usersFactory.getTrip(customer).success(function (trip) {
                $scope.loadCars().then(function () {
                $scope.trip = trip.trip;
                $scope.accordionStatus.hideTripData = false;
                console.log(trip.data[0].car);
                $scope.onCarSelection(trip.data[0].car);
                $scope.accordionStatus.hideCarData = false;
                carsFactory.getLastClosedTrip(trip.data[0].car).success(function (trip) {
                    $scope.lastTrip = trip.data[0].trip;
                    $scope.lastTripUser = trip.data[0].customer;
                    $scope.accordionStatus.hideLastTripData = false;
                }).error(function () {
                    $scope.accordionStatus.hideLastTripData = true;
                });
            });
        }).error(function () {
            $scope.accordionStatus.hideTripData = true;
            $scope.accordionStatus.hideCarData = true;
            $scope.accordionStatus.hideLastTripData = true;
            $scope.zoomOut();
        });
    };

    $scope.contact = {};
    $scope.submitRequest = function () {

        var request = {
            'tipo_chiamata': $scope.contact.callType,
            'azione_eseguita': $scope.contact.actionDone,
            'descrizione': $scope.contact.description
        };

        if ($scope.trip !== undefined) {
            request.id_corsa = $scope.trip.id;
        }
        if ($scope.car !== undefined && searchByCar === true) {
            request.targa_veicolo = $scope.car.vettura_targa;
        }
        if ($scope.customer !== undefined && searchByCustomer === true) {
            request.id_cliente = $scope.customer.id;
        }
        ticketsFactory.submitRequest(request).success(function () {

            $scope.accordionStatus.showSuccessMessageTicket = true;
            $scope.accordionStatus.showFormTicket = false;

            $scope.contact.callType = "";
            $scope.contact.description = "";
            $scope.contact.actionDone = "";

            $timeout(function () {
                $scope.accordionStatus.showSuccessMessageTicket = false;
                $scope.accordionStatus.showFormTicket = true;
                $scope.accordionStatus.requestOpen = false;
            }, 3000);

        }).error(function () {
        });
    };

    $scope.mapOptions = {
        center: {
            latitude: 45.46,
            longitude: 9.25
        },
        zoom: 12,
        doCluster: true
    };

    $scope.clusterOptions = {
        styles: [
            {
                url: '/assets-modules/call-center/images/marker-cluster.png',
                width: 35,
                height: 35,
                textColor: '#ffffff',
                textSize: 12,
                name: 'uno'
            },
        ],
        maxZoom: 17
    };

    $scope.mapEvents = {
        zoom_changed: function (map) {

            var zoom = map.getZoom();
            var maxLevelZoom = 20;

            if (zoom >= maxLevelZoom) {
                $scope.mapOptions.doCluster = false;
            } else {
                $scope.mapOptions.doCluster = true;
            }
        }
    };

    uiGmapIsReady.promise().then(function (maps) {
        var reservationContent = document.createElement("div"),
            infoBoxOptions = {
                alignBottom: false,
                disableAutoPan: false,
                pixelOffset: new google.maps.Size(-300, 0),
                infoBoxClearance: new google.maps.Size(1, 1),
                isHidden: false,
                pane: "floatPane",
                enableEventPropagation: true,
                boxStyle: { 
                  width: "600px"
                 },
                closeBoxMargin: "10px 2px 2px 2px",
               /* closeBoxURL: "http://www.google.com/intl/en_us/mapfiles/close.gif"*/
            };

        infoBox = new InfoBox();

        $scope.markerEvents = {
            click: function (marker, event, car) {
                searchCar(car.plate, false);

                var content = [
"<div class=\"module-pop-up\" style=\"margin:0 auto;\">",
"<a id=\"btn-close\" class=\"btn-close\"></a>",
"<div class=\"module-pop-up-content\"><div class=\"block-car-data bg-ct4 clearfix\"><div class=\"block-heading text-align-center\"><span class=\"block-info\">Auto scelta</span><h1 id=\"licence-plate\" class=\"block-title\">{{plate}}</h1></div>",
"<div class=\"block-content clearfix\">",
"<div id=\"left-column\" class=\"block-column bw-f w-2-4\">",
"<div class=\"block-image\"><img src=\"assets-modules/call-center/images/car.png\" alt=\"\"></div>",
"<div class=\"block-label-status\"><span class=\"block-info\">Stato interni</span><div class=\"block-bar\"><div id=\"int_cleanliness\" class=\"block-bar-value\">",
"<div class=\"block-bar\"><div id=\"int_cleanliness\" class=\"block-bar-value {{int0}}\"></div></div></div></div></div>",
"<div class=\"block-label-status\"><span class=\"block-info\">Stato esterni</span><div class=\"block-bar\"><div id=\"ext_cleanliness\" class=\"block-bar-value\">",
"<div id=\"ext_cleanliness\" class=\"block-bar-value {{est0}}\"></div></div></div></div></div>",
"<div id=\"right-column\" class=\"block-column last bw-f w-2-4\">",
"<div class=\"block-wrapper-car-data-info bg-ct4\">",
"<div id=\"block-right-top\" class=\"block-car-data-info\"><span class=\"block-data-name\"><i class=\"fa fa-map-marker\"></i> Dove si trova</span><span id=\"location\" class=\"block-data-value\">{{latitude}}, {{longitude}} <reverse-geocode lat=\"latitude\" lng=\"longitude\"></reverse-geocode></span></div>",
"<div class=\"block-car-data-info\"><span id=\"block-right-bottom-title\" class=\"block-data-name\"><i id=\"circle-icon\" class=\"fa fa-sun-o\"></i>Autonomia</span><span id=\"block-right-bottom-text\" class=\"block-data-value\">{{battery}} % batteria</span></div>",
"<div id=\"btn-reserve\" class=\"block-wrapper-btn\" style=\"display:none\"><a id=\"reserve-text\" href=\"/login\" class=\"btn-link ct3\"><i class=\"fa fa-circle-o-notch fa-spin\"></i></a></div>",
"<div id=\"step2-buttons\" class=\"block-wrapper-btn\" style=\"display:none\"><button id=\"btn-back\" class=\"reset pull-left\"><i class=\"fa fa-angle-left\"></i> Annulla</button><button id=\"btn-confirm\" class=\"pull-right ct2\">Conferma <i class=\"fa fa-angle-right\"></i></button></div><div>",
"<button id=\"btn-done\" class=\"ct2\" style=\"display:none; margin:0 auto;\">Chiudi <i class=\"fa fa-check\"></i></button>",
"</div></div></div></div></div></div></div>"
                ].join("");

                switch(car.intCleanliness) {
                    case 'clean':car.int0 = 'w100';break;
                    case 'average':car.int0 = 'w75';break;
                    case 'dirty':car.int0 = 'w25';break;
                }
                switch(car.extCleanliness) {
                    case 'clean':car.est0 = 'w100';break;
                    case 'average':car.est0 = 'w75';break;
                    case 'dirty':car.est0 = 'w25';break;
                }         

                reservationContent.innerHTML = $interpolate(content)(car);

                infoBoxOptions.content = reservationContent;
                infoBox.close();
                infoBox.setOptions(infoBoxOptions);
                infoBox.open(maps[0].map, marker);
            }
        };
    });

    /*uiGmapGoogleMapApi.then(function (maps) {
        
    });*/
});