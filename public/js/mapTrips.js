var lat=45.46419;
var lon=9.19161;
var zoom=13;

var url_image = 'http://maps.gstatic.com/intl/de_de/mapfiles/ms/micons/red-pushpin.png';

var init = function (events) {
    
    var size = new OpenLayers.Size(21,25);
    var offset = new OpenLayers.Pixel(-(size.w/2), -size.h);
    //var icon = new OpenLayers.Icon('http://maps.gstatic.com/intl/de_de/mapfiles/ms/micons/red-pushpin.png', size, offset);
    
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
    map.addLayer(layerCycleMap);
    
    //star for sugli eventi in json
    //for (var event in events) {
    
        //set lonlat per punto
        var lonLat = lonLatFunction(lon, lat/*event['lon'], event['lat']*/);

        //switch type event
        //switch (event['type']){
            //case 'xxxxxxx':
                //set image per punto
                var icon = setIcon(url_image, size, offset);
                //braek;
        //}
        
        
        //set marker in mappa con lonlat e img
        var markers = new OpenLayers.Layer.Markers( "Markers" );
        map.addLayer(markers);
        markers.addMarker(new OpenLayers.Marker(lonLat,icon));
        
    //}
    //end for su eventi json

    //set centro mappa
    map.setCenter(lonLat, zoom);
    
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