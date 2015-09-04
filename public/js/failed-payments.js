/* global $ */

$(function() {
    "use strict";

    var table = $('#js-payments-table'),
        search = $('#js-value'),
        column = $('#js-column'),
        typeClean = $('#js-clean-type');

    search.val('');
    column.val('select');

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

    table.dataTable({
        "processing": true,
        "serverSide": true,
        "bStateSave": false,
        "bFilter": false,
        "sAjaxSource": "/payments/failed-payments-datatable",
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
            aoData.push({ "name": "fixedColumn", "value": "e.status"});
            aoData.push({ "name": "fixedValue", "value": "wrong_payment"});
            aoData.push({ "name": "fixedLike", "value": false});
        },
        "order": [[0, 'desc']],
        "columns": [
            {data: 'e.createdAt'},
            {data: 'cu.name'},
            {data: 'cu.surname'},
            {data: 'e.tripMinutes'},
            {data: 'e.parkingMinutes'},
            {data: 'e.discountPercentage'},
            {data: 'e.totalCost'},
            {data: 'button'}
        ],
        "columnDefs": [
            {
                targets: 6,
                "render": function (data) {
                    return renderAmount(data);
                }
            },
            {
                targets: 7,
                data: 'button',
                searchable: false,
                sortable: false,
                render: function (data) {
                    return '<div class="btn-group">' +
                        '<a href="/payments/retry/' + data + '" class="btn btn-default">Prosegui</a> ' +
                        '</div>';
                }
            }
        ],
        "lengthMenu": [
            [10, 20, 30],
            [10, 20, 30]
        ],
        "pageLength": 10,
        "pagingType": "bootstrap_full_number",
        "language": {
            "sEmptyTable": "Nessun cliente presente nella tabella",
            "sInfo": "Vista da _START_ a _END_ di _TOTAL_ elementi",
            "sInfoEmpty": "Vista da 0 a 0 di 0 elementi",
            "sInfoFiltered": "(filtrati da _MAX_ elementi totali)",
            "sInfoPostFix": "",
            "sInfoThousands": ",",
            "sLengthMenu": "Visualizza _MENU_ elementi",
            "sLoadingRecords": "Caricamento...",
            "sProcessing": "Elaborazione in corso...",
            "sSearch": "Cerca:",
            "sZeroRecords": "La ricerca non ha portato alcun risultato.",
            "oPaginate": {
                "sFirst": "Inizio",
                "sPrevious": "Precedente",
                "sNext": "Successivo",
                "sLast": "Fine"
            },
            "oAria": {
                "sSortAscending": ": attiva per ordinare la colonna in ordine crescente",
                "sSortDescending": ": attiva per ordinare la colonna in ordine decrescente"
            }
        }
    });

    $('#js-search').click(function() {
        table.fnFilter();
    });

    $('#js-clear').click(function() {
        search.val('');
        search.prop('disabled', false);
        typeClean.hide();
        search.show();
        column.val('select');
    });
});
