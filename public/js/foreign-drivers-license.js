/* global filters:true, $ confirm document translate, getSessionVars:true */
$(function() {
    "use strict";

    // DataTables
    var table = $("#js-files-table");

    // Define DataTables Filters
    var dataTableVars = {
        searchValue: $("#js-value"),
        column: $("#js-column"),
        iSortCol_0: 0,
        sSortDir_0: "desc",
        iDisplayLength: 10
    };

    dataTableVars.searchValue.val("");
    dataTableVars.column.val("select");

    if ( typeof getSessionVars !== "undefined"){
        getSessionVars(filters, dataTableVars);
    }

    function showStatus(data) {
        var confirmText;
        var status;
        if (data.e.valid) {
            confirmText = 'id: ' + data.e.customer + ' ' + data.e.customer_name + ' ' + data.e.customer_surname + ' ' + translate("confirmRevoke");
            return '<span class="validation-btn-info btn btn-success btn-xs disabled">' + translate("valid") + '</span>' +
                '<a href="/customers/foreign-drivers-license/revoke/' +
                data.e.id +
                '" onclick="return confirm(\'' + confirmText + '\')" class="validation-btn btn btn-default btn-xs">'+translate("revoke")+'</a>';
        }
        status = data.e.first_time ?
            '<span class="validation-btn-info btn btn-warning btn-xs disabled">' + translate("pending") + '</span>' :
            '<span class="validation-btn-info btn btn-danger btn-xs disabled">' + translate("revoked") + '</span>';
        confirmText = 'id: ' + data.e.customer + ' ' + data.e.customer_name + ' ' + data.e.customer_surname + ' ' + translate("confirmValidate");
        return status +
            '<a href="/customers/foreign-drivers-license/validate/' +
            data.e.id +
            '" onclick="return confirm(\'' + confirmText + '\')" class="validation-btn btn btn-default btn-xs">'+translate("validate")+'</a>';
    }

    table.dataTable({
        "processing": true,
        "serverSide": true,
        "bStateSave": false,
        "bFilter": false,
        "sAjaxSource": "/customers/foreign-drivers-license/datatable",
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
            aoData.push({"name": "column", "value": $(dataTableVars.column).val()});
            aoData.push({"name": "searchValue", "value": dataTableVars.searchValue.val().trim()});
        },
        "order": [[dataTableVars.iSortCol_0, dataTableVars.sSortDir_0]],
        "columns": [
            {data: "e.customer"},
            {data: "e.customer_name"},
            {data: "e.customer_surname"},
            {data: "e.customer_address"},
            {data: "e.customer_birthdate"},
            {data: "e.customer_birthplace"},
            {data: "e.drivers_license_number"},
            {data: "e.drivers_license_authority"},
            {data: "e.drivers_license_country"},
            {data: "e.drivers_license_release_date"},
            {data: "e.drivers_license_name"},
            {data: "e.drivers_license_categories"},
            {data: "e.drivers_license_expire"},
            {data: "e.id"},
            {data: showStatus}
        ],
        "columnDefs": [
            {
                targets: 13,
                searchable: false,
                sortable: false,
                data: "e.customer",
                render: function (data) {
                    return '<a href="/customers/foreign-drivers-license/download/' + data +
                        '" class="validation-btn btn btn-default btn-xs">' + translate("download") + '</a>';
                }
            },
            {
                targets: 14,
                searchable: false,
                sortable: false
            }
        ],
        "lengthMenu": [
            [10, 50, 100],
            [10, 50, 100]
        ],
        "pageLength": dataTableVars.iDisplayLength,
        "pagingType": "bootstrap_full_number",
        "language": {
            "sEmptyTable": translate("sDrivingLicenseEmptyTable"),
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

    $("#js-search").click(function () {
        table.fnFilter();
    });

    $("#js-clear").click(function () {
        dataTableVars.searchValue.val("");
        dataTableVars.column.val("select");
    });
});
