/* global $ confirm */

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
            return '<a href="/customers/foreign-drivers-license/validate/' +
                data.e.id +
                '" class="btn btn-default btn-xs" onclick="return confirm(\'Confermare la validazione?\')">Valida</a>';
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
                    return '<a href="/customers/foreign-drivers-license/download/' + data + '" class="btn btn-default btn-xs">Scarica</a>';
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
            "sEmptyTable": "Nessun file caricato",
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

    $('#js-search').click(function () {
        table.fnFilter();
    });

    $('#js-clear').click(function () {
        search.val('');
        column.val('select');
    });
});
