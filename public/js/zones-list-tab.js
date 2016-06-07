/* global $, confirm, dataTable */
//$(function () {
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
                    return "<div class=\"btn-group\"><input class=\"visualizza\" type=\"checkbox\" name=\"visualizza\" data-id=\"" + data + "\" data-on-text=\"" + translate("zonesListTabDataOnText") + "\" data-off-text=\"" + translate("zonesListTabDataOffText") + "\" data-on-color=\"info\" data-label-width=\"20\"><a href=\"#map\" data-id=\"" + data + "\" class=\"btn btn-default focus\"><span class=\"glyphicon glyphicon-screenshot\"></span> " + translate("zonesListFocusText") + "</a><a href=\"/zones/edit/" + data + "\" class=\"btn btn-default\">" + translate("modify") + "</a></div>";
                }
            }
        ],
        "lengthMenu": [
            [100, 200, 300],
            [100, 200, 300]
        ],
        "pageLength": 100,
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
        },
        "initComplete": function() {
            // Init Bootstrap Switch
            $("input.visualizza")
                .bootstrapSwitch()
                .on("switchChange.bootstrapSwitch",
                    function(event, state) {
                        var zoneId = $(this).data("id");
                        if(state){
                            drawZone(zoneId);
                        } else {
                            removeZone(zoneId);
                        }
                    }
                );

            // Listen to Focus Button
            $("a.focus").click(function(){
                var zoneId = $(this).data("id");
                var extent = zonesFC[zoneId].getGeometry().getExtent();
                map.getView().fit(extent, map.getSize());
            });

           // Readjust columns width (because bootstrapSwitch resizing).
           this.DataTable().columns.adjust().draw();
        }
    });

   $('#js-search').click(function() {
        table.fnFilter();
    });

    $('#js-clear').click(function() {
        search.val('');
        column.val('select');
    });

    ///// OpenStreetMap Section /////
    // Set the vector source; will contain the map data
    var vectorSource = {};

    // Set the features collection
    var zonesFC = {};

    // Adding features to Feature Collection
    $.each(zones,function(key,val){
        zonesFC[key] = new ol.Feature({
            geometry: format.readGeometry(val, {featureProjection: 'EPSG:3857'})
        });
        zonesFC[key].setId(key);
    });

    // The collection of features selected
    var featureOverlaySource = {};

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

    var vectorSource = new ol.source.Vector({
        projection: "EPSG:3857",
        format: new ol.format.GeoJSON()
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
        interactions: ol.interaction.defaults({mouseWheelZoom:false}),
        controls: ol.control.defaults({
          attributionOptions: /** @type {olx.control.AttributionOptions} */ ({
            collapsible: false
          })
        }),
        view: view
    });

    vectorSource.addFeature(new ol.Feature(new ol.geom.Circle([5e6, 7e6], 1e6)));

    var format = new ol.format.GeoJSON(); 

    var drawZone = function(zoneId) {
        vectorSource.addFeature(zonesFC[zoneId]);
    };

    var removeZone = function(zoneId) {
        vectorSource.removeFeature(zonesFC[zoneId]);
    };

    // Window Resize Action Bind
    var resizeId;
    $(window).resize(function() {
        clearTimeout(resizeId);
        resizeId = setTimeout(doneResizing, 500);
    });
    doneResizing = function(){
        var newHeight = $(window).height();
        $(".map").css("height", newHeight - 280);
        map.updateSize();
    };

    // Set to the map the current page height
    doneResizing();
//});