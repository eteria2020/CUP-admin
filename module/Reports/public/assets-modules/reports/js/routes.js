// Check if var $.oe has been declared
if (typeof $.oe === 'undefined') {
    $.extend({
	    oe: {}
    });
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

// $.oe Vars Definition {
	$.oe.today	 		= new Date();
	$.oe.todayFormatted	= $.oe.today.getFullYear() 	+ '-' + ("0" + ($.oe.today.getMonth()+1)).slice(-2) + '-' + ("0" + $.oe.today.getDate()).slice(-2);

	$.oe.urlDate 			= "";
	
	// Set the vector source; will contain the map data
	$.oe.vectorSource = {};
	
	// Set the features collection
	$.oe.tracks = {};
	
	// The collection of features selected
	$.oe.featureOverlaySource = {}; 
	
	// The Ol3 Vector containing the selected overlay
	$.oe.featureOverlay;

	// flag for Overlight features
	$.oe.highlightStyleCache;
	$.oe.highlight;
	
	// The loaded trips (id)
	$.oe.trips = [];
	
	// The HTML <li> of the trips
	$.oe.items = [];
	
	// The loaded tracks (features)
	$.oe.features = [];
	
	// Flag to trigger the map mousehover listener 
	$.oe.listenHover = true;
	
	// The number of trips to load
	$.oe.tripsnumber = 0;
	
	// Flag to get the maintainer trips or the customers one
	// true = maintainers | false = clients
	$.oe.maintainer = false;
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
   
    
	// Init Bootstrap Switch
	$('#maintainer')
		.bootstrapSwitch()
		.on('switchChange.bootstrapSwitch', 
			function(event, state) {
				$.oe.maintainer = !state; // true | false
			});
});

$(window).load(function() {
	
	$.oe.fn.getCityData();
	loadTracks();
    
    // Resize the MAP
    doneResizing();
    
    activateHoverButton();
    activateMaintainerButton();
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


$.oe.vectorSource = new ol.source.Vector({
	projection: 'EPSG:3857',
	format: new ol.format.GPX()
});

$.oe.tripslayer = new ol.layer.Vector(
{
	source:	$.oe.vectorSource,
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

$.oe.map = new ol.Map({
	layers: [OSM, $.oe.tripslayer],
	target: document.getElementById('map'),
	view: view
});



// Set the overlay (another layer) for the selected tracks
$.oe.featureOverlaySource = new ol.source.Vector({});
$.oe.featureOverlay = new ol.layer.Vector({
	source: $.oe.featureOverlaySource,
	style: function(feature, resolution) {
	    	return hoverstyle[feature.getGeometry().getType()];
		}
});
// Add the overlay to the MAP ol.obj
$.oe.featureOverlay.setMap($.oe.map);
	
// Bind the entire map mouse moving
$.oe.map.on('pointermove', function(evt) {
	if (evt.dragging) {
		return;
	}
	if (!$.oe.listenHover){
		return;
	}
	var pixel = $.oe.map.getEventPixel(evt.originalEvent);
	displayFeatureInfo(pixel);
});

// Bind the blick of the map
$.oe.map.on('click', function(evt) {
	displayFeatureInfo(evt.pixel);
});
	

// Map MouseMove Handler
function displayFeatureInfo(pixel) {
	var selectedfeatures = [];
	
	
	$.oe.map.forEachFeatureAtPixel(pixel, function(feature, layer) {
		selectedfeatures.push(feature);
	});
	
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
		}
		
		
		document.getElementById('info').innerHTML = info.join(', ') || '(unknown)';
		$.oe.map.getTarget().style.cursor = 'pointer';
	} else {
		document.getElementById('info').innerHTML = '&nbsp;';
		$.oe.map.getTarget().style.cursor = '';
	}
};


function changeTrackColor(track)
{
	// Add the track selected to the selected overlay
	if (track !== $.oe.highlight) {
		if ($.oe.highlight) {
			// Remove The Track from the selected tracks
			$.oe.featureOverlaySource.removeFeature($.oe.highlight);
		}
		if (track) {
			// Add the Track from the selected tracks
			$.oe.featureOverlaySource.addFeature(track);
		}
		$.oe.highlight = track;
	}
}


function activateHoverButton()
{
	$('button#hoverenable').addClass("btn-success");
	
	$('button#hoverenable').click(
		function () {
		    if($.oe.listenHover){
			    $.oe.listenHover = false;
			    
		        $('button#hoverenable').removeClass("btn-success");
		        $('button#hoverenable').addClass("btn-danger");
		    }else{
		        $.oe.listenHover = true;
			    
		        $('button#hoverenable').addClass("btn-success");
		        $('button#hoverenable').removeClass("btn-danger");
		    }
		}
	);
}

function activateMaintainerButton()
{
	/*$('button#maintainer').addClass("btn-danger");
	
	$('button#maintainer').click(
		function () {
		    if($.oe.maintainer){
			    $.oe.maintainer = false;
			    
		        $('button#maintainer').removeClass("btn-success");
		        $('button#maintainer').addClass("btn-danger");
		    }else{
		        $.oe.maintainer = true;
			    
		        $('button#maintainer').addClass("btn-success");
		        $('button#maintainer').removeClass("btn-danger");
		    }
		}
	);*/
}

function activeMapInteraction(){
	$('div#over').remove();
}

function deactiveMapInteraction(){
	$('.map').after('<div id="over"><span class="glyphicon glyphicon-refresh glyphicon-refresh-animate" aria-hidden="true"></span></div>');
}

function deactiveListActions()
{
	// Set it as disabled
	$("#steps").attr('disabled',true);
	
	// Remove other bind events();
	$('.way').off();
}


function activateListActions()
{
	// Remove other bind events();
	$('.way').off();
	
	// Set it as enabled
	$("#steps").attr('disabled',false);
	
	// Add hover function to highlight tracks
	$(".way").hover(
		function () {
			if (!$.oe.listenHover){
				return;
			}
			
			var thisId= $(this).attr("id");
			
			// Remove the "green color class" to the others <li>
			$(".way:not(#"+thisId+")").removeClass("list-group-item-success");
		
			var f = $.grep( $.oe.features, function(e){ return parseInt(e.id) == parseInt(thisId); });
			
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
			
			var f = $.grep( $.oe.features, function(e){ return parseInt(e.id) == parseInt(thisId); });
			
       	 	if(f.length != 1 ){
            	$(this).addClass("list-group-item-danger");
       	 	}else{
	       	 	$(this).removeClass("list-group-item-danger");
				$(this).addClass("list-group-item-success");
				var extent = f[0].getGeometry().getExtent();
				$.oe.map.getView().fit(extent , $.oe.map.getSize());
				changeTrackColor(f[0]);
            }
		}
	);
}


///////////////// LOAD DATA /////////////////


function loadTracks() {
	deactiveListActions();
	deactiveMapInteraction()
	
	// Get The date from the DateTime Input
	$.oe.urlDate	= $("div.date input").val();
	
	// Get the number of trips to load
	$.oe.tripsnumber	= $("#ex6").slider().val();
	
	// Disable the Data Update Button to prevent error
	$('button#dataupdate')
		.prop('disabled', true)
		.removeClass('btn-default')
		.addClass('btn-warning');
		
	$('button#dataupdate span')
		.removeClass('glyphicon-screenshot')
		.addClass('glyphicon-refresh glyphicon-refresh-animate');
	
	$.oe.trips = [];
	$.oe.items = [];

	$.ajax({
		method: 'POST',
		url: 'api/get-trips',
		dataType: "json",
		data: {
			end_date: 		$.oe.urlDate,
			trips_number:	$.oe.tripsnumber,
			maintainer:		$.oe.maintainer
		}
	})
	.success(function(data) {
		if(typeof(data) == "undefined" || data === null || data.count === 0)
		{
			alert("Non sono state trovate corse con i filtri selezionati");
			
			// Enable the Data Update Button
			$("button#dataupdate").prop('disabled', false);
			
			// Bind its action
			$("button#dataupdate").one('click','',function(event){
				loadTracks();
			});
		}else{
			$.each(data, function(key, val)
			{
				if (val._id>-1) {
							
					val.end_trip 	= (typeof val.end_trip 		== 'string') 	? val.end_trip 		: val.end_trip.date;
					val.begin_trip  = (typeof val.begin_trip	== 'string') 	? val.begin_trip 	: val.begin_trip.date;
										
					val.end_trip 	= moment(val.end_trip).format("X");
					val.begin_trip 	= moment(val.begin_trip).format("X");
					
					var duration = Math.round((val.end_trip - val.begin_trip)/60);
	
					//if (duration > 3 && val._id != 0) {
						$.oe.trips.push(val._id);
						
						val.VIN = val.VIN ? val.VIN : val.vin;
	
						var date = moment(val.begin_trip*1000).format("DD/MM/YYYY HH:mm");
	
						var li = '<li href="#" class="list-group-item way" id="' + val._id + '">'+
							'<h5 class="list-group-item-heading">'+ date +' <b>'+ val.VIN+'</b></h5>'+
							'<p class="list-group-item-text">'+
							 val._id + ' ' + duration + 'min';
							 
						if (typeof(val.points) != "undefined" && val.points !== null){
							li += ' ('+ val.points +')';
						} 
						
						li += '</p></li>';
							
						$.oe.items.push(li);
					//}
				}
			});
			
			$("#trips").html($.oe.items.join(""));
			
			getTripsData();
		}
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
			trips_id: 		$.oe.trips
		},
		timeout: 180000		// Timeout = 3 minutes (2 minutes ~= 300 tracks)
	})
	.success(function(data,status,jsonXHR) {
		$.oe.highlightStyleCache = null;
		$.oe.highlight = null;
		
		// Remove all Features
		$.oe.vectorSource.clear();
		$.oe.featureOverlaySource.clear();
		
		var format = new ol.format.GPX();
		var points = 0;
		
		// Reading the xmlDoc
		xmlDoc = jsonXHR.responseXML;
		
		$.oe.features = [];
        $.oe.features = format.readFeatures(xmlDoc,  {featureProjection: 'EPSG:3857'});
		
		console.log("Features");
		console.log($.oe.features);

        for (var i=0; i<$.oe.features.length; i++) {
	        var trackFeature = $.oe.features[i];
		
			trackFeature.id = trackFeature.get('name');
		    trackFeature.setGeometry(trackFeature.getGeometry());    
			$.oe.vectorSource.addFeature(trackFeature);
			
			//points += trackFeature.geometry.components.length;
        }

        // Determino il numero di elementi caricati
        $("span#points").text(points);
		$("span#trips").text($.oe.features.length);
        
        //console.log(features.length);
        $.oe.vectorSource.changed();
       
	})
	.complete(function(){	
		// Enable the Data Update Button
		$('button#dataupdate')
			.prop('disabled', false)
			.removeClass('btn-warning')
			.addClass('btn-default');
			
		$('button#dataupdate span')
			.removeClass('glyphicon-refresh glyphicon-refresh-animate')
			.addClass('glyphicon-screenshot');

		
		// Bind its action
		$("button#dataupdate").one('click','',function(event){
			loadTracks();
		});
		
		// Activate the list of trips
		activateListActions();
		
		// Activate the map
		activeMapInteraction();
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
    $.oe.map.updateSize();
}
