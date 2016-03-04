/* global $ translate */

$(function() {
    "use strict";

    var table = $('#unpayed-trips-table');
    var search = $('#js-value');
    var column = $('#js-column');
    var filterWithNull = false;
    search.val('');
    column.val('select');


    table.dataTable({
        "processing": true,
        "serverSide": true,
        "bStateSave": false,
        "bFilter": false,
        "sAjaxSource": "/trips/not-payed-datatable",
        "fnServerData": function ( sSource, aoData, fnCallback, oSettings ) {
            oSettings.jqXHR = $.ajax( {
                "dataType": 'json',
                "type": "POST",
                "url": sSource,
                "data": aoData,
                "success": fnCallback,
                "error": function() {
                }
            } );
        },
        "fnServerParams": function ( aoData ) {

            if(filterWithNull) {
                aoData.push({ "name": "column", "value": ''});
                aoData.push({ "name": "searchValue", "value": ''});
                aoData.push({ "name": "columnNull", "value": "e.timestampEnd"});
            } else {
                aoData.push({ "name": "column", "value": $(column).val()});
                aoData.push({ "name": "searchValue", "value": search.val().trim()});
            }

        },
        "order": [[0, 'desc']],
        "columns": [
            {data: 'e.id'},
            {data: 'cu.surname'},
            {data: 'cu.name'},
            {data: 'cc.rfid'},
            {data: 'c.plate'},
            {data: 'f.name'},
            {data: 'e.kmBeginning'},
            {data: 'e.kmEnd'},
            {data: 'e.timestampBeginning'},
            {data: 'e.timestampEnd'},
            {data: 'e.duration'},
            {data: 'e.parkSeconds'},
            {data: 'e.totalCost'}
        ],
        "columnDefs": [

            {
                targets: [1, 2],
                "render": function (data, type, row) {
                    return '<a href="/customers/edit/' + row.cu.id + '" title="' + translate("showProfile") + ' ' + row.cu.name + ' ' + row.cu.surname + ' ">' + data + '</a>';
                }
            },
            {
                targets: 12,
                sortable: false,
                "render": function (data) {
                    return renderCostButton(data);
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

    $('#js-search').click(function() {
        table.fnFilter();
    });

    $('#js-clear').click(function() {
        search.val('');
        column.val('select');
        search.prop('disabled', false);
        filterWithNull = false;
        search.show();
    });

    $(column).change(function() {
        var value = $(this).val();

        search.show();
        search.val('');

        if(value === 'c.timestampEnd') {
            filterWithNull = true;
            search.prop('disabled', true);
        } else {
            filterWithNull = false;
            search.prop('disabled', false);
        }
    });

    function toStringKeepZero(value)
    {
        return ((value < 10) ? '0' : '') + value;
    }

    function renderAmount(amount)
    {
        return (Math.floor(amount / 100)) +
            ',' +
            toStringKeepZero(amount % 100) +
            ' \u20ac';
    }

    function renderCostButton(data)
    {
        var amount = data.amount;
        if (amount !== 'FREE') {
            return amount !== '' ?
            '<a href="/trips/details/' + data.id + '?tab=cost">' + renderAmount(parseInt(amount)) + '</a>' : '';
        } else {
            return amount;
        }
    }
});
