/* global  filters:true, translate:true, $, getSessionVars:true, jstz:true, moment:true */
$(function() {
    "use strict";

    // Detect user timezone
    var userTimeZone = moment.tz.guess(); // Determines the time zone of the browser client

    // DataTables
    var table = $("#js-notifications-table");

    // Define DataTables Filters
    var dataTableVars = {
        searchValue: $("#js-value"),
        column: $("#js-column"),
        iSortCol_0: 2,
        sSortDir_0: "desc",
        iDisplayLength: 10
    };

    var filterDate = false,
        filterDateField = "";

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
        "sAjaxSource": "/notifications/datatable",
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
                    if (typeof data === "number"){
                        return moment(data, "X").tz(userTimeZone).format("DD-MM-YYYY - HH:mm:ss");
                    }
                    return "";
                }
            },
            {
                targets: 7,
                data: "button",
                searchable: false,
                sortable: false,
                render: function (data, type, row) {
                    var buttons = "<div class=\"btn-group\">";

                    // Check if the notification have no ack date.
                    if (typeof row.e.acknowledgeDate !== "number"){
                        buttons += "<div class=\"btn btn-default\" id=\"ack-button\" data-id=\"" + data + "\">" +
                        translate("acknowledgment") + "</div> ";
                    }

                    buttons += "<a href=\"/cars/details/" + data + "\" class=\"btn btn-default\">" +
                    translate("details") + "</a></div>";
                    return buttons;
                }
            }
        ],
        "lengthMenu": [
            [dataTableVars.iDisplayLength, 200, 300],
            [dataTableVars.iDisplayLength, 200, 300]
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
    });

    $(".date-picker").datepicker({
        autoclose: true,
        format: "yyyy-mm-dd",
        weekStart: 1
    });

    $(dataTableVars.column).change(function() {
        var value = $(this).val();
        if (value === "submitDate" || value === "sentDate" || value === "acknowledgeDate" ) {
            filterDate = true;
            filterDateField = value;
            dataTableVars.searchValue.val("");
            $(dataTableVars.searchValue).datepicker({
                autoclose: true,
                format: "yyyy-mm-dd",
                weekStart: 1
            });
        } else {
            filterDate = false;
            filterDateField = "";
            dataTableVars.searchValue.val("");
            $(dataTableVars.searchValue).datepicker("remove");
        }
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
