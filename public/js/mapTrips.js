var zoom=13;
var url_image_default = "/img/car-icon.png";
var url_image_SW_BOOT = "/img/car-icon.png";
var url_image_RFID = "/img/car-icon.png";
var url_image_BATTERY = "/img/car-icon.png";
var url_image_SPEED = "/img/car-icon.png";
var url_image_AREA = "/img/car-icon.png";
var url_image_CHARGING = "/img/car-icon.png";
var url_image_ENGINE = "/img/car-icon.png";
var url_image_SOS = "/img/car-icon.png";
var url_image_PARK = "/img/car-icon.png";
var url_image_CMD = "/img/car-icon.png";
var url_image_CLEANLINESS = "/img/car-icon.png";
var url_image_OBCFAIL = "/img/car-icon.png";
var url_image_OBCOK = "/img/car-icon.png";
var url_image_KEY = "/img/car-icon.png";
var url_image_READY = "/img/car-icon.png";
var url_image_GEAR = "/img/GEAR.PNG";
var url_image_DIAG = "/img/car-icon.png";
var url_image_CARPLATE = "/img/car-icon.png";
var url_image_3G = "/img/car-icon.png";
var url_image_MAINTENANCE = "/img/car-icon.png";
var url_image_OUTOFORDER = "/img/car-icon.png";
var url_image_SELFCLOSE = "/img/car-icon.png";
var url_image_DEVICEINFO = "/img/car-icon.png";
var url_image_SHUTDOWN = "/img/car-icon.png";
var url_image_LEASE = "/img/car-icon.png";
var url_image_SOC = "/img/car-icon.png";
var url_image_AREA = "/img/car-icon.png";
var url_image_MENU_CLICK = "/img/car-icon.png";

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
                var icon = setIcon(url_image_default, size, offset);
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








/*
 * COME FARE DELLE LINEE TRA PUNTI
 * 
    var lineLayer = new OpenLayers.Layer.Vector("Line Layer"); 
    
    map.addLayer(lineLayer);                    
    map.addControl(new OpenLayers.Control.DrawFeature(lineLayer, OpenLayers.Handler.Path));                                     
    var points = new Array(
        new OpenLayers.Geometry.Point(9.22, 45.62).transform(new OpenLayers.Projection("EPSG:4326"), map.getProjectionObject()),
        new OpenLayers.Geometry.Point(9.21, 45.61).transform(new OpenLayers.Projection("EPSG:4326"), map.getProjectionObject()),
        new OpenLayers.Geometry.Point(9.20, 44.60).transform(new OpenLayers.Projection("EPSG:4326"), map.getProjectionObject())
    );

    var lineFeature = new OpenLayers.Feature.Vector(new OpenLayers.Geometry.LineString(points), null, {fillColor:'#143D29', fillOpacity:0.4});
    lineLayer.addFeatures(lineFeature);
*/