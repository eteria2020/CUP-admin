/* global filters:true, $ confirm document translate, getSessionVars:true */
$(function() {
    "use strict";

    // DataTables
    var table = $("#js-fines-table");
    
    var chageBtnPay = false;

    // Define DataTables Filters
    var dataTableVars = {
        searchValue: $("#js-value"),
        column: $("#js-column"),
        iSortCol_0: 0,
        sSortDir_0: "desc",
        iDisplayLength: 10,
        from: $("#js-date-from"),
        to: $("#js-date-to"),
        columnFromDate: $("#js-date-to"),
        columnToDate: $("#js-date-to")
    };

    var filterWithNull = true;
    
    var filterDate = false;
    var filterDateField = "e.insertTs";

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
            if (filterWithNull) {
                if (filterWithoutLike) {
                    aoData.push({"name": "column", "value": ""});
                    aoData.push({"name": "searchValue", "value": ""});
                    aoData.push({"name": "columnWithoutLike", "value": columnWithoutLike});
                    aoData.push({"name": "columnValueWithoutLike", "value": columnValueWithoutLike});
                } else {
                    aoData.push({"name": "column", "value": $(dataTableVars.column).val()});
                    aoData.push({"name": "searchValue", "value": dataTableVars.searchValue.val().trim()});
                }
            } else {
                aoData.push({"name": "column", "value": ""});
                aoData.push({"name": "searchValue", "value": ""});
            }
            aoData.push({ "name": "from", "value": $(dataTableVars.from).val()});
            aoData.push({ "name": "to", "value": $(dataTableVars.to).val()});
            aoData.push({ "name": "columnFromDate", "value": filterDateField});
            aoData.push({ "name": "columnFromEnd", "value": filterDateField});
            aoData.push({ "name": "fixedLike", "value": false});
        },
        "order": [[dataTableVars.iSortCol_0, dataTableVars.sSortDir_0]],
        "columns": [
            {data: "e.id"},
            {data: "e.payed"},
            {data: "e.customerId"},
            {data: "e.vehicleFleetId"},
            {data: "e.tripId"},
            {data: "e.carPlate"},
            {data: "e.violationAuthority"},
            {data: "e.amount"},
            {data: "e.violationDescription"},
            {data: "e.complete"},
            {data: "e.insertTs"}
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
                        case 0: return '<center><span class="glyphicon glyphicon-ok" title="Già addebitata"></span></center>';
                        case 1: return '<center><input class="checkbox" type="checkbox" name="check[]" value="'+row.fines.id+'" title="Addebitabile"></center>';
                        case 2: return '<center><span class="glyphicon glyphicon-remove" title="Non addebitabile"></span></center>';
                        default: return '---';   
                    }
                }
            },
            {
                targets: [2],
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
                targets: [3],
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
                targets: [4],
                sortable: false,
                "render": function (data, type, row) {
                    var str = row.fines.violationDescription;
                    return str.substring(0, 20) + '...';
                }
            },
            {
                targets: [5],
                sortable: false,
                "render": function (data, type, row) {
                    return (row.fines.vehicleFleetId != null) ? row.fines.vehicleFleetId : '---';
                }
            },
            {
                targets: [6],
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
                targets: [7],
                sortable: false,
                "render": function (data, type, row) {
                    return '<a href="/cars/edit/'+row.fines.carPlate+'">'+row.fines.carPlate+'</a>';
                }
            },
            {
                targets: [8],
                sortable: false,
                "render": function (data, type, row) {
                    var str = row.fines.violationAuthority;
                    return str.substring(0, 15) + '...';
                }
            },
            {
                targets: [9],
                sortable: false,
                "render": function (data, type, row) {
                    return renderAmount(row.fines.amount);
                }
            },
            {
                targets: [10],
                sortable: false,
                "render": function (data, type, row) {
                    if(row.fines.complete){
                        return 'Verbale in uscita (completo)';
                    }else{
                        return 'Verbale appena dematerializzato';
                    }
                }
            },
            {
                targets: [11],
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
        if($('#js-date-from').val() != ""){
            chageBtnPay = true;
            $('#pay-fines').fadeIn();
            $('#pay-fines').text("Paga multe tramite date");
        }else{
            $('#pay-fines').fadeOut();
            $('#pay-fines').text();
        }
        columnValueWithoutLike = dataTableVars.searchValue.val();

        // Filter Action
        table.fnFilter();
    });

    $("#js-clear").click(function() {
        dataTableVars.searchValue.val("");
        dataTableVars.from.val("");
        dataTableVars.to.val("");
        dataTableVars.searchValue.prop("disabled", false);
        typeClean.hide();
        dataTableVars.searchValue.show();
        dataTableVars.column.val("select");
        filterWithNull = true;
    });
        
    // Select Changed Action
    $(dataTableVars.column).change(function() {
        // Selected Column
        var value = $(this).val();
        dataTableVars.searchValue.val();
        filterWithNull = false;
        /*
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
        }*/

        // Column that need the standard "LIKE" search operator
        /*
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
        */
    });
    /*
    var intId = setInterval(function(){$("th").removeClass("sorting_desc");$("th").removeClass("sorting_asc");},100);
    setTimeout(function(){clearInterval(intId);},2000);
    */
    
    
    
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
            $('#pay-fines').fadeIn();
            $('#pay-fines').text("Paga multe sel.");
        }else{
            $('.checkbox').each(function(){
                this.checked = false;
            });
            if(chageBtnPay){
                $('#pay-fines').text("Paga multe tramite date");
            }else{
                $('#pay-fines').fadeOut();
                $('#pay-fines').text();
            }
        }
    });

    $(document).on("click", ".checkbox", function () {
        var selected = new Array();
        $(".checkbox:checked").each(function () {
            selected.push($(this).val());
        });
        if (selected.length > 0) {
            $('#pay-fines').fadeIn();
            $('#pay-fines').text("Paga multe sel.");
        } else {
            if(chageBtnPay){
                $('#pay-fines').text("Paga multe tramite date");
            }else{
                $('#pay-fines').fadeOut();
                $('#pay-fines').text();
            }
        }
    });
    
    $('#js-date-from').change(function() {
        if($(this).val() == ""){
            chageBtnPay = false;
        }
    });
    
    //create exra_payment and try payment to single fine in page details
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
    /*
    $('#pay-fines-betweenDate').click(function (){
        if($('#js-date-from').val() == "" && $('#js-date-to').val() == ""){
            $('#titleModal').text("Errore:");
            $('#body-text-modal').html("<div><p>Inserire almeno la data di partenza</p></div>");
            //$('#btn-modal-close').show();
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
                    var row = '';
                    result.forEach(function(element) {
                        selected.push(element['id']);
                        row += "<tr>" +
                                    "<th>"+element['name']+"</th>" +
                                    "<th>"+element['surname']+"</th>" +
                                    "<th>"+element['fleet']+"</th>" +
                                    "<th>"+element['trip_id']+"</th>" +
                                    "<th>"+element['car_plate']+"</th>" +
                                "</tr>";
                        
                    });
                    $('#titleModal').text("Elenco multe estratte");
                    $('#body-text-modal').html("<div style='width: 550px; height: 300px; overflow-y: scroll;'>" +
                                                "<table class='table table-striped table-bordered table-hover'>" +
                                                    "<thead>" +
                                                        "<tr>" +
                                                            "<th>Nome</th>" +
                                                            "<th>Cognome</th>" +
                                                            "<th>Flotta</th>" +
                                                            "<th>Targa</th>" +
                                                            "<th>Trip ID</th>" +
                                                        "</tr>" +
                                                    "</thead>" +
                                                    row +
                                                    "</table>" +
                                                "</div>");
                    //payFines(selected);
                },
                error: function () {
                    $('#titleModal').text("Errore:");
                    $('#body-text-modal').html("<div><p>Si è verificato un errore durante la procedura</p></div>");
                }
            });
        }
    });
    */
    
    var selecId = new Array();
    
    $('#pay-fines').click(function () {
        if ($('#pay-fines').text() == 'Paga multe sel.') {
            console.log("per seleizone");
            $(".checkbox:checked").each(function () {
                selecId.push($(this).val());
            });
            if (confirm("Stai mandando in pagemnto " + selecId.length + " multe. Confermi?")) {
                payFines(selecId);
            }
        } else {
            console.log("per data");
            if ($('#js-date-from').val() == "" && $('#js-date-to').val() == "") {
                console.log("date errate");
                console.log();
                $('#titleModal').text("Errore:");
                $('#body-text-modal').html("<div><p>Inserire almeno la data di partenza</p></div>");
                $('#btn-modal-close').show();
            } else {
                console.log("date corrette");
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
                        console.log(result['fine']);
                        var fine = result['fine'];
                        fine.forEach(function (element) {
                            selecId.push(element['id']);
                        });
                        console.log(selecId);
                        $('#body-text-modal').html("<div><p>Stai mettendo in pagemnto " + fine.length + "multe su " + result['nTotal']['nTotalFines'] + ". Continuare?</p></div>");
                        $('#btn-modal-close').show();
                        $('#btn-pay').show();
                    },
                    error: function () {
                        $('#titleModal').text("Errore:");
                        $('#body-text-modal').html("<div><p>Si è verificato un errore durante la procedura</p></div>");
                    }
                });
            }
        }
    });
    
    $('#btn-pay').click(function (){
        $('#conteiner-btn').fadeOut();
        $('#btn-modal-close').hide();
        $('#btn-pay').hide();
        payFines(selecId);
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
                    $('#conteiner-btn').fadeIn();
                    $('#btn-modal-close').show();
                }else{
                    $('#titleModal').text("Risulatato:");
                    $('#body-text-modal').html("<div><p>Multe passate: "+ result.n_success +"</p><p>Multe non passate: "+ result.n_fail +"</p></div>");
                    $('#conteiner-btn').fadeIn();
                    $('#btn-modal-close').show();
                }
            },
            error: function () {
                $('#titleModal').text("Errore NON GESTITO:");
                $('#body-text-modal').html("<div><p>Si è verificato un errore durante la procedura</p></div>");
                $('#conteiner-btn').fadeIn();
                $('#btn-modal-close').show();
            }
        });
    }
    
    $(document).on("click", "#btn-modal-close", function () {
        location.reload();
    });
});
