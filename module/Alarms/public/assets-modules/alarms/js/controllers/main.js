'use strict';

angular.module('SharengoCsApp').controller('SharengoCsController', function (
    $scope,
    $filter,
    $interpolate,
    carsFactory,
    usersFactory,
    poisFactory,
    fleetsFactory,
    //mapFactory,
    ticketsFactory,
    uiGmapGoogleMapApi,
    uiGmapIsReady,
    $timeout,
    gettextCatalog
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

    $scope.markerColors = {
        green: "#43A34C",
        red: "#D90000",
        yellow: "#FFC926",
        brown: "#BF8F00",
        orange: "#EC9416",
        white: "#FFFFFF",
        purple: "#7030A0",
        azzure: "#0338FF",
        black: "#000000"
    };

    $scope.markerFilters = {
        libere: true,
        prenotate: true,
        h24: true,
        manut: true,
        inuso: true,
        nobatt: true,
        no3g: true,
        nogps: true,
        sporche: true,
        ricarica: true
    }

    $scope.markerCounters = {
        libere: 0,
        prenotate: 0,
        h24: 0,
        manut: 0,
        inuso: 0,
        nobatt: 0,
        no3g:0,
        nogps:0,
        sporche: 0,
        ricarica: 0
    };

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
    $scope.initial = {
        mapCenter: {
            latitude: 44.5563793,
            longitude: 11.3180998
        },
        mapZoom: 7
    }
    $scope.mapOptions = {
        center: $scope.initial.mapCenter,
        zoom: $scope.initial.mapZoom,
        doCluster: true,
        control: {}
    };
    function getCookie(name) {
      var value = "; " + document.cookie;
      var parts = value.split("; " + name + "=");
      if (parts.length == 2) return parts.pop().split(";").shift();
    }
    gettextCatalog.setCurrentLanguage(getCookie('lang'));
    //gettextCatalog.debug = true;

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
    };

    $scope.searchAddress = {
        address: '',
        number: '',
        city: $scope.defaultFleet.name,
        search: function () {
            $scope.mapLoader = true;
            if (!geocoder) {
                geocoder = new google.maps.Geocoder();
            }
            var address = $scope.searchAddress.number+', '+$scope.searchAddress.address+', '+$scope.searchAddress.city;
            geocoder.geocode( { 'address': address}, function(results, status) {
                if (status == google.maps.GeocoderStatus.OK) {
                    $scope.mapOptions.center = {
                        latitude: results[0].geometry.location.lat(),
                        longitude: results[0].geometry.location.lng() + 0.00010
                    };
                    $scope.mapOptions.zoom = 14;
                    addMarker($scope.mapOptions.center.latitude, $scope.mapOptions.center.longitude);
                } else {
                    alert(gettextCatalog.getString('Geocode was not successful for the following reason: ') + status);
                }
                $scope.mapLoader = false;
                $scope.$digest();
            });
        }
    };

    function resetMarkerCounters(){
        for(var counter in $scope.markerCounters){
            if($scope.markerCounters.hasOwnProperty(counter)){
                $scope.markerCounters[counter] = 0;
            }
        };
    }

    function resetMarkerFilters(){
        for(var filter in $scope.markerFilters){
            if($scope.markerFilters.hasOwnProperty(filter)){
                $scope.markerFilters[filter] = true;
            }
        };
    }

    resetMarkerCounters();
    resetMarkerFilters();

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
                anchor: new google.maps.Point(12,25)
            };
            resetMarkerCounters();

            var now = new Date().getTime();
            cars.forEach(function (car) {
                car.id = car.plate; //Date.parse(car.vettura_id.replace(' ', 'T'));
               /* car.latitude = car.vettura_lat;
                car.longitude = car.vettura_lon;*/
                car.options = {};
                var carCharging = '';

                carCharging = '-charging';
                car.options.labelClass='marker_labels';
                //car.options.labelAnchor='12 32';

                car.options.labelContent=car.battery+'%';
                car.options.charging = false;

                car.carIcon = JSON.parse(JSON.stringify(markerIcon)); // fast clone
                car.iconSelected = car.carIcon;

                var date = car.lastContact?new Date(car.lastContact.date).getTime()/1000:null;
                var now  = new Date().getTime()/1000;

                if(car.status=='maintenance'){
                    car.carIcon['fillColor'] = $scope.markerColors['red'];
                    car.options.group = 'manut';
                    car.iconSelected = car.carIcon;
                }else if(car.reservation){
                    car.carIcon['fillColor'] = $scope.markerColors['black'];
                    car.options.group = 'prenotate';
                    car.iconSelected = car.carIcon;
                }else if(car.busy){
                    car.carIcon['fillColor'] = $scope.markerColors['yellow'];
                    car.options.group = 'inuso';
                    car.iconSelected = car.carIcon;
                }else if(car.status!='operative'){
                    if(date === null || now>(date+3600)){
                        car.options.labelContent=car.battery+'% <span class="txt-label">' + gettextCatalog.getString('3G') + '</span>';
                        car.carIcon['fillColor'] = $scope.markerColors['brown'];
                        car.options.group = 'no3g';
                        car.iconSelected = car.carIcon;
                    }else if(car.gps_ok == false){
                        car.options.labelContent=car.battery+'% <span class="txt-label">' + gettextCatalog.getString('GPS') + '</span>';
                        car.carIcon['fillColor'] = $scope.markerColors['brown'];
                        car.options.group = 'nogps';
                        car.iconSelected = car.carIcon;
                    }else if(car.battery<20){
                        car.options.labelContent=car.battery+'% <i class="fa fa-battery-half"></i>';
                        car.carIcon['fillColor'] = $scope.markerColors['brown'];
                        car.options.group = 'nobatt';
                        car.iconSelected = car.carIcon;
                    }else{
                        if(car.charging){
                            car.options.group = 'ricarica';
                            car.iconSelected = car.carIcon;
                            car.carIcon['fillColor'] = $scope.markerColors['brown'];
                            car.carIcon['strokeColor'] = $scope.markerColors['azzure'];
                        } else{
                            console.log('nooper',car.plate,car);
                        }
                    }
                }else {
                    if(car.sinceLastTrip && car.sinceLastTrip>1440){
                        car.carIcon['fillColor'] = $scope.markerColors['azzure'];
                        car.options.group = 'h24';
                        car.iconSelected = car.carIcon;
                    }else if(car.extCleanliness=='dirty' || car.intCleanliness=='dirty'){
                        car.carIcon['fillColor'] = $scope.markerColors['purple'];
                        car.options.group = 'sporche';
                        car.iconSelected = car.carIcon;
                    } else{
                        car.carIcon['fillColor'] = $scope.markerColors['green'];
                        car.options.group = 'libere';
                        car.iconSelected = car.carIcon;
                    }
                }

                if(car.charging){
                    car.options.labelContent += '<i class="fa fa-plug"></i>';
                    car.options.charging = true;
                }

                $scope.markerCounters[car.options.group] +=1;
            });
            $scope.cars = cars;
            $scope.mapLoader = false;


            $scope.$watch("markerFilters", function(filt){
                cars.forEach(function (carl) {
                    var toSet = false;
                    if ($scope.markerFilters[carl.options.group]) {
                        toSet = true;
                    }
                /*    if ($scope.markerFilters['ricarica'] && carl.options.charging) {
                        toSet = true;
                    }*/
                    carl.options.visible = toSet;

                });
                $scope.mapOptions.control.refresh();
            },true);

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
            var firstOption = {
                code: "XX",
                id: 0,
                name: "Tutti",
                zoomLevel: $scope.initial.mapZoom,
                latitude: $scope.initial.mapCenter.latitude,
                longitude: $scope.initial.mapCenter.longitude,
                isDefalt: false
            };
            $scope.fleets.push(firstOption);
            $scope.fleets = $scope.fleets.concat(fleets.data);

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

        $scope.car = car;
        if(zoom){
          $scope.zoom({
              latitude: car.latitude,
              longitude: parseFloat(car.longitude) + 0.00010
          }, 21);
        }
        $scope.searchAddress.city = car.fleet.name;
        $scope.defaultFleet = car.fleet;
    };

    $scope.zoomOut = function () {
        if(!$scope.defaultFleet){
            $scope.zoom(
                {
                    latitude: $scope.defaultFleet.latitude,
                    longitude: $scope.defaultFleet.longitude
                },
                $scope.defaultFleet.mapZoom
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
        usersFactory.getLastTrips(customer).success(function (trips) {
            customer.lastTrips = trips.data;
        });
        $scope.customer = customer;
    };

    function searchCar(value, unit, zoom) {
        var cars,
            car;

        resetMarkerFilters();

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
                        $scope.noResult = gettextCatalog.getString('un altro n° unità');
                    } else {
                        $scope.noResult = gettextCatalog.getString('un\'altra targa');
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
                $scope.accordionStatus.hideCarData = false;
                if(typeof trip.data[0] !== 'undefined'){
                    $scope.onCarSelection(trip.data[0].car,true);
                    carsFactory.getLastClosedTrip(trip.data[0].car).success(function (trip) {
                        $scope.lastTrip = trip.data[0];
                        $scope.lastTripUser = trip.data[0].customer;
                        $scope.accordionStatus.hideLastTripData = false;
                    }).error(function () {
                        $scope.accordionStatus.hideLastTripData = true;
                    });
                }else{
                    $scope.accordionStatus.hideLastTripData = false;
                }
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

    $scope.updateCounters = function(){
        if(typeof mainMaps !== 'undefined'){
            var mapBounds = mainMaps[0].map.getBounds();
            resetMarkerCounters();

            $scope.cars.forEach(function (car) {
                if(mapBounds.contains(new google.maps.LatLng(car.latitude, car.longitude))){
                    $scope.markerCounters[car.options.group] += 1;
                 //   if(car.options.charging) $scope.markerCounters['ricarica'] += 1;
                }
            });
        }
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
        },
        idle: function(map){
            $scope.updateCounters();
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
        $scope.updateCounters();
    });
    $scope.markerEvents = {
            click: function (marker, event, car) {
                searchCar(car.plate, false,false);

                var content = [
"<div class=\"module-pop-up\" style=\"margin:0 auto;\">",
"<a id=\"btn-close\" class=\"btn-close\"></a>",
"<div class=\"module-pop-up-content\"><div class=\"block-car-data bg-ct4 clearfix\"><div class=\"block-heading text-align-center\"><span class=\"block-info\">" + gettextCatalog.getString('Auto scelta') + "</span><h1 id=\"licence-plate\" class=\"block-title\">{{plate}}</h1></div>",
"<div class=\"block-content clearfix\">",
"<div id=\"left-column\" class=\"block-column bw-f w-2-4\">",
"<div class=\"block-image\"><img src=\"assets-modules/call-center/images/car.png\" alt=\"\"></div>",
"<div id=\"left-info\"><div class=\"block-label-status\"><span class=\"block-info\">" + gettextCatalog.getString('Stato interni') + "</span><div class=\"block-bar\"><div id=\"int_cleanliness\" class=\"block-bar-value\">",
"<div class=\"block-bar\"><div id=\"int_cleanliness\" class=\"block-bar-value {{int0}}\"></div></div></div></div></div>",
"<div class=\"block-label-status\"><span class=\"block-info\">" + gettextCatalog.getString('Stato esterni') + "</span><div class=\"block-bar\"><div id=\"ext_cleanliness\" class=\"block-bar-value\">",
"<div id=\"ext_cleanliness\" class=\"block-bar-value {{est0}}\"></div></div></div></div></div></div>",
"<div id=\"right-column\" class=\"block-column last bw-f w-2-4\">",
"<div class=\"block-wrapper-car-data-info bg-ct4\">",
"<div id=\"block-right-top\" class=\"block-car-data-info\"><span class=\"block-data-name\"><i class=\"fa fa-map-marker\"></i> " + gettextCatalog.getString('Dove si trova') + "</span><span id=\"location\" class=\"block-data-value\">{{latitude}}, {{longitude}} <reverse-geocode lat=\"latitude\" lng=\"longitude\"></reverse-geocode></span></div>",
"<div class=\"block-car-data-info\"><span id=\"block-right-bottom-title\" class=\"block-data-name\"><i id=\"circle-icon\" class=\"fa fa-sun-o\"></i> " + gettextCatalog.getString('Autonomia') + "</span><span id=\"block-right-bottom-text\" class=\"block-data-value\">{{battery}} % " + gettextCatalog.getString('batteria') + "</span></div>",
"<div class=\"block-car-data-info\"><span id=\"block-right-bottom-title\" class=\"block-data-name\"><i id=\"circle-icon\" class=\"fa fa-clock-o\"></i> " + gettextCatalog.getString('Ultimo contatto') + "</span><span id=\"block-right-bottom-text\" class=\"block-data-value\">{{lastContact.date | dateSharengoFormat}}</span></div>",
"<div id=\"btn-reserve\" class=\"block-wrapper-btn\" style=\"display:none\"><a id=\"reserve-text\" href=\"/login\" class=\"btn-link ct3\"><i class=\"fa fa-circle-o-notch fa-spin\"></i></a></div>",
"<div id=\"step2-buttons\" class=\"block-wrapper-btn\" style=\"display:none\"><button id=\"btn-back\" class=\"reset pull-left\"><i class=\"fa fa-angle-left\"></i> " + gettextCatalog.getString('Annulla') + "</button><button id=\"btn-confirm\" class=\"pull-right ct2\">" + gettextCatalog.getString('Conferma') + " <i class=\"fa fa-angle-right\"></i></button></div><div>",
"<button id=\"btn-done\" class=\"ct2\" style=\"display:none; margin:0 auto;\">" + gettextCatalog.getString('Chiudi') + " <i class=\"fa fa-check\"></i></button>",
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
