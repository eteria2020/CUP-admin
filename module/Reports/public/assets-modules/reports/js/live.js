// Check if var $.oe has been declared
if (typeof $.oe === 'undefined') {
    $.extend({
	    oe: {}
    });
}
	
// $.oe Vars Definition
	// Set the timeout needed for the page resize bind function
	$.oe.timeout = 0
//}

// The magic!
$(document).ready(function(){
	$.oe.fn.getCityData(createButtons);    
});

$(window).load(function() {
	doneResizing();
	getCarsGeoData();
});



function createButtons(){
	$.each($.oe.city,function(key,val){
		// Create and populate the city prop
		val.ol = {};
		val.ol.coordinate = ol.proj.fromLonLat([val.params.center.longitude,val.params.center.latitude]);

		// Create a button for every city
		$('#header-buttons').prepend('<button type="button" class="btn btn-default" id="pan-to-'+ val.fleet_code +'">Pan to '+ val.fleet_name +'</button>');


		// Handle the click action for every city button 
		$("#pan-to-"+ val.fleet_code).click(function()
		{
			var pan = ol.animation.pan(
			{
				duration: 2000,
				source: /** @type {ol.Coordinate} */ (view.getCenter())
			});
			map.beforeRender(pan);
			view.setCenter(val.ol.coordinate);
			view.setZoom(12);
		});

	});
}

var fill = new ol.style.Fill({
   color: '#EDE431'
 });
 var stroke = new ol.style.Stroke({
   color: '#FF0000',
   width: 1.25
 });


var iconStyle = new ol.style.Style({
     image: new ol.style.Circle({
         fill: fill,
         stroke: stroke,
         radius: 7
     }),
     fill: fill,
     stroke: stroke
   })

var vectorSource = new ol.source.Vector({
	extractStyles: false,
	projection: 'EPSG:3857',
	format: new ol.format.GeoJSON()
});




var vector = new ol.layer.Vector(
	{
	source:vectorSource,
    style : iconStyle
	}
);

var view = new ol.View({
  // the view's initial state
  center: ol.proj.transform([9.185, 45.465], 'EPSG:4326', 'EPSG:3857'),
  zoom: 12
});



var raster = new ol.layer.Tile(
{
	source: new ol.source.Stamen(
	{
		layer: 'toner'
	})
});

var OSM = new ol.layer.Tile(
{
	source: new ol.source.OSM()
});

var map = new ol.Map(
{
	layers:[OSM, vector],
	target: 'map',
	view: view,
	eventListeners:
		{"zoomend": zoomChanged}
	
});


map.on("moveend", zoomChanged);


var lastZoom;
function zoomChanged()
{
	zoom = map.getView().getZoom();
	if (lastZoom!=zoom)
	{
		console.log(zoom);
		lastZoom = zoom;

	}
}


var info = $('div#info');
info.tooltip({
	animation: true,
	trigger: 'manual'
});


var displayFeatureInfo = function(pixel) {
  info.css({
    left: pixel[0] + 'px',
    top: (pixel[1] - 5) + 'px'
  });
  var feature = map.forEachFeatureAtPixel(pixel, function(feature, layer) {
    return feature;
  });
  if (feature) {
    info.tooltip('hide')
        .attr('data-original-title', feature.get('plate'))
        .tooltip('fixTitle')
        .tooltip('show');
  } else {
    info.tooltip('hide');
  }
};

map.on('pointermove', function(evt) {
	if (evt.dragging) {
		info.tooltip('hide');
		return;
	}
	displayFeatureInfo(map.getEventPixel(evt.originalEvent));
});

map.on('click', function(evt) {
	displayFeatureInfo(evt.pixel);
});


$("#pan-to-milan").click(function()
{
	var pan = ol.animation.pan(
	{
		duration: 2000,
		source: /** @type {ol.Coordinate} */ (view.getCenter())
	});
	map.beforeRender(pan);
	view.setCenter(city.milan);
	view.setZoom(12);
});

$("#pan-to-florence").click(function()
{
	var pan = ol.animation.pan(
	{
		duration: 2000,
		source: /** @type {ol.Coordinate} */ (view.getCenter())
	});
	map.beforeRender(pan);
	view.setCenter(city.florence);
	view.setZoom(12);
});


function getCarsGeoData() {
    console.log("a");

	$.ajax({
		method: 'POST',
		url: '/reports/api/get-cars-geo-data',
		dataType: "json"
	})
	.success(function(data) {
		var format = new ol.format.GeoJSON();
        var features = format.readFeatures(data,  {featureProjection: 'EPSG:3857'});

        for (var i=0; i<features.length; i++) {
          features[i].setId(features[i].get('plate'));
          var ft = vectorSource.getFeatureById(features[i].getId());
          if (ft) {
            ft.setGeometry(features[i].getGeometry());
          } else {
            vectorSource.addFeature(features[i]);
            console.log("Add: "+features[i].get('plate'));
          }
        }

        // Determino il numero di elementi caricati
        $("#element-counter input").val(features.length);
        //console.log(features.length);
        vectorSource.changed();
	});
}


setInterval( getCarsGeoData, 2500);


// Window Resize Action Bind
var id;
$(window).resize(function() {
    clearTimeout(id);
    id = setTimeout(doneResizing, 500);
});


function doneResizing(){
	var newHeight 			= $(window).height();
    $(".row.mainrow").css("height", newHeight -280); //-110);
    $(".map").css("height", newHeight -280);
    map.updateSize();
}

