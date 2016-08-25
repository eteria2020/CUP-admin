/* global  filters:true, translate:true, $, getSessionVars:true, jstz:true, moment:true, ol: true, document:true */
$(function() {
    "use strict";

    // Detect user timezone
    var userTimeZone = moment.tz.guess(); // Determines the time zone of the browser client

    ///////////////////// BEGIN MAP SECTION /////////////////////

    // The MAP
    var OSM = new ol.layer.Tile({
        source: new ol.source.OSM()
    });

    /**
     * The car icon
     * @type {olx.style.IconOptions}
     */
    var carIcon = new ol.style.Icon({
        anchor: [0.5, 1],
        anchorXUnits: "fraction",
        anchorYUnits: "fraction",
        opacity: 0.75,
        scale: 0.15,
        src: "/img/car-icon.png"
    });

    /**
     * The car pint
     * @type {olx.style.Circle}
     */
    var carPoint = new ol.style.Circle({
        radius: 2,
        fill: new ol.style.Fill({
            color: "rgba(255,0,0,0.9)"
        }),
        stroke: null
    });

    // Set the vector source; will contain the map data
    var positionLayerVectorSource = new ol.source.Vector({
        projection: "EPSG:3857",
        format: new ol.format.GeoJSON()
    });

    var geoPositionLon = $("#point-lon").html();
    var geoPositionLat = $("#point-lat").html();

    var geoPositionCoordinates = ol.proj.transform(
        [
            Number(geoPositionLon),
            Number(geoPositionLat)
        ],
        "EPSG:4326",
        "EPSG:3857"
    );

    var geoPositionPoint = new ol.geom.Point(geoPositionCoordinates);

    var view = new ol.View({
        // the view"s initial state
        center: geoPositionCoordinates,
        zoom: 16
    });

    /**
     * @type {ol.Vector}
     */
    var positionLayer = new ol.layer.Vector({
        source: positionLayerVectorSource,
        style:
        [
            new ol.style.Style({
                image: carIcon
            }),
            new ol.style.Style({
                image: carPoint
            })
        ]
    });

    var map = new ol.Map({
        layers: [OSM, positionLayer],
        target: document.getElementById("map"),
        interactions: ol.interaction.defaults({ mouseWheelZoom: false }),
        controls: ol.control.defaults({
            attributionOptions: /** @type {olx.control.AttributionOptions} */ ({
                collapsible: false
            })
        }),
        view: view
    });

    var geoPositionFeature = new ol.Feature({
        geometry: geoPositionPoint
    });

    // Add the position object on the positionLayerVectorSource
    positionLayerVectorSource.addFeature(geoPositionFeature);

    ///////////////////// END MAP SECTION /////////////////////

    // Parse DateTime with user browser TimeZone
    $(".date-time").each(function(){
        var dateTimeStamp = parseInt($(this).html(), 10);
        var momentDate;
        if (dateTimeStamp !== "NaN"){
            momentDate = moment(dateTimeStamp, "X");
            if (momentDate.isValid()){
                $(this).html(momentDate.tz(userTimeZone).format("DD-MM-YYYY - HH:mm:ss"));
            }
        }
    });

    // Execute reverse geocoding
    $.ajax({
        method: "GET",
        url: "http://nominatim.openstreetmap.org/reverse?json_callback=?",
        processData: true,
        dataType: "jsonp",
        data: {
            format: "json",
            lon: geoPositionLon,
            lat: geoPositionLat,
            zoom: 16,
            addressdetails: 1
        },
        timeout: 180000 // Timeout = 3 minutes (2 minutes ~= 300 tracks)
    })
    .success(function(data) {
        $.each(data.address, function(key, val){
            $(".address." + key).html(val);
        });
    });
});
