// Define Glbal vars
/* global thiscity:true, d3:true, dc:true, crossfilter:true, ol:true $ document window setInterval clearInterval clearTimeout setTimeout */

// Check if var $.oe has been declared
if (typeof $.oe === 'undefined') {
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
        console.log('error : ' + msg.d);
    }*/
});

$.extend($.scrollTo.defaults, {
    axis: 'y',
    duration: 500,
    interrupt: true         //  If true will cancel the animation if the user scrolls. Default is false
    //easing: 'linear'
});

///////////// $.oe Vars Definition /////////////
$.oe.today = new Date();
$.oe.todayFormatted = $.oe.today.getFullYear() + '-' +
    ("0" + ($.oe.today.getMonth() + 1)).slice(-2) + '-' +
    ("0" + $.oe.today.getDate()).slice(-2);

// Set the vector source; will contain the map data
$.oe.vectorSource = {};

// Set the elements style
$.oe.mapStyle = {
    fill: {},
    stroke: {},
    iconStyle: {}
};

// The Layer containing the cars point
$.oe.carLayer = {};

// The View element of ol
$.oe.view = {};

// The map element
$.oe.map = {};

// The base map layer
$.oe.osmLayer = {};

// Set the timeout needed for the page resize bind function
$.oe.timeout = 0;

// The element containing the car tracks
$.oe.lineTracks = [];

// The car trail layer
$.oe.trailLayer = {};

// Set the vector source; will contain the map data
$.oe.lineVectorSource = {};

// The interval object
$.oe.refreshInterval = {};

// Flag to set if stopped cars should be hide
$.oe.hideStopped = false;

// Flag to determinate the number of olf points to show on the map
$.oe.pointsToShow = 120;

$.oe.updatedBeforeOld = 5;
////////////////////////////////////////////////

// The magic!
$(document).ready(function(){
    $.oe.fn.getCityData($.oe.fn.createButtons);
});

$(window).load(function() {
    $.oe.fn.doneResizing();
    $.oe.fn.getCarsGeoData();
    $.oe.fn.activateMapActions();

    // Bind map move
    $.oe.map.on("moveend", $.oe.fn.zoomChanged);

    $.oe.fn.activateDataRefresh();
    $.oe.fn.activateHideStoppedButton();
});

var info = $('div#info'),
    id;

// Setup the graphics style of elements

// Fills
$.oe.mapStyle.fill.moving = new ol.style.Fill({
    color: '#00FF00'
});
$.oe.mapStyle.fill.stand = new ol.style.Fill({
    color: '#EDE431'
});
$.oe.mapStyle.fill.old = new ol.style.Fill({
    color: 'rgba(5, 5, 255,0.9)'//color: '#311431'
});
$.oe.mapStyle.fill.stop= new ol.style.Fill({
    color: '#FF0000'
});

// Strokes
$.oe.mapStyle.stroke = new ol.style.Stroke({
    color: '#FF0000',
    width: 1.25
});

// Styles
$.oe.mapStyle.movingPointStyle = new ol.style.Style({
    image: new ol.style.Circle({
        fill: $.oe.mapStyle.fill.moving,
        stroke: $.oe.mapStyle.stroke,
        radius: 7
    }),
    fill: $.oe.mapStyle.fill.moving,
    stroke: $.oe.mapStyle.stroke
});
$.oe.mapStyle.standPointStyle = new ol.style.Style({
    image: new ol.style.Circle({
        fill: $.oe.mapStyle.fill.stand,
        stroke: $.oe.mapStyle.stroke,
        radius: 7
    }),
    fill: $.oe.mapStyle.fill.stand,
    stroke: $.oe.mapStyle.stroke
});
$.oe.mapStyle.stopPointStyle = new ol.style.Style({
    image: new ol.style.Circle({
        fill: $.oe.mapStyle.fill.stop,
        stroke: $.oe.mapStyle.stroke,
        radius: 7
    }),
    fill: $.oe.mapStyle.fill.stop,
    stroke: $.oe.mapStyle.stroke
});
$.oe.mapStyle.oldPointStyle = new ol.style.Style({
    image: new ol.style.Circle({
        fill: $.oe.mapStyle.fill.old,
        radius: 4
    }),
    fill: $.oe.mapStyle.fill.old
});
$.oe.mapStyle.lineStyle = new ol.style.Style({
    stroke: new ol.style.Stroke({
        color: 'rgba(5, 5, 255,0.7)',
        width: 5
    })
});
$.oe.mapStyle.emptyStyle = new ol.style.Style({});

// Setup the elements of the map
$.oe.vectorSource = new ol.source.Vector({
    extractStyles: false,
    projection: 'EPSG:3857',
    format: new ol.format.GeoJSON()
});
$.oe.carLayer = new ol.layer.Vector({
    source: $.oe.vectorSource,
    style: $.oe.mapStyle.movingPointStyle
});
$.oe.view = new ol.View({
    // the view's initial state
    center: ol.proj.transform([9.185, 45.465], 'EPSG:4326', 'EPSG:3857'),
    zoom: 12
});
$.oe.osmLayer = new ol.layer.Tile({
    source: new ol.source.OSM()
});

$.oe.lineStringVectorSource = new ol.source.Vector();
$.oe.lineStringTrailLayer = new ol.layer.Vector({
    source: $.oe.lineStringVectorSource,
    style: $.oe.mapStyle.lineStyle
});

$.oe.lineVectorSource = new ol.source.Vector();
$.oe.trailLayer = new ol.layer.Vector({
    source: $.oe.lineVectorSource,
    style: $.oe.mapStyle.oldPointStyle
});

$.oe.map = new ol.Map({
    layers: [$.oe.osmLayer, $.oe.trailLayer, $.oe.lineStringTrailLayer, $.oe.carLayer],
    target: 'map',
    view: $.oe.view,
    eventListeners: {"zoomend": $.oe.fn.zoomChanged}
});

$.oe.fn.zoomChanged = function(){
    var zoom = $.oe.map.getView().getZoom();
    if ($.oe.lastZoom !== zoom)
    {
        $.oe.lastZoom = zoom;
    }
};

info.tooltip({
    animation: true,
    trigger: 'manual'
});


$.oe.fn.displayFeatureInfo = function(pixel) {
    info.css({
        left: pixel[0] + 'px',
        top: (pixel[1] - 5) + 'px'
    });
    var feature = $.oe.map.forEachFeatureAtPixel(pixel, function(innerFeature) {
        return innerFeature;
    });
    if (feature) {
        info.tooltip('hide')
            .attr('data-original-title', feature.get('plate'))
            .tooltip('fixTitle')
            .tooltip('show');
        $('div.info').html(feature.get('plate'));
    } else {
        info.tooltip('hide');
    }
};

$.oe.fn.activateMapActions = function(){
    $.oe.map.on('pointermove', function(evt) {
        if (evt.dragging) {
            info.tooltip('hide');
            return;
        }
        $.oe.fn.displayFeatureInfo($.oe.map.getEventPixel(evt.originalEvent));
    });

    $.oe.map.on('click', function(evt) {
        $.oe.fn.displayFeatureInfo(evt.pixel);
    });

    // Create info element on footer
    $('footer.footer').prepend('<div class="info"></div>');
};

$.oe.tempLine = {};
$.oe.tempFeature = {};

$.oe.fn.getCarsGeoData = function(){
    $.ajax({
        method: 'POST',
        url: '/reports/api/get-cars-geo-data',
        dataType: "json"
    })
    .success(function(data) {
        var format = new ol.format.GeoJSON();
        var features = format.readFeatures(data, {featureProjection: 'EPSG:3857'});

        // For every feature loaded ( ol.geom.Point )
        for (var i = 0; i < features.length; i++) {
            features[i].setId(features[i].get('plate'));
            var ft = $.oe.vectorSource.getFeatureById(features[i].getId());
            var line = $.oe.lineVectorSource.getFeatureById(features[i].getId());
            var lineString = $.oe.lineStringVectorSource.getFeatureById(features[i].getId());

            // If we already have the feature --> update coords and do other stuff
            if (ft) {
                // Getting the coordinates of the last registered point from the trailVectorSource
                var c1 = line.getGeometry().getLastCoordinate();
                var c2 = features[i].getGeometry().getCoordinates();

                // Check if the old coordinates == the refreshed one
                if ( c1[0] == c2[0] && c1[1] == c2[1]){
                    ft.setStyle($.oe.mapStyle.standPointStyle);

                    // Check if the cars is standing for more than 5 data refresh
                    if (typeof line.getGeometry().get('standing') !== 'undefined' ){
                        var standing = parseInt(line.getGeometry().get('standing'));

                        if (standing >= $.oe.updatedBeforeOld) {
                            // The car is stopped

                            // Check if we have to show it or hide it.
                            if($.oe.hideStopped){
                                ft.setStyle($.oe.mapStyle.emptyStyle);
                            } else {
                                ft.setStyle($.oe.mapStyle.stopPointStyle);
                            }
                        }
                        line.getGeometry().set('standing', standing + 1);
                    } else {
                        // First time standing
                        line.getGeometry().set('standing', 1);
                    }
                } else {
                    // The car is moving
                    ft.setStyle($.oe.mapStyle.movingPointStyle);
                    line.getGeometry().set('standing', 0);
                }
                ft.setGeometry(features[i].getGeometry());
            } else {
                // We create the feature
                $.oe.vectorSource.addFeature(features[i]);
            }

            if (line) {
                // Adding new point to the track
                line.getGeometry().appendPoint(features[i].getGeometry());

                // Remove more than $.oe.pointsToShow points
                if (line.getGeometry().getCoordinates().length >= $.oe.pointsToShow) {
                    line.getGeometry().setCoordinates(line.getGeometry().getCoordinates().slice(1, $.oe.pointsToShow));
                }
            } else {
                // New Car
                line = new ol.Feature({
                    geometry: new ol.geom.MultiPoint([], 'XY'),
                    name: 'Punto Scia'
                });

                line.setId(features[i].get('plate'));
                line.getGeometry().appendPoint(features[i].getGeometry());
                $.oe.lineVectorSource.addFeature(line);
            }

            if (lineString) {
                lineString.setGeometry(new ol.geom.LineString(line.getGeometry().getCoordinates(), 'XY'));
            } else {
                var multistring = new ol.Feature({
                    geometry: new ol.geom.LineString(line.getGeometry().getCoordinates(), 'XY'),
                    name: 'Punto Scia'
                });

                multistring.setId(features[i].get('plate'));
                $.oe.lineStringVectorSource.addFeature(multistring);
            }
        }

        // Determinate the number of cars loaded
        $("#element-counter input").val(features.length);

        // Update the graphic vectors
        $.oe.vectorSource.changed();
        $.oe.lineVectorSource.changed();
        $.oe.lineStringVectorSource.changed();
    });
};

$.oe.fn.createButtons = function(){
    $.each($.oe.city, function(key, val){
        // Create and populate the city prop
        val.ol = {};
        val.ol.coordinate = ol.proj.fromLonLat([val.params.center.longitude, val.params.center.latitude]);

        // Create a button for every city
        $('#header-buttons').prepend('<button type="button" class="btn btn-default" id="pan-to-' + val.fleet_code + '">Pan to ' + val.fleet_name + '</button>');

        // Handle the click action for every city button
        $("#pan-to-" + val.fleet_code).click(function(){
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

$.oe.fn.hideStopped = function(){
    $.each($.oe.lineVectorSource.getFeatures(), function(key, val){
        var standing = val.getGeometry().get('standing');

        if(typeof standing !== 'undefined'){
            if (standing > 5) {
                // Setting a null Style, make it invisible (override VectoreSource Style)
                val.setStyle($.oe.mapStyle.emptyStyle);

                var lastPoint = $.oe.vectorSource.getFeatureById(val.getId());

                if(typeof standing !== 'undefined'){
                    // Set a null Style to the last revealed points
                    lastPoint.setStyle($.oe.mapStyle.emptyStyle);
                }
            }
        }
    });
};

$.oe.fn.showStopped = function(){
    $.each($.oe.lineVectorSource.getFeatures(), function(key, val){
        var standing = val.getGeometry().get('standing');

        if(typeof standing !== 'undefined'){
            if (standing > 5) {
                // Remove custom Style, getting the VectorSource one
                val.setStyle();

                var lastPoint = $.oe.vectorSource.getFeatureById(val.getId());

                if(typeof standing !== 'undefined'){
                    // Set a null Style to the last revealed points
                    lastPoint.setStyle($.oe.mapStyle.stopPointStyle);
                }

            }
        }
    });
};

// Bind buttons actions
$.oe.fn.activateHideStoppedButton = function()
{
    $('button#hidestopped').click(
        function () {
            if($.oe.hideStopped){
                $.oe.hideStopped = false;

                $('button#hidestopped')
                    .removeClass('btn-success')
                    .addClass('btn-default');

                $.oe.fn.showStopped();
            }else{
                $.oe.hideStopped = true;

                $('button#hidestopped')
                    .removeClass('btn-default')
                    .addClass('btn-success');

                $.oe.fn.hideStopped();
            }
        }
    );
};

$.oe.fn.activateDataRefresh = function(){
    $('button#dataupdate')
        .removeClass('btn-danger')
        .removeClass('btn-default')
        .addClass('btn-success');
    $('button#dataupdate span')
        .addClass('glyphicon-refresh-animate');

    $.oe.refreshInterval = setInterval($.oe.fn.getCarsGeoData, 2500);

    // Bind its action
    $("button#dataupdate").one('click', '', function(){
        $.oe.fn.deactivateDataRefresh();
    });
};

$.oe.fn.deactivateDataRefresh = function(){
    $('button#dataupdate')
        .removeClass('btn-success')
        .addClass('btn-danger');
    $('button#dataupdate span')
        .removeClass('glyphicon-refresh-animate');

    clearInterval($.oe.refreshInterval);

    // Bind its action
    $("button#dataupdate").one('click', '', function(){
        $.oe.fn.activateDataRefresh();
    });
};


$.oe.fn.doneResizing = function(){
    var newHeight = $(window).height();
    $(".row.mainrow").css("height", newHeight - 280); //-110);
    $(".map").css("height", newHeight - 280);
    $.oe.map.updateSize();
};


// Window Resize Action Bind
$(window).resize(function() {
    clearTimeout(id);
    id = setTimeout($.oe.fn.doneResizing, 500);
});
