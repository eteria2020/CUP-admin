/* global $, confirm, dataTable */
$(function () {
    var table = $("#js-zones-table");
    var search = $("#js-value");
    var column = $("#js-column");
    var filterWithoutLike = false;
    var columnWithoutLike = false;
    var columnValueWithoutLike = false;

    search.val("");
    column.val("select");

    table.dataTable({
        "processing": true,
        "serverSide": true,
        "bStateSave": false,
        "bFilter": false,
        "sAjaxSource": "/zones/datatable",
        "fnServerData": function (sSource, aoData, fnCallback, oSettings) {
            oSettings.jqXHR = $.ajax({
                "dataType": "json",
                "type": "POST",
                "url": sSource,
                "data": aoData,
                "success": fnCallback
            });
        },
        "fnServerParams": function (aoData) {
            if (filterWithoutLike) {
                aoData.push({ "name": "column", "value": "" });
                aoData.push({ "name": "searchValue", "value": "" });
                aoData.push({ "name": "columnWithoutLike", "value": columnWithoutLike });
                aoData.push({ "name": "columnValueWithoutLike", "value": columnValueWithoutLike });
            } else {
                aoData.push({ "name": "column", "value": $(column).val() });
                aoData.push({ "name": "searchValue", "value": search.val().trim() });
            }
        },
        "order": [[0, "desc"]],
        "columns": [
            { data: "e.name" },
            { data: "e.active" },
            { data: "e.hidden" },
            { data: "e.invoiceDescription" },
            { data: "e.revGeo" },
            { data: "button" }
        ],
        "columnDefs": [
            {
                targets: [1, 2, 4],
                render: function (data, type, row) {
                        return (data === true) ? '<span class="glyphicon glyphicon-ok"></span>' : '<span class="glyphicon glyphicon-remove"></span>';
                }
            },
            {
                targets: 4,
                sortable: false,
                searchable: false
            },
            {
                targets: 5,
                data: "button",
                searchable: false,
                sortable: false,
                render: function (data) {
                    return "<div class=\"btn-group\"><div class=\"btn btn-default\" data-id=\"" + data + "\">Visualizza</div><a href=\"/zones/edit/" + data + "\" class=\"btn btn-default\">Modifica</a></div>";
                }
            }
        ],
        "lengthMenu": [
            [5, 10, 30],
            [5, 10, 30]
        ],
        "pageLength": 5,
        "pagingType": "bootstrap_full_number",
        "language": {
            "sEmptyTable":     translate("sCustomersEmptyTable"),
            "sInfo":           translate("sInfo"),
            "sInfoEmpty":      translate("sInfoEmpty"),
            "sInfoFiltered":   translate("sInfoFiltered"),
            "sInfoPostFix":    "",
            "sInfoThousands":  ",",
            "sLengthMenu":     translate("sLengthMenu"),
            "sLoadingRecords": translate("sLoadingRecords"),
            "sProcessing":     translate("sProcessing"),
            "sSearch":         translate("sSearch"),
            "sZeroRecords":    translate("sZeroRecords"),
            "oPaginate": {
                "sFirst":      translate("oPaginateFirst"),
                "sPrevious":   translate("oPaginatePrevious"),
                "sNext":       translate("oPaginateNext"),
                "sLast":       translate("oPaginateLast"),
            },
            "oAria": {
                "sSortAscending":   translate("sSortAscending"),
                "sSortDescending":  translate("sSortDescending")
            }
        }
    });

   $('#js-search').click(function() {
        table.fnFilter();
    });

    $('#js-clear').click(function() {
        search.val('');
        column.val('select');
    });

    $(column).change(function() {
        var value = $(this).val();

        if(value == 'beginningTs') {
            filterDate = true;
            search.val('');
            $(search).datepicker({
                autoclose: true,
                format: 'yyyy-mm-dd',
                weekStart: 1
            });

        } else {
            filterDate = false;
            search.val('');
            $(search).datepicker("remove");
        }

    });


    ///// OpenStreetMap Section /////
    // Set the vector source; will contain the map data
    var vectorSource = {};

    // Set the features collection
    var zonesFC = {};

    // The collection of features selected
    var featureOverlaySource = {};

    // The loaded tracks (features)
    var zones = [];

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

    var vectorSource = new ol.source.Vector({
        projection: "EPSG:3857",
        format: new ol.format.GPX()
    });

    var zonesLayer = new ol.layer.Vector({
        source: vectorSource,
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

    var map = new ol.Map({
        layers: [OSM, zonesLayer],
        target: document.getElementById("map"),
        view: view
    });

    // Set the overlay for the selected zones
    var featureOverlaySource = new ol.source.Vector({});
    var featureOverlay = new ol.layer.Vector({
        source: featureOverlaySource,
        style:
            function(feature){
                return hoverstyle[feature.getGeometry().getType()];
            }
    });
    // Add the overlay to the MAP ol.obj
    featureOverlay.setMap(map);

    // Bind the entire map mouse moving
    map.on("pointermove", function(evt) {
        if (evt.dragging) {
            return;
        }
        if (!$.oe.listenHover){
            return;
        }
        var pixel = map.getEventPixel(evt.originalEvent);
    });

});