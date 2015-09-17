$(function() {

    var table    = $('#js-invoices-table');
    var search   = $('#js-value');
    var column   = $('#js-column');
    var typePayment = $('#js-payment-type');

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
        },
        "order": [[0, 'asc']],
        "columns": [
            {data: 'e.invoiceNumber'},
            {data: 'e.invoiceDate'},
            {data: 'e.customerName'},
            {data: 'e.customerSurname'},
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
                targets: 4,
                "render": function ( data, type, row ) {
                    return renderType(data);
                }
            },
            {
                targets: 5,
                className: "sng-dt-right",
                "render": function ( data, type, row ) {
                    return renderAmount(data);
                }
            },
            {
                targets: 6,
                sortable: false,
                className: "sng-dt-center",
                "render": function ( data, type, row ) {
                    return renderLink(data);
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
        typePayment.hide();
        search.show();
    });

    function formatData()
    {
        var value = $(column).val();
        var searchValue = $(search).val();
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

    function renderLink(id)
    {
        return '<a href=' + pdfPath + id + '><i class="fa fa-download"></i></a>';
    }

    function toStringKeepZero(value)
    {
        return ((value < 10) ? '0' : '') + value;
    }
});
