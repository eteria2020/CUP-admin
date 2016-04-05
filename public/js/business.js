/* global $ confirm translate */

$(function() {
    'use strict';

    var table = $('#js-business-table');
    var search = $('#js-value');
    var column = $('#js-column');
    search.val('');
    column.val('select');

    table.dataTable({
        "processing": true,
        "serverSide": true,
        "bStateSave": false,
        "bFilter": false,
        "sAjaxSource": "/business/datatable",
        "fnServerData": function ( sSource, aoData, fnCallback, oSettings ) {
            oSettings.jqXHR = $.ajax( {
                "dataType": 'json',
                "type": "POST",
                "url": sSource,
                "data": aoData,
                "success": fnCallback
            } );
        },
        "fnServerParams": function ( aoData ) {
            aoData.push({ "name": "column", "value": $(column).val()});
            aoData.push({ "name": "searchValue", "value": search.val().trim()});
        },
        "order": [[0, 'desc']],
        "columns": [
            {data: 'e.name'},
            {data: 'e.code'},
            {data: 'e.domains'},
            {data: 'e.city'},
            {data: 'e.phone'},
            {data: 'e.vatNumber'},
            {data: 'e.insertedTs'},
            {data: 'button'}
        ],

        "columnDefs": [
            {
                targets: 2,
                searchable: false,
                sortable: false,
                "render": function (data) {
                    var out = '';
                    $.each(data, function(i, val) {
                        out += val + "<br>";
                    });
                    return out;
                }
            },
            {
                targets: 7,
                data: 'button',
                searchable: false,
                sortable: false,
                render: function (data) {
                    return '<a href="/business/edit/' + data + '" class="btn btn-default btn-xs">' + translate("modify") + '</a>';
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
            "sEmptyTable": translate("sEmptyTable"),
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
    });
});
