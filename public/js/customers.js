$(function() {

    var table    = $('#js-customers-table');
    var search   = $('#js-value');
    var column   = $('#js-column');
    search.val('');
    column.val('select');

    table.dataTable({
        "processing": true,
        "serverSide": true,
        "bStateSave": false,
        "bFilter": false,
        "sAjaxSource": "/customers/datatable",
        "fnServerData": function ( sSource, aoData, fnCallback, oSettings ) {
            oSettings.jqXHR = $.ajax( {
                "dataType": 'json',
                "type": "POST",
                "url": sSource,
                "data": aoData,
                "success": fnCallback,
                "error": function(jqXHR, textStatus, errorThrown) {

                    /*
                    if (jqXHR.status == '200' &&
                        textStatus == 'parsererror') {

                        bootbox.alert('La tua sessione è scaduta, clicca sul pulsante OK per tornare alla pagina di login.', function(r) {
                            document.location.href = '/user/login';
                        });

                        //@tofix user come here also if the response is wrong

                    }*/
                }
            } );
        },
        "fnServerParams": function ( aoData ) {
            aoData.push({ "name": "column", "value": $(column).val()});
            aoData.push({ "name": "searchValue", "value": search.val().trim()});
        },
        "order": [[0, 'desc']],
        "columns": [
            {data: 'e-id'},
            {data: 'e-name'},
            {data: 'e-surname'},
            {data: 'e-mobile'},
            {data: 'e-cardCode'},
            {data: 'e-driverLicense'},
            {data: 'e-driverLicenseExpire'},
            {data: 'e-email'},
            {data: 'e-taxCode'},
            {data: 'e-registration'},
            {data: 'button'}
        ],
        "columnDefs": [
            {
                targets: 9,
                searchable: false,
                sortable: false
            },
            {
                targets: 10,
                data: 'button',
                searchable: false,
                sortable: false,
                render: function (data, type, row) {
                    return'<a href="/customers/edit/' + data + '" class="btn btn-default btn-xs">Modifica</a>';
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

    $('.date-picker').datepicker({
        autoclose: true,
        format: 'dd-mm-yy',
        weekStart: 1
    });
});