$(function() {

    var table    = $('#js-invoices-table');
    var search   = $('#js-value');
    var column   = $('#js-column');
    var typeClean = $('#js-clean-type');

    search.val('');
    column.val('select');

    table.dataTable({
        "processing": true,
        "serverSide": true,
        "bStateSave": false,
        "bFilter": false,
        "sAjaxSource": "/invoices/datatable",
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
            aoData.push({ "name": "searchValue", "value": formatData().trim()});
            aoData.push({ "name": "idColumn", "value": "e.customer"});
            aoData.push({ "name": "idValue", "value": customerId});
        },
        "order": [[0, 'asc']],
        "columns": [
            {data: 'e.invoiceNumber'},
            {data: 'e.invoiceDate'},
            {data: 'e.type'},
            {data: 'e.amount'},
            {data: 'link'}
        ],
        "columnDefs": [
            {
                targets: 1,
                "render": function ( data, type, row ) {
                    return renderDate(data);
                }
            },
            {
                targets: 2,
                "render": function ( data, type, row ) {
                    return renderType(data);
                }
            },
            {
                targets: 3,
                "render": function ( data, type, row ) {
                    return renderAmount(data);
                }
            },
            {
                targets: 4,
                sortable: false
            }
        ],
        "lengthMenu": [
            [100, 200, 300],
            [100, 200, 300]
        ],
        "pageLength": 100,
        "pagingType": "bootstrap_full_number",
        "language": {
            "sEmptyTable":     "Nessuna fattura presente nella tabella",
            "sInfo":           "Vista da _START_ a _END_ di _TOTAL_ elementi",
            "sInfoEmpty":      "Vista da 0 a 0 di 0 elementi",
            "sInfoFiltered":   "(filtrati da _MAX_ elementi totali)",
            "sInfoPostFix":    "",
            "sInfoThousands":  ",",
            "sLengthMenu":     "Visualizza _MENU_ elementi",
            "sLoadingRecords": "Caricamento...",
            "sProcessing":     "Elaborazione in corso...",
            "sSearch":         "Cerca:",
            "sZeroRecords":    "La ricerca non ha portato alcun risultato.",
            "oPaginate": {
                "sFirst":      "Inizio",
                "sPrevious":   "Precedente",
                "sNext":       "Successivo",
                "sLast":       "Fine"
            },
            "oAria": {
                "sSortAscending":  ": attiva per ordinare la colonna in ordine crescente",
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
        filterWithoutLike = false;
    });

    $(column).change(function() {
        var value = $(this).val();
        search.val('');
        typeClean.hide();
        search.show();
    });

    function formatData()
    {
        var value = $(column).val();
        var searchValue = $(search).val();
        /*
        if (value == 'e.invoiceDate') {
            // remove slash
            searchValue.replace(/\//g, '');
        } else if (value == 'e.amount') {
            // remove comma, EUR symbol and spaces
            searchValue.replace(/,|\u20ac|s+/g, '');
        } else if (value == 'e.type') {
            switch (searchValue) {
                case 'Iscrizione':
                    searchValue = 'FIRST_PAYMENT';
                    break;
                case 'Corse':
                    searchValue = 'TRIP';
                    break;
                case 'Sanzione':
                    searchValue = 'PENALTY';
            }
        }
        */
        return searchValue;
    }

    function renderDate(date)
    {
        return toStringKeepZero(date % 100) + '/' +
        toStringKeepZero(Math.floor((date / 100) % 100)) + '/' +
        (Math.floor(date / 10000));
    }

    function renderAmount(amount)
    {
        return (Math.floor(amount / 100)) +
            ',' +
            toStringKeepZero(amount % 100) +
            ' \u20ac';
    }

    function renderType(type)
    {
        switch (type) {
            case 'FIRST_PAYMENT':
                return 'Iscrizione';
            case 'TRIP':
                return 'Corse';
            case 'PENALTY':
                return 'Sanzione';
        }
    }

    function toStringKeepZero(value)
    {
        return ((value < 10) ? '0' : '') + value;
    }
});
