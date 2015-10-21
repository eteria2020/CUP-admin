'use strict';

angular.module('SharengoCsApp').controller('SharengoCsController', function (
    $scope,
    $interpolate,
    carsFactory,
    usersFactory,
    poisFactory,
    fleetsFactory,
    //mapFactory,
    ticketsFactory,
    uiGmapGoogleMapApi,
    uiGmapIsReady,
    $timeout
) {
    var infoBox;
    //necessary to avoid errors with network delay
    $scope.cars = [];
    $scope.pois = [];
    $scope.fleets = [];
    $scope.defaultFleet = false;
    $scope.mapLoader = true;

    var searchByCar = false;
    var searchByCustomer = false;
    var reservationContent,infoBoxOptions,infoBox,mainMaps;
    $scope.poisVisible = true;

    var markerColors = {
        green: "#43A34C",
        red: "#D90000",
        yellow: "#FFC926",
        brown: "#BF8F00",
        orange: "#EC9416",
        white: "#FFFFFF",
        purple: "#7030A0",
        azzure: "#0338FF",
        black: "#000000"
    }

    $scope.accordionStatus = {
        researchOpen: true,
        hideCustomerData: true,
        customerDataOpen: true,
        hideCarsSelect: true,
        carsSelectOpen: true,
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

    $scope.mapOptions = {
        center: {
            latitude: 44.5563793,
            longitude: 11.3180998
        },
        zoom: 7,
        doCluster: true
    };

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
        city: $scope.defaultFleet.name,
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
            var markerIcon = {
                path: "m12 0.17197c-2.1153-0.02006-4.1832 1.1007-5.353 2.8575h-5.8194c-0.87431 0.03867-0.62803 1.0025-0.66208 1.588v7.2926c0.12395 0.818 1.0743 0.54221 1.6589 0.59125h6.5752c1.8937 3.0788 3.2217 6.5719 3.5598 10.185 0.17907-0.11908 0.20248-1.7168 0.43776-2.366 0.63275-2.7555 1.6947-5.4243 3.2034-7.8189h7.572c0.87431-0.03867 0.62803-1.0025 0.66208-1.588v-7.2926c-0.12395-0.818-1.0743-0.54221-1.6589-0.59125h-4.8226c-1.174-1.7594-3.233-2.876-5.353-2.8578z",
                fillColor: '#43A34C',
                fillOpacity: 1,
                strokeWeight: 1,
                strokeColor:'#000000',
                strokeOpacity:1,
                scale: 1.4,
                anchor: new google.maps.Point(11,25)
            };
            cars.forEach(function (car) {
                car.id = car.plate; //Date.parse(car.vettura_id.replace(' ', 'T'));
               /* car.latitude = car.vettura_lat;
                car.longitude = car.vettura_lon;*/
                car.options = [];
                var carCharging = '';

                carCharging = '-charging';
                car.options.labelClass='marker_labels';
                //car.options.labelAnchor='12 32';
                if(car.charging){
                    car.options.labelContent=car.battery+'% <i class="fa fa-plug"></i>';
                }else{
                    car.options.labelContent=car.battery+'%';
                }
                
                car.carIcon = JSON.parse(JSON.stringify(markerIcon)); // fast clone
                car.iconSelected = car.carIcon;

                if(car.sinceLastTrip && car.sinceLastTrip>1440){
                    car.carIcon['fillColor'] = markerColors['azzure'];
                    car.iconSelected = car.carIcon;
                }else{
                    car.carIcon['fillColor'] = markerColors['green'];
                    car.iconSelected = car.carIcon;
                }
                if(car.status!='operative'){
                    if(car.status=='maintenance'){
                        car.carIcon['fillColor'] = markerColors['red'];
                        car.iconSelected = car.carIcon;
                    }else{
                        car.carIcon['fillColor'] = markerColors['orange'];
                        car.iconSelected = car.carIcon;
                    }
                }else if(car.busy){
                    car.carIcon['fillColor'] = markerColors['yellow'];
                    car.iconSelected = car.carIcon;
                    
                }else if(car.reservation){
                    car.carIcon['fillColor'] = markerColors['black'];
                    car.iconSelected = car.carIcon;
                }
            });
            $scope.cars = cars;
            $scope.mapLoader = false;
        });
    };

    $scope.loadPois = function () {
        $scope.mapLoader = true;
        return poisFactory.getPois().success(function (pois) {
            pois = pois.data;
            pois.forEach(function (poi) {
                poi.options = [];
                poi.latitude = poi.lat;
                poi.longitude = poi.lon;
                poi.options.labelClass='pois_labels';
                poi.options.labelContent = '<i class="fa fa-plug"></i>';
                poi.poiIcon = '/assets-modules/call-center/images/blank.png';
                poi.options.visible = $scope.poisVisible;
            });
            $scope.pois = pois;
            $scope.mapLoader = false;
        });
    };    
    $scope.loadFleets = function(){
        fleetsFactory.getFleets().success(function (fleets) {
            $scope.fleets = fleets.data;
            var defaultFleet = fleets.data.filter(function(fleet) {
                return fleet.isDefault;
            });
            $scope.defaultFleet = defaultFleet.length > 0 ? defaultFleet.shift() : false;
            $scope.zoomOut();
        });
    };

    $scope.changeFleet = function(){
        $scope.zoomOut();
    };

    $scope.loadFleets();
    $scope.loadCars();
    $scope.loadPois();

    $scope.togglePois = function(){

    }

    $scope.zoom = function (center, zoom) {
        $scope.mapOptions.center = {
            latitude: center.latitude,
            longitude: center.longitude
        };
        $scope.mapOptions.zoom = zoom;
    };

    $scope.onCarSelection = function (car,zoom) {
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
        if(zoom){
          $scope.zoom({
              latitude: car.latitude,
              longitude: parseFloat(car.longitude) + 0.00010
          }, 21);
        }
        $scope.searchAddress.city = car.fleet.name;
        $scope.defaultFleet = car.fleet;
        //$scope.changeFleet();

    };

    $scope.zoomOut = function () {
        if(!$scope.defaultFleet){
            $scope.zoom(
                {
                    latitude: $scope.mapOptions.center.latitude,
                    longitude: $scope.mapOptions.center.longitude
                }, 
                $scope.mapOptions.zoom
            );
        }else{
            $scope.zoom(
                {
                    latitude: $scope.defaultFleet.latitude,
                    longitude: $scope.defaultFleet.longitude
                }, 
                $scope.defaultFleet.zoomLevel
            );
            $scope.searchAddress.city = $scope.defaultFleet.name;
        }

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

    function searchCar(value, unit, zoom) {
        var cars,
            car;

        if (value !== undefined) {
            $scope.accordionStatus.hideCustomerSelect = true;

            $scope.loadCars().then(function () {
                cars = $scope.cars.filter(function (car) {
                    if (unit) {
                        return parseInt(car.label,10)===parseInt(value, 10);
                    }

                    return car.plate.toUpperCase().search(value.toUpperCase())!==-1;
                });

                $scope.carsFound = cars;

                if (cars && cars.length === 1) {
                    car = cars[0];
                    $scope.accordionStatus.hideCarsSelect = true;
                }else if (cars && cars.length > 1){
                    $scope.accordionStatus.hideCarsSelect = false;
                    return true;
                }

                if (car) {
                    clearSearch();
                    searchByCar = true;
                    searchByCustomer = false;
                    $scope.accordionStatus.showNoResult = false;
                    $scope.accordionStatus.hideRequest = false;
                    $scope.accordionStatus.researchOpen = false;

                    $scope.onCarSelection(car,zoom);

                    $scope.accordionStatus.hideCarData = false;

                    carsFactory.getTrip(car).success(function (trip) {
                        if(typeof trip.data[0] !== 'undefined'){
                            $scope.onCustomerSelection(trip.data[0].customer);
                            $scope.accordionStatus.hideTripData = false;
                            $scope.accordionStatus.hideCustomerData = true;
                        }else{
                            $scope.accordionStatus.hideTripData = true;
                            $scope.accordionStatus.hideCustomerData = true;
                        }
                        $scope.trip = trip.trip;
                    }).error(function () {
                        $scope.accordionStatus.hideTripData = true;
                        $scope.accordionStatus.hideCustomerData = true;
                    });
                    carsFactory.getLastClosedTrip(car).success(function (trip) {
                        if(typeof trip.data[0] !== 'undefined') {
                            $scope.lastTrip = trip.data[0];
                            $scope.lastTripUser = trip.data[0].customer;
                            $scope.accordionStatus.hideLastTripData = false;
                        }else{
                            $scope.accordionStatus.hideLastTripData = true;
                        }
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
        searchCar(value, unit, true);
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
                $scope.onCarSelection(trip.data[0].car,true);
                $scope.accordionStatus.hideCarData = false;
                carsFactory.getLastClosedTrip(trip.data[0].car).success(function (trip) {
                    $scope.lastTrip = trip.data[0];
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
        mainMaps = maps;
        reservationContent = document.createElement("div");
        infoBoxOptions = {
                alignBottom: false,
                disableAutoPan: false,
                pixelOffset: new google.maps.Size(-250, 0),
                infoBoxClearance: new google.maps.Size(1, 1),
                isHidden: false,
                pane: "floatPane",
                enableEventPropagation: true,
                boxStyle: {
                    width: "500px"
                },
                closeBoxMargin: "10px 2px 2px 2px",
               /* closeBoxURL: "http://www.google.com/intl/en_us/mapfiles/close.gif"*/
            };

        infoBox = new InfoBox();
    });
    $scope.markerEvents = {
            click: function (marker, event, car) {
                searchCar(car.plate, false,false);

                var content = [
"<div class=\"module-pop-up\" style=\"margin:0 auto;\">",
"<a id=\"btn-close\" class=\"btn-close\"></a>",
"<div class=\"module-pop-up-content\"><div class=\"block-car-data bg-ct4 clearfix\"><div class=\"block-heading text-align-center\"><span class=\"block-info\">Auto scelta</span><h1 id=\"licence-plate\" class=\"block-title\">{{plate}}</h1></div>",
"<div class=\"block-content clearfix\">",
"<div id=\"left-column\" class=\"block-column bw-f w-2-4\">",
"<div class=\"block-image\"><img src=\"assets-modules/call-center/images/car.png\" alt=\"\"></div>",
"<div id=\"left-info\"><div class=\"block-label-status\"><span class=\"block-info\">Stato interni</span><div class=\"block-bar\"><div id=\"int_cleanliness\" class=\"block-bar-value\">",
"<div class=\"block-bar\"><div id=\"int_cleanliness\" class=\"block-bar-value {{int0}}\"></div></div></div></div></div>",
"<div class=\"block-label-status\"><span class=\"block-info\">Stato esterni</span><div class=\"block-bar\"><div id=\"ext_cleanliness\" class=\"block-bar-value\">",
"<div id=\"ext_cleanliness\" class=\"block-bar-value {{est0}}\"></div></div></div></div></div></div>",
"<div id=\"right-column\" class=\"block-column last bw-f w-2-4\">",
"<div class=\"block-wrapper-car-data-info bg-ct4\">",
"<div id=\"block-right-top\" class=\"block-car-data-info\"><span class=\"block-data-name\"><i class=\"fa fa-map-marker\"></i> Dove si trova</span><span id=\"location\" class=\"block-data-value\">{{latitude}}, {{longitude}} <reverse-geocode lat=\"latitude\" lng=\"longitude\"></reverse-geocode></span></div>",
"<div class=\"block-car-data-info\"><span id=\"block-right-bottom-title\" class=\"block-data-name\"><i id=\"circle-icon\" class=\"fa fa-sun-o\"></i> Autonomia</span><span id=\"block-right-bottom-text\" class=\"block-data-value\">{{battery}} % batteria</span></div>",
"<div class=\"block-car-data-info\"><span id=\"block-right-bottom-title\" class=\"block-data-name\"><i id=\"circle-icon\" class=\"fa fa-clock-o\"></i> Ultimo contatto</span><span id=\"block-right-bottom-text\" class=\"block-data-value\">{{lastContact.date | dateSharengoFormat}}</span></div>",
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
                infoBox.open(mainMaps[0].map, marker);
            }
        };
    /*uiGmapGoogleMapApi.then(function (maps) {

    });*/
});
