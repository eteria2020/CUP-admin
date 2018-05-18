/* global filters:true, $ confirm document translate, getSessionVars:true */
$(function() {
    "use strict";

    // DataTables
    var table = $("#js-fines-table");

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
            {data: "e.payed"},
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
                    return '<a href="/fines/details/' + row.fines.id + '">' + row.fines.id + '</a>';
                }
            },
            {
                targets: [1],
                searchable: false,
                sortable: false,
                render: function (data, type, row) {
                    switch (row.fines.checkable){
                        case 0: return '<center><span class="glyphicon glyphicon-ok"></span></center>';
                        case 1: return '<center><input class="checkbox" type="checkbox" name="check[]" value="'+row.fines.id+'"></center>';
                        case 2: return '<center><span class="glyphicon glyphicon-remove"></span></center>';
                        default: return '---';   
                    }
                }
            },
            {
                targets: [2],
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
                targets: [3],
                searchable: false,
                sortable: false,
                render: function (data, type, row) {
                    if(row.fines.payed != null ){
                        return row.fines.payed;
                    }else{
                        return '---';
                    }
                }
            },
            {
                targets: [4],
                sortable: false,
                "render": function (data, type, row) {
                    if(row.fines.customerId>0){
                        return '<a href="/customers/edit/'+row.fines.customerId+'">'+row.fines.customerId+'</a>';
                    }else{
                        return '---';
                    }
                }
            },
            {
                targets: [5],
                sortable: false,
                "render": function (data, type, row) {
                    var str = row.fines.violationDescription;
                    return str.substring(0, 20) + '...';
                }
            },
            {
                targets: [6],
                sortable: false,
                "render": function (data, type, row) {
                    return (row.fines.vehicleFleetId != null) ? row.fines.vehicleFleetId : '---';
                }
            },
            {
                targets: [7],
                sortable: false,
                "render": function (data, type, row) {
                    if(row.fines.tripId>0){
                        return '<a href="/trips/details/'+row.fines.tripId+'">'+row.fines.tripId+'</a>';
                    }else{
                        return '---';
                    }
                }
            },
            {
                targets: [8],
                sortable: false,
                "render": function (data, type, row) {
                    return '<a href="/cars/edit/'+row.fines.carPlate+'">'+row.fines.carPlate+'</a>';
                }
            },
            {
                targets: [9],
                sortable: false,
                "render": function (data, type, row) {
                    var str = row.fines.violationAuthority;
                    return str.substring(0, 15) + '...';
                }
            },
            {
                targets: [10],
                sortable: false,
                "render": function (data, type, row) {
                    return renderAmount(row.fines.amount);
                }
            },
            {
                targets: [11],
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
                targets: [12],
                sortable: false,
                "render": function (data, type, row) {
                    return row.fines.violationTimestamp;
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
    
    
    
    /*
     * 
     * 
     * 
     * 
     * 
     */
    
     $('#checkAll').click(function () {
        if ($('#checkAll').is(':checked')) {
            $('.checkbox').each(function(){
                this.checked = true;
            });
            $('#pay-fines-selected').prop('disabled', false);
            $('#pay-fines-betweenDate').prop('disabled', true);
        }else{
            $('.checkbox').each(function(){
                this.checked = false;
            });
            $('#pay-fines-selected').prop('disabled', true);
            $('#pay-fines-betweenDate').prop('disabled', false);
        }
    });

    $(document).on("click", ".checkbox", function () {
        var selected = new Array();
        $(".checkbox:checked").each(function () {
            selected.push($(this).val());
        });
        if (selected.length > 0) {
            $('#pay-fines-selected').prop('disabled', false);
            $('#pay-fines-betweenDate').prop('disabled', true);
        } else {
            $('#pay-fines-selected').prop('disabled', true);
            $('#pay-fines-betweenDate').prop('disabled', false);
        }
    });
    
    $('#pay-fines-betweenDate').click(function (){
        if($('#js-date-from').val() == "" && $('#js-date-to').val() == ""){
            $('#titleModal').text("Errore:");
            $('#body-text-modal').html("<div><p>Inserire almeno la data di partenza</p></div>");
            $('#btn-modal-close').show();
        } else {
            $.ajax({
                type: "POST",
                url: "/fines/find-fines-between-date/",
                data: {'from': $('#js-date-from').val(),
                        'to': $('#js-date-to').val()
                      },
                beforeSend: function () {
                    $('#titleModal').text("In elaborazione...");
                    $('#body-text-modal').html("<div><i class='fa fa-spinner fa-pulse fa-2x fa-fw'></i></div>");
                },
                success: function (data) {
                    var result = JSON.parse(data.toString());
                    var selected = new Array();
                    result.forEach(function(element) {
                        selected.push(element['id']);
                    });
                    payFines(selected);
                },
                error: function () {
                    $('#titleModal').text("Errore:");
                    $('#body-text-modal').html("<div><p>Si è verificato un errore durante la procedura</p></div>");
                }
            });
        }
    });
    
    $('#js-fine-try').click(function () {
        $.ajax({
            type: "POST",
            url: "/fines/pay/",
            data: {'check': [$('#id_penalty').html()]},
            success: function (data) {
                var result = JSON.parse(data);
                if(result.error == true){
                    $('#resultPay').html("<br><div class='alert alert-danger'>Errore di sistema</div>");
                    fadeOutPopUp();
                }else{
                    if(result.n_success == 1){
                        $('#resultPay').html("<br><div class='alert alert-success'>Penale addebitata e passata</div>");
                        fadeOutPopUp();
                    } else {
                        $('#resultPay').html("<br><div class='alert alert-warning'>Errore. Penale addebiatta ma non passata</div>");
                        fadeOutPopUp();
                    }
                }
            },
            error: function () {
               $('#resultPay').html("<br><div class='alert alert-danger'>Errore di sistema</div>");
               fadeOutPopUp();
            }
        });
    });
    
    function fadeOutPopUp() {
        $('#resultPay').fadeIn();
        setTimeout(function () {
            $('#resultPay').fadeOut();
            location.reload();
        }, 2000);
    }
    
    $('#pay-fines-selected').click(function () {
        var selected = new Array();
        $(".checkbox:checked").each(function () {
            selected.push($(this).val());
        });
        if(confirm("Stai mandando in pagemnto " + selected.length + " multe. Confermi?")){
            payFines(selected);
        }
    });

    function payFines(selected) {
        $.ajax({
            type: "POST",
            url: "/fines/pay/",
            data: {'check': selected},
            beforeSend: function () {
                $('#titleModal').text("In elaborazione...");
                $('#body-text-modal').html("<div><i class='fa fa-spinner fa-pulse fa-2x fa-fw'></i></div>");
            },
            success: function (data) {
                var result = JSON.parse(data);
                if(result.error == true){
                    $('#titleModal').text("Errore:");
                    $('#body-text-modal').html("<div><p>Si è verificato un errore durante la procedura</p></div>");
                    $('#btn-modal-close').show();
                }else{
                    $('#titleModal').text("Risulatato:");
                    $('#body-text-modal').html("<div><p>Multe passate: "+ result.n_success +"</p><p>Multe non passate: "+ result.n_fail +"</p></div>");
                    $('#btn-modal-close').show();
                }
            },
            error: function () {
                $('#titleModal').text("Errore NON GESTITO:");
                $('#body-text-modal').html("<div><p>Si è verificato un errore durante la procedura</p></div>");
                $('#btn-modal-close').show();
            }
        });
    }
    
    $(document).on("click", "#btn-modal-close", function () {
        location.reload();
    });
});
