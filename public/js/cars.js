/* global  filters:true */
$(function() {
    // DataTables
    var table = $("#js-cars-table");

    // Define DataTables Filters
    var dataTableVars = {
        searchValue: $("#js-value"),
        column: $("#js-column"),
        iSortCol_0: 0,
        sSortDir_0: "desc",
        iDisplayLength: 100
    };

    var columnWithoutLike = false;
    var columnValueWithoutLike = false;
    var filterWithoutLike = false;

    dataTableVars.searchValue.val("");
    dataTableVars.column.val("select");

    if ( typeof getSessionVars === "undefined"){
        console.log("datatalbe-session-data.js Not loaded.");
    } else {
        getSessionVars(filters, dataTableVars);
    }

    table.dataTable({
        "processing": true,
        "serverSide": true,
        "bStateSave": false,
        "bFilter": false,
        "sAjaxSource": "/cars/datatable",
        "fnServerData": function ( sSource, aoData, fnCallback, oSettings ) {
            oSettings.jqXHR = $.ajax( {
                "dataType": "json",
                "type": "POST",
                "url": sSource,
                "data": aoData,
                "success": fnCallback
            } );
        },
        "fnServerParams": function ( aoData ) {
            if (filterWithoutLike) {
                aoData.push({ "name": "column", "value": ""});
                aoData.push({ "name": "searchValue", "value": ""});
                aoData.push({ "name": "columnWithoutLike", "value": columnWithoutLike});
                aoData.push({ "name": "columnValueWithoutLike", "value": columnValueWithoutLike});
            } else {
                aoData.push({ "name": "column", "value": $(dataTableVars.column).val()});
                aoData.push({ "name": "searchValue", "value": dataTableVars.searchValue.val().trim()});
            }
        },
        "order": [[dataTableVars.iSortCol_0, dataTableVars.sSortDir_0]],
        "columns": [
            {data: "e.plate"},
            {data: "e.label"},
            {data: "f.name"},
            {data: "e.battery"},
            {data: "e.lastContact"},
            {data: "e.km"},
            {data: "clean"},
            {data: "position"},
            {data: "e.status"},
            {data: "ci.gps"},
            {data: "ci.firmwareVersion"},
            {data: "ci.softwareVersion"},
            {data: "positionLink"},
            {data: "button"}
        ],
        "columnDefs": [
            {
                targets: 6,
                sortable: false
            },
            {
                targets: 7,
                sortable: false
            },
            {
                targets: 9,
                sortable: true,
                type: "string"
            },
            {
                targets: 10,
                sortable: true,
                type: "string"
            },
            {
                targets: 11,
                sortable: true,
                type: "string"
            },
            {
                targets: 12,
                sortable: false
            },
            {
                targets: 13,
                data: "button",
                searchable: false,
                sortable: false,
                render: function (data) {
                    return '<div class="btn-group">' +
                        '<a href="/cars/edit/' + data + '" class="btn btn-default">' + translate("modify")  + '</a> ' +
                        '<a href="/cars/delete/' + data + '" class="btn btn-default js-delete">' + translate("delete")  + '</a>' +
                        '</div>';
                }
            }
        ],
        "lengthMenu": [
            [100, 200, 300],
            [100, 200, 300]
        ],
        "pageLength": dataTableVars.iDisplayLength,
        "pagingType": "bootstrap_full_number",
        "language": {
            "sEmptyTable":     translate("sCarsEmptyTable"),
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

    $("#js-search").click(function() {
        table.fnFilter();
    });

    $("#js-clear").click(function() {
        dataTableVars.searchValue.val("");
        dataTableVars.searchValue.prop("disabled", false);
        dataTableVars.typeClean.hide();
        dataTableVars.searchValue.show();
        dataTableVars.column.val("select");
        filterWithoutLike = false;
    });

    $("#js-cars-table").on("click", ".js-delete", function() {
        return confirm(translate("confirmCarDelete"));
    });

    $(dataTableVars.column).change(function() {
        var value = $(this).val();

        if (value === "e.plate" || value === "e.label" || value === "f.name" || value === "ci.gps" || value === "ci.firmwareVersion" || value === "ci.softwareVersion") {
            filterWithoutLike = false;
            dataTableVars.searchValue.val("");
            dataTableVars.searchValue.prop("disabled", false);
            dataTableVars.typeClean.hide();
            dataTableVars.searchValue.show();
        } else {
            filterWithoutLike = true;
            dataTableVars.searchValue.val("");
            dataTableVars.searchValue.prop("disabled", true);
            dataTableVars.typeClean.hide();
            dataTableVars.searchValue.show();

            switch (value) {
                case "e.running":
                case "e.hidden":
                case "e.active":
                case "e.busy":
                    columnWithoutLike = value;
                    columnValueWithoutLike = true;
                    break;
                case "e.intCleanliness":
                    dataTableVars.typeClean.show();
                    dataTableVars.searchValue.hide();
                    columnWithoutLike = value;
                    columnValueWithoutLike = dataTableVars.typeClean.val();
                    $(dataTableVars.typeClean).change(function() {
                        columnValueWithoutLike = dataTableVars.typeClean.val();
                    });
                    break;
                case "e.extCleanliness":
                    dataTableVars.typeClean.show();
                    dataTableVars.searchValue.hide();
                    columnWithoutLike = value;
                    columnValueWithoutLike = dataTableVars.typeClean.val();
                    $(dataTableVars.typeClean).change(function() {
                        columnValueWithoutLike = dataTableVars.typeClean.val();
                    });
                    break;
                case "e.statusMaintenance":
                    columnWithoutLike = "e.status";
                    columnValueWithoutLike = "maintenance";
                    break;
                case "e.statusOperative":
                    columnWithoutLike = "e.status";
                    columnValueWithoutLike = "operative";
                    break;
                case "e.statusNotOperative":
                    columnWithoutLike = "e.status";
                    columnValueWithoutLike = "out_of_order";
                    break;
            }
        }
    });
});
