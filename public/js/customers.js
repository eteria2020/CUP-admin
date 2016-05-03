/* global  filters:true */
$(function() {
    // DataTables
    var table = $('#js-customers-table');

    // Define DataTables Filters
    var searchValue = $('#js-value');
    var column = $('#js-column');
    var iSortCol_0 = 0;
    var sSortDir_0 = "desc";
    var iDisplayLength = 100;

    if(typeof filters !== "undefined"){
        if(typeof filters.searchValue !== "undefined"){
            searchValue.val(filters.searchValue);
        }
        if(typeof filters.column !== "undefined"){
            column.val(filters.column);
        }
        if(typeof filters.iSortCol_0 !== "undefined"){
            iSortCol_0=filters.iSortCol_0;
        }
        if(typeof filters.sSortDir_0 !== "undefined"){
            sSortDir_0=filters.sSortDir_0;
        }
        if(typeof filters.iDisplayLength !== "undefined"){
            iDisplayLength=filters.iDisplayLength;
        }
    }

    table.dataTable({
        "processing": true,
        "serverSide": true,
        "bStateSave": false,
        "bFilter": false,
        "sAjaxSource": "/customers/datatable",
        "fnServerData": function ( sSource, aoData, fnCallback, oSettings ) {
            oSettings.jqXHR = $.ajax( {
                "dataType": 'json',
                "type": "POST",
                "url": sSource,
                "data": aoData,
                "success": fnCallback,
                "error": function(jqXHR, textStatus, errorThrown) {

                    /*
                    if (jqXHR.status == '200' &&
                        textStatus == 'parsererror') {

                        bootbox.alert('La tua sessione è scaduta, clicca sul pulsante OK per tornare alla pagina di login.', function(r) {
                            document.location.href = '/user/login';
                        });

                        //@tofix user come here also if the response is wrong

                    }*/
                }
            } );
        },
        "fnServerParams": function ( aoData ) {
            aoData.push({ "name": "column", "value": $(column).val()});
            aoData.push({ "name": "searchValue", "value": searchValue.val().trim()});
        },
        "order": [[iSortCol_0, sSortDir_0]],
        "columns": [
            {data: 'e.id'},
            {data: 'e.name'},
            {data: 'e.surname'},
            {data: 'e.mobile'},
            {data: 'cc.code'},
            {data: 'e.driverLicense'},
            {data: 'e.driverLicenseExpire'},
            {data: 'e.email'},
            {data: 'e.taxCode'},
            {data: 'e.registration'},
            {data: 'button'}
        ],
        "columnDefs": [
            {
                targets: 9,
                searchable: false,
                sortable: false
            },
            {
                targets: 10,
                data: 'button',
                searchable: false,
                sortable: false,
                render: function (data, type, row) {
                    return'<a href="/customers/edit/' + data + '" class="btn btn-default btn-xs">' + translate("modify") + '</a>';
                }
            }
        ],
        "lengthMenu": [
            [100, 200, 300],
            [100, 200, 300]
        ],
        "pageLength": iDisplayLength,
        "pagingType": "bootstrap_full_number",
        "language": {
            "sEmptyTable":     translate("sCustomersEmptyTable"),
            "sInfo":           translate("sInfo"),
            "sInfoEmpty":      translate("sInfoEmpty"),
            "sInfoFiltered":   translate("sInfoFiltered"),
            "sInfoPostFix":    "",
            "sInfoThousands":  ",",
            "sLengthMenu":     translate("sLengthMenu"),
            "sLoadingRecords": translate("sLoadingRecords"),
            "sProcessing":     translate("sProcessing"),
            "sSearch":         translate("sSearch"),
            "sZeroRecords":    translate("sZeroRecords"),
            "oPaginate": {
                "sFirst":      translate("oPaginateFirst"),
                "sPrevious":   translate("oPaginatePrevious"),
                "sNext":       translate("oPaginateNext"),
                "sLast":       translate("oPaginateLast"),
            },
            "oAria": {
                "sSortAscending":   translate("sSortAscending"),
                "sSortDescending":  translate("sSortDescending")
            }
        }
    });

    $('#js-search').click(function() {
        table.fnFilter();
    });

    $('#js-clear').click(function() {
        searchValue.val('');
        column.val('select');
    });

    $('.date-picker').datepicker({
        autoclose: true,
        format: 'dd-mm-yy',
        weekStart: 1
    });

    $(document).on('click','#js-remove-card',function(e) {

        var removeCardConfirm = confirm(translate("removeCardConfirm"));

        if(removeCardConfirm) {
            var customer = $(this).data('id');

            $.ajax({
                url: '/customers/remove-card/' + customer,
                type: 'POST',
                data: {},
                processData: false,
                contentType: false,
                dataType: 'json',
                cache: false,
                statusCode: {
                    200: function (response) {
                        $('#js-with-code').hide();
                        $('#js-no-code').show();
                    },
                    500: function (response) {
                        alert(translate("ajaxError"));
                    }
                }
            });
        }
    });

    $(document).on('click', '#js-assign-card', function(e) {
        var customer = $(this).data('id');
        var code = $(this).data('code');
        $.ajax({
            url: '/customers/assign-card/' + customer,
            type: 'POST',
            data: {
                code: code
            },
            cache: false,
            statusCode: {
                200: function (response) {
                    $('#js-assign-card').hide();
                    $('#js-no-code').hide();
                    $('#js-with-code').show();
                    $('#js-code').text(code);
                    $('#typeahead-input').val('');
                },
                500: function (response) {
                    alert(translate("ajaxError"));
                }
            }
        });
    });

    $(document).on('click', '#js-remove-bonus', function(e) {
        var removeBonus = confirm(translate("removeBonusConfirm"));

        if(removeBonus) {

            var customer = $(this).data('id');
            var bonus = $(this).data('bonus');

            $.ajax({
                url: '/customers/remove-bonus/' + customer,
                type: 'POST',
                data: {
                    bonus: bonus
                },
                cache: false,
                statusCode: {
                    200: function (response) {
                        $('#js-row-bonus-' + bonus).hide();
                        $('#js-message').show();

                        setTimeout(function(){
                            $('#js-message').hide();
                        }, 3000);
                    },
                    500: function (response) {
                        alert(translate("ajaxError"));
                    }
                }
            });

        }
    });
});