/* global $, filters:true, translate:true, getSessionVars:true */
$(function() {
    // DataTables
    var table = $("#js-cars-table");

    // Define DataTables Filters
    var dataTableVars = {
        searchValue: $("#js-value"),
        column: $("#js-column"),
        iSortCol_0: 0,
        sSortDir_0: "desc",
        typeClean: $("#js-clean-type"),
        iDisplayLength: 100
    };

    var columnWithoutLike = false;
    var columnValueWithoutLike = false;
    var filterWithoutLike = false;

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
        "sAjaxSource": "/cars/datatable",
        "fnServerData": function ( sSource, aoData, fnCallback, oSettings ) {
            oSettings.jqXHR = $.ajax( {
                "dataType": "json",
                "type": "POST",
                "url": sSource,
                "data": aoData,
                "success": fnCallback,
                "statusCode": {
                    200: function(data, textStatus, jqXHR) {
                        loginRedirect(data, textStatus, jqXHR);
                    }
                }
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
            {data: "e.hidden"},
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
                render: function (data, type, row) {
                     return (data === true) ? '<span class="glyphicon glyphicon-ok"></span>' : '<span class="glyphicon glyphicon-remove"></span>';
                 }
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
                sortable: true,
                type: "string"
            },
            {
                targets: 13,
                sortable: false
            },
            {
                targets: 14,
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
            "sEmptyTable": translate("sCarsEmptyTable"),
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

function blackbox(plate){
    document.getElementById("blackbox-"+plate).removeAttribute("onclick");
    document.getElementById("blackbox-"+plate).style.cursor = "wait";
    $.get('cars/blackbox-coordinates?plate='+plate,
        function(data){
            data = JSON.parse(data);
            if (data.status == "OK") {
                var win = window.open(data.link, '_blank');
                if (win) {
                    document.getElementById("blackbox-"+plate).style.cursor = "not-allowed";
                    setTimeout(function () {
                        document.getElementById("blackbox-"+plate).setAttribute("onclick", "blackbox('"+plate+"')");
                        document.getElementById("blackbox-"+plate).style.cursor = "pointer";
                    }, 30000);
                    win.focus();
                } else {
                    //Browser has blocked it
                    document.getElementById("blackbox-"+plate).style.cursor = "not-allowed";
                    setTimeout(function () {
                        document.getElementById("blackbox-"+plate).setAttribute("onclick", "blackbox('"+plate+"')");
                        document.getElementById("blackbox-"+plate).style.cursor = "pointer";
                    }, 30000);
                    alert('Please allow popups for this page');
                }
            } else if (data.status == "KO"){
                document.getElementById("blackbox-"+plate).style.cursor = "not-allowed";
                alert("No black box coordinates found");
            }
    });



}