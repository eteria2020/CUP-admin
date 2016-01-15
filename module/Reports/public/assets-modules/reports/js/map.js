// Define Glbal vars
/* global thiscity:true, d3:true, dc:true, crossfilter:true, ol:true $ document window clearTimeout setTimeout */

$(function () {
    "use strict";

    var vector = new ol.layer.Heatmap({
            source: $.oe.vectorSource,
            radius: 12,
            opacity: 0.7,
            blur: 14,
            weight: function() {
                return $.oe.params.baseWeight + $.oe.params.weight;
            }
        }),
        view = new ol.View({
            // the view's initial state
            center: ol.proj.transform([9.185, 45.465], 'EPSG:4326', 'EPSG:3857'),
            zoom: 12
        }),
        OSM = new ol.layer.Tile({
            source: new ol.source.OSM()
        }),
        map,
        lastZoom,
        cnt = 0;

    function zoomChanged() {
        var zoom = map.getView().getZoom();

        if (lastZoom !== zoom) {
            vector.setRadius(zoom * 1.0);
            $.oe.params.weight = (0.4 * zoom / 25);
            lastZoom = zoom;
        }
    }

    map = new ol.Map({
        layers: [OSM, vector],
        target: 'map',
        view: view,
        eventListeners: {"zoomend": zoomChanged}
    });

    // Check if var $.oe has been declared
    if (typeof $.oe === 'undefined') {
        $.extend({
            oe: {}
        });
    }

    ///////////// $.oe Vars Definition /////////////
    $.oe.today = new Date();
    $.oe.aMonthAgo = new Date(); $.oe.aMonthAgo.setMonth($.oe.today.getMonth() - 1);

    $.oe.todayFormatted = $.oe.today.getFullYear() + '-' +
        ("0" + ($.oe.today.getMonth() + 1)).slice(-2) + '-' +
        ("0" + $.oe.today.getDate()).slice(-2);
    $.oe.aMonthAgoFormatted = $.oe.aMonthAgo.getFullYear() + '-' +
        ("0" + ($.oe.aMonthAgo.getMonth() + 1)).slice(-2) + '-' +
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
    ////////////////////////////////////////////////

    // The magic!
    $(document).ready(function(){
        $.oe.fn.getCityData($.oe.fn.createButtons);
    });

    $(window).load(function() {
        $.oe.fn.doneResizing();
    });


    $.oe.fn.createButtons = function(){
        $.each($.oe.city, function(key, val){
            // Create and populate the city prop
            val.ol = {};
            val.ol.coordinate = ol.proj.fromLonLat([val.params.center.longitude, val.params.center.latitude]);

            // Create a button for every city
            $('#header-buttons').prepend(
                '<button type="button" class="btn btn-default" id="pan-to-' +
                val.fleet_code +
                '">Pan to ' +
                val.fleet_name +
                '</button>'
            );

            // Handle the click action for every city button
            $("#pan-to-" + val.fleet_code).click(function()
            {
                var pan = ol.animation.pan({
                    duration: 2000,
                    source: view.getCenter()
                });
                map.beforeRender(pan);
                view.setCenter(val.ol.coordinate);
                view.setZoom(12);
            });

        });
    };

    // Setting Up the star date to a month ago.
    $("#datepicker #end").val( $.oe.todayFormatted );
    $("#datepicker #start").val( $.oe.aMonthAgoFormatted );

    $.oe.vectorSource = new ol.source.Vector({
        extractStyles: false,
        projection: 'EPSG:3857',
        loader:
            function(extent, resolution, projection) {
                $.ajax({
                    method: 'POST',
                    url: '/reports/api/get-trips-geo-data',
                    data: {
                        startDate: $.oe.params.dateFrom,
                        endDate: $.oe.params.dateTo,
                        begend: $.oe.params.begend
                    },
                    dataType: "json"
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

    map.on("moveend", zoomChanged);

    $('#weight').slider({
        formatter: function(value) {
            $.oe.params.baseWeight = value / 10;
            vector.getSource().changed();
            return '' + value / 10;
        }
    });

    $("#change-begend").click(function(){
        $(this).text(function(i, text){
            return text === "Change to Ending Location" ? "Change to Beginning Location" : "Change to Ending Location";
        });

        changeFilterBegEnd($.oe.params.begend === 0 ? 1 : 0);
    });

    $('.input-daterange').datepicker({
        format: "yyyy-mm-dd",
        language: "it",
        endDate: $.oe.today,
        orientation: "bottom auto",
        autoclose: true
    });

    $('.input-daterange')
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
        map.updateSize();
    };
});
