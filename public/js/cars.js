$(function() {

    var table    = $('#js-cars-table');
    var search   = $('#js-value');
    var column   = $('#js-column');
    var typeClean = $('#js-clean-type');
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
            {data: 'e.plate'},
            {data: 'e.label'},
            {data: 'e.battery'},
            {data: 'e.lastContact'},
            {data: 'e.km'},
            {data: 'clean'},
            {data: 'position'},
            {data: 'e.status'},
            {data: 'positionLink'},
            {data: 'button'}
        ],

        "columnDefs": [
            {
                targets: 5,
                sortable: false
            },
            {
                targets: 6,
                sortable: false
            },
            {
                targets: 8,
                sortable: false
            },
            {
                targets: 9,
                data: 'button',
                searchable: false,
                sortable: false,
                render: function (data) {
                    var externalUrl = 'http://84.247.195.41/cartasi/template/form/car_action_2.php?targa='+data;
                    return '<div class="btn-group">' +
                        '<a href="/cars/edit/' + data + '" type="button" class="btn btn-default">Modifica</a> ' +
                        '<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"><i class="fa fa-angle-down"></i></button> ' +
                        '<ul class="dropdown-menu" role="menu">' +
                        '<li><a href="/cars/delete/' + data + '" class="js-delete">Elimina</a></li>' +
                        '<li><a href="'+externalUrl+'">Scarica Whitelist</a></li>' +
                        '<li><a href="'+externalUrl+'">Cancella e riscarica Whitelist</a></li>' +
                        '<li><a href="'+externalUrl+'">Apri Portiere</a></li>' +
                        '<li><a href="'+externalUrl+'">Chiudi Portiere</a></li>' +
                        '<li><a href="'+externalUrl+'">Abilita Motore</a></li>' +
                        '<li><a href="'+externalUrl+'">Disabilita Motore</a></li>' +
                        '<li><a href="'+externalUrl+'">Rispedisci corse </a></li>' +
                        '</ul>' +
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
        search.prop('disabled', false);
        typeClean.hide();
        search.show();
        column.val('select');
        filterWithoutLike = false;
    });
    
    $('#js-cars-table').on('click', '.js-delete', function() {
        return confirm("Confermi l'eliminazione dell'auto? L'operazione non è annullabile");
    });

    $(column).change(function() {

        var value = $(this).val();

        if (value == 'e.plate' || value == 'e.label') {

            filterWithoutLike = false;
            search.val('');
            search.prop('disabled', false);
            typeClean.hide();
            search.show();

        } else {

            filterWithoutLike = true;
            search.val('');
            search.prop('disabled', true);
            typeClean.hide();
            search.show();

            switch (value) {

                case 'e.running':
                case 'e.hidden':
                case 'e.active':
                case 'e.busy':
                    columnWithoutLike = value;
                    columnValueWithoutLike = true;
                    break;
                case 'e.intCleanliness':
                    typeClean.show();
                    search.hide();
                    columnWithoutLike = value;
                    columnValueWithoutLike = typeClean.val();
                    $(typeClean).change(function() {
                        columnValueWithoutLike = typeClean.val();
                    });
                    break;
                case 'e.extCleanliness':
                    typeClean.show();
                    search.hide();
                    columnWithoutLike = value;
                    columnValueWithoutLike = typeClean.val();
                    $(typeClean).change(function() {
                        columnValueWithoutLike = typeClean.val();
                    });
                    break;
                case 'e.statusMaintenance':
                    columnWithoutLike = 'e.status';
                    columnValueWithoutLike = 'maintenance';
                    break;

                case 'e.statusOperative':
                    columnWithoutLike = 'e.status';
                    columnValueWithoutLike = 'operative';
                    break;
                case 'e.statusNotOperative':
                    columnWithoutLike = 'e.status';
                    columnValueWithoutLike = 'out_of_order';
                    break;
            }
        }
    });

});