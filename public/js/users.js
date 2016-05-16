/* global $, filters:true, translate:true, getSessionVars:true */
$(function() {
    // DataTable
    var table = $("#js-users-table");

    // Define DataTables Filters
    var dataTableVars = {
        searchValue: $("#js-value"),
        column: $("#js-column"),
        iSortCol_0: 0,
        sSortDir_0: "asc",
        iDisplayLength: 10
    };

    var filterWithNull = false;

    dataTableVars.searchValue.val("");
    dataTableVars.column.val("select");

    table.dataTable({
        "processing": true,
        "serverSide": true,
        "bStateSave": false,
        "bFilter": false,
        "sAjaxSource": "/users/datatable",
        "fnServerData": function ( sSource, aoData, fnCallback, oSettings ) {
            oSettings.jqXHR = $.ajax( {
                "dataType": "json",
                "type": "POST",
                "url": sSource,
                "data": aoData,
                "success": function (msg) {
                    fnCallback(msg);
                    $("#total-users").html(msg.recordsTotal);
                },
                "error": function() {}
            });
        },
        "fnServerParams": function ( aoData ) {
            if (filterWithNull) {
                aoData.push({ "name": "column", "value": ""});
                aoData.push({ "name": "searchValue", "value": ""});
            } else {
                aoData.push({ "name": "column", "value": $(dataTableVars.column).val()});
                aoData.push({ "name": "searchValue", "value": dataTableVars.searchValue.val().trim()});
            }
        },
        "order": [[dataTableVars.iSortCol_0, dataTableVars.sSortDir_0]],
        "columns": [
            {data: "e.id"},
            {data: "e.displayName"},
            {data: "e.email"},
            {data: "e.role"},
            {data: "button"}
        ],
        "columnDefs": [
            {
                targets: 4,
                data: "button",
                searchable: false,
                sortable: false,
                render: function (data) {
                    return '<a href="/users/edit/' + data + '" class="btn btn-sm btn-success">' + translate("modify") + '</a>';
                }
            }
        ],
        "lengthMenu": [
            [10, 20, 100],
            [10, 20, 100]
        ],
        "pageLength": dataTableVars.iDisplayLength,
        "pagingType": "bootstrap_full_number",
        "language": {
            "sEmptyTable": translate("sTripEmptyTable"),
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
        dataTableVars.searchValue.prop("disabled", false);
        filterWithNull = false;
        dataTableVars.searchValue.show();
    });

    $(dataTableVars.column).change(function() {
        var value = $(this).val();
        dataTableVars.searchValue.show();
        dataTableVars.searchValue.val("");
        filterWithNull = false;
        dataTableVars.searchValue.prop("disabled", false);
    });
});
