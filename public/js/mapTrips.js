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

var zoom = 14;
var popup = null;

var init = function (events) {

    var size = new OpenLayers.Size(42, 50);
    var offset = new OpenLayers.Pixel(-(size.w / 2), -size.h);

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

    events.forEach(function (event) {

        switch (event['eventType']) {
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
                popup = createPopup(event['lon'], event['lat']);
                map.addPopup(popup);
            } else {
                destroyPopup();
                popup = createPopup(event['lon'], event['lat']);
                map.addPopup(popup);
            }
        });


        /*
         //mouseover event view popup
         markers.events.register('mouseover', markers, function(evt) {
         popup = new OpenLayers.Popup.FramedCloud("Popup",
         lonLatFunction(event['lon'], event['lat']),
         null,
         '<div>\n\
         Longitudine: ' + event['lon'] + '\
         <br>\n\
         Latitudine: ' + event['lat'] + '\
         </div>',
         null,
         false);
         map.addPopup(popup);
         });
         //mouseout event popup hide
         markers.events.register('mouseout', markers, function(evt) {
         popup.hide();
         });
         */

    });

    //set center map, to first event
    map.setCenter(lonLatFunction(events[0]['lon'], events[0]['lat']), zoom);

};

var createPopup = function (lon, lat, ) {
    return  popup = new OpenLayers.Popup.FramedCloud("chicken",
                        lonLatFunction(lon, lat),
                        new OpenLayers.Size(700, 700),
                        '<div>\n\
                            Longitudine: ' + lon + '\
                            <br>\n\
                            Latitudine: ' + lat + '\
                        </div>',
                        null, true);
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
