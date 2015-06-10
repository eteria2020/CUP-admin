$(function() {

    var table    = $('#js-cars-table');
    var search   = $('#js-value');
    var column   = $('#js-column');
    search.val('');
    column.val('select');

    table.dataTable({
        "processing": true,
        "serverSide": true,
        "bStateSave": false,
        "bFilter": false,
        "sAjaxSource": "/cars/datatable",
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
            {data: 'plate'},
            {data: 'label'},
            {data: 'manufactures'},
            {data: 'model'},
            {data: 'clean'},
            {data: 'position'},
            {data: 'lastContact'},
            {data: 'rpm'},
            {data: 'speed'},
            {data: 'km'},
            {data: 'running'},
            {data: 'parking'},
            {data: 'hidden'},
            {data: 'active'},
            {data: 'status'},
            {data: 'busy'},
            {data: 'notes'},
            {data: 'button'}
        ],

        "columnDefs": [
            {
                targets: 4,
                sortable: false
            },
            {
                targets: 5,
                sortable: false
            },
            {
                targets: 17,
                data: 'button',
                searchable: false,
                sortable: false,
                render: function (data) {
                    return' <div class="btn-group" role="group">' +
                        '<a href="/cars/edit/' + data + '" class="btn btn-default btn-xs">Modifica</a>' +
                        '<a href="/cars/delete/' + data + '" class="btn btn-default btn-xs js-delete">Elimina</a>' +
                        '</div>';
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
    
    $('#js-cars-table').on('click', '.js-delete', function() {
        return confirm("Confermi l'eliminazione dell'auto? L'operazione non è annullabile");
    });
    
});