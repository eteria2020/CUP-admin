/* global filters:true, $ confirm document translate, getSessionVars:true */
$(function() {
    "use strict";

     // DataTables
    var table = $("#js-extra-table");

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

    table.dataTable({
        "processing": true,
        "serverSide": true,
        "bStateSave": false,
        "bFilter": false,
        "sAjaxSource": "/payments/failed-extra-datatable",
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
            aoData.push({ "name": "fixedLike", "value": false});
        },
        "order": [[dataTableVars.iSortCol_0, dataTableVars.sSortDir_0]],
        "columns": [
            {data: "e.id"},
            {data: "e.generatedTs"},
            {data: "cu.id"},
            {data: "cu.name_surname"},
            {data: "cu.mobile"},
            {data: "e.reasons"},
            {data: "e.totalCost"},
            {data: "e.payed"},
            {data: "button"}
        ],
        "columnDefs": [
            {
                targets: 2,
                "render": function (data, type, row) {
                    return '<a href="/customers/edit/' + row.cu.id +
                        '" title="' + translate("customersDetailId") + ' ' + row.cu.name_surname + ' ">' + data + '</a>';
                }
            },
            {
                targets: 3,
                sortable: false,
                "render": function (data, type, row) {
                    return '<a href="/customers/edit/' + row.cu.id +
                        '" title="' + translate("customersDetailId") + ' ' + row.cu.name_surname + ' ">' + data + '</a>';
                }
            },
            {
                targets: 4,
                sortable: false
            },
            {
                targets: 5,
                "render": function (data, type, row) {
                    if (typeof row.e.reasons[0] === 'undefined' || row.e.reasons[0] === null) {
                        return '';
                    }else{
                        return row.e.reasons[0][0][0].substring(0, 20) + '...';
                    }
                }
            },
            {
                targets: 6,
                sortable: false,
                className: "sng-dt-right sng-no-wrap",
                "render": function (data, type, row) {
                    return (row.e.payed) ? 'Si' : 'No';
                }
            },
            {
                targets: 7,
                "render": function (data, type, row) {
                    return renderAmount(row.e.totalCost);
                }
            },
            {
                targets: 8,
                data: "button",
                searchable: false,
                sortable: false,
                render: function (data) {
                    return '<div class="btn-group">' +
                        '<a href="/payments/retry-extra/' + data + '" class="btn btn-default">' + translate("details") + '</a> ' +
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


    var filterDate = false;
    var filterDateField = "";
        
    // Select Changed Action
    $(dataTableVars.column).change(function() {
        dataTableVars.searchValue.val("")
        // Selected Column
        var value = $(this).val();

        filterDate = false;
        filterDateField = "";
        dataTableVars.searchValue.show();
        dataTableVars.searchValue.prop("disabled", false);
        $(dataTableVars.searchValue).datepicker("remove");
        
        switch (value) {
            case "e.generatedTs":
                filterDate = true;
                filterDateField = value;
                dataTableVars.searchValue.val("");
                $(dataTableVars.searchValue).datepicker({
                    autoclose: true,
                    format: "yyyy-mm-dd",
                    weekStart: 1
                });
                break;
            case "cu.id":
            case "e.reasons":
            case "e.id":
                dataTableVars.searchValue.val();
                break;
            case "cu.surname":
            case "cu.email":
                filterWithoutLike = false;
                dataTableVars.searchValue.val("");
                dataTableVars.searchValue.prop("disabled", false);
                typeClean.hide();
                dataTableVars.searchValue.show();
                break;
            case "e.status":
                if($('#js-column option:selected').text() === "Pagato SI"){
                    dataTableVars.searchValue.val("payed_correctly");
                }else{
                    dataTableVars.searchValue.val("wrong_payment");
                }
                dataTableVars.searchValue.prop("disabled", true);
                break;
            default:
                dataTableVars.searchValue.val("");
                dataTableVars.searchValue.prop("disabled", true);
                break;
        }
    });

});