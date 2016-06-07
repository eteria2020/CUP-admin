/* global $, confirm, dataTable, zones, ol, translate, getSessionVars, filters, renderTable, document, window, clearTimeout, setTimeout */
$(function () {
    "use strict";

    // DataTable
    var table = $("#js-zones-table");

    // Define DataTables Filters
    var dataTableVars = {
        searchValue: $("#js-value"),
        column: $("#js-column"),
        iSortCol_0: 0,
        sSortDir_0: "desc",
        iDisplayLength: 5
    };

    var filterWithoutLike = false;
    var columnWithoutLike = false;
    var columnValueWithoutLike = false;

    // Set the vector source; will contain the map data
    var vectorSource = {};

    // Set the features collection
    var zonesFC = {};

    // Set the bootstrapSwitchCollection
    var switchCollection = [];

    var format = new ol.format.GeoJSON();

    var drawZone = function (zoneId) {
        vectorSource.addFeature(zonesFC[zoneId]);
    };

    var removeZone = function (zoneId) {
        vectorSource.removeFeature(zonesFC[zoneId]);
    };

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
        "Polygon": [new ol.style.Style({
            fill: new ol.style.Fill({
                color: 'rgba(67, 163, 76, 0.5)'
            }),
            stroke: new ol.style.Stroke({
                color: 'red',
                width: 2
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

    var resizeId;

    var zonesLayer = new ol.layer.Vector({
        source: vectorSource,
        style:
        function (feature) {
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
        interactions: ol.interaction.defaults({ mouseWheelZoom: false }),
        controls: ol.control.defaults({
            attributionOptions: /** @type {olx.control.AttributionOptions} */ ({
                collapsible: false
            })
        }),
        view: view
    });

    var doneResizing = function () {
        var newHeight = $(window).height();
        $(".map").css("height", newHeight - 280);
        map.updateSize();
    };

    var renderTable = function (jXHRData) {
        // Adding features to Feature Collection
        $.each(jXHRData, function (key, val) {

            var areaUse = val.e.areaUse;
            var id = val.e.id;

            if (typeof zonesFC[id] === "undefined") {
                zonesFC[id] = new ol.Feature({
                    geometry: format.readGeometry(areaUse, { featureProjection: 'EPSG:3857' })
                });
                zonesFC[id].setId(id);
            }
        });

        // Adding the Bootstrap Switch on each record "view" button
        $.each($("input.visualizza"), function (key, val) {
            var id = $(val).data("id");

            if (typeof switchCollection[id] === "undefined") {
                switchCollection[id] = false;
            }

            $(val)
                .bootstrapSwitch({
                    state: switchCollection[id]
                })
                .on("switchChange.bootstrapSwitch",
                function (event, state) {
                    var zoneId = $(this).data("id");
                    if (state) {
                        switchCollection[id] = true;
                        drawZone(zoneId);
                    } else {
                        switchCollection[id] = false;
                        removeZone(zoneId);
                    }
                }
                );
        });

        // Listen to Focus Button
        $("a.focus").click(function () {
            var zoneId = $(this).data("id");
            var extent = zonesFC[zoneId].getGeometry().getExtent();
            map.getView().fit(extent, map.getSize());
        });
    };

    dataTableVars.searchValue.val("");
    dataTableVars.column.val("select");

    if ( typeof getSessionVars !== "undefined"){
        getSessionVars(filters, dataTableVars);
    }

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
                "success": function (msg) {
                    fnCallback(msg);
                    renderTable(msg.data);
                }
            });
        },
        "fnServerParams": function (aoData) {
            if (filterWithoutLike) {
                aoData.push({ "name": "column", "value": "" });
                aoData.push({ "name": "searchValue", "value": "" });
                aoData.push({ "name": "columnWithoutLike", "value": columnWithoutLike });
                aoData.push({ "name": "columnValueWithoutLike", "value": columnValueWithoutLike });
            } else {
                aoData.push({ "name": "column", "value": $(dataTableVars.column).val() });
                aoData.push({ "name": "searchValue", "value": dataTableVars.searchValue.val().trim() });
            }
        },
        "order": [[dataTableVars.iSortCol_0, dataTableVars.sSortDir_0]],
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
                render: function (data) {
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
                    return "<div class=\"btn-group\"><input class=\"visualizza\" type=\"checkbox\" name=\"visualizza\" data-id=\"" + data +
                    "\" data-on-text=\"" + translate("zonesListTabDataOnText") + "\" data-off-text=\"" + translate("zonesListTabDataOffText") +
                    "\" data-on-color=\"info\" data-label-width=\"20\"><a href=\"#map\" data-id=\"" + data +
                    "\" class=\"btn btn-default focus\"><span class=\"glyphicon glyphicon-screenshot\"></span> " +
                    translate("zonesListFocusText") + "</a><a href=\"/zones/edit/" + data + "\" class=\"btn btn-default\">" +
                    translate("modify") + "</a></div>";
                }
            }
        ],
        "lengthMenu": [
            [dataTableVars.iDisplayLength, 10, 100],
            [dataTableVars.iDisplayLength, 10, 100]
        ],
        "pageLength": dataTableVars.iDisplayLength,
        "pagingType": "bootstrap_full_number",
        "language": {
            "sEmptyTable": translate("sCustomersEmptyTable"),
            "sInfo": translate("sInfo"),
            "sInfoEmpty": translate("sInfoEmpty"),
            "sInfoFiltered": translate("sInfoFiltered"),
            "sInfoPostFix": "",
            "sInfoThousands": ",",
            "sLengthMenu": translate("sLengthMenu"),
            "sLoadingRecords": translate("sLoadingRecords"),
            "sProcessing": translate("sProcessing"),
            "sSearch": translate("sSearch"),
            "sZeroRecords": translate("sZeroRecords"),
            "oPaginate": {
                "sFirst": translate("oPaginateFirst"),
                "sPrevious": translate("oPaginatePrevious"),
                "sNext": translate("oPaginateNext"),
                "sLast": translate("oPaginateLast")
            },
            "oAria": {
                "sSortAscending": translate("sSortAscending"),
                "sSortDescending": translate("sSortDescending")
            }
        },
        "initComplete": function () {
            // Readjust columns width (because bootstrapSwitch resizing).
            this.DataTable().columns.adjust().draw();
        }
    });

    $('#js-search').click(function () {
        table.fnFilter();
    });

    $('#js-clear').click(function () {
        dataTableVars.searchValue.val('');
        dataTableVars.column.val('select');
    });

    vectorSource = new ol.source.Vector({
        projection: "EPSG:3857",
        format: new ol.format.GeoJSON()
    });

    vectorSource.addFeature(new ol.Feature(new ol.geom.Circle([5e6, 7e6], 1e6)));

    // Window Resize Action Bind
    $(window).resize(function () {
        clearTimeout(resizeId);
        resizeId = setTimeout(doneResizing, 500);
    });
    // Set to the map the current page height
    doneResizing();
});
