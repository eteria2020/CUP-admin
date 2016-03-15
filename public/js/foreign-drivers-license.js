/* global $ confirm document translate */

$(function() {
    'use strict';

    var table = $('#js-files-table');
    var search = $('#js-value');
    var column = $('#js-column');
    search.val('');
    column.val('select');

    function showValid(data) {
        if (data.e.valid) {
            return '<span class="label label-success">Validato</span>';
        } else {
            var confirmText = 'id: ' + data.e.customer + ' ' + data.e.customer_name + ' ' + data.e.customer_surname + ' ' + translate("confirmValidate")
            return '<a href="//' +
                data.e.id +
                '" onclick="return confirm(\'' + confirmText + '\')" class="btn btn-default btn-xs">'+translate("validate")+'</a>';
        }
    }

    table.dataTable({
        "processing": true,
        "serverSide": true,
        "bStateSave": false,
        "bFilter": false,
        "sAjaxSource": "/customers/foreign-drivers-license/datatable",
        "fnServerData": function (sSource, aoData, fnCallback, oSettings) {
            oSettings.jqXHR = $.ajax({
                "dataType": 'json',
                "type": "POST",
                "url": sSource,
                "data": aoData,
                "success": fnCallback
            });
        },
        "fnServerParams": function (aoData) {
            aoData.push({"name": "column", "value": $(column).val()});
            aoData.push({"name": "searchValue", "value": search.val().trim()});
        },
        "order": [[0, 'desc']],
        "columns": [
            {data: 'e.customer'},
            {data: 'e.customer_name'},
            {data: 'e.customer_surname'},
            {data: 'e.customer_address'},
            {data: 'e.customer_birthdate'},
            {data: 'e.customer_birthplace'},
            {data: 'e.drivers_license_number'},
            {data: 'e.drivers_license_authority'},
            {data: 'e.drivers_license_country'},
            {data: 'e.drivers_license_release_date'},
            {data: 'e.drivers_license_name'},
            {data: 'e.drivers_license_categories'},
            {data: 'e.drivers_license_expire'},
            {data: 'e.id'},
            {data: showValid}
        ],
        "columnDefs": [
            {
                targets: 13,
                searchable: false,
                sortable: false,
                data: 'e.customer',
                render: function (data) {
                    return '<a href="/customers/foreign-drivers-license/download/' + data + '" class="btn btn-default btn-xs">'+translate("download")+'</a>';
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
        "pageLength": 100,
        "pagingType": "bootstrap_full_number",
        "language": {
            "sEmptyTable":     translate("sDrivingLicenseEmptyTable"),
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
                "sLast":       translate("oPaginateLast")
            },
            "oAria": {
                "sSortAscending":   translate("sSortAscending"),
                "sSortDescending":  translate("sSortDescending")
            }
        }
    });

    $('#js-search').click(function () {
        table.fnFilter();
    });

    $('#js-clear').click(function () {
        search.val('');
        column.val('select');
    });
});
