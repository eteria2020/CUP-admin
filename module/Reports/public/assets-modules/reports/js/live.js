// Define Glbal vars
/* global thiscity:true, d3:true, dc:true, crossfilter:true, ol:true $ document window setInterval clearTimeout setTimeout */

$(function () {
    "use strict";

    var fill = new ol.style.Fill({
            color: '#EDE431'
        }),
        stroke = new ol.style.Stroke({
            color: '#FF0000',
            width: 1.25
        }),
        iconStyle = new ol.style.Style({
            image: new ol.style.Circle({
                fill: fill,
                stroke: stroke,
                radius: 7
            }),
            fill: fill,
            stroke: stroke
        }),
        vectorSource = new ol.source.Vector({
            extractStyles: false,
            projection: 'EPSG:3857',
            format: new ol.format.GeoJSON()
        }),
        vector = new ol.layer.Vector({
            source: vectorSource,
            style: iconStyle
        }),
        view = new ol.View({
            // the view's initial state
            center: ol.proj.transform([9.185, 45.465], 'EPSG:4326', 'EPSG:3857'),
            zoom: 12
        }),
        OSM = new ol.layer.Tile({
            source: new ol.source.OSM()
        }),
        map = new ol.Map({
            layers: [OSM, vector],
            target: 'map',
            view: view,
            eventListeners: {"zoomend": $.oe.fn.zoomChanged}
        }),
        lastZoom,
        info = $('div#info'),
        id;

    // Check if var $.oe has been declared
    if (typeof $.oe === 'undefined') {
        $.extend({
            oe: {}
        });
    }

    ///////////// $.oe Vars Definition /////////////
    // Set the timeout needed for the page resize bind function
    $.oe.timeout = 0;
    ////////////////////////////////////////////////

    // The magic!
    $(document).ready(function(){
        $.oe.fn.getCityData($.oe.fn.createButtons);
    });

    $(window).load(function() {
        $.oe.fn.doneResizing();
        $.oe.fn.getCarsGeoData();
    });

    $.oe.fn.doneResizing = function(){
        var newHeight = $(window).height();
        $(".row.mainrow").css("height", newHeight - 280); //-110);
        $(".map").css("height", newHeight - 280);
        map.updateSize();
    };

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
            $("#pan-to-" + val.fleet_code).click(function(){
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

    $.oe.fn.zoomChanged = function(){
        var zoom = map.getView().getZoom();
        if (lastZoom !== zoom)
        {
            lastZoom = zoom;
        }
    };

    map.on("moveend", $.oe.fn.zoomChanged);

    info.tooltip({
        animation: true,
        trigger: 'manual'
    });

    $.oe.fn.displayFeatureInfo = function(pixel) {
        info.css({
            left: pixel[0] + 'px',
            top: (pixel[1] - 5) + 'px'
        });
        var feature = map.forEachFeatureAtPixel(pixel, function(innerFeature) {
            return innerFeature;
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
        $.oe.fn.displayFeatureInfo(map.getEventPixel(evt.originalEvent));
    });

    map.on('click', function(evt) {
        $.oe.fn.displayFeatureInfo(evt.pixel);
    });

    $.oe.fn.getCarsGeoData = function(){
        $.ajax({
            method: 'POST',
            url: '/reports/api/get-cars-geo-data',
            dataType: "json"
        })
        .success(function(data) {
            var format = new ol.format.GeoJSON();
            var features = format.readFeatures(data, {featureProjection: 'EPSG:3857'});

            for (var i = 0; i < features.length; i++) {
                features[i].setId(features[i].get('plate'));
                var ft = vectorSource.getFeatureById(features[i].getId());
                if (ft) {
                    ft.setGeometry(features[i].getGeometry());
                } else {
                    vectorSource.addFeature(features[i]);
                }
            }

            // Determino il numero di elementi caricati
            $("#element-counter input").val(features.length);
            //console.log(features.length);
            vectorSource.changed();
        });
    };

    setInterval($.oe.fn.getCarsGeoData, 2500);

    // Window Resize Action Bind
    $(window).resize(function() {
        clearTimeout(id);
        id = setTimeout($.oe.fn.doneResizing, 500);
    });
});
