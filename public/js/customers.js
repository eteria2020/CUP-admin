/* global  filters:true, $, document, translate:true, getSessionVars:true */
$(function() {
    // DataTables
    var table = $("#js-customers-table");

    // Define DataTables Filters
    var dataTableVars = {
        searchValue: $("#js-value"),
        column: $("#js-column"),
        iSortCol_0: 0,
        sSortDir_0: "desc",
        iDisplayLength: 100
    };

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
        "sAjaxSource": "/customers/datatable",
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
            aoData.push({ "name": "column", "value": $(dataTableVars.column).val()});
            aoData.push({ "name": "searchValue", "value": dataTableVars.searchValue.val().trim()});
        },
        "order": [[dataTableVars.iSortCol_0, dataTableVars.sSortDir_0]],
        "columns": [
            {data: "e.id"},
            {data: "e.name"},
            {data: "e.surname"},
            {data: "e.mobile"},
            {data: "cc.code"},
            {data: "e.driverLicense"},
            {data: "e.driverLicenseExpire"},
            {data: "e.email"},
            {data: "e.taxCode"},
            {data: "e.registration"},
            {data: "button"}
        ],
        "columnDefs": [
            {
                targets: 9,
                searchable: false,
                sortable: false
            },
            {
                targets: 10,
                data: "button",
                searchable: false,
                sortable: false,
                render: function (data) {
                    return '<a href="/customers/edit/' + data + '" class="btn btn-default btn-xs">' + translate("modify") + '</a>';
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
        table.fnFilter();
    });

    $("#js-clear").click(function() {
        dataTableVars.searchValue.val("");
        dataTableVars.column.val("select");
    });

    $(".date-picker").datepicker({
        autoclose: true,
        format: "dd-mm-yy",
        weekStart: 1
    });

    $(document).on("click", "#js-remove-card", function() {
        var removeCardConfirm = confirm(translate("removeCardConfirm"));
        var customer = $(this).data("id");

        if (removeCardConfirm) {
            $.ajax({
                url: "/customers/remove-card/" + customer,
                type: "POST",
                data: {},
                processData: false,
                contentType: false,
                dataType: "json",
                cache: false,
                statusCode: {
                    200: function () {
                        $("#js-with-code").hide();
                        $("#js-no-code").show();
                    },
                    500: function () {
                        alert(translate("ajaxError"));
                    }
                }
            });
        }
    });

    $(document).on("click", "#js-assign-card", function() {
        var customer = $(this).data("id");
        var code = $(this).data("code");
        $.ajax({
            url: "/customers/assign-card/" + customer,
            type: "POST",
            data: {
                code: code
            },
            cache: false,
            statusCode: {
                200: function (response) {
                    $("#js-assign-card").hide();
                    $("#js-no-code").hide();
                    $("#js-with-code").show();
                    $("#js-code").text(code);
                    $("#typeahead-input").val("");
                },
                500: function (response) {
                    alert(translate("ajaxError"));
                }
            }
        });
    });

    $(document).on("click", "#js-remove-bonus", function() {
        var removeBonus = confirm(translate("removeBonusConfirm"));
        var customer = $(this).data("id");
        var bonus = $(this).data("bonus");

        if (removeBonus) {
            $.ajax({
                url: "/customers/remove-bonus/" + customer,
                type: "POST",
                data: {
                    bonus: bonus
                },
                cache: false,
                statusCode: {
                    200: function (response) {
                        $("#js-row-bonus-" + bonus).hide();
                        $("#js-message").show();
                        setTimeout(function(){
                            $("#js-message").hide();
                        }, 3000);
                    },
                    500: function (response) {
                        alert(translate("ajaxError"));
                    }
                }
            });
        }
    });
    
    $(document).on("click", "#js-remove-point", function() {
        var removePonit = confirm(translate("removepointConfirm"));
        var customer = $(this).data("id");
        var point = $(this).data("point");

        if (removePonit) {
            $.ajax({
                url: "/customers/remove-point/" + customer,
                type: "POST",
                data: {
                    point: point
                },
                cache: false,
                statusCode: {
                    200: function (response) {
                        $("#js-row-point-" + point).hide();
                        $("#js-message").show();
                        setTimeout(function(){
                            $("#js-message").hide();
                        }, 3000);
                    },
                    500: function (response) {
                        alert(translate("ajaxError"));
                    }
                }
            });
        }
    });//fine click #js-remove-point
});

