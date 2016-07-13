/* global  filters:true, translate:true, $, getSessionVars:true */
$(function() {
    // DataTables
    var table = $("#js-reservations-table");

    // Define DataTables Filters
    var dataTableVars = {
        searchValue: $("#js-value"),
        column: $("#js-column"),
        iSortCol_0: 0,
        sSortDir_0: "desc",
        iDisplayLength: 100
    };

    var filterDate = false;

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
        "sAjaxSource": "/reservations/datatable",
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
            if (filterDate) {
                aoData.push({ "name": "column", "value": ""});
                aoData.push({ "name": "searchValue", "value": ""});
                aoData.push({ "name": "from", "value": dataTableVars.searchValue.val().trim()});
                aoData.push({ "name": "to", "value": dataTableVars.searchValue.val().trim()});
                aoData.push({ "name": "columnFromDate", "value": "e.beginningTs"});
                aoData.push({ "name": "columnFromEnd", "value": "e.beginningTs"});
            } else {
                aoData.push({ "name": "column", "value": $(dataTableVars.column).val()});
                aoData.push({ "name": "searchValue", "value": dataTableVars.searchValue.val().trim()});
            }
        },
        "order": [[dataTableVars.iSortCol_0, dataTableVars.sSortDir_0]],
        "columns": [
            {data: "e.id"},
            {data: "e.carPlate"},
            {data: "e.customer"},
            {data: "e.cards"},
            {data: "e.active"}
        ],
        "columnDefs": [
            {
                targets: 0,
                sortable: false
            },
            {
                targets: 1,
                sortable: false,
                "render": function (data, type, row) {
                    return '<a href="/cars/edit/' + row.e.carPlate + '" title="' + translate("showCarPlate") + ' ' + data + '">' + data + '</a>';
                }
            },
            {
                targets: 2,
                sortable: false,
                "render": function (data, type, row) {
                    if (data !== "") {
                        return '<a href="/customers/edit/' + row.e.customerId + '" title="' + translate("showProfile") + ' ' + data + '">' + data + '</a>';
                    }
                    return "";
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
        }
    });

    $("#js-search").click(function() {
        table.fnFilter();
    });

    $("#js-clear").click(function() {
        dataTableVars.searchValue.val("");
        dataTableVars.column.val("select");
    });

    $(".date-picker").datepicker({
        autoclose: true,
        format: "yyyy-mm-dd",
        weekStart: 1
    });

    $(dataTableVars.column).change(function() {
        var value = $(this).val();
        if (value === "beginningTs") {
            filterDate = true;
            dataTableVars.searchValue.val("");
            $(dataTableVars.searchValue).datepicker({
                autoclose: true,
                format: "yyyy-mm-dd",
                weekStart: 1
            });
        } else {
            filterDate = false;
            dataTableVars.searchValue.val("");
            $(dataTableVars.searchValue).datepicker("remove");
        }
    });
});
