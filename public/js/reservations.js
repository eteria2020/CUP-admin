$(function() {

    var table    = $('#js-reservations-table');
    var search   = $('#js-value');
    var column   = $('#js-column');
    var filterDate = false;
    search.val('');
    column.val('select');

    table.dataTable({
        "processing": true,
        "serverSide": true,
        "bStateSave": false,
        "bFilter": false,
        "sAjaxSource": "/reservations/datatable",
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

            if(filterDate) {
                aoData.push({ "name": "column", "value": ''});
                aoData.push({ "name": "searchValue", "value": ''});
                aoData.push({ "name": "from", "value": search.val().trim()});
                aoData.push({ "name": "to", "value": search.val().trim()});
                aoData.push({ "name": "columnFromDate", "value": "e.beginningTs"});
                aoData.push({ "name": "columnFromEnd", "value": "e.beginningTs"});
            } else {
                aoData.push({ "name": "column", "value": $(column).val()});
                aoData.push({ "name": "searchValue", "value": search.val().trim()});
            }
        },
        "order": [[0, 'desc']],
        "columns": [
            {data: 'e.id'},
            {data: 'e.carPlate'},
            {data: 'e.customer'},
            {data: 'e.cards'},
            {data: 'e.active'}
        ],

        "columnDefs": [
            {
                targets: 0,
                sortable: false
            },
            {
                targets: 1,
                sortable: false
            },
            {
                targets: 2,
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
            "sEmptyTable":     "Nessun cliente presente nella tabella",
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
        column.val('select');
    });

    $('.date-picker').datepicker({
        autoclose: true,
        format: 'yyyy-mm-dd',
        weekStart: 1
    });

    $(column).change(function() {
        var value = $(this).val();

        if(value == 'beginningTs') {
            filterDate = true;
            search.val('');
            $(search).datepicker({
                autoclose: true,
                format: 'yyyy-mm-dd',
                weekStart: 1
            });

        } else {
            filterDate = false;
            search.val('');
            $(search).datepicker("remove");
        }

    });

});