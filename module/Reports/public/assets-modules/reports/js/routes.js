// Define Glbal vars
/* global translate:true, moment:true, d3:true, dc:true, crossfilter:true, ol:true, moment:true, tripid:true $ document window clearTimeout setTimeout */

// Check if var $.oe has been declared
if (typeof $.oe === "undefined") {
    $.extend({
        oe: {}
    });
}

$.ajaxSetup({
    async: true,
    cache: false,
    timeout: 180000,        // set to 2minutes
    queue: false/*,
    error: function (msg) {
        alert("error : " + msg.d);
    }*/
});

$.extend($.scrollTo.defaults, {
    axis: "y",
    duration: 500,
    interrupt: true         //  If true will cancel the animation if the user scrolls. Default is false
    //easing: "linear"
});


///////////// $.oe Vars Definition /////////////
$.oe.today = new Date();
$.oe.todayFormatted = $.oe.today.getFullYear() + "-" +
    ("0" + ($.oe.today.getMonth() + 1)).slice(-2) + "-" +
    ("0" + $.oe.today.getDate()).slice(-2);

$.oe.urlDate = "";

// Set the vector source; will contain the map data
$.oe.vectorSource = {};

// Set the features collection
$.oe.tracks = {};

// The collection of features selected
$.oe.featureOverlaySource = {};

/// The loaded trips (id)
$.oe.trips = [];

// The HTML <li> of the trips
$.oe.items = [];

// The loaded tracks (features)
$.oe.features = [];

// Flag to trigger the map mousehover listener
$.oe.listenHover = true;

// Flag to trigger the map click listener
$.oe.listenClick = true;

// The number of trips to load
$.oe.tripsnumber = 0;

// Flag to get the maintainer trips or the customers one
// true = maintainers | false = clients
$.oe.maintainer = false;

// Flag to change view center on carTooltip move
$.oe.fixView = false;

// Object created to use on single track.
// Useful to calculate the ratio for track / maptrack.
$.oe.time = {
    start: Infinity,
    stop: -Infinity,
    duration: 0
};

// The Car tooltip
$.oe.tooltip = {};

// Set the costant of used space by the toolbars
// This value will be subtract to the total page height.
$.oe.pageHeightReduce = -255;
////////////////////////////////////////////////

$(document).ready(function()
{
    // DateTime Picker
    $("#datetimepicker1").datetimepicker({
        sideBySide: true,
        maxDate: Date(),
        defaultDate: Date(),
        format: "YYYY-MM-DD HH:mm:ss"
    });

    // Create The Slider
    $("#ex6").slider();

    // Listen to Slider Change Value (Ther"s also the on("slide" bind, that listen
    // only the slide action, not also the click on a specific section of the
    // slidebar.
    $("#ex6").on("change", function(slideEvt) {
        $("#ex6SliderVal").text(slideEvt.value.newValue + " "+ translate("corse precedenti a"));
    });


    // Init Bootstrap Switch
    $("#maintainer")
        .bootstrapSwitch()
        .on("switchChange.bootstrapSwitch",
            function(event, state) {
                $.oe.maintainer = !state; // true | false
            });
});

$(window).load(function() {

    $.oe.fn.getCityData();

    if (typeof tripid !== "undefined") {
        // If ther"s only one trip
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

// The MAP
var OSM = new ol.layer.Tile({
    source: new ol.source.OSM()
});


var style = {
    "Point": [new ol.style.Style({
        image: new ol.style.Circle({
            fill: new ol.style.Fill({
                color: "rgba(255,255,0,0.4)"
            }),
            radius: 5,
            stroke: new ol.style.Stroke({
                color: "#ff0",
                width: 1
            })
        })
    })],
    "LineString": [new ol.style.Style({
        stroke: new ol.style.Stroke({
            color: "#f00",
            width: 3
        })
    })],

    // Tracks
    "MultiLineString": [new ol.style.Style({
        stroke: new ol.style.Stroke({
            color: "rgba(30,140,0,0.7)",
            width: 5
        })
    })]
};

var hoverstyle = {
    "Point": [new ol.style.Style({
        image: new ol.style.Circle({
            fill: new ol.style.Fill({
                color: "rgba(255,255,0,0.4)"
            }),
            radius: 5,
            stroke: new ol.style.Stroke({
                color: "#ff0",
                width: 1
            })
        })
    })],
    "LineString": [new ol.style.Style({
        stroke: new ol.style.Stroke({
            color: "#f00",
            width: 6
        })
    })],

    // Tracks
    "MultiLineString": [new ol.style.Style({
        stroke: new ol.style.Stroke({
            color: "rgba(140,30,0,0.8)",
            width: 6
        })
    })]
};


$.oe.vectorSource = new ol.source.Vector({
    projection: "EPSG:3857",
    format: new ol.format.GPX()
});

$.oe.tripslayer = new ol.layer.Vector({
    source: $.oe.vectorSource,
    style:
        function(feature) {
            return style[feature.getGeometry().getType()];
        }
});

var view = new ol.View({
    // the view"s initial state
    center: ol.proj.transform([9.185, 45.465], "EPSG:4326", "EPSG:3857"),
    zoom: 12
});

$.oe.map = new ol.Map({
    layers: [OSM, $.oe.tripslayer],
    target: document.getElementById("map"),
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
$.oe.map.on("pointermove", function(evt) {
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
$.oe.map.on("click", function(evt) {
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

        for (i = 0; i < selectedfeatures.length; ++i) {
            info.push(selectedfeatures[i].get("name"));

            $.oe.fn.changeTrackColor(selectedfeatures[i]);
        }

        //get the selected <li> track of the first selected
        var element = $("#" + selectedfeatures[0].id);

        // Remove the "green color class" to the others <li>
        $(".way:not(#" + selectedfeatures[0].id + ")").removeClass("list-group-item-success");

        // If the feature is not selceted, the we select it
        if (!element.hasClass("list-group-item-success")) {
            element.removeClass("list-group-item-danger");
            element.addClass("list-group-item-success");

            element.parent().stop();
            element.parent().scrollTo(element);
        }


        document.getElementById("info").innerHTML = info.join(", ") || "(unknown)";
        $.oe.map.getTarget().style.cursor = "pointer";
    } else {
        document.getElementById("info").innerHTML = "&nbsp;";
        $.oe.map.getTarget().style.cursor = "";
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
    $("button#hoverenable").removeClass("btn-default");
    $("button#hoverenable").addClass("btn-success");

    $("button#hoverenable").click(
        function () {
            if($.oe.listenHover){
                $.oe.listenHover = false;

                $("button#hoverenable").removeClass("btn-success");
                $("button#hoverenable").addClass("btn-danger");
            }else{
                $.oe.listenHover = true;

                $("button#hoverenable").addClass("btn-success");
                $("button#hoverenable").removeClass("btn-danger");
            }
        }
    );
};

$.oe.fn.activateFixView = function()
{
    $("button#fixview").removeClass("btn-default");
    $("button#fixview").addClass("btn-danger");

    $("button#fixview").click(
        function () {
            if($.oe.fixView){
                $.oe.fixView = false;

                $("button#fixview").removeClass("btn-success");
                $("button#fixview").addClass("btn-danger");
            }else{
                $.oe.fixView = true;

                $("button#fixview").addClass("btn-success");
                $("button#fixview").removeClass("btn-danger");
            }
        }
    );
};


$.oe.fn.activeMapInteraction = function(){
    $("div#over").remove();
};

$.oe.fn.deactiveMapInteraction = function(){
    $(".map").after("<div id=\"over\"><span class=\"glyphicon glyphicon-refresh glyphicon-refresh-animate\" aria-hidden=\"true\"></span></div>");
};

$.oe.fn.deactiveListActions = function()
{
    // Set it as disabled
    $("#steps").attr("disabled", true);

    // Remove other bind events();
    $(".way").off();
};


$.oe.fn.activateListActions = function()
{
    // Remove other bind events();
    $(".way").off();

    // Set it as enabled
    $("#steps").attr("disabled", false);

    // Add hover function to highlight tracks
    $(".way").hover(
        function () {
            if (!$.oe.listenHover){
                return;
            }

            var thisId = $(this).attr("id");

            // Remove the "green color class" to the others <li>
            $(".way:not(#" + thisId + ")").removeClass("list-group-item-success");

            var f = $.grep( $.oe.features, function(e){ return parseInt(e.id) === parseInt(thisId); });

            if(f.length !== 1 ){
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
            if (!$.oe.listenClick){
                return;
            }

            var thisId = $(this).attr("id");

            // Remove the "green color class" to the others <li>
            $(".way:not(#" + thisId + ")").removeClass("list-group-item-success");

            var f = $.grep( $.oe.features, function(e){ return parseInt(e.id) === parseInt(thisId); });

            if(f.length !== 1 ){
                $(this).addClass("list-group-item-danger");
            }else{
                $(this).removeClass("list-group-item-danger");
                $(this).addClass("list-group-item-success");
                var extent = f[0].getGeometry().getExtent();
                $.oe.map.getView().fit(extent, $.oe.map.getSize());
                $.oe.fn.changeTrackColor(f[0]);
            }
        }
    );
};

$.oe.fn.zoomOnTrip = function(tripId)
{
    var f = $.grep( $.oe.features, function(e){ return parseInt(e.id) === parseInt(tripId); });
    if(f.length > 0 ){
        var extent = f[0].getGeometry().getExtent();
        $.oe.map.getView().fit(extent, $.oe.map.getSize());
    }
};

///////////////// LOAD DATA /////////////////

/**
 *  This complex func, load a single Track
 *  It"s called when the page has a "/tripid" appendix
 */
$.oe.fn.loadSingleTrack = function()
{
    $.oe.items = [];

    // Create new Input containing the trips info
    $(".btn-toolbar").html("<div class=\"col-md-12 row\"><div class=\"col-md-2\"><div class=\"input-group\"><span class=\"input-group-addon\">" + translate("Targa Veicolo") + "</span><input type=\"text\" class=\"form-control\" id=\"carplate\" aria-describedby=\"basic-addon3\"></div></div><div class=\"col-md-2\"><div class=\"input-group\"><span class=\"input-group-addon\">" + translate("Durata") + " (hh:mm:ss)</span><input type=\"text\" class=\"form-control\" id=\"duration\" aria-describedby=\"basic-addon3\"></div></div><div class=\"col-md-2\"><div class=\"input-group\"><span class=\"input-group-addon\">" + translate("Numero Punti") + "</span><input type=\"text\" class=\"form-control\" id=\"pointsnumber\" aria-describedby=\"basic-addon3\"></div></div><div class=\"col-md-3\"><div class=\"input-group\"><span class=\"input-group-addon\">" + translate("Data Inizio Corsa") + "</span><input type=\"text\" class=\"form-control\" id=\"date-beg\" aria-describedby=\"basic-addon3\"></div></div><div class=\"col-md-3\"><div class=\"input-group\"><span class=\"input-group-addon\">" + translate("Data Fine Corsa") + "</span><input type=\"text\" class=\"form-control\" id=\"date-end\" aria-describedby=\"basic-addon3\"></div></div></div>");

    // Remove unneeded DOM Elements
    $(".col-md-2.rightbar").remove();
    $(".col-md-10.leftbar").prop("class", "col-md-12");

    // Set new page title
    $(".page-header h1").html(translate("Itinerario") + " " + $.oe.trips[0]);

    // Set the new page height reduce (because we will load more rows on toolbar)
    $.oe.pageHeightReduce = -305;

    $.oe.fn.doneResizing();

    $.ajax({
        method: "POST",
        url: "/reports/api/get-trip/" + $.oe.trips[0],
        dataType: "json"
    })
    .success(function(data) {
        if(typeof data === "undefined" || data === null || data.count === 0)
        {
            alert(translate("Non sono state trovate corse con i filtri selezionati"));
        } else {
            if (data._id > -1) {
                data.end_trip = (typeof data.end_trip === "string") ? data.end_trip : data.end_trip.date;
                data.begin_trip = (typeof data.begin_trip === "string") ? data.begin_trip : data.begin_trip.date;

                data.end_trip = moment(data.end_trip).format("X");
                data.begin_trip = moment(data.begin_trip).format("X");

                var duration = moment(data.end_trip * 1000).subtract(moment(data.begin_trip * 1000)).format("HH:mm:ss");

                data.VIN = data.VIN ? data.VIN : data.vin;

                $("#carplate").val(data.VIN);
                $("#duration").val(duration);
                $("#date-beg").val( moment(data.begin_trip * 1000).format("DD/MM/YYYY HH:mm"));
                $("#date-end").val( moment(data.end_trip * 1000).format("DD/MM/YYYY HH:mm"));

                if (typeof data.points !== "undefined" && data.points !== null){
                    $("#pointsnumber").val(data.points);
                }
            }

            $("#trips").html($.oe.items.join(""));

            $.oe.fn.getTripsData(function(){
                // Deactive Uneeded functions
                $.oe.fn.deactiveListActions();

                // Zoom Map to the unique loaded track
                $.oe.fn.zoomOnTrip(data._id);

                // If the trips point number is not passed trought JSON data
                // we obtain it from feature obj.
                if ($("#pointsnumber").val() === ""){
                    $("#pointsnumber").val( $.oe.features[0].getGeometry().v.length / 4 );
                }

                $.oe.time.start = ($.oe.features[0].getGeometry().getFirstCoordinate()[3]) * 1000;
                $.oe.time.stop = ($.oe.features[0].getGeometry().getLastCoordinate()[3]) * 1000;
                $.oe.time.duration = $.oe.time.stop - $.oe.time.start;

                $(".btn-toolbar").append("<div class=\"col-md-12 secondrow row\"><div class=\"col-md-2 controls\"><div class=\"input-group\"><span class=\"input-group-addon\">" + translate("Controlli") + "</span><div class=\"btn-group\" role=\"group\" aria-label=\"\"><button type=\"button\" class=\"btn btn-default stop\" aria-label=\"" + translate("Ferma") + "\"><span class=\"glyphicon glyphicon glyphicon-stop\" aria-hidden=\"true\"></span></button><button type=\"button\" class=\"btn btn-default pause\" aria-label=\"" + translate("Pausa") + "\"><span class=\"glyphicon glyphicon-pause\" aria-hidden=\"true\"></span></button><button type=\"button\" class=\"btn btn-default play\" aria-label=\"" + translate("Avvia") + "\"><span class=\"glyphicon  glyphicon-play\" aria-hidden=\"true\"></span></button></div></div></div><div class=\"col-md-2\"><div class=\"input-group\"><span class=\"input-group-addon\">" + translate("Velocit&agrave;") + " m/s</span><input type=\"text\" class=\"form-control\" id=\"current-speed-ms\" aria-describedby=\"basic-addon3\"></div></div><div class=\"col-md-2\"><div class=\"input-group\"><span class=\"input-group-addon\">" + translate("Velocit&agrave;") + " Km/h</span><input type=\"text\" class=\"form-control\" id=\"current-speed-km\" aria-describedby=\"basic-addon3\"></div></div><div class=\"col-md-3\"><div class=\"input-group\" id=\"ex7CurrentSliderValLabel\"><span class=\"input-group-addon\">" + translate("Data Rilevazione Punto") + "</span><input type=\"text\" class=\"form-control\" id=\"ex7SliderVal\" aria-describedby=\"basic-addon3\"></div></div></div><div class=\"col-md-12 thirdrow row\"><div class=\"col-md-10 dataslider tripsprogression\"><input id=\"ex7\" type=\"text\" data-slider-min=\"0\" data-slider-max=\"100000\" data-slider-step=\"1\" data-slider-value=\"0\"/></div><div class=\"col-md-2\"><button id=\"fixview\" type=\"button\" class=\"btn btn-default\" aria-label=\"\">" + translate("Blocca Vista su Auto") + "<span class=\" glyphicon glyphicon-magnet\" aria-hidden=\"true\"></span></button></div></div>");

                // Create The Slider
                $("#ex7").slider({
                    formatter: function(value) {
                        var mi = ($.oe.time.duration / 100000) * value;
                        var date = moment().startOf("day").add(mi, "milliseconds");
                        return translate("Durata Corsa") + ": " + date.format("HH:mm:ss");
                    }
                });

                // Create the tooltop
                $.oe.fn.createCarTooltip();

                // Stop hover action
                $.oe.listenHover = false;
                $.oe.listenClick = false;

                // Activate FifxView buttons
                $.oe.fn.activateFixView();

                $.oe.tripslayer.setStyle(
                    new ol.style.Style({
                        stroke: new ol.style.Stroke({
                            color: "rgba(110, 69, 255, 0.80)",
                            width: 6
                        })
                    })
                );

                // Listen to Slider Change Value (Ther"s also the on("slide" bind, that listen
                // only the slide action, not also the click on a specific section of the
                // slidebar.
                $("#ex7").on("change", function(slideEvt) {
                    var time = $.oe.fn.getTimeOfTripPercentage(slideEvt.value.newValue / 1000);
                    $.oe.fn.setcarTooltipPosition(time.format("X"));

                    $("#ex7SliderVal").val(time.format("DD/MM/YYYY HH:mm:ss"));

                    $.oe.tooltip.carTooltipElement.innerHTML = time.format("HH:mm:ss");

                    var ms = $.oe.fn.getSpeed(time);
                    var kmh = Math.round((ms * 18) / 5);

                    $("#current-speed-ms").val(ms);
                    $("#current-speed-km").val(kmh);
                });

                $.oe.fn.createTimer();
                $.oe.fn.activateTimerButtons();
            });
        }
    });
};

/**
 *  This complex func, load more tracks
 */
$.oe.fn.loadTracks = function()
{
    $.oe.fn.deactiveListActions();
    $.oe.fn.deactiveMapInteraction();

    // Get The date from the DateTime Input
    $.oe.urlDate = $("div.date input").val();

    // Get the number of trips to load
    $.oe.tripsnumber = $("#ex6").slider().val();

    // Disable the Data Update Button to prevent error
    $("button#dataupdate")
        .prop("disabled", true)
        .removeClass("btn-default")
        .addClass("btn-warning");
    $("button#dataupdate span")
        .addClass("glyphicon-refresh-animate");

    $.oe.trips = [];
    $.oe.items = [];

    $.ajax({
        method: "POST",
        url: "/reports/api/get-trips",
        dataType: "json",
        data: {
            end_date: $.oe.urlDate,
            trips_number: $.oe.tripsnumber,
            maintainer: $.oe.maintainer
        }
    })
    .success(function(data) {
        if(typeof data === "undefined" || data === null || data.count === 0)
        {
            alert(translate("Non sono state trovate corse con i filtri selezionati"));

            // Enable the Data Update Button
            $("button#dataupdate").prop("disabled", false);

            // Bind its action
            $("button#dataupdate").one("click", "", function(){
                $.oe.fn.loadTracks();
            });
        }else{
            $.each(data, function(key, val)
            {
                if (val._id > -1) {

                    val.end_trip = (typeof val.end_trip === "string") ? val.end_trip : val.end_trip.date;
                    val.begin_trip = (typeof val.begin_trip === "string") ? val.begin_trip : val.begin_trip.date;

                    val.end_trip = moment(val.end_trip).format("X");
                    val.begin_trip = moment(val.begin_trip).format("X");

                    var duration = Math.round((val.end_trip - val.begin_trip) / 60);

                    $.oe.trips.push(val._id);

                    val.VIN = val.VIN ? val.VIN : val.vin;

                    var date = moment(val.begin_trip * 1000).format("DD/MM/YYYY HH:mm");

                    var li = "<li href=\"#\" class=\"list-group-item way\" id=\"" + val._id +
                        "\"><div class=\"open-button\"><a type=\"button\" href=\"" +
                        window.location.href + "/" + val._id +
                        "\" class=\"btn btn-default\" aria-label=\"Left Align\" role=\"button\">" + translate("Apri") +
                        "<span class=\"glyphicon glyphicon-screenshot\" aria-hidden=\"true\"></span></a></div>" +
                        "<h5 class=\"list-group-item-heading\">" + date +
                        " <b>" + val.VIN + "</b></h5><p class=\"list-group-item-text\">" +
                        val._id + " " + duration + "min";

                    if (typeof val.points !== "undefined" && val.points !== null) {
                        li += " (" + val.points + ")";
                    }

                    li += "</p></li>";

                    $.oe.items.push(li);
                }
            });

            $("#trips").html($.oe.items.join(""));

            $.oe.fn.getTripsData();
        }
    });

    //newTrack(features, "way_J10_vers_albine", "", "http://core.sharengo.it/ui/log-data.php?id_trip=4410");
};

/**
 *  This complex func, load the GPX file containing all the tracks
 *
 *  @param function  callback  If it"s a valid function, callback()
 *                             will be excetued at the end
 */
$.oe.fn.getTripsData = function(callback)
{
    $.ajax({
        method: "POST",
        url: "/reports/api/get-trip-points-from-logs",
        processData: true,
        dataType: "xml",
        data: {
            trips_id: $.oe.trips
        },
        timeout: 180000 // Timeout = 3 minutes (2 minutes ~= 300 tracks)
    })
    .success(function(data, status, jsonXHR) {
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
        $.oe.features = format.readFeatures(xmlDoc, {featureProjection: "EPSG:3857"});

        for (var i = 0; i < $.oe.features.length; i++) {
            var trackFeature = $.oe.features[i];

            trackFeature.id = trackFeature.get("name");
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
        $("button#dataupdate")
            .prop("disabled", false)
            .removeClass("btn-warning")
            .addClass("btn-default");

        $("button#dataupdate span")
            .removeClass("glyphicon-refresh-animate");


        // Bind its action
        $("button#dataupdate").one("click", "", function(){
            $.oe.fn.loadTracks();
        });

        // Activate the list of trips
        $.oe.fn.activateListActions();

        // Activate the map
        $.oe.fn.activeMapInteraction();

        if (typeof callback === "function") {
            callback();
        }
    });
};



/**
 * Creates a new help tooltip
 */
$.oe.fn.createCarTooltip = function() {
    /**
     * The car icon of the tooltip
     * @type {olx.style.IconOptions}
     */
    $.oe.tooltip.carIcon = new ol.style.Icon({
        anchor: [0.5, 1],
        anchorXUnits: "fraction",
        anchorYUnits: "fraction",
        opacity: 0.75,
        scale: 0.07,
        src: "/img/car-icon.png"
    });

    /**
     * The car pint of the tooltip
     * @type {olx.style.Circle}
     */
    $.oe.tooltip.carPoint = new ol.style.Circle({
        radius: 2,
        fill: new ol.style.Fill({
            color: "rgba(255,0,0,0.9)"
        }),
        stroke: null
    });

    /**
    * Overlay to show the help messages.
    * @type {ol.Vector}
    */
    $.oe.tooltip.featureOverlay = new ol.layer.Vector({
        source: new ol.source.Vector(),
        map: $.oe.map,
        style:
        [
            new ol.style.Style({
                image: $.oe.tooltip.carIcon
            }),
            new ol.style.Style({
                image: $.oe.tooltip.carPoint
            })
        ]
    });

    if ($.oe.tooltip.carTooltipElement) {
        $.oe.tooltip.carTooltipElement.parentNode.removeChild($.oe.tooltip.carTooltipElement);
    }
    $.oe.tooltip.carTooltipElement = document.createElement("div");
    $.oe.tooltip.carTooltipElement.className = "tooltip point-tooltip";
    $.oe.tooltip.carTooltip = new ol.Overlay({
        element: $.oe.tooltip.carTooltipElement,
        offset: [15, 0],
        positioning: "center-left"
    });
    $.oe.map.addOverlay($.oe.tooltip.carTooltip);
};

$.oe.fn.createTimer = function() {
    $.timer("timer", function() {
        var newval = parseInt($("#ex7").val()) + 50;

        if(newval >= 100000){
            $.timer("timer").stop();
            return;
        }

        $("#ex7").slider("setValue", newval);

        var time = $.oe.fn.getTimeOfTripPercentage(newval / 1000);
        $.oe.fn.setcarTooltipPosition(time.format("X"));

        $("#ex7SliderVal").val(time.format("DD/MM/YYYY HH:mm:ss"));
        $.oe.tooltip.carTooltipElement.innerHTML = time.format("HH:mm:ss");

        var ms = $.oe.fn.getSpeed(time);
        var kmh = Math.round((ms * 18) / 5);

        $("#current-speed-ms").val(ms);
        $("#current-speed-km").val(kmh);
    }, 1, {timeout: 100000000});
};

$.oe.fn.activateTimerButtons = function(){
    // Play button
    $("div.controls button.play").on("click", function(){
        if($.timer("timer").status() === "paused"){
            $.timer("timer").resume();
        } else {
            $.timer("timer").start();
        }
    });

    // Pause Button
    $("div.controls button.pause").on("click", function(){
        $.timer("timer").pause();
    });

    // Stop Button
    $("div.controls button.stop").on("click", function(){
        // If the timer is in pause, we can"t stop it. So we resume it.
        if ($.timer("timer").status() === "paused") {
            $.timer("timer").resume();
        }

        $.timer("timer").stop();
        $("#ex7").slider("setValue", 0);

        var time = $.oe.fn.getTimeOfTripPercentage(0);
        $.oe.fn.setcarTooltipPosition(time.format("X"));

        $("#ex7SliderVal").val(time.format("DD/MM/YYYY HH:mm:ss"));
        $.oe.tooltip.carTooltipElement.innerHTML = time.format("HH:mm:ss");

        $("#current-speed-ms").val(0);
        $("#current-speed-km").val(0);
    });
};


/**
 * This func return the moment() (time) of
 * the trip at a specific percentage
 *
 * @param   int     percent  The percent value ( 0 <= percent <= 100 )
 * @return {moment}
 */
$.oe.fn.getTimeOfTripPercentage = function(percent){
    var step = ($.oe.time.duration / 100) * percent;
    return moment($.oe.time.start + step);
};



/**
 * This func return the speed (meters/second) of
 * a specific point, getting a time (moment object).
 * (It use the pointAtTime(pointTime - 1second)).
 *
 * @param   {moment}     pointTime  The time of a point to calculate the speed.
 * @return  float        the speed expressed in m/s
 */
$.oe.fn.getSpeed = function(pointTime){
    var geometry = /** @type {ol.geom.LineString} */ ($.oe.features[0].getGeometry());

    //var pointTime = $.oe.fn.getTimeOfTripPercentage(percent);
    var actualCoordinate = geometry.getCoordinateAtM(parseInt(pointTime.format("X")), true);
    var beforeCoordinate = geometry.getCoordinateAtM(parseInt(new moment(pointTime).subtract(1, "seconds").format("X")), true);

    // Check if this is the first point of trip
    if (actualCoordinate === geometry.getFirstCoordinate()) {
        return 0;
    }

    // Create a new segment
    var lineGeometry = new ol.geom.LineString([beforeCoordinate, actualCoordinate]);
    var length = 0;

    // get points of geometry (for simplicity assume 2 points)
    var lineCoordinates = lineGeometry.getCoordinates();

    // transform points from map projection (usually spherical mercator) to WGS84
    var mapProjection = $.oe.map.getView().getProjection();

    // create sphere to measure on
    var wgs84Sphere = new ol.Sphere(6378137); // one of WGS84 earth radius"

    for (var i = 0, ii = lineCoordinates.length - 1; i < ii; ++i) {
        var t1 = ol.proj.transform(lineCoordinates[i], mapProjection, "EPSG:4326");
        var t2 = ol.proj.transform(lineCoordinates[i + 1], mapProjection, "EPSG:4326");

        // get distance on sphere
        length += wgs84Sphere.haversineDistance(t1, t2);
    }

    // Calculate meters (with two decimals)
    return (Math.round(length * 100) / 100);
};

/**
 * This func change the position of the carTooltip on the $.oe.map
 *
 * @param   int     seconds  The second value (Unix format) of the
 *                           trips moment where the tooltip should
 *                           be placed
 *                  example  moment(stratime).format("x")
 */
$.oe.fn.setcarTooltipPosition = function(seconds) {
    var feature = $.oe.features[0];

    var geometry = /** @type {ol.geom.LineString} */ (feature.getGeometry());
    var coordinate = geometry.getCoordinateAtM(seconds, true);
    var highlight = feature.get("highlight");

    $.oe.tooltip.carTooltip.setPosition(coordinate);

    if (highlight === undefined) {
        highlight = new ol.Feature(new ol.geom.Point(coordinate));
        feature.set("highlight", highlight);
        $.oe.tooltip.featureOverlay.getSource().addFeature(highlight);
    } else {
        highlight.getGeometry().setCoordinates(coordinate);
    }

    if($.oe.fixView) {
        $.oe.map.getView().setCenter($.oe.tooltip.carTooltip.getPosition());
    }

    $.oe.map.render();
};


// Window Resize Action Bind
var id;
$(window).resize(function() {
    clearTimeout(id);
    id = setTimeout($.oe.fn.doneResizing, 500);
});


$.oe.fn.doneResizing = function() {
    var newHeight = $(window).height();
    $(".row.mainrow").css("height", newHeight + $.oe.pageHeightReduce);//-280); //-110);
    $(".map").css("height", newHeight + $.oe.pageHeightReduce);
    $.oe.map.updateSize();
};
