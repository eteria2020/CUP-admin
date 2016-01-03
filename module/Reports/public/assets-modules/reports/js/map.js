// Check if var global has been declared
if (typeof global === 'undefined') {
    var global = {};
}

// Global Vars Definition
	global.today	 	= new Date();
	global.aMonthAgo 	= new Date(); global.aMonthAgo.setMonth(global.today.getMonth() - 1);

	global.todayFormatted		= global.today.getFullYear() 	+ '-' + ("0" + (global.today.getMonth()+1)).slice(-2) + '-' + ("0" + global.today.getDate()).slice(-2);
	global.aMonthAgoFormatted	= global.aMonthAgo.getFullYear() + '-' + ("0" + (global.aMonthAgo.getMonth()+1)).slice(-2) + '-' + ("0" + global.aMonthAgo.getDate()).slice(-2);

	global.params = {
		dateFrom 	: global.aMonthAgoFormatted,
		dateTo		: global.todayFormatted,
		begend		: 0, 	// 0 ==> Beginning Hour ||  1 ==> Ending Hour
	    weight      : 0.192,
	    base_weight : 0.4
	}

	// Set the timeout needed for the page resize bind function
	global.timeout = 0;
	
	// Set the vector source; will contain the map data
	global.vectorSource = {};
	
	// Set the features collection
	global.features = {};
// }

// The magic!
$(document).ready(function(){
	getCityData(createButtons);
});

$(window).load(function() {
	doneResizing();
});


function createButtons(){
	$.each(global.city,function(key,val){
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


// Setting Up the star date to a month ago.
$("#datepicker #end")	.val( global.todayFormatted );
$("#datepicker #start")	.val( global.aMonthAgoFormatted );


global.vectorSource = new ol.source.Vector({
	extractStyles: 	false,
	projection: 	'EPSG:3857',
	loader: 		function(extent, resolution, projection) {
						$.ajax({
							method: 'POST',
							url: '/reports/api/get-trips-geo-data',
							data: {
								start_date:  	global.params.dateFrom,
								end_date: 		global.params.dateTo,
								begend:			global.params.begend
							},
							dataType: "json"
						})
						.success(function(response) {
				            var format = new ol.format.GeoJSON();
				            
				            global.features = format.readFeatures(
				            	response,
								{featureProjection: projection}
							);
				            
				            global.vectorSource.addFeatures(global.features);
				
							// Determino il numero di elementi caricati
							$("#element-counter input").val(global.features.length);
				        });
	},
	format: 		new ol.format.GeoJSON()
});


// This functions will change the source loader params to pass to the url
// so we can make a specific ajax call
function changeFilterDateFrom(dateFrom) {
	global.params.dateFrom = dateFrom;
	global.vectorSource.clear(true);
}
function changeFilterDateTo(dateTo){
	global.params.dateTo = dateTo;
	global.vectorSource.clear(true);
}
function changeFilterBegEnd(begend){
	global.params.begend = begend;
	global.vectorSource.clear(true);
}


var vector = new ol.layer.Heatmap({
	source:		global.vectorSource,
	radius: 	12,
    opacity: 	0.7,
    blur: 		14,
    weight: 	function(f) {
      				return  global.params.base_weight + global.params.weight;
    			}
});

var view = new ol.View({
	// the view's initial state
	center: ol.proj.transform([9.185, 45.465], 'EPSG:4326', 'EPSG:3857'),
	zoom: 12
});



var raster = new ol.layer.Tile({
	source: new ol.source.Stamen({
		layer: 'toner'
	})
});

var OSM = new ol.layer.Tile({
	source: new ol.source.OSM()
});

var map = new ol.Map({
	layers:[OSM, vector],
	target: 		'map',
	view: 			view,
	eventListeners:	{"zoomend": zoomChanged}
});


map.on("moveend", zoomChanged);

var lastZoom;
function zoomChanged(){
	zoom = map.getView().getZoom();
	
	if (lastZoom!=zoom)	{
		vector.setRadius(zoom*1.0);
        global.params.weight =  (0.4*zoom/25);
		console.log(zoom);
		lastZoom = zoom;

	}
}

//console.log(vector);

var cnt = 0;
function animate(){
    /*
	feats = fsource.getFeatures();
	console.log(feats[cnt]);
	vector.getSource().addFeature(feats[cnt]);
    */

    global.params.weight = 0.1*cnt;
    console.log(global.params.weight);
    vector.getSource().changed();
    //map.renderSync();
	cnt++;
    if (cnt>10) cnt=0;
}


//setInterval(animate,100);


$('#weight').slider({
	formatter: function(value) {
        global.params.base_weight = value/10;
        vector.getSource().changed();
      	return '' + value/10;
	}
});

$("#change-begend").click(function()
{
	$(this).text(function(i, text){
    	return text === "Change to Ending Location" ? "Change to Beginning Location" : "Change to Ending Location";
    })

	global.params.begend == 0 ? changeFilterBegEnd(1) : changeFilterBegEnd(0);
});

console.log(global.today);

$('.input-daterange').datepicker({
    format: "yyyy-mm-dd",
    language: "it",
    endDate:	global.today,
    orientation: "bottom auto",
    autoclose: true
});

$('.input-daterange').datepicker()
    .on("changeDate", function(e) {
        if (e.target.id == "start"){
            // id = start
			changeFilterDateFrom($(e.target).val());
        }else{
            // id = end
			changeFilterDateTo($(e.target).val());
        }
});


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

