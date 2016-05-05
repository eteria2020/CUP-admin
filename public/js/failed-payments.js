/* global filters:true, $ confirm document translate, getSessionVars:true */
$(function() {
    "use strict";

     // DataTables
    var table = $("#js-payments-table");

   // Define DataTables Filters
    var dataTableVars = {
        searchValue: $("#js-value"),
        column: $("#js-column"),
        iSortCol_0: 0,
        sSortDir_0: "desc",
        iDisplayLength: 10
    };

    var typeClean = $("#js-clean-type"),
        filterWithoutLike = false,
        columnWithoutLike = false,
        columnValueWithoutLike = false;

    dataTableVars.searchValue.val("");
    dataTableVars.column.val("select");

    if ( typeof getSessionVars !== "undefined"){
        getSessionVars(filters, dataTableVars);
    }

    function toStringKeepZero(value)
    {
        return ((value < 10) ? "0" : "") + value;
    }

    function renderAmount(amount)
    {
        return (Math.floor(amount / 100)) + "," + toStringKeepZero(amount % 100) + " \u20ac";
    }

    function renderDiscount(discount)
    {
        return discount + "%";
    }

    function renderMin(min)
    {
        return min + " min.";
    }

    table.dataTable({
        "processing": true,
        "serverSide": true,
        "bStateSave": false,
        "bFilter": false,
        "sAjaxSource": "/payments/failed-payments-datatable",
        "fnServerData": function ( sSource, aoData, fnCallback, oSettings ) {
            oSettings.jqXHR = $.ajax( {
                "dataType": "json",
                "type": "POST",
                "url": sSource,
                "data": aoData,
                "success": fnCallback
            } );
        },
        "fnServerParams": function ( aoData ) {
            if (filterWithoutLike) {
                aoData.push({ "name": "column", "value": ""});
                aoData.push({ "name": "searchValue", "value": ""});
                aoData.push({ "name": "columnWithoutLike", "value": columnWithoutLike});
                aoData.push({ "name": "columnValueWithoutLike", "value": columnValueWithoutLike});
            } else {
                aoData.push({ "name": "column", "value": $(dataTableVars.column).val()});
                aoData.push({ "name": "searchValue", "value": dataTableVars.searchValue.val().trim()});
            }

            aoData.push({ "name": "fixedColumn", "value": "e.status"});
            aoData.push({ "name": "fixedValue", "value": "wrong_payment"});
            aoData.push({ "name": "fixedLike", "value": false});
        },
        "order": [[dataTableVars.iSortCol_0, dataTableVars.sSortDir_0]],
        "columns": [
            {data: "e.firstPaymentTryTs"},
            {data: "cu.id"},
            {data: "cu.name"},
            {data: "cu.surname"},
            {data: "cu.mobile"},
            {data: "cu.email"},
            {data: "e.trip"},
            {data: "e.tripMinutes"},
            {data: "e.parkingMinutes"},
            {data: "e.discountPercentage"},
            {data: "e.totalCost"},
            {data: "button"}
        ],
        "columnDefs": [
            {
                targets: 1,
                className: "sng-dt-right"
            },
            {
                targets: 6,
                className: "sng-dt-right",
                "render": function (data) {
                    return '<a href="/trips/details/' + data +
                        '" title="' + translate("tripDetailId") + " " + data +
                        ' ">' + data + '</a>';
                }
            },
            {
                targets: [1, 2, 3],
                "render": function (data, type, row) {
                    return '<a href="/customers/edit/' + row.cu.id +
                        '" title="' + translate("customersDetailId") + ' ' + row.cu.name +
                        ' ' + row.cu.surname + ' ">' + data + '</a>';
                }
            },
            {
                targets: 7,
                className: "sng-dt-right sng-no-wrap",
                "render": function (data) {
                    return renderMin(data);
                }
            },
            {
                targets: 8,
                className: "sng-dt-right",
                "render": function (data) {
                    return renderMin(data);
                }
            },
            {
                targets: 9,
                className: "sng-dt-right",
                "render": function (data) {
                    return renderDiscount(data);
                }
            },
            {
                targets: 10,
                className: "sng-dt-right sng-no-wrap",
                "render": function (data) {
                    return renderAmount(data);
                }
            },
            {
                targets: 11,
                data: "button",
                searchable: false,
                sortable: false,
                render: function (data) {
                    return '<div class="btn-group">' +
                        '<a href="/payments/retry/' + data + '" class="btn btn-default">' + translate("continue") + '</a> ' +
                        '</div>';
                }
            }
        ],
        "lengthMenu": [
            [10, 20, 30],
            [10, 20, 30]
        ],
        "pageLength": dataTableVars.iDisplayLength,
        "pagingType": "bootstrap_full_number",
        "language": {
            "sEmptyTable": translate("sCustomersEmptyTable"),
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

    $("#js-search").click(function() {
        // Always set the columnValueWithoutLike (even for columns that will be filtered with the "LIKE" stmt.).
        columnValueWithoutLike = dataTableVars.searchValue.val();

        // Filter Action
        table.fnFilter();
    });

    $("#js-clear").click(function() {
        dataTableVars.searchValue.val("");
        dataTableVars.searchValue.prop("disabled", false);
        typeClean.hide();
        dataTableVars.searchValue.show();
        dataTableVars.column.val("select");
    });

    // Select Changed Action
    $(dataTableVars.column).change(function() {
        // Selected Column
        var value = $(this).val();

        // Column that need the standard "LIKE" search operator
        if (value === "cu.surname") {
            filterWithoutLike = false;
            dataTableVars.searchValue.val("");
            dataTableVars.searchValue.prop("disabled", false);
            typeClean.hide();
            dataTableVars.searchValue.show();
        } else {
            filterWithoutLike = true;
            dataTableVars.searchValue.val("");
            dataTableVars.searchValue.prop("disabled", false);
            typeClean.hide();
            dataTableVars.searchValue.show();

            switch (value) {
                // Columns that need a "=" instead the standard "LIKE" search operator.
                case "e.trip":
                    columnWithoutLike = value;
                    //columnValueWithoutLike = true;
                    break;
            }
        }
    });

});
