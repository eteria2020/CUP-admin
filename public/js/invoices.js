/* global  filters:true, translate:true, $ */
$(function() {
    // DataTables
    var table = $("#js-invoices-table");

    // Define DataTables Filters
    var dataTableVars = {
        searchValue: $("#js-value"),
        column: $("#js-column"),
        iSortCol_0: 0,
        sSortDir_0: "desc",
        iDisplayLength: 100
    };

    var typePayment = $("#js-payment-type");

    dataTableVars.searchValue.val("");
    dataTableVars.column.val("select");

    if ( typeof getSessionVars === "undefined"){
        console.log("datatalbe-session-data.js Not loaded.");
        return;
    } else {
        getSessionVars(filters, dataTableVars);
    }

    table.dataTable({
        "processing": true,
        "serverSide": true,
        "bStateSave": false,
        "bFilter": false,
        "sAjaxSource": "/invoices/datatable",
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
            aoData.push({ "name": "column", "value": $(dataTableVars.column).val()});
            aoData.push({ "name": "searchValue", "value": $(dataTableVars.searchValue).val().trim()});
        },
        "order": [[dataTableVars.iSortCol_0, dataTableVars.sSortDir_0]],
        "columns": [
            {data: "e.invoiceNumber"},
            {data: "e.invoiceDate"},
            {data: "cu.name"},
            {data: "cu.surname"},
            {data: "e.type"},
            {data: "e.amount"},
            {data: "link"}
        ],
        "columnDefs": [
            {
                targets: 1,
                "render": function ( data ) {
                    return renderDate(data);
                }
            },
            {
                targets: [2, 3],
                "render": function ( data, type, row ) {
                    return '<a href="/customers/edit/'+row.cu.id+'" title="' + translate("showProfileOf") + ' '+row.cu.name+' '+row.cu.surname+'">'+data+'</a>';
                }
            },
            {
                targets: 4,
                "render": function ( data ) {
                    return renderType(data);
                }
            },
            {
                targets: 5,
                className: "sng-dt-right",
                "render": function ( data ) {
                    return renderAmount(data);
                }
            },
            {
                targets: 6,
                sortable: false,
                className: "sng-dt-center",
                "render": function ( data ) {
                    return renderLink(data);
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
            "sEmptyTable": translate("sInvoicesEmptyTable"),
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
        dataTableVars.searchValue.prop("disabled", false);
        dataTableVars.searchValue.show();
        dataTableVars.column.val("select");
    });

    $(dataTableVars.column).change(function() {
        dataTableVars.searchValue.val("");
        typePayment.hide();
        dataTableVars.searchValue.show();
    });

    function renderDate(date)
    {
        return toStringKeepZero(date % 100) + "/" +
        toStringKeepZero(Math.floor((date / 100) % 100)) + "/" +
        (Math.floor(date / 10000));
    }

    function renderAmount(amount)
    {
        return (Math.floor(amount / 100)) +
            "," +
            toStringKeepZero(amount % 100) +
            " \u20ac";
    }

    function renderType(type)
    {
        switch (type) {
            case "FIRST_PAYMENT":
                return translate("renderFirstPayment");
            case "TRIP":
                return translate("renderTrip");
            case "PENALTY":
                return translate("renderPenality");
            case "BONUS_PACKAGE":
                return translate("renderBonusPackage");
        }
    }

    function renderLink(id)
    {
        return '<a href=' + pdfPath + id + '><i class="fa fa-download"></i></a>';
    }

    function toStringKeepZero(value)
    {
        return ((value < 10) ? "0" : "") + value;
    }
});
