var zoom=13;
var url_image_DEFAULT = "/img/DEFAULT.PNG";
var url_image_SW_BOOT = "/img/SW_BOOT.PNG";
var url_image_RFID = "/img/RFID.PNG";
var url_image_BATTERY = "/img/BATTERY.PNG";
var url_image_SPEED = "/img/SPEED.PNG";
var url_image_AREA = "/img/AREA.PNG";
var url_image_CHARGING = "/img/CHARGING.PNG";
var url_image_ENGINE = "/img/ENGINE.PNG";
var url_image_SOS = "/img/SOS.PNG";
var url_image_PARK = "/img/PARK.PNG";
var url_image_CMD = "/img/CMD.PNG";
var url_image_CLEANLINESS = "/img/CLEANLINESS.PNG";
var url_image_OBCFAIL = "/img/OBCFAIL.PNG";
var url_image_OBCOK = "/img/OBCOK.PNG";
var url_image_KEY = "/img/KEY.PNG";
var url_image_READY = "/img/READY.PNG";
var url_image_GEAR = "/img/GEAR.PNG";
var url_image_DIAG = "/img/DIAG.PNG";
var url_image_CARPLATE = "/img/CARPLATE.PNG";
var url_image_3G = "/img/3G.PNG";
var url_image_MAINTENANCE = "/img/MAINTENANCE.PNG";
var url_image_OUTOFORDER = "/img/OUTOFORDER.PNG";
var url_image_SELFCLOSE = "/img/SELFCLOSE.PNG";
var url_image_DEVICEINFO = "/img/DEVICEINFO.PNG";
var url_image_SHUTDOWN = "/img/SHUTDOWN.PNG";
var url_image_LEASE = "/img/LEASE.PNG";
var url_image_SOC = "/img/SOC.PNG";
//var url_image_AREA = "/img/.PNG";
var url_image_MENU_CLICK = "/img/MENU_CLICK.PNG";

var init = function (events) {
   
    var size = new OpenLayers.Size(42,50);
    var offset = new OpenLayers.Pixel(-(size.w/2), -size.h);
    
    map = new OpenLayers.Map ("map", {
        controls:[ 
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
    
    events.forEach(function(event) {
        
        switch (event['eventType']){
            case 'SW_BOOT':
                var icon = setIcon(url_image_SW_BOOT, size, offset);
                break;
            case 'RFID':
                var icon = setIcon(url_image_RFID, size, offset);
                break;
            case 'BATTERY':
                var icon = setIcon(url_image_BATTERY, size, offset);
                break;
            case 'SPEED':
                var icon = setIcon(url_image_SPEED, size, offset);
                break;
            case 'AREA':
                var icon = setIcon(url_image_AREA, size, offset);
                break;
            case 'CHARGING':
                var icon = setIcon(url_image_CHARGING, size, offset);
                break;
            case 'ENGINE':
                var icon = setIcon(url_image_ENGINE, size, offset);
                break;
            case 'SOS':
                var icon = setIcon(url_image_SOS, size, offset);
                break;
            case 'PARK':
                var icon = setIcon(url_image_PARK, size, offset);
                break;
            case 'CMD':
                var icon = setIcon(url_image_CMD, size, offset);
                break;
            case 'CLEANLINESS':
                var icon = setIcon(url_image_CLEANLINESS, size, offset);
                break;
            case 'OBCFAIL':
                var icon = setIcon(url_image_OBCFAIL, size, offset);
                break;
            case 'OBCOK':
                var icon = setIcon(url_image_OBCOK, size, offset);
                break;
            case 'KEY':
                var icon = setIcon(url_image_KEY, size, offset);
                break;
            case 'READY':
                var icon = setIcon(url_image_READY, size, offset);
                break;
            case 'GEAR':
                var icon = setIcon(url_image_GEAR, size, offset);
                break;
            case 'DIAG':
                var icon = setIcon(url_image_DIAG, size, offset);
                break;
            case 'CARPLATE':
                var icon = setIcon(url_image_CARPLATE, size, offset);
                break;
            case '3G':
                var icon = setIcon(url_image_3G, size, offset);
                break;
            case 'MAINTENANCE':
                var icon = setIcon(url_image_MAINTENANCE, size, offset);
                break;
            case 'OUTOFORDER':
                var icon = setIcon(url_image_OUTOFORDER, size, offset);
                break;
            case 'SELFCLOSE':
                var icon = setIcon(url_image_SELFCLOSE, size, offset);
                break;
            case 'DEVICEINFO':
                var icon = setIcon(url_image_DEVICEINFO, size, offset);
                break;
            case 'SHUTDOWN':
                var icon = setIcon(url_image_SHUTDOWN, size, offset);
                break;
            case 'LEASE':
                var icon = setIcon(url_image_LEASE, size, offset);
                break;
            case 'SOC':
                var icon = setIcon(url_image_SOC, size, offset); 
                break;
            /*
            case 'AREA':
                var icon = setIcon(url_image_AREA, size, offset);
                break;
            */
            case 'MENU_CLICK':
                var icon = setIcon(url_image_MENU_CLICK, size, offset);
                break;
            default:
                var icon = setIcon(url_image_DEFAULT, size, offset);
                break;
        }

        //set marker in map with lonlat e img
        var markers = new OpenLayers.Layer.Markers( "Markers" );
        map.addLayer(markers);
        markers.addMarker(new OpenLayers.Marker(lonLatFunction(event['lon'], event['lat']),icon));
        
    });

    //set center map, to first event
    map.setCenter(lonLatFunction(events[0]['lon'], events[0]['lat']), zoom);
    
};

var lonLatFunction = function (lon, lat) {
    return new OpenLayers.LonLat( lon ,lat )
      .transform(
        new OpenLayers.Projection("EPSG:4326"), // transform from WGS 1984
        map.getProjectionObject() // to Spherical Mercator Projection
      );
}

var setIcon = function (url_image, size, offset) {
    return new OpenLayers.Icon(url_image, size, offset);
}