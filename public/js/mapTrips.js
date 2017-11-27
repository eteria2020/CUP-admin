var lat=45.46419;
var lon=9.19161;
var zoom=13;

var init = function () {
    
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
    
    var lonLat = new OpenLayers.LonLat( lon ,lat )
      .transform(
        new OpenLayers.Projection("EPSG:4326"), // transform from WGS 1984
        map.getProjectionObject() // to Spherical Mercator Projection
      );
      
      
    var size = new OpenLayers.Size(21,25);
    var offset = new OpenLayers.Pixel(-(size.w/2), -size.h);
    var icon = new OpenLayers.Icon('http://maps.gstatic.com/intl/de_de/mapfiles/ms/micons/red-pushpin.png', size, offset);
    
    
    
    var markers = new OpenLayers.Layer.Markers( "Markers" );
    map.addLayer(markers);
    markers.addMarker(new OpenLayers.Marker(lonLat,icon));


    
    /*
    var marker2 = new OpenLayers.Layer.Marker({
            position: new OpenLayers.Marker(lonLat), 
            map: map,
            title:"static marker"
    });
    */
    
    /*
    markers.addMarker(new khtml.maplib.overlay.Marker(
    
    
     {position: new khtml.maplib.LatLng(9.19, 45.45),
        icon: {
                    url: "http://maps.gstatic.com/intl/de_de/mapfiles/ms/micons/red-pushpin.png",
                    size: {width: 26, height: 32},
                    origin: {x: 0, y: 0},
                    anchor: {
                            x: "-10px",
                            y: "-32px"
                    }
            },
        title: "moveable marker"
    }));
     
    */
    
    
   
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

    map.setCenter(lonLat, zoom);
    
};
