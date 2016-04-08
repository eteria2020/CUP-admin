/* global $, confirm, dataTable */
$(function () {
    var table = $("#js-cars-configurations-table");
    var search = $("#js-value");
    var column = $("#js-column");
    var filterWithoutLike = false;
    var columnWithoutLike = false;
    var columnValueWithoutLike = false;

    search.val("");
    column.val("select");

    table.dataTable({
        "processing": true,
        "serverSide": true,
        "bStateSave": false,
        "bFilter": false,
        "sAjaxSource": "/cars-configurations/datatable",
        "fnServerData": function (sSource, aoData, fnCallback, oSettings) {
            oSettings.jqXHR = $.ajax({
                "dataType": "json",
                "type": "POST",
                "url": sSource,
                "data": aoData,
                "success": fnCallback
            });
        },
        "fnServerParams": function (aoData) {
            if (filterWithoutLike) {
                aoData.push({ "name": "column", "value": "" });
                aoData.push({ "name": "searchValue", "value": "" });
                aoData.push({ "name": "columnWithoutLike", "value": columnWithoutLike });
                aoData.push({ "name": "columnValueWithoutLike", "value": columnValueWithoutLike });
            } else {
                aoData.push({ "name": "column", "value": $(column).val() });
                aoData.push({ "name": "searchValue", "value": search.val().trim() });
            }
            // Set the column that shouldn't be null.
            aoData.push({ "name": "columnNotNull", "value": "e.model" });
        },
        "order": [[0, "desc"]],
        "columns": [
            { data: "f.name" },
            { data: "e.model" },
            { data: "e.key" },
            { data: "e.value" },
            { data: "button" }
        ],

        "columnDefs": [
            {
                targets: 3,
                sortable: false,
                searchable: false
            },
            {
                targets: 4,
                data: 'button',
                searchable: false,
                sortable: false,
                render: function (data) {
                    return "<div class=\"btn-group\"><a href=\"/cars-configurations/details/" + data + "\" class=\"btn btn-default\">Dettagli</a><a href=\"/cars-configurations/edit/" + data + "\" class=\"btn btn-default\">Modifica</a><a href=\"/cars-configurations/delete/" + data + "\" class=\"btn btn-default js-delete\">Elimina</a></div>";
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
            "sEmptyTable": "Nessuna configurazione presente nella tabella",
            "sInfo": "Vista da _START_ a _END_ di _TOTAL_ elementi",
            "sInfoEmpty": "Vista da 0 a 0 di 0 elementi",
            "sInfoFiltered": "(filtrati da _MAX_ elementi totali)",
            "sInfoPostFix": "",
            "sInfoThousands": ",",
            "sLengthMenu": "Visualizza _MENU_ elementi",
            "sLoadingRecords": "Caricamento...",
            "sProcessing": "Elaborazione in corso...",
            "sSearch": "Cerca:",
            "sZeroRecords": "La ricerca non ha portato alcun risultato.",
            "oPaginate": {
                "sFirst": "Inizio",
                "sPrevious": "Precedente",
                "sNext": "Successivo",
                "sLast": "Fine"
            },
            "oAria": {
                "sSortAscending": ": attiva per ordinare la colonna in ordine crescente",
                "sSortDescending": ": attiva per ordinare la colonna in ordine decrescente"
            }
        }
    });

    $("#js-search").click(function () {
        table.fnFilter();
    });

    $("#js-clear").click(function () {
        search.val("");
        search.prop("disabled", false);
        search.show();
        column.val("select");
        filterWithoutLike = false;
    });

$("#js-cars-configurations-table").on("click", ".js-delete", function () {
        return confirm("Confermi l'eliminazione della configurazione? L'operazione non è annullabile");
    });

    $(column).change(function () {
        var value = $(this).val();

        if (value === "f.name" || value === "e.model" || value === "e.key") {
            filterWithoutLike = false;
            search.val("");
            search.prop("disabled", false);
            search.show();
        } else {
            filterWithoutLike = true;
            search.val("");
            search.prop("disabled", true);
            search.show();

            columnWithoutLike = value;
            columnValueWithoutLike = true;
        }
    });

});
