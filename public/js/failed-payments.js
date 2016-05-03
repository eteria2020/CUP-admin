/* global filters:true, $ confirm document translate */
$(function() {
    "use strict";

     // DataTables
    var table = $("#js-payments-table");

    // Define DataTables Filters
    var searchValue = $("#js-value");
    var column = $("#js-column");
    var iSortCol_0 = 0;
    var sSortDir_0 = "desc";
    var iDisplayLength = 10;

    var typeClean = $("#js-clean-type"),
        filterWithoutLike = false,
        columnWithoutLike = false,
        columnValueWithoutLike = false;

    searchValue.val("");
    column.val("select");

    if (typeof filters !== "undefined"){
        if (typeof filters.searchValue !== "undefined"){
            searchValue.val(filters.searchValue);
        }
        if (typeof filters.column !== "undefined"){
            column.val(filters.column);
        }
        if (typeof filters.iSortCol_0 !== "undefined"){
            iSortCol_0 = filters.iSortCol_0;
        }
        if (typeof filters.sSortDir_0 !== "undefined"){
            sSortDir_0 = filters.sSortDir_0;
        }
        if (typeof filters.iDisplayLength !== "undefined"){
            iDisplayLength = filters.iDisplayLength;
        }
    }

    function toStringKeepZero(value)
    {
        return ((value < 10) ? "0" : "") + value;
    }

    function renderAmount(amount)
    {
        return (Math.floor(amount / 100)) +
            "," +
            toStringKeepZero(amount % 100) +
            " \u20ac";
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
            if(filterWithoutLike) {
                aoData.push({ "name": "column", "value": ""});
                aoData.push({ "name": "searchValue", "value": ""});
                aoData.push({ "name": "columnWithoutLike", "value": columnWithoutLike});
                aoData.push({ "name": "columnValueWithoutLike", "value": columnValueWithoutLike});
            } else {
                aoData.push({ "name": "column", "value": $(column).val()});
                aoData.push({ "name": "searchValue", "value": searchValue.val().trim()});
            }

            aoData.push({ "name": "fixedColumn", "value": "e.status"});
            aoData.push({ "name": "fixedValue", "value": "wrong_payment"});
            aoData.push({ "name": "fixedLike", "value": false});
        },
        "order": [[iSortCol_0, sSortDir_0]],
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
                        '" title="' + translate("tripDetailId") + ' ' + data +
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
        "pageLength": iDisplayLength,
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
        columnValueWithoutLike = searchValue.val();

        // Filter Action
        table.fnFilter();
    });

    $("#js-clear").click(function() {
        searchValue.val("");
        searchValue.prop("disabled", false);
        typeClean.hide();
        searchValue.show();
        column.val("select");
    });

    // Select Changed Action
    $(column).change(function() {
        // Selected Column
        var value = $(this).val();

        // Column that need the standard "LIKE" search operator
        if (value === "cu.surname") {
            filterWithoutLike = false;
            searchValue.val("");
            searchValue.prop("disabled", false);
            typeClean.hide();
            searchValue.show();
        } else {
            filterWithoutLike = true;
            searchValue.val("");
            searchValue.prop("disabled", false);
            typeClean.hide();
            searchValue.show();

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
