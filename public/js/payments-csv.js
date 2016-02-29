$(function() {

    var orderSpecs = [[1, 'desc']];

    var languageSpecs = {
        "sEmptyTable":     translate("sEmptyTable"),
        "sInfo":           translate("sInfo"),
        "sInfoEmpty":      translate("sInfoEmpty"),
        "sInfoFiltered":   translate("sInfoFiltered"),
        "sInfoPostFix":    "",
        "sInfoThousands":  ",",
        "sLengthMenu":     translate("sLengthMenu"),
        "sLoadingRecords": translate("sLoadingRecords"),
        "sProcessing":     translate("sProcessing"),
        "sSearch":         translate("sSearch"),
        "sZeroRecords":    translate("sZeroRecords"),
        "oPaginate": {
            "sFirst":      translate("oPaginateFirst"),
            "sPrevious":   translate("oPaginatePrevious"),
            "sNext":       translate("oPaginateNext"),
            "sLast":       translate("oPaginateLast")
        },
        "oAria": {
            "sSortAscending":   translate("sSortAscending"),
            "sSortDescending":  translate("sSortDescending")
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
