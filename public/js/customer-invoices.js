$(function() {

    var table    = $('#js-invoices-table');
    var search   = $('#js-value');
    var column   = $('#js-column');
    var typeClean = $('#js-clean-type');

    search.val('');
    column.val('select');

    table.dataTable({
        "processing": true,
        "serverSide": true,
        "bStateSave": false,
        "bFilter": false,
        "sAjaxSource": "/invoices/datatable",
        "fnServerData": function ( sSource, aoData, fnCallback, oSettings ) {
            oSettings.jqXHR = $.ajax( {
                "dataType": 'json',
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
            aoData.push({ "name": "column", "value": $(column).val()});
            aoData.push({ "name": "searchValue", "value": formatData().trim()});
            aoData.push({ "name": "fixedColumn", "value": "e.customer"});
            aoData.push({ "name": "fixedValue", "value": customerId});
            aoData.push({ "name": "fixedLike", "value": false});
        },
        "order": [[0, 'asc']],
        "columns": [
            {data: 'e.invoiceNumber'},
            {data: 'e.invoiceDate'},
            {data: 'e.type'},
            {data: 'e.amount'},
            {data: 'link'}
        ],
        "columnDefs": [
            {
                targets: 1,
                "render": function ( data, type, row ) {
                    return renderDate(data);
                }
            },
            {
                targets: 2,
                "render": function ( data, type, row ) {
                    return renderType(data);
                }
            },
            {
                targets: 3,
                "render": function ( data, type, row ) {
                    return renderAmount(data);
                }
            },
            {
                targets: 4,
                sortable: false,
                "render": function ( data, type, row ) {
                    return renderLink(data);
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
            "sEmptyTable":     translate("sInvoicesEmptyTable"),
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
        search.prop('disabled', false);
        typeClean.hide();
        search.show();
        column.val('select');
        filterWithoutLike = false;
    });

    $(column).change(function() {
        var value = $(this).val();
        search.val('');
        typeClean.hide();
        search.show();
    });

    function formatData()
    {
        var value = $(column).val();
        var searchValue = $(search).val();
        /*
        if (value == 'e.invoiceDate') {
            // remove slash
            searchValue.replace(/\//g, '');
        } else if (value == 'e.amount') {
            // remove comma, EUR symbol and spaces
            searchValue.replace(/,|\u20ac|s+/g, '');
        } else if (value == 'e.type') {
            switch (searchValue) {
                case 'Iscrizione':
                    searchValue = 'FIRST_PAYMENT';
                    break;
                case 'Corse':
                    searchValue = 'TRIP';
                    break;
                case 'Sanzione':
                    searchValue = 'PENALTY';
            }
        }
        */
        return searchValue;
    }

    function renderDate(date)
    {
        return toStringKeepZero(date % 100) + '/' +
        toStringKeepZero(Math.floor((date / 100) % 100)) + '/' +
        (Math.floor(date / 10000));
    }

    function renderAmount(amount)
    {
        return (Math.floor(amount / 100)) +
            ',' +
            toStringKeepZero(amount % 100) +
            ' \u20ac';
    }

    function renderType(type)
    {
        switch (type) {
            case 'FIRST_PAYMENT':
                return 'Iscrizione';
            case 'TRIP':
                return 'Corse';
            case 'PENALTY':
                return 'Sanzione';
            case 'BONUS_PACKAGE':
                return 'Pacchetto minuti';
        }
    }

    function renderLink(id)
    {
        return '<a href=' + pdfPath + id + '><i class="fa fa-download"></i></a>';
    }

    function toStringKeepZero(value)
    {
        return ((value < 10) ? '0' : '') + value;
    }
});
