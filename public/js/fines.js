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

    var filterWithNull = false;
    
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
    
    dataTableVars.column.val("select");

    table.dataTable({
        "processing": true,
        "serverSide": true,
        "bStateSave": false,
        "bFilter": false,
        "sAjaxSource": "/fines/datatable",
        "createdRow": function (row, data, dataIndex, cells) {
            switch (data.cu.type) {
                case 1:
                    $('td', row).css('background', '#ffe6e6');
                    break;
                case 2:
                    $('td', row).css('background', '#f2ffcc');
                    break;
                case 3:
                    $('td', row).css('background', '#dfdfdf');
                    break;
            }
        },
        "fnServerData": function (sSource, aoData, fnCallback, oSettings) {
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
            }).done(function (aoData) {
//                if (aoData['visible'] === true) {
//                    $(".glyphicon-remove").parent().parent().parent().show();
//                } else {
//                    $(".glyphicon-remove").parent().parent().parent().hide();
//                }
            });
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
            aoData.push({ "name": "from", "value": $("#js-date-from").val()});
            aoData.push({ "name": "to", "value": $("#js-date-to").val()});
            aoData.push({ "name": "columnFromDate", "value": filterDateField});
            aoData.push({ "name": "columnFromEnd", "value": filterDateField});
            
            if ($("input[name='remove-not-payed']:checked").val() == 1) {
                aoData.push({ "name": "columnNotNull", "value": ["e.customer","e.trip","e.car"] });
                aoData.push({ "name": "columnWhereValue", "value": [true,true,'payed_correctly','invoiced', false] });
                aoData.push({ "name": "columnWhere", "value": "e.charged = :where_4 AND e.payable = :where_0 AND e.complete = :where_1 AND (e.extrapayment IS NULL OR NOT (ep.status = :where_2 OR ep.status = :where_3))" });
            }
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
            {data: "e.insertTs"},
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
                    if(row.fines.carPlate != null){
                        return '<a href="/cars/edit/'+row.fines.carPlate+'">'+row.fines.carPlate+'</a>';
                    }else{
                        return '---';
                    }
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
        $("#js-date-from").val("");
        $("#js-date-to").val("");
        dataTableVars.searchValue.prop("disabled", false);
        typeClean.hide();
        dataTableVars.searchValue.show();
        dataTableVars.column.val("select");
        filterWithNull = false;
    });
        
    // Select Changed Action
    $(dataTableVars.column).change(function() {
        // Selected Column
        dataTableVars.searchValue.prop("disabled", false);
        var value = $(this).val();
        if (value === "e.complete") {
            dataTableVars.searchValue.val("true");
            dataTableVars.searchValue.prop("disabled", true);
        } else{
            dataTableVars.searchValue.val();
        }
        filterWithNull = true;
    });
    /*
    var intId = setInterval(function(){$("th").removeClass("sorting_desc");$("th").removeClass("sorting_asc");},100);
    setTimeout(function(){clearInterval(intId);},2000);
    */
    
     $('#checkAll').click(function () {
        if ($('#checkAll').is(':checked')) {
            $('.checkbox').each(function(){
                this.checked = true;
            });
            $('#pay-fines').fadeIn();
            $('#pay-fines').text("Paga multe sel.");
            $('#not-payable').fadeIn();
            $('#not-payable').text("No addebito sel.");
        }else{
            $('.checkbox').each(function(){
                this.checked = false;
            });
            if(chageBtnPay){
                $('#pay-fines').text("Paga multe tramite date");
            }else{
                $('#pay-fines').fadeOut();
                $('#pay-fines').text();
                $('#not-payable').fadeOut();
                $('#not-payable').text();
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
            $('#not-payable').fadeIn();
            $('#not-payable').text("No addebito sel.");
        } else {
            if(chageBtnPay){
                $('#pay-fines').text("Paga multe tramite date");
            }else{
                $('#pay-fines').fadeOut();
                $('#pay-fines').text();
                $('#not-payable').fadeOut();
                $('#not-payable').text();
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
            location.reload(true);
        }, 2000);
    }
    
    var selecId = new Array();
    
    $('#pay-fines').click(function () {
        if ($('#pay-fines').text() == 'Paga multe sel.') {
            $(".checkbox:checked").each(function () {
                selecId.push($(this).val());
            });
            if (confirm("Stai mandando in pagamento " + selecId.length + " multe. Vuoi proseguire?")) {
                payFines(selecId);
            }else{
                $('#titleModal').text("Notifica:");
                $('#body-text-modal').html("<div><p>Hai annullato il processo di pagamento delle multe selezionate</p></div>");
                $('#btn-modal-close').show();
            }
        } else {
            if ($('#js-date-from').val() == "" && $('#js-date-to').val() == "") {
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
                        var fine = result['fine'];
                        fine.forEach(function (element) {
                            selecId.push(element['id']);
                        });
                        $('#titleModal').text("Multe estratte:");
                        $('#body-text-modal').html("<div><p>Stai mettendo in pagemnto " + fine.length + " multe su " + result['nTotal']['nTotalFines'] + ". Continuare?</p></div>");
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

    $('#not-payable').click(function () {
        if ($('#not-payable').text() == 'No addebito sel.') {
            $(".checkbox:checked").each(function () {
                selecId.push($(this).val());
            });
            if (confirm("Stai contrassegnando come non addebitabili " + selecId.length + " multe. Vuoi proseguire?")) {
                notPayableFines(selecId);
            } else {
                $('#titleModal').text("Notifica:");
                $('#body-text-modal').html("<div><p>Hai annullato il processo delle multe selezionate</p></div>");
                $('#btn-modal-close').show();
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
                    $('#titleModal').text("Risultato:");
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

    function notPayableFines(selected) {
        $.ajax({
            type: "POST",
            url: "/fines/payable/",
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
                    $('#titleModal').text("Risultato:");
                    $('#body-text-modal').html("<div><p>Multe segnate come non addebitabili: "+ result.n_success +"</p><p>Multe non passate: "+ result.n_fail +"</p></div>");
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
        location.reload(true);
    });
    
//    $("input[@name='remove-not-payed']").change(function(){
//    // Do something interesting here
//    });

    
    
    $('input[type=radio][name=remove-not-payed]').on('change', function () {
        
        // Filter Action
        table.fnFilter();
    });
        
});
