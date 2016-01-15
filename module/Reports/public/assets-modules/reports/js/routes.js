// Define Glbal vars
/* global thiscity:true, d3:true, dc:true, crossfilter:true, ol:true, moment:true */

// Check if var $.oe has been declared
if (typeof $.oe === 'undefined') {
    $.extend({
        oe: {}
    });
}

$.ajaxSetup({
    async: true,
    cache : false,
    timeout: 180000,        // set to 2minutes
    queue: false,
    error: function (msg) { alert('error : ' + msg.d); }
});

$.extend($.scrollTo.defaults, {
    axis: 'y',
    duration: 500,
    interrupt: true         //  If true will cancel the animation if the user scrolls. Default is false
    //easing: 'linear'
});


///////////// $.oe Vars Definition /////////////
$.oe.today = new Date();
$.oe.todayFormatted = $.oe.today.getFullYear()     + '-' + ("0" + ($.oe.today.getMonth()+1)).slice(-2) + '-' + ("0" + $.oe.today.getDate()).slice(-2);

$.oe.urlDate = "";

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
////////////////////////////////////////////////

$(document).ready(function()
{
    // DateTime Picker
    $('#datetimepicker1').datetimepicker({
        sideBySide: true,
        maxDate:    Date(),
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

    if (typeof tripid !=='undefined') {
        // If ther's only one trip
        $.oe.trips.push(tripid);
        $.oe.fn.loadSingleTrack();
    } else {
        // More trips
        $.oe.fn.loadTracks();
    }

    // Resize the MAP
    $.oe.fn.doneResizing();

    $.oe.fn.activateHoverButton();
});


var projection = ol.proj.get('EPSG:3857');

// The MAP
var OSM = new ol.layer.Tile({
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

$.oe.tripslayer = new ol.layer.Vector({
    source:    $.oe.vectorSource,
    style :
        function(feature) {
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
    style:
        function(feature){
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
    $.oe.fn.displayFeatureInfo(pixel);
});

// Bind the blick of the map
$.oe.map.on('click', function(evt) {
    $.oe.fn.displayFeatureInfo(evt.pixel);
});
    

// Map MouseMove Handler
$.oe.fn.displayFeatureInfo = function(pixel) {
    var selectedfeatures = [];
    
    $.oe.map.forEachFeatureAtPixel(pixel, function(feature) {
        selectedfeatures.push(feature);
    });
    
    if (selectedfeatures.length > 0) {
        var info = [];
        var i;
        
        for (i = 0 ; i <  selectedfeatures.length; ++i) {
            info.push(selectedfeatures[i].get('name'));
                        
            $.oe.fn.changeTrackColor(selectedfeatures[i]);
        }
        
        //get the selected <li> track of the first selected
        var element = $("#" + selectedfeatures[0].id);
        
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


$.oe.fn.changeTrackColor = function(track)
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
};


$.oe.fn.activateHoverButton = function()
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
};

$.oe.fn.activeMapInteraction = function(){
    $('div#over').remove();
};

$.oe.fn.deactiveMapInteraction = function(){
    $('.map').after('<div id="over"><span class="glyphicon glyphicon-refresh glyphicon-refresh-animate" aria-hidden="true"></span></div>');
};

$.oe.fn.deactiveListActions = function()
{
    // Set it as disabled
    $("#steps").attr('disabled',true);
    
    // Remove other bind events();
    $('.way').off();
};


$.oe.fn.activateListActions = function()
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
                $.oe.fn.changeTrackColor(f[0]);
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
                $.oe.fn.changeTrackColor(f[0]);
            }
        }
    );
};

$.oe.fn.zoomOnTrip = function(tripId)
{
    var f = $.grep( $.oe.features, function(e){ return parseInt(e.id) == parseInt(tripId); });
    if(f.length > 0 ){
        var extent = f[0].getGeometry().getExtent();
        $.oe.map.getView().fit(extent , $.oe.map.getSize());
    }
};

///////////////// LOAD DATA /////////////////

$.oe.fn.loadSingleTrack = function() 
{
    $.oe.items = [];

    // Remove unneeded DOM Elements
    $(".btn-toolbar").html('<div class="col-md-2"><div class="input-group"><span class="input-group-addon">Targa Veicolo</span><input type="text" class="form-control" id="carplate" aria-describedby="basic-addon3"></div></div><div class="col-md-2"><div class="input-group"><span class="input-group-addon">Durata (min)</span><input type="text" class="form-control" id="duration" aria-describedby="basic-addon3"></div></div><div class="col-md-3"><div class="input-group"><span class="input-group-addon">Data Inizio Corsa</span><input type="text" class="form-control" id="date-beg" aria-describedby="basic-addon3"></div></div><div class="col-md-3"><div class="input-group"><span class="input-group-addon">Data Fine Corsa</span><input type="text" class="form-control" id="date-end" aria-describedby="basic-addon3"></div></div><div class="col-md-2"><div class="input-group"><span class="input-group-addon">Numero Punti</span><input type="text" class="form-control" id="pointsnumber" aria-describedby="basic-addon3"></div></div>');

    $('.col-md-2.rightbar').remove();
    $('.col-md-10.leftbar').prop('class','col-md-12');

    $('.page-header h1').html('Route '+$.oe.trips[0]);

    $.oe.fn.doneResizing();

    $.ajax({
        method: 'POST',
        url: '/reports/api/get-trip/'+$.oe.trips[0],
        dataType: "json",
    })
    .success(function(data) {
        if(typeof(data) == "undefined" || data === null || data.count === 0)
        {
            alert("Non sono state trovate corse con i filtri selezionati");
        }else{
            if (data._id>-1) {
                data.end_trip = (typeof data.end_trip == 'string') ? data.end_trip : data.end_trip.date;
                data.begin_trip = (typeof data.begin_trip == 'string') ? data.begin_trip : data.begin_trip.date;

                data.end_trip = moment(data.end_trip).format("X");
                data.begin_trip = moment(data.begin_trip).format("X");

                var duration = Math.round((data.end_trip - data.begin_trip)/60);
                
                data.VIN = data.VIN ? data.VIN : data.vin;

                $('#carplate').val(data.VIN);
                $('#duration').val(duration);
                $('#date-beg').val( moment(data.begin_trip*1000).format("DD/MM/YYYY HH:mm"));
                $('#date-end').val( moment(data.end_trip*1000).format("DD/MM/YYYY HH:mm"));

                if (typeof(data.points) != "undefined" && data.points !== null){
                    $('#pointsnumber').val(data.points);
                }
            }

            $("#trips").html($.oe.items.join(""));

            $.oe.fn.getTripsData(function(){
                $.oe.fn.zoomOnTrip(data._id);

                if ($('#pointsnumber').val() == ''){
                    $('#pointsnumber').val( $.oe.features[0].getGeometry().v.length / 4 );
                }
            });
        }
    });
}

$.oe.fn.loadTracks = function() 
{
    $.oe.fn.deactiveListActions();
    $.oe.fn.deactiveMapInteraction();
    
    // Get The date from the DateTime Input
    $.oe.urlDate    = $("div.date input").val();
    
    // Get the number of trips to load
    $.oe.tripsnumber    = $("#ex6").slider().val();
    
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
            end_date:           $.oe.urlDate,
            trips_number:       $.oe.tripsnumber,
            maintainer:         $.oe.maintainer
        }
    })
    .success(function(data) {
        if(typeof(data) == "undefined" || data === null || data.count === 0)
        {
            alert("Non sono state trovate corse con i filtri selezionati");

            // Enable the Data Update Button
            $("button#dataupdate").prop('disabled', false);

            // Bind its action
            $("button#dataupdate").one('click','',function(){
                $.oe.fn.loadTracks();
            });
        }else{
            $.each(data, function(key, val)
            {
                if (val._id>-1) {

                    val.end_trip     = (typeof val.end_trip         == 'string')     ? val.end_trip         : val.end_trip.date;
                    val.begin_trip  = (typeof val.begin_trip    == 'string')     ? val.begin_trip     : val.begin_trip.date;

                    val.end_trip     = moment(val.end_trip).format("X");
                    val.begin_trip     = moment(val.begin_trip).format("X");

                    var duration = Math.round((val.end_trip - val.begin_trip)/60);

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
                }
            });
            
            $("#trips").html($.oe.items.join(""));
            
            $.oe.fn.getTripsData();
        }
    });

    //newTrack(features, "way_J10_vers_albine", "", "http://core.sharengo.it/ui/log-data.php?id_trip=4410");
};
console.log("TUATTA");
$.oe.fn.getTripsData = function(callback) 
{
    $.ajax({
        method:            'POST',
        url:            '/reports/api/get-trip-points-from-logs',
        processData:    true,
        dataType:        'xml',
        data: {
            trips_id:         $.oe.trips
        },
        timeout: 180000        // Timeout = 3 minutes (2 minutes ~= 300 tracks)
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
        var xmlDoc = jsonXHR.responseXML;

        $.oe.features = [];
        $.oe.features = format.readFeatures(xmlDoc,  {featureProjection: 'EPSG:3857'});

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
        $("button#dataupdate").one('click','',function(){
            $.oe.fn.loadTracks();
        });
        
        // Activate the list of trips
        $.oe.fn.activateListActions();
        
        // Activate the map
        $.oe.fn.activeMapInteraction();
        
        typeof callback === 'function' && callback();
    });
};


// Window Resize Action Bind
var id;
$(window).resize(function() {
    clearTimeout(id);
    id = setTimeout($.oe.fn.doneResizing, 500);
});


$.oe.fn.doneResizing = function() {
    var newHeight             = $(window).height();
    $(".row.mainrow").css("height", newHeight -280); //-110);
    $(".map").css("height", newHeight -280);
    $.oe.map.updateSize();
};

$.oe.fn.rebluildGUI = function() {
    
};