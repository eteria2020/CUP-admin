$(function() {

    var table    = $('#js-trips-table');
    var search   = $('#js-value');
    var column   = $('#js-column');
    var from = $('#js-date-from');
    var to = $('#js-date-to');
    var filterWithNull = false;
    search.val('');
    column.val('select');


    table.dataTable({
        "processing": true,
        "serverSide": true,
        "bStateSave": false,
        "bFilter": false,
        "sAjaxSource": "/trips/datatable",
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

            if(filterWithNull) {
                aoData.push({ "name": "column", "value": ''});
                aoData.push({ "name": "searchValue", "value": ''});
                aoData.push({ "name": "columnNull", "value": "e.timestampEnd"});
            } else {
                aoData.push({ "name": "column", "value": $(column).val()});
                aoData.push({ "name": "searchValue", "value": search.val().trim()});
            }

            aoData.push({ "name": "from", "value": $(from).val().trim()});
            aoData.push({ "name": "to", "value": $(to).val().trim()});
            aoData.push({ "name": "columnFromDate", "value": "e.timestampBeginning"});
            aoData.push({ "name": "columnFromEnd", "value": "e.timestampEnd"});
        },
        "order": [[0, 'desc']],
        "columns": [
            {data: 'e-id'},
            {data: 'cu-surname'},
            {data: 'cu-name'},
            {data: 'cu-mobile'},
            {data: 'cc-code'},
            {data: 'c-plate'},
            {data: 'c-label'},
            {data: 'e-kmBeginning'},
            {data: 'e-kmEnd'},
            {data: 'e-timestampBeginning'},
            {data: 'e-timestampEnd'},
            {data: 'duration'},
            {data: 'e-parkSeconds'},
            {data: 'price'},
            {data: 'StatoQuadro'},
            {data: 'c-parking'},
            {data: 'e-payable'}
        ],
        "aoColumnDefs": [
            { 'bSortable': false, 'aTargets': [11,13,14]}
        ],
        "lengthMenu": [
            [100, 200, 300],
            [100, 200, 300]
        ],
        "pageLength": 100,
        "pagingType": "bootstrap_full_number",
        "language": {
            "sEmptyTable":     "Nessuna corsa presente nella tabella",
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
        from.val('');
        to.val('');
        column.val('select');
        search.prop('disabled', false);
        filterWithNull = false;
    });

    $('.date-picker').datepicker({
        autoclose: true,
        format: 'yyyy-mm-dd',
        weekStart: 1
    });

    $(column).change(function() {
        var value = $(this).val();

        if(value == 'c.timestampEnd') {
            filterWithNull = true;
            search.val('');
            search.prop('disabled', true);
        } else {
            filterWithNull = false;
            search.val('');
            search.prop('disabled', false);
        }

    });


});