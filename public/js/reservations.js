$(function() {

    var table    = $('#js-reservations-table');
    var search   = $('#js-value');
    var column   = $('#js-column');
    var filterDate = false;
    search.val('');
    column.val('select');

    table.dataTable({
        "processing": true,
        "serverSide": true,
        "bStateSave": false,
        "bFilter": false,
        "sAjaxSource": "/reservations/datatable",
        "fnServerData": function ( sSource, aoData, fnCallback, oSettings ) {
            oSettings.jqXHR = $.ajax( {
                "dataType": 'json',
                "type": "POST",
                "url": sSource,
                "data": aoData,
                "success": fnCallback
            } );
        },
        "fnServerParams": function ( aoData ) {

            if(filterDate) {
                aoData.push({ "name": "column", "value": ''});
                aoData.push({ "name": "searchValue", "value": ''});
                aoData.push({ "name": "from", "value": search.val().trim()});
                aoData.push({ "name": "to", "value": search.val().trim()});
                aoData.push({ "name": "columnFromDate", "value": "e.beginningTs"});
                aoData.push({ "name": "columnFromEnd", "value": "e.beginningTs"});
            } else {
                aoData.push({ "name": "column", "value": $(column).val()});
                aoData.push({ "name": "searchValue", "value": search.val().trim()});
            }
        },
        "order": [[0, 'desc']],
        "columns": [
            {data: 'e.id'},
            {data: 'e.carPlate'},
            {data: 'e.customer'},
            {data: 'e.cards'},
            {data: 'e.active'}
        ],

        "columnDefs": [
            {
                targets: 0,
                sortable: false
            },
            {
                targets: 1,
                sortable: false,
                "render": function (data, type, row) {
                    return '<a href="/cars/edit/'+row.e.carPlate+'" title="' + translate("showCarPlate") + ' '+data+'">'+data+'</a>';
                }
            },
            {
                targets: 2,
                sortable: false,
                "render": function (data, type, row) {
                    if (data !== '') {
                        return '<a href="/customers/edit/'+row.e.customerId+'" title="' + translate("showProfile") + ' '+data+'">'+data+'</a>';
                    } else {
                        return '';
                    }
                }
            }
        ],
        "lengthMenu": [
            [100, 200, 300],
            [100, 200, 300]
        ],
        "pageLength": 100,
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
        search.val('');
        column.val('select');
    });

    $('.date-picker').datepicker({
        autoclose: true,
        format: 'yyyy-mm-dd',
        weekStart: 1
    });

    $(column).change(function() {
        var value = $(this).val();

        if(value == 'beginningTs') {
            filterDate = true;
            search.val('');
            $(search).datepicker({
                autoclose: true,
                format: 'yyyy-mm-dd',
                weekStart: 1
            });

        } else {
            filterDate = false;
            search.val('');
            $(search).datepicker("remove");
        }

    });

});
