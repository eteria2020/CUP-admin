var img_DEFAULT = "/img/DEFAULT.PNG";
var img_SW_BOOT = "/img/SW_BOOT.PNG";
var img_RFID = "/img/RFID.PNG";
var img_BATTERY = "/img/BATTERY.PNG";
var img_SPEED = "/img/SPEED.PNG";
var img_AREA = "/img/AREA.PNG";
var img_CHARGING = "/img/CHARGING.PNG";
var img_ENGINE = "/img/ENGINE.PNG";
var img_SOS = "/img/SOS.PNG";
var img_PARK = "/img/PARK.PNG";
var img_CMD = "/img/CMD.PNG";
var img_CLEANLINESS = "/img/CLEANLINESS.PNG";
var img_OBCFAIL = "/img/OBCFAIL.PNG";
var img_OBCOK = "/img/OBCOK.PNG";
var img_KEY = "/img/KEY.PNG";
var img_READY = "/img/READY.PNG";
var img_GEAR = "/img/GEAR.PNG";
var img_DIAG = "/img/DIAG.PNG";
var img_CARPLATE = "/img/CARPLATE.PNG";
var img_3G = "/img/3G.PNG";
var img_MAINTENANCE = "/img/MAINTENANCE.PNG";
var img_OUTOFORDER = "/img/OUTOFORDER.PNG";
var img_SELFCLOSE = "/img/SELFCLOSE.PNG";
var img_DEVICEINFO = "/img/DEVICEINFO.PNG";
var img_SHUTDOWN = "/img/SHUTDOWN.PNG";
var img_LEASE = "/img/LEASE.PNG";
var img_SOC = "/img/SOC.PNG";
//var img_AREA = "/img/.PNG";
var img_MENU_CLICK = "/img/MENU_CLICK.PNG";
var img_TRIP = "/img/car-icon.png"

var lon_default = 12.48;
var lat_defautl = 41.88;
var zoom_defaul = 5;

var zoom = 14;
var media_lon = 0;
var media_lat = 0;
var popup = null;

var set_default_center = true;

var init = function (events, logs) {

    map = new OpenLayers.Map("map", {
        controls: [
            new OpenLayers.Control.Navigation(),
            new OpenLayers.Control.PanZoomBar(),
            new OpenLayers.Control.ScaleLine(),
            new OpenLayers.Control.Permalink('permalink'),
            new OpenLayers.Control.MousePosition(),
            new OpenLayers.Control.Attribution()
        ],
        projection: new OpenLayers.Projection("EPSG:900913"),
        displayProjection: new OpenLayers.Projection("EPSG:4326")
    });

    var mapnik = new OpenLayers.Layer.OSM("OpenStreetMap (Mapnik)");
    map.addLayer(mapnik);

    layerCycleMap = new OpenLayers.Layer.OSM.CycleMap("CycleMap");
    
    var size = new OpenLayers.Size(42, 50);
    var offset = new OpenLayers.Pixel(-(size.w / 2), -size.h);
    
    if (logs.length != 0) {
        logs.forEach(function (log) {
            var icon = setIcon(img_TRIP, size, offset);    
            var markers = new OpenLayers.Layer.Markers("Markers");
            map.addLayer(markers);
            markers.addMarker(new OpenLayers.Marker(lonLatFunction(log['lon'], log['lat']), icon));
            
            markers.events.register("click", markers, function (e) {
                if (popup == null) {
                    popup = createPopup(log, false);
                    map.addPopup(popup);
                } else {
                    destroyPopup();
                    popup = createPopup(log, false);
                    map.addPopup(popup);
                }
            });
            media_lon += log['lon'];
            media_lat += log['lat'];
        });
    }

    if (events.length != 0) {
        set_default_center = false;
        events.forEach(function (event) {
            switch (event['label']) {
                case 'SW_BOOT':
                    var icon = setIcon(img_SW_BOOT, size, offset);
                    break;
                case 'RFID':
                    var icon = setIcon(img_RFID, size, offset);
                    break;
                case 'BATTERY':
                    var icon = setIcon(img_img_BATTERY, size, offset);
                    break;
                case 'SPEED':
                    var icon = setIcon(img_img_SPEED, size, offset);
                    break;
                case 'AREA':
                    var icon = setIcon(img_AREA, size, offset);
                    break;
                case 'CHARGING':
                    var icon = setIcon(img_CHARGING, size, offset);
                    break;
                case 'ENGINE':
                    var icon = setIcon(img_ENGINE, size, offset);
                    break;
                case 'SOS':
                    var icon = setIcon(img_SOS, size, offset);
                    break;
                case 'PARK':
                    var icon = setIcon(img_PARK, size, offset);
                    break;
                case 'CMD':
                    var icon = setIcon(img_CMD, size, offset);
                    break;
                case 'CLEANLINESS':
                    var icon = setIcon(img_CLEANLINESS, size, offset);
                    break;
                case 'OBCFAIL':
                    var icon = setIcon(img_OBCFAIL, size, offset);
                    break;
                case 'OBCOK':
                    var icon = setIcon(img_OBCOK, size, offset);
                    break;
                case 'KEY':
                    var icon = setIcon(img_KEY, size, offset);
                    break;
                case 'READY':
                    var icon = setIcon(img_READY, size, offset);
                    break;
                case 'GEAR':
                    var icon = setIcon(img_GEAR, size, offset);
                    break;
                case 'DIAG':
                    var icon = setIcon(img_DIAG, size, offset);
                    break;
                case 'CARPLATE':
                    var icon = setIcon(img_CARPLATE, size, offset);
                    break;
                case '3G':
                    var icon = setIcon(img_3G, size, offset);
                    break;
                case 'MAINTENANCE':
                    var icon = setIcon(img_MAINTENANCE, size, offset);
                    break;
                case 'OUTOFORDER':
                    var icon = setIcon(img_OUTOFORDER, size, offset);
                    break;
                case 'SELFCLOSE':
                    var icon = setIcon(img_SELFCLOSE, size, offset);
                    break;
                case 'DEVICEINFO':
                    var icon = setIcon(img_DEVICEINFO, size, offset);
                    break;
                case 'SHUTDOWN':
                    var icon = setIcon(img_SHUTDOWN, size, offset);
                    break;
                case 'LEASE':
                    var icon = setIcon(img_LEASE, size, offset);
                    break;
                case 'SOC':
                    var icon = setIcon(img_SOC, size, offset);
                    break;
                    /*
                     case 'AREA':
                     var icon = setIcon(img_AREA, size, offset);
                     break;
                     */
                case 'MENU_CLICK':
                    var icon = setIcon(img_MENU_CLICK, size, offset);
                    break;
                default:
                    var icon = setIcon(img_DEFAULT, size, offset);
                    break;
            }

            //set marker in map with lonlat e img
            var markers = new OpenLayers.Layer.Markers("Markers");
            map.addLayer(markers);
            markers.addMarker(new OpenLayers.Marker(lonLatFunction(event['lon'], event['lat']), icon));

            markers.events.register("click", markers, function (e) {
                if (popup == null) {
                    popup = createPopup(event, true);
                    map.addPopup(popup);
                } else {
                    destroyPopup();
                    popup = createPopup(event, true);
                    map.addPopup(popup);
                }
            });
            media_lon += event['lon'];
            media_lat += event['lat'];
        });
    }

    if(set_default_center)
        map.setCenter(lonLatFunction(lon_default, lat_defautl), zoom_defaul);
    else
        map.setCenter(lonLatFunction(media_lon/(events.length+logs.length), media_lat/(events.length+logs.length)), zoom);
};

var createPopup = function (data, value) {
    if(value){
        return  popup = new OpenLayers.Popup.FramedCloud("chicken",
                lonLatFunction(data['lon'], data['lat']),
                new OpenLayers.Size(200, 1000),
                popupHtmlCodeEvent(data),
                null, true);
    }else{
        return  popup = new OpenLayers.Popup.FramedCloud("chicken",
                lonLatFunction(data['lon'], data['lat']),
                new OpenLayers.Size(200, 1000),
                popupHtmlCodeLog(data),
                null, true);
    }
}
                                                                                                       
var popupHtmlCodeEvent = function (event) {
    return '<div>' + 
                '<b>ID</b>: ' + event['id'] + 
                '<br>' +
                '<b>Data</b>: ' + event['date'] + 
                '<br>' +
                '<b>Batteria</b>: ' + event['battery'] + 
                '<br>' +
                '<b>KM</b>: ' + event['km'] + 
                '<br>' +
                '<b>Evento</b>: ' + event['eventTypeId'] + 
                '<br>' +
                '<b>Label</b>: ' + event['label'] + 
                '<br>' +
                '<b>Valore</b>: ' + event['textVal'] +
                '<br>' +
                '<b>Intval</b>: ' + event['intVal'] + 
                '<br>' +
                '<b>Posizione</b>: ' + event['lon'] + '|' + event['lat'] + 
            '</div>';
}

var popupHtmlCodeLog = function (log) {
    return '<div>' +
                '<b>ID</b>: ' + log['id'] + 
                '<br>' +
                '<b>SOC</b>: ' + log['SOC'] + 
                '<br>' +
                '<b>LogTime</b>: ' + log['logTime'] + 
                '<br>' +
                '<b>Posizione</b>: ' + log['lon'] + '|' + log['lat'] + 
            '</div>';
}

var destroyPopup = function () {
    popup.destroy();
    popup = null;
}

var lonLatFunction = function (lon, lat) {
    return new OpenLayers.LonLat(lon, lat)
            .transform(
                    new OpenLayers.Projection("EPSG:4326"), // transform from WGS 1984
                    map.getProjectionObject() // to Spherical Mercator Projection
                    );
}

var setIcon = function (url_image, size, offset) {
    return new OpenLayers.Icon(url_image, size, offset);
}
