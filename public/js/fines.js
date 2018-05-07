/* global filters:true, $ confirm document translate, getSessionVars:true */
$(function() {
    "use strict";

    // DataTables
    var table = $("#js-fines-table");

    // Define DataTables Filters
    var dataTableVars = {
        searchValue: $("#js-value"),
        column: $("#js-column"),
        iSortCol_0: 1,
        sSortDir_0: "desc",
        iDisplayLength: 0
    };

    var typeClean = $("#js-clean-type"),
        filterWithoutLike = false,
        columnWithoutLike = false,
        columnValueWithoutLike = false;

    dataTableVars.searchValue.val("");
    dataTableVars.column.val("select");

    if (typeof getSessionVars !== "undefined"){
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
        "sAjaxSource": "/fines/datatable",
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
            aoData.push({ "name": "fixedLike", "value": false});
        },
        "order": [[dataTableVars.iSortCol_0, dataTableVars.sSortDir_0]],
        "columns": [
            {data: "e.id"},
            {data: "e.charged"},
            {data: "e.customerId"},
            {data: "e.vehicleFleetId"},
            {data: "e.tripId"},
            {data: "e.carPlate"},
            {data: "e.violationAuthority"},
            {data: "e.amount"},
            {data: "e.violationDescription"},
            {data: "e.complete"}
        ],
        "columnDefs": [
            {
                targets: [0],
                data: "button",
                searchable: false,
                sortable: false,
                render: function (data, type, row) {
                    return '<center><input class="checkbox" type="checkbox" name="check[]" value="'+row.fines.id+'"></center>';
                }
            },
            {
                targets: [1],
                sortable: false,
                "render": function (data, type, row) {
                    if(row.fines.charged){
                        return 'Si';
                    }else{
                        return 'No';
                    }
                }
            },
            {
                targets: [2],
                sortable: false,
                "render": function (data, type, row) {
                    if(row.fines.customerId>0){
                        return '<a href="/customers/edit/'+row.fines.customerId+'">'+row.fines.customerId+'</a>';
                    }else{
                        return 'no customer defined';
                    }
                }
            },
            {
                targets: [3],
                sortable: false,
                "render": function (data, type, row) {
                    return row.fines.violationDescription;
                }
            },
            {
                targets: [4],
                sortable: false,
                "render": function (data, type, row) {
                    if(row.fines.vehicleFleetId>0){
                        switch (row.fines.vehicleFleetId){
                            case 1:
                                return "Milano";
                            case 2:
                                return "Firenze";
                            case 3:
                                return "Roma";
                            case 4:
                                return "Modena";
                            default:
                                return row.fines.vehicleFleetId;
                        }
                    }else{
                        return 'no fleet defined';
                    }
                }
            },
            {
                targets: [5],
                sortable: false,
                "render": function (data, type, row) {
                    if(row.fines.tripId>0){
                        return '<a href="/trips/details/'+row.fines.tripId+'">'+row.fines.tripId+'</a>';
                    }else{
                        return 'no trip defined';
                    }
                }
            },
            {
                targets: [6],
                sortable: false,
                "render": function (data, type, row) {
                    return '<a href="/cars/edit/'+row.fines.carPlate+'">'+row.fines.carPlate+'</a>';
                }
            },
            {
                targets: [7],
                sortable: false,
                "render": function (data, type, row) {
                    return row.fines.violationAuthority;
                }
            },
            {
                targets: [8],
                sortable: false,
                "render": function (data, type, row) {
                    return renderAmount(row.fines.amount);
                }
            },
            {
                targets: [9],
                sortable: false,
                "render": function (data, type, row) {
                    if(row.fines.complete){
                        return 'Verbale appena dematerializzato';
                    }else{
                        return 'Verbale in uscita (completo)';
                    }
                }
            },
            {
                targets: [10],
                sortable: false,
                "render": function (data, type, row) {
                    return row.fines.violationTimestamp;
                }
            },
            {
                targets: [11],
                data: "button",
                searchable: false,
                sortable: false,
                render: function (data, type, row) {
                    return '<div class="btn-group">' +
                        '<a href="/fines/details/' + row.fines.id + '" class="btn btn-default">Dettagli</a> ' +
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
        if ((value === "e.violationDescription")||(value === "e.carPlate")||(value === "e.vehicleFleetId")||(value === "e.customerId")||(value === "e.tripId")) {
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
        }
    });
    var intId = setInterval(function(){$("th").removeClass("sorting_desc");$("th").removeClass("sorting_asc");},100);
    setTimeout(function(){clearInterval(intId);},2000);
});
