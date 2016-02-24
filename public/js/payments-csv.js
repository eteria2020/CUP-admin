$(function() {

    var orderSpecs = {
        1: "desc"
    };

    var languageSpecs = {
        "sEmptyTable":     "Nessun elemento presente nella tabella",
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
    };

    // Create the datatable and order it by descending date
    $('#new-table').DataTable({
        order: orderSpecs,
        language: languageSpecs
    });
    $('#analyzed-table').DataTable({
        order: orderSpecs,
        language: languageSpecs
    });
    $('#unresolved-table').DataTable({
        order: orderSpecs,
        language: languageSpecs
    });
    $('#resolved-table').DataTable({
        order: orderSpecs,
        language: languageSpecs
    });
});