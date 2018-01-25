/* global  filters:true, translate:true, $, getSessionVars:true, jstz:true, moment:true, document: true */
$(function() {
    "use strict";
    
    setTimeout(function () {
        if ($('#refresh').text() === "ON")
            location.reload();
    }, 15000);

    $(document).on("click", "#refresh", function () {
        var refresh = "";
        if ($('#refresh').text() === "ON") {
            $('#divRefresh').html("<h4>Auto-refresh: &nbsp<button type='button' style='width: 80px;' class='btn red' id='refresh'>OFF</button></h4>");
            refresh = "off";
        } else {
            $('#divRefresh').html("<h4>Auto-refresh: &nbsp<button type='button' style='width: 80px;' class='btn green' id='refresh'>ON</button></h4>");
            refresh = "on";
            setTimeout(function () {
                if ($('#refresh').text() === "ON") 
                    location.reload();
            }, 15000);
        }
        $.ajax({
            type: "POST",
            url: "/notifications/auto-refresh-notifications",
            data: {'refresh': refresh},
            success: function (data) {
            },
            error: function () {
                console.log("ERROR auto-refresh-notifications");
            }
        });
    });

    $(document).on("click", "#sound", function () {
        var onOff = "";
        if ($('#sound').text() === "ON") {
            $('#divSoundAllarm ').html("<h4>Sound: &nbsp<button type='button' style='width: 80px;' class='btn red' id='sound'>OFF</button></h4>");
            onOff = "off";
        } else {
            $('#divSoundAllarm ').html("<h4>Sound: &nbsp<button type='button' style='width: 80px;' class='btn green' id='sound'>ON</button></h4>");
            onOff = "on";
        }
        $.ajax({
            type: "POST",
            url: "/notifications/on-off-allarm",
            data: {'onOff': onOff},
            success: function (data) {
            },
            error: function () {
                console.log("ERROR on-off-allarm");
            }
        });
    });

    // Detect user timezone
    var userTimeZone = moment.tz.guess(); // Determines the time zone of the browser client

    // DataTables
    var table = $("#js-notifications-table");

    // Define DataTables Filters
    var dataTableVars = {
        searchValue: $("#js-value"),
        notificationsCategory: $("#js-notification-category"),
        notificationsProtocol: $("#js-notification-protocol"),
        column: $("#js-column"),
        from: $("#js-value"),
        columnFromDate: $("#js-value"),
        to: $("#js-value"),
        columnToDate: $("#js-value"),
        iSortCol_0: 2,
        sSortDir_0: "desc",
        iDisplayLength: 10
    };

    var filterDate = false,
        filterDateField = "";

    var renderSearchField = function(selectDOMObject){
        var selectVal = selectDOMObject.val();

        filterDate = false;
        filterDateField = "";
        dataTableVars.searchValue.show();
        dataTableVars.searchValue.prop("disabled", false);
        $(dataTableVars.searchValue).datepicker("remove");
        dataTableVars.notificationsProtocol.hide();
        dataTableVars.notificationsCategory.hide();

        switch (selectVal) {
            case "e.submitDate":
            case "e.sentDate":
            case "e.acknowledgeDate":
                filterDate = true;
                filterDateField = selectVal;
                dataTableVars.searchValue.val("");
                $(dataTableVars.searchValue).datepicker({
                    autoclose: true,
                    format: "yyyy-mm-dd",
                    weekStart: 1
                });
                break;
            case "nc.name":
                dataTableVars.searchValue.hide();
                dataTableVars.searchValue.val(dataTableVars.notificationsCategory.val());
                dataTableVars.notificationsCategory.show();

                // Bind notificationsCategory select change action
                $(dataTableVars.notificationsCategory).change(function() {
                    dataTableVars.searchValue.val(dataTableVars.notificationsCategory.val());
                });
                break;
            case "np.name":
                dataTableVars.searchValue.hide();
                dataTableVars.searchValue.val(dataTableVars.notificationsProtocol.val());
                dataTableVars.notificationsProtocol.show();

                // Bind notificationsProtocol select change action
                $(dataTableVars.notificationsProtocol).change(function() {
                    dataTableVars.searchValue.val(dataTableVars.notificationsProtocol.val());
                });
                break;
            case "e.id":
            case "e.subject":
                dataTableVars.searchValue.val("");
                break;
            case "e.webuser":
                dataTableVars.searchValue.val();
                break;
            default:
                dataTableVars.searchValue.val("");
                dataTableVars.searchValue.prop("disabled", true);
                break;
        }
    };

    dataTableVars.searchValue.val("");
    dataTableVars.column.val("select");

    if ( typeof getSessionVars !== "undefined"){
        getSessionVars(filters, dataTableVars);
        renderSearchField($(dataTableVars.column));
    }

    table.dataTable({
        "processing": true,
        "serverSide": true,
        "bStateSave": false,
        "bFilter": false,
        "sAjaxSource": "/notifications/datatable",
        "fnServerData": function (sSource, aoData, fnCallback, oSettings) {
            oSettings.jqXHR = $.ajax({
                "dataType": "json",
                "type": "POST",
                "url": sSource,
                "data": aoData,
                "success": fnCallback
            }).done(function (aoData) {
                if (aoData['onOff'] === "on") {
                    $('#divSoundAllarm').html("<h4>Sound: &nbsp<button type='button' style='width: 80px;' class='btn green' id='sound'>ON</button></h4>");
                } else {
                    $('#divSoundAllarm').html("<h4>Sound: &nbsp<button type='button' style='width: 80px;' class='btn red' id='sound'>OFF</button></h4>");
                }
                if (aoData['checkAllarm'] && aoData['onOff'] === "on") {
                    $('#audioAllarmDiv').html("<audio id='audio' src='/audio/beep45.wav' autoplay></audio>");
                }
                if (aoData['refresh'] === "on") {
                    $('#divRefresh').html("<h4>Auto-refresh: &nbsp<button type='button' style='width: 80px;' class='btn green' id='refresh'>ON</button></h4>");
                } else {
                    $('#divRefresh').html("<h4>Auto-refresh: &nbsp<button type='button' style='width: 80px;' class='btn red' id='refresh'>OFF</button></h4>");
                }
            });
        },
        "fnServerParams": function ( aoData ) {
            if (filterDate) {
                aoData.push({ "name": "column", "value": ""});
                aoData.push({ "name": "searchValue", "value": ""});
                aoData.push({ "name": "from", "value": dataTableVars.searchValue.val().trim()});
                aoData.push({ "name": "to", "value": dataTableVars.searchValue.val().trim()});
                aoData.push({ "name": "columnFromDate", "value": filterDateField});
                aoData.push({ "name": "columnFromEnd", "value": filterDateField});
            } else {
                aoData.push({ "name": "column", "value": $(dataTableVars.column).val()});
                aoData.push({ "name": "searchValue", "value": dataTableVars.searchValue.val().trim()});
            }
        },
        "order": [[dataTableVars.iSortCol_0, dataTableVars.sSortDir_0]],
        "columns": [
            {data: "e.id"},
            {data: "e.subject"},
            {data: "e.submitDate"},
            {data: "e.sentDate"},
            {data: "e.acknowledgeDate"},
            {data: "e.webuser"},
            {data: "nc.name"},
            {data: "np.name"}
        ],
        "columnDefs": [
            {
                targets: 0,
                sortable: true,
                render: function (data, type, row) {
                    return '<a href="notifications/details/' +  row.e.id + ' ">' + row.e.id + '</a>';
                }
                
            },
            {
                targets: [2, 3, 4],
                render: function (data) {
                    var momentDate;
                    if (typeof data === "number") {
                        momentDate = moment(data, "X");
                        if (momentDate.isValid()) {
                            return momentDate.tz(userTimeZone).format("DD-MM-YYYY - HH:mm:ss");
                        }
                    }
                    return "";
                }
            },
            {
                targets: 5,
                render: function (data, type, row) {
                    if (data == null) {
                        var buttons = "<div class=\"btn-group\">" +
                                "<a href=\"/notifications/take-charge/" + row.e.id + "\" class=\"btn btn-default\">" +
                                "Prendi in carico </a></div>";
                        return buttons;
                    } else {
                        return data;
                    }
                }
            }
        ],
        "lengthMenu": [
            [dataTableVars.iDisplayLength, dataTableVars.iDisplayLength * 5, dataTableVars.iDisplayLength * 10],
            [dataTableVars.iDisplayLength, dataTableVars.iDisplayLength * 5, dataTableVars.iDisplayLength * 10]
        ],
        "pageLength": dataTableVars.iDisplayLength,
        "pagingType": "bootstrap_full_number",
        "language": {
            "sEmptyTable": translate("sNotificationsEmptyTable"),
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
        dataTableVars.notificationsProtocol.hide();
        dataTableVars.notificationsCategory.hide();
    });

    $(".date-picker").datepicker({
        autoclose: true,
        format: "yyyy-mm-dd",
        weekStart: 1
    });

    $(dataTableVars.column).change(function() {
        renderSearchField($(this));
    });
});
