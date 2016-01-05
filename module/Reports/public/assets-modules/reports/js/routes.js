// Check if var global has been declared
if (typeof global === 'undefined') {
    var global = {};
}

$.ajaxSetup({
    async: true,
    cache : false,
    timeout: 180000,		// set to 2minutes
    queue: false,
    error: function (msg) { alert('error : ' + msg.d); console.log(msg); }
});

$.extend($.scrollTo.defaults, {
  axis: 'y',
  duration: 500,
  interrupt: true 		//  If true will cancel the animation if the user scrolls. Default is false
  //easing: 'linear'
});

// Global Vars Definition {
	global.today	 		= new Date();
	global.todayFormatted	= global.today.getFullYear() 	+ '-' + ("0" + (global.today.getMonth()+1)).slice(-2) + '-' + ("0" + global.today.getDate()).slice(-2);

	global.urlDate 			= "";
	
	// Set the vector source; will contain the map data
	global.vectorSource = {};
	
	// Set the features collection
	global.tracks = {};
	
	// The collection of features selected
	global.featureOverlaySource = {}; 
	
	// The Ol3 Vector containing the selected overlay
	global.featureOverlay;

	// flag for Overlight features
	global.highlightStyleCache;
	global.highlight;
	
	// The loaded trips (id)
	global.trips = [];
	
	// The HTML <li> of the trips
	global.items = [];
	
	// The loaded tracks (features)
	global.features = [];
	
	// Flag to trigger the map mousehover listener 
	global.listenHover = true;
	
	// The number of trips to load
	global.tripsnumber = 0;
// }

$(document).ready(function()
{
	
	// DateTime Picker
	$('#datetimepicker1').datetimepicker({
	 	sideBySide: true,
		maxDate:	Date(),
		defaultDate: Date(),
		format: 'YYYY-MM-DD HH:mm:ss'
	});
    
    // Create The Slider
	$("#ex6").slider();

	// Listen to Slider Change Value (Ther's also the on("slide" bind, that listen
	// only the slide action, not also the click on a specific section of the
	// slidebar.
	$("#ex6").on("change", function(slideEvt) {
		$("#ex6SliderVal").text(slideEvt.value.newValue+" corse prima di");

		// Also update the urlLimit value
		urlLimit = slideEvt.value.newValue;
	});
   
    
});

$(window).load(function() {
	
	getCityData();
	loadTracks();
    
    // Resize the MAP
    doneResizing();
    
    activateHoverButton();
});


var projection = ol.proj.get('EPSG:3857');

// The MAP
var OSM = new ol.layer.Tile(
{
	source: new ol.source.OSM()
});


var style = {
	'Point': [new ol.style.Style({
		image: new ol.style.Circle({
			fill: new ol.style.Fill({
				color: 'rgba(255,255,0,0.4)'
			}),
			radius: 5,
			stroke: new ol.style.Stroke({
				color: '#ff0',
				width: 1
			})
		})
	})],
	'LineString': [new ol.style.Style({
		stroke: new ol.style.Stroke({
			color: '#f00',
			width: 3
		})
	})],
	
	// Tracks
	'MultiLineString': [new ol.style.Style({
		stroke: new ol.style.Stroke({
			color: 'rgba(30,140,0,0.7)',
			width: 5
		})
	})]
};

var hoverstyle = {
	'Point': [new ol.style.Style({
		image: new ol.style.Circle({
			fill: new ol.style.Fill({
				color: 'rgba(255,255,0,0.4)'
			}),
			radius: 5,
			stroke: new ol.style.Stroke({
				color: '#ff0',
				width: 1
			})
		})
	})],
	'LineString': [new ol.style.Style({
		stroke: new ol.style.Stroke({
			color: '#f00',
			width: 6
		})
	})],
	
	// Tracks
	'MultiLineString': [new ol.style.Style({
		stroke: new ol.style.Stroke({
			color: 'rgba(140,30,0,0.8)',
			width: 6
		})
	})]
};


global.vectorSource = new ol.source.Vector({
	projection: 'EPSG:3857',
	format: new ol.format.GPX()
});

global.tripslayer = new ol.layer.Vector(
{
	source:	global.vectorSource,
    style :
    	function(feature, resolution) {
	    	return style[feature.getGeometry().getType()];
		}
});

var view = new ol.View({
	// the view's initial state
	center: ol.proj.transform([9.185, 45.465], 'EPSG:4326', 'EPSG:3857'),
	zoom: 12
});

global.map = new ol.Map({
	layers: [OSM, global.tripslayer],
	target: document.getElementById('map'),
	view: view
});



// Set the overlay (another layer) for the selected tracks
global.featureOverlaySource = new ol.source.Vector({});
global.featureOverlay = new ol.layer.Vector({
	source: global.featureOverlaySource,
	style: function(feature, resolution) {
	    	return hoverstyle[feature.getGeometry().getType()];
		}
});
// Add the overlay to the MAP ol.obj
global.featureOverlay.setMap(global.map);
	
// Bind the entire map mouse moving
global.map.on('pointermove', function(evt) {
	if (evt.dragging) {
		return;
	}
	if (!global.listenHover){
		return;
	}
	var pixel = global.map.getEventPixel(evt.originalEvent);
	displayFeatureInfo(pixel);
});

// Bind the blick of the map
global.map.on('click', function(evt) {
	displayFeatureInfo(evt.pixel);
});
	

// Map MouseMove Handler
function displayFeatureInfo(pixel) {
	var selectedfeatures = [];
	
	
	global.map.forEachFeatureAtPixel(pixel, function(feature, layer) {
		selectedfeatures.push(feature);
	});
	
	
	console.log("Selected Features");
	console.log(selectedfeatures);
	
	if (selectedfeatures.length > 0) {
		var info = [];
		var i;
		
		for (i = 0 ; i <  selectedfeatures.length; ++i) {
			info.push(selectedfeatures[i].get('name'));
						
			changeTrackColor(selectedfeatures[i]);
		}
		
		//get the selected <li> track of the first selected
		element = $("#" + selectedfeatures[0].id);
		
		// Remove the "green color class" to the others <li>
		$(".way:not(#"+selectedfeatures[0].id+")").removeClass("list-group-item-success");

		// If the feature is not selceted, the we select it
		if (!element.hasClass("list-group-item-success")) {
			element.removeClass("list-group-item-danger");
			element.addClass("list-group-item-success");
			
			element.parent().stop();
			element.parent().scrollTo(element);
			console.log("SCROLL");
		}
		
		
		document.getElementById('info').innerHTML = info.join(', ') || '(unknown)';
		global.map.getTarget().style.cursor = 'pointer';
	} else {
		document.getElementById('info').innerHTML = '&nbsp;';
		global.map.getTarget().style.cursor = '';
	}
};


function changeTrackColor(track)
{
	// Add the track selected to the selected overlay
	if (track !== global.highlight) {
		if (global.highlight) {
			// Remove The Track from the selected tracks
			global.featureOverlaySource.removeFeature(global.highlight);
		}
		if (track) {
			// Add the Track from the selected tracks
			global.featureOverlaySource.addFeature(track);
		}
		global.highlight = track;
	}
}


function activateHoverButton()
{
	$('button#hoverenable').addClass("btn-success");
	
	$('button#hoverenable').click(
		function () {
		    if(global.listenHover){
			    global.listenHover = false;
			    
		        $('button#hoverenable').removeClass("btn-success");
		        $('button#hoverenable').addClass("btn-danger");
		    }else{
		        global.listenHover = true;
			    
		        $('button#hoverenable').addClass("btn-success");
		        $('button#hoverenable').removeClass("btn-danger");
		    }
		}
	);
}


function activateListActions()
{
	// Remove other bind events();
	$('.way').off();
	
	// Add hover function to highlight tracks
	$(".way").hover(
		function () {
			if (!global.listenHover){
				return;
			}
			
			var thisId= $(this).attr("id");
			
			// Remove the "green color class" to the others <li>
			$(".way:not(#"+thisId+")").removeClass("list-group-item-success");
		
			var f = $.grep( global.features, function(e){ return parseInt(e.id) == parseInt(thisId); });
			
       	 	if(f.length != 1 ){
            	$(this).addClass("list-group-item-danger");
       	 	}else{
	       	 	$(this).removeClass("list-group-item-danger");
				$(this).addClass("list-group-item-success");
				changeTrackColor(f[0]);
            }
		}
	);

	$(".way").click(
		function () {
			var thisId= $(this).attr("id");
			
			// Remove the "green color class" to the others <li>
			$(".way:not(#"+thisId+")").removeClass("list-group-item-success");
			
			var f = $.grep( global.features, function(e){ return parseInt(e.id) == parseInt(thisId); });
			
       	 	if(f.length != 1 ){
            	$(this).addClass("list-group-item-danger");
       	 	}else{
	       	 	$(this).removeClass("list-group-item-danger");
				$(this).addClass("list-group-item-success");
				var extent = f[0].getGeometry().getExtent();
				global.map.getView().fit(extent , global.map.getSize());
				changeTrackColor(f[0]);
            }
		}
	);
}


///////////////// LOAD DATA /////////////////


function loadTracks() {
	// Get The date from the DateTime Input
	global.urlDate	= $("div.date input").val();
	
	// Get the number of trips to load
	global.tripsnumber	= $("#ex6").slider().val();
	
	// Disable the Data Update Button to prevent error
	$("button#dataupdate").prop('disabled', true);
	
	global.trips = [];
	global.items = [];

	$.ajax({
		method: 'POST',
		url: 'api/get-trips-from-logs',
		dataType: "json",
		data: {
			end_date: 		global.urlDate,
			trips_number:	global.tripsnumber
		}
	})
	.success(function(data) {
		$.each(data, function(key, val)
		{
			if (val._id>-1) {
				val.end_trip 	= moment(val.end_trip.date).format("X");
				val.begin_trip 	= moment(val.begin_trip.date).format("X");
				
				var duration = Math.round((val.end_trip - val.begin_trip)/60);

				//if (duration > 3 && val._id != 0) {
					global.trips.push(val._id);

					var date = moment(val.begin_trip*1000).format("DD/MM/YYYY HH:mm");

					global.items.push('<li href="#" class="list-group-item way" id="' + val._id + '">'+
						'<h5 class="list-group-item-heading">'+ date +' <b>'+ val.VIN+'</b></h5>'+
						'<p class="list-group-item-text">'+
						 val._id + ' ' + duration + 'min (' + val.points +')'+
						'</p>'+
						'</li>');
				//}
			}
		});
		
		$("#trips").html(global.items.join(""));
		getTripsData();
		activateListActions();
	});

	//newTrack(features, "way_J10_vers_albine", "", "http://core.sharengo.it/ui/log-data.php?id_trip=4410");
}



function getTripsData() {
	$.ajax({
		method:			'POST',
		url:			'/reports/api/get-trip-points-from-logs',
        processData:	true,
        dataType:		'xml',
		data: {
			trips_id: 		global.trips
		},
		timeout: 180000		// Timeout = 3 minutes (2 minutes ~= 300 tracks)
	})
	.success(function(data,status,jsonXHR) {
		global.highlightStyleCache = null;
		global.highlight = null;
		
		// Remove all Features
		global.vectorSource.clear();
		global.featureOverlaySource.clear();
		
		var format = new ol.format.GPX();
		var points = 0;
		
		// Reading the xmlDoc
		xmlDoc = jsonXHR.responseXML;
		
		global.features = [];
        global.features = format.readFeatures(xmlDoc,  {featureProjection: 'EPSG:3857'});
		
		console.log("Features");
		console.log(global.features);

        for (var i=0; i<global.features.length; i++) {
	        var trackFeature = global.features[i];
		
			trackFeature.id = trackFeature.get('name');
		    trackFeature.setGeometry(trackFeature.getGeometry());    
			global.vectorSource.addFeature(trackFeature);
			
			//points += trackFeature.geometry.components.length;
        }

        // Determino il numero di elementi caricati
        $("span#points").text(points);
		$("span#trips").text(global.features.length);
        
        //console.log(features.length);
        global.vectorSource.changed();
       
	})
	.complete(function(){	
		// Enable the Data Update Button
		$("button#dataupdate").prop('disabled', false);
		
		// Bind its action
		$("button#dataupdate").one('click','',function(event){
			loadTracks();
		});
	});
}


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
    global.map.updateSize();
}
