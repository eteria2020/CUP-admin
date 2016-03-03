/* global translate */
// Define Glbal vars
/* global  d3:true, dc:true, crossfilter:true, ol:true $ document window setTimeout */

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

///////////// $.oe Vars Definition /////////////
$.oe.today = new Date();
$.oe.aMonthAgo = new Date(); $.oe.aMonthAgo.setMonth($.oe.today.getMonth() - 1);

$.oe.todayFormatted = $.oe.today.getFullYear() + "-" +
    ("0" + ($.oe.today.getMonth() + 1)).slice(-2) + "-" +
    ("0" + $.oe.today.getDate()).slice(-2);
$.oe.aMonthAgoFormatted = $.oe.aMonthAgo.getFullYear() + "-" +
    ("0" + ($.oe.aMonthAgo.getMonth() + 1)).slice(-2) + "-" +
    ("0" + $.oe.aMonthAgo.getDate()).slice(-2);

$.oe.params = {
    dateFrom: $.oe.aMonthAgoFormatted,
    dateTo: $.oe.todayFormatted,
    begend: 0,     // 0 ==> Beginning Hour ||  1 ==> Ending Hour
    weight: 0.192,
    baseWeight: 0.4
};

// Set the timeout needed for the page resize bind function
$.oe.timeout = 0;

// Set the vector source; will contain the map data
$.oe.vectorSource = {};

// Set the features collection
$.oe.features = {};

// The base map layer
$.oe.osmLayer = {};
////////////////////////////////////////////////

// The magic!
$(document).ready(function(){
    $.oe.fn.getCityData($.oe.fn.createButtons);
    $.oe.fn.createDataPicker();

    // Setting Up the star date to a month ago.
    $("#datepicker #end").val( $.oe.todayFormatted );
    $("#datepicker #start").val( $.oe.aMonthAgoFormatted );
});

$(window).load(function() {
    $.oe.fn.doneResizing();

    // Bind map move
    $.oe.map.on("moveend", $.oe.fn.zoomChanged);
});


$.oe.fn.createButtons = function(){
    $.each($.oe.city, function(key, val){
        // Create and populate the city prop
        val.ol = {};
        val.ol.coordinate = ol.proj.fromLonLat([val.params.center.longitude, val.params.center.latitude]);

        // Create a button for every city
        $("#header-buttons").prepend(
            "<button type=\"button\" class=\"btn btn-default\" id=\"pan-to-\"" +
            val.fleet_code +
            "\">" + translate("Spostati su") + " " +
            val.fleet_name +
            "</button>"
        );


        // Handle the click action for every city button
        $("#pan-to-" + val.fleet_code).click(function()
        {
            var pan = ol.animation.pan({
                duration: 2000,
                source: $.oe.view.getCenter()
            });
            $.oe.map.beforeRender(pan);
            $.oe.view.setCenter(val.ol.coordinate);
            $.oe.view.setZoom(12);
        });
    });
};

$.oe.vectorSource = new ol.source.Vector({
    extractStyles: false,
    projection: "EPSG:3857",
    loader:
        function(extent, resolution, projection) {
            $.ajax({
                method: "POST",
                url: "/reports/api/get-trips-geo-data",
                data: {
                    start_date: $.oe.params.dateFrom,
                    end_date: $.oe.params.dateTo,
                    begend: $.oe.params.begend
                },
                dataType: "json",
                beforeSend: function() {
                    $.oe.fn.deactiveMapInteraction();
                }
            })
            .success(function(response) {
                var format = new ol.format.GeoJSON();

                $.oe.features = format.readFeatures(
                    response,
                    {featureProjection: projection}
                );

                $.oe.vectorSource.addFeatures($.oe.features);

                // Determino il numero di elementi caricati
                $("#element-counter input").val($.oe.features.length);
            })
            .complete(function() {
                $.oe.fn.activeMapInteraction();
            });
        },
    format: new ol.format.GeoJSON()
});


// This functions will change the source loader params to pass to the url
// so we can make a specific ajax call
function changeFilterDateFrom(dateFrom) {
    $.oe.params.dateFrom = dateFrom;
    $.oe.vectorSource.clear(true);
}
function changeFilterDateTo(dateTo){
    $.oe.params.dateTo = dateTo;
    $.oe.vectorSource.clear(true);
}
function changeFilterBegEnd(begend){
    $.oe.params.begend = begend;
    $.oe.vectorSource.clear(true);
}


$.oe.layerHeatmap = new ol.layer.Heatmap({
    source: $.oe.vectorSource,
    radius: 12,
    opacity: 0.7,
    blur: 14,
    weight:
        function() {
            return $.oe.params.baseWeight + $.oe.params.weight;
        }
});

$.oe.view = new ol.View({
    // the view"s initial state
    center: ol.proj.transform([9.185, 45.465], "EPSG:4326", "EPSG:3857"),
    zoom: 12
});

$.oe.osmLayer = new ol.layer.Tile({
    source: new ol.source.OSM()
});

$.oe.map = new ol.Map({
    layers: [$.oe.osmLayer, $.oe.layerHeatmap],
    target: "map",
    view: $.oe.view,
    eventListeners: {"zoomend": $.oe.fn.zoomChanged}
});

$.oe.fn.zoomChanged = function(){
    var zoom = $.oe.map.getView().getZoom();

    if ($.oe.lastZoom !== zoom) {
        $.oe.layerHeatmap.setRadius(zoom * 1.0);
        $.oe.params.weight = (0.4 * zoom / 25);
        $.oe.lastZoom = zoom;
    }
};

var cnt = 0;
$.oe.fn.animate = function() {
    $.oe.params.weight = 0.1 * cnt;
    $.oe.layerHeatmap.getSource().changed();
    //map.renderSync();
    cnt++;
    if (cnt > 10) {
        cnt = 0;
    }
};


$("#weight").slider({
    formatter: function(value) {
        $.oe.params.baseWeight = value / 10;
        $.oe.layerHeatmap.getSource().changed();
        return "" + value / 10;
    }
});

$("#change-begend").click(function(){
    $(this).text(function(i, text){
        return text === translate("Passa a Posizione di Arrivo") ? translate("Passa a Posizione di Partenza") : translate("Passa a Posizione di Arrivo");
    });

    changeFilterBegEnd($.oe.params.begend === 0 ? 0 : 1);
});


$.oe.fn.createDataPicker = function(){
    $(".input-daterange").datepicker({
        format: "yyyy-mm-dd",
        language: "it",
        end_date: $.oe.today,
        orientation: "bottom auto",
        autoclose: true
    });

    $(".input-daterange")
        .datepicker()
        .on("changeDate", function(e) {
            if (e.target.id === "start"){
                // id = start
                changeFilterDateFrom($(e.target).val());
            }else{
                // id = end
                changeFilterDateTo($(e.target).val());
            }
        });
};

$.oe.fn.activeMapInteraction = function(){
    $("div#over").remove();
};

$.oe.fn.deactiveMapInteraction = function(){
    $(".map").after("<div id=\"over\"><span class=\"glyphicon glyphicon-refresh glyphicon-refresh-animate\" aria-hidden=\"true\"></span></div>");
};

// Window Resize Action Bind
var id;
$(window).resize(function() {
    clearTimeout(id);
    id = setTimeout($.oe.fn.doneResizing, 500);
});


$.oe.fn.doneResizing = function(){
    var newHeight = $(window).height();
    $(".row.mainrow").css("height", newHeight - 280); //-110);
    $(".map").css("height", newHeight - 280);
    $.oe.map.updateSize();
};
