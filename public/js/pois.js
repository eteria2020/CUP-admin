$(function() {

    var table    = $('#js-pois-table');
    var search   = $('#js-value');
    var column   = $('#js-column');
    var filterWithoutLike = false;
    var columnWithoutLike = false;
    var columnValueWithoutLike  = false;

    search.val('');
    column.val('select');

    table.dataTable({
        "processing": true,
        "serverSide": true,
        "bStateSave": false,
        "bFilter": false,
        "sAjaxSource": "/configurations/manage-pois/datatable",
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

            if(filterWithoutLike) {
                aoData.push({ "name": "column", "value": ''});
                aoData.push({ "name": "searchValue", "value": ''});
                aoData.push({ "name": "columnWithoutLike", "value": columnWithoutLike});
                aoData.push({ "name": "columnValueWithoutLike", "value": columnValueWithoutLike});
            } else {
                aoData.push({ "name": "column", "value": $(column).val()});
                aoData.push({ "name": "searchValue", "value": search.val().trim()});
            }

        },
        "order": [[0, 'desc']],
        "columns": [
            {data: 'e.id'},
            {data: 'e.type'},
            {data: 'e.code'},
            {data: 'e.name'},
            {data: 'e.address'},
            {data: 'e.town'},
            {data: 'e.zipCode'},
            {data: 'e.province'},
            {data: 'button'}
        ],

        "columnDefs": [
            {
                targets: 8,
                data: 'button',
                searchable: false,
                sortable: false,
                render: function (data) {
                    return '<div class="btn-group">' +
                        '<a href="/configurations/manage-pois/edit/' + data + '" class="btn btn-default">Modifica</a> ' +
                        '<a href="/configurations/manage-pois/delete/' + data + '" class="btn btn-default js-delete">Elimina</a>' +
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
            "sEmptyTable":     "Nessun pois presente nella tabella",
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
        search.show();
        search.show();
        column.val('select');
        filterWithoutLike = false;
    });

    $('#js-pois-table').on('click', '.js-delete', function() {
        return confirm("Confermi l'eliminazione del poi? L'operazione non è annullabile");
    });

});
