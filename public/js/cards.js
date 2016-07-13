/* global $ */

$(function() {
    'use strict';

    var table = $('#js-cards-table');
    table.dataTable({
        "processing": true,
        "serverSide": true,
        "bStateSave": false,
        "bFilter": false,
        "sAjaxSource": "/customers/card/datatable",
        "fnServerData": function ( sSource, aoData, fnCallback, oSettings ) {
            oSettings.jqXHR = $.ajax( {
                "dataType": 'json',
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
            aoData.push({ "name": "column", "value": ''});
            aoData.push({ "name": "searchValue", "value": ''});
        },
        "order": [[0, 'desc']],
        "columns": [
            {data: 'e.code'},
            {data: 'e.rfid'},
            {data: 'e.isAssigned'},
            {data: 'e.notes'},
            {data: 'e.assignable'},
            {data: 'cu.surname'}
        ],
        "lengthMenu": [
            [100, 200, 300],
            [100, 200, 300]
        ],
        "pageLength": 100,
        "pagingType": "bootstrap_full_number",
        "language": {
            "sEmptyTable":     translate("sCardsEmptyTable"),
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
});
