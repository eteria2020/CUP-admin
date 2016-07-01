/* global $, confirm, dataTable, translate */
$(function () {
    // DataTables
    var table = $("#js-cars-configurations-table");

    // Define DataTables Filters
    var dataTableVars = {
        searchValue: $("#js-value"),
        column: $("#js-column"),
        iSortCol_0: 0,
        sSortDir_0: "desc",
        iDisplayLength: 100
    };

    var filterWithoutLike = false;
    var columnWithoutLike = false;
    var columnValueWithoutLike = false;

    dataTableVars.searchValue.val("");
    dataTableVars.column.val("select");

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
                aoData.push({ "name": "column", "value": $(dataTableVars.column).val() });
                aoData.push({ "name": "searchValue", "value": dataTableVars.searchValue.val().trim() });
            }
            // Set the column that shouldn't be null.
            aoData.push({ "name": "columnNotNull", "value": "c.plate" });
        },
        "order": [[dataTableVars.iSortCol_0, dataTableVars.sSortDir_0]],
        "columns": [
            { data: "c.plate" },
            { data: "e.key" },
            { data: "e.value" },
            { data: "button" }
        ],

        "columnDefs": [
            {
                targets: 2,
                sortable: false,
                searchable: false
            },
            {
                targets: 3,
                data: "button",
                searchable: false,
                sortable: false,
                render: function (data) {
                    return "<div class=\"btn-group\"><a href=\"/cars-configurations/details/" +
                        data + "\" class=\"btn btn-default\">" + translate("Dettagli") +
                        "</a><a href=\"/cars-configurations/edit/" +
                        data + "\" class=\"btn btn-default\">" + translate("Modifica") +
                        "</a><a href=\"/cars-configurations/delete/" +
                        data + "\" class=\"btn btn-default js-delete\">" + translate("Elimina") + "</a></div>";
                }
            }
        ],
        "lengthMenu": [
            [100, 200, 300],
            [100, 200, 300]
        ],
        "pageLength": dataTableVars.iDisplayLength,
        "pagingType": "bootstrap_full_number",
        "language": {
            "sEmptyTable": translate("sCarsEmptyTable"),
            "sInfo": translate("sInfo"),
            "sInfoEmpty": translate("sInfoEmpty"),
            "sInfoFiltered": translate("sInfoFiltered"),
            "sInfoPostFix": "",
            "sInfoThousands": ",",
            "sLengthMenu": translate("sLengthMenu"),
            "sLoadingRecords": translate("sLoadingRecords"),
            "sProcessing": translate("sProcessing"),
            "sSearch": translate("sSearch"),
            "sZeroRecords": translate("sZeroRecords"),
            "oPaginate": {
                "sFirst": translate("oPaginateFirst"),
                "sPrevious": translate("oPaginatePrevious"),
                "sNext": translate("oPaginateNext"),
                "sLast": translate("oPaginateLast")
            },
            "oAria": {
                "sSortAscending": translate("sSortAscending"),
                "sSortDescending": translate("sSortDescending")
            }
        }
    });

    $("#js-search").click(function () {
        table.fnFilter();
    });

    $("#js-clear").click(function () {
        dataTableVars.searchValue.val("");
        dataTableVars.searchValue.prop("disabled", false);
        dataTableVars.searchValue.show();
        dataTableVars.column.val("select");
        filterWithoutLike = false;
    });

    $("#js-cars-configurations-table").on("click", ".js-delete", function () {
        return confirm(translate("confirmCarConfigurationDelete"));
    });

    $(dataTableVars.column).change(function () {
        var value = $(this).val();
        if (value === "c.plate" || value === "e.key") {
            filterWithoutLike = false;
            dataTableVars.searchValue.val("");
            dataTableVars.searchValue.prop("disabled", false);
            dataTableVars.searchValue.show();
        } else {
            filterWithoutLike = true;
            dataTableVars.searchValue.val("");
            dataTableVars.searchValue.prop("disabled", true);
            dataTableVars.searchValue.show();

            columnWithoutLike = value;
            columnValueWithoutLike = true;
        }
    });
});
