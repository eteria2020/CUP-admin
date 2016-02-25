$(document).ready(function() {
    var orderSpecs = {
        1: "desc"
    };

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
            "sLast":       translate("oPaginateLast"),
        },
        "oAria": {
            "sSortAscending":   translate("sSortAscending"),
            "sSortDescending":  translate("sSortDescending")
        }
    };

    $.fn.dataTable.moment('DD-MM-YYYY HH:mm:ss');

    // Create the datatable and order it by descending date
    $('#trips-table').DataTable({
        language: languageSpecs,
        lengthMenu: [
            [100, 200, 300],
            [100, 200, 300]
        ],
        pageLength: 100
    });
});
