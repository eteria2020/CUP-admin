/* global  filters:true, translate:true, $, getSessionVars:true, jstz:true, moment:true, document: true */
$(function() {
    "use strict";
    
    $('#btnAllarmDiv').hide();

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
        "fnServerData": function ( sSource, aoData, fnCallback, oSettings ) {
            oSettings.jqXHR = $.ajax( {
                "dataType": "json",
                "type": "POST",
                "url": sSource,
                "data": aoData,
                "success": fnCallback
            }).done(function( aoData ) {
                if(aoData['checkAllarm']){
                    $('#btnAllarmDiv').show();
                    $('#audioAllarmDiv').html("<audio id='audio' src='/audio/beep45.wav' autoplay></audio>");
                }
            });
        },
        "fnServerParams": function ( aoData ) {
            if (filterDate) {
                aoData.push({ "name": "column", "value": ""});
                aoData.push({ "name": "searchValue", "value": ""});
                aoData.push({ "name": "from", "value": dataTableVars.searchValue.val().trim()});
                aoData.push({ "name": "to", "value": dataTableVars.searchValue.val().trim()});
                aoData.push({ "name": "columnFromDate", "value": "e." + filterDateField});
                aoData.push({ "name": "columnFromEnd", "value": "e." + filterDateField});
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
            {data: "np.name"},
            {data: "button"}
        ],
        "columnDefs": [
            {
                targets: 0,
                sortable: true
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
                    console.log(data);
                    console.log(row);
                    if (data == null) {
                        var buttons = "<div class=\"btn-group\">" +
                                "<a href=\"/notifications/take-charge/" + row.e.id + "\" class=\"btn btn-default\">" +
                                "Prendi in carico </a></div>";
                        return buttons;
                    } else {
                        return data;
                    }
                }
            },
            {
                targets: 8,
                data: "button",
                searchable: false,
                sortable: false,
                render: function (data, type, row) {
                    var buttons = "<div class=\"btn-group\">";
                    /*
                    // Check if the notification have no ack date.
                    if (typeof row.e.acknowledgeDate !== "number" && row.np.name === "Web") {
                        buttons += "<div class=\"btn btn-default\" id=\"ack-button\" data-id=\"" + data + "\">" +
                                translate("acknowledgment") + "</div> ";
                    }
                    */
                    buttons += "<a href=\"/notifications/details/" + data + "\" class=\"btn btn-default\">" +
                            translate("details") + "</a></div>";
                    return buttons;
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

    $('#btnAllarm').click(function () {
        $.ajax({
            type: "POST",
            url: "/notifications/stop-allarm",
            data: {'checkAllarm': false},
            success: function (data) {
                $('#audioAllarmDiv').html("<audio id='audio' src='/audio/beep45.wav'></audio>");
                $('#btnAllarmDiv').hide();
            },
            error: function () {
                console.log("ERROR stop-allarm");
            }
        });
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

    $(document).on("click", "#ack-button", function() {
        var thisButton = $(this);
        var notificationId = thisButton.data("id");
        var ackColumn = thisButton.parents("tr").children()[4];

        $.ajax({
            method: "GET",
            url: "/notifications/acknowledgment/" + notificationId,
            dataType: "json",
            beforeSend: function(){
                $(".dataTables_processing").show();
            }
        })
        .success(function(data) {
            var dateTimeStamp;
            if (typeof data.dateTimeStamp === "number"){
                dateTimeStamp = data.dateTimeStamp;
                $(ackColumn).html(moment(dateTimeStamp, "X").tz(userTimeZone).format("DD-MM-YYYY - HH:mm:ss"));
                thisButton.remove();
            }
        })
        .complete(function() {
            $(".dataTables_processing").hide();
        });
    });
});
