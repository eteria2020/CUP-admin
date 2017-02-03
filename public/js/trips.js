/* global $, filters:true, translate:true, getSessionVars:true */
$(function() {
    // DataTable
    var table = $("#js-trips-table");

    // Define DataTables Filters
    var dataTableVars = {
        searchValue: $("#js-value"),
        column: $("#js-column"),
        iSortCol_0: 0,
        sSortDir_0: "desc",
        iDisplayLength: 100,
        from: $("#js-date-from"),
        columnFromDate: "e.timestampBeginning",
        to: $("#js-date-to"),
        columnFromEnd: "e.timestampEnd"
    };

    var filterWithNull = false;

    dataTableVars.searchValue.val("");
    dataTableVars.column.val("select");

    if ( typeof getSessionVars !== "undefined"){
        getSessionVars(filters, dataTableVars);
    }

    table.dataTable({
        "processing": true,
        "serverSide": true,
        "bStateSave": false,
        "bFilter": false,
        "sAjaxSource": "/trips/datatable",
        "fnServerData": function ( sSource, aoData, fnCallback, oSettings ) {
            oSettings.jqXHR = $.ajax( {
                "dataType": "json",
                "type": "POST",
                "url": sSource,
                "data": aoData,
                "success": fnCallback,
                "statusCode": {
                    200: function(data, textStatus, jqXHR) {
                        loginRedirect(data, textStatus, jqXHR);
                    }
                }
            });
        },
        "fnServerParams": function ( aoData ) {
            if (filterWithNull) {
                aoData.push({ "name": "column", "value": ""});
                aoData.push({ "name": "searchValue", "value": ""});
                aoData.push({ "name": "columnNull", "value": "e.timestampEnd"});
            } else {
                aoData.push({ "name": "column", "value": $(dataTableVars.column).val()});
                aoData.push({ "name": "searchValue", "value": dataTableVars.searchValue.val().trim()});
            }
            aoData.push({ "name": "from", "value": $(dataTableVars.from).val().trim()});
            aoData.push({ "name": "to", "value": $(dataTableVars.to).val().trim()});
            aoData.push({ "name": "columnFromDate", "value": dataTableVars.columnFromDate});
            aoData.push({ "name": "columnFromEnd", "value": dataTableVars.columnFromEnd});
        },
        "order": [[dataTableVars.iSortCol_0, dataTableVars.sSortDir_0]],
        "columns": [
            {data: "e.id"},
            {data: "cu.email"},
            {data: "cu.surname"},
            {data: "cu.name"},
            {data: "cu.mobile"},
            {data: "cc.code"},
            {data: "c.plate"},
            {data: "e.kmBeginning"},
            {data: "e.kmEnd"},
            {data: "e.timestampBeginning"},
            {data: "e.timestampEnd"},
            {data: "duration"},
            {data: "e.parkSeconds"},
            {data: "c.keyStatus"},
            {data: "c.parking"},
            {data: "e.payable"},
            {data: "payed"},
            {data: "e.totalCost"},
            {data: "f.name"},
            {data: "e.idLink"}
        ],
        "columnDefs": [
            {
                targets: 1,
                visible: false
            },
            {
                targets: [2, 3],
                "render": function (data, type, row) {
                    return '<a href="/customers/edit/' + row.cu.id + '" title="' +
                        translate("showProfile") + " " + row.cu.name + " " +
                        row.cu.surname + ' ">' + data + '</a>';
                }
            },
            {
                targets: 11,
                sortable: false
            },
            {
                targets: 12,
                sortable: false,
                "render": function ( data ) {
                    return renderParkingMinutes(data);
                }
            },
            {
                targets: 13,
                sortable: false
            },
            {
                targets: 14,
                sortable: false
            },
            {
                targets: 17,
                sortable: false,
                "render": function ( data ) {
                    return renderCostButton(data);
                }
            },
            {
                targets: 19,
                sortable: false,
                "render": function ( data ) {
                    return renderInfoButton(data);
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
            "sEmptyTable": translate("sTripEmptyTable"),
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
        table.fnFilter();
    });

    $("#js-clear").click(function() {
        dataTableVars.searchValue.val("");
        dataTableVars.from.val("");
        dataTableVars.to.val("");
        dataTableVars.column.val("select");
        dataTableVars.searchValue.prop("disabled", false);
        filterWithNull = false;
        dataTableVars.searchValue.show();
    });

    $(".date-picker").datepicker({
        autoclose: true,
        format: "yyyy-mm-dd",
        weekStart: 1
    });

    $(dataTableVars.column).change(function() {
        var value = $(this).val();

        dataTableVars.searchValue.show();
        dataTableVars.searchValue.val("");

        if (value === "c.timestampEnd") {
            filterWithNull = true;
            dataTableVars.searchValue.prop("disabled", true);
        } else {
            filterWithNull = false;
            dataTableVars.searchValue.prop("disabled", false);
        }
    });

    function renderCostButton(data)
    {
        var amount = data.amount;
        if (amount !== "FREE") {
            return amount !== "" ?
                '<a href="/trips/details/' + data.id + '?tab=cost">' +
                renderAmount(parseInt(amount)) + '</a>' : "";
        }
        return amount;
    }

    function renderInfoButton(data)
    {
        return '<div class="btn-group">' + '<a href="/trips/details/' + data +
            '" class="btn btn-default">' + translate("details") + '</a> ' + '</div>';
    }

    function renderAmount(amount)
    {
        return (Math.floor(amount / 100)) + "," + toStringKeepZero(amount % 100) + " \u20ac";
    }

    function toStringKeepZero(value)
    {
        return ((value < 10) ? "0" : "") + value;
    }

    function renderParkingMinutes(data){
        data = data.replace(" sec", "").trim();
        if (data === "") {
            data = "0";
        }

        data = Math.round(parseInt(data) / 60);
        data = data.toString() + " min";
        return data;
    }
});
