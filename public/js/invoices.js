/* global  filters:true */
$(function() {
    // DataTables
    var table = $("#js-invoices-table");

    // Define DataTables Filters
    var searchValue = $("#js-value");
    var column = $("#js-column");
    var iSortCol_0 = 0;
    var sSortDir_0 = "desc";
    var iDisplayLength = 100;

    var typePayment = $('#js-payment-type');

    searchValue.val("");
    column.val("select");

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
        "sAjaxSource": "/invoices/datatable",
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
            aoData.push({ "name": "column", "value": $(column).val()});
            aoData.push({ "name": "searchValue", "value": $(searchValue).val().trim()});
        },
        "order": [[iSortCol_0, sSortDir_0]],
        "columns": [
            {data: 'e.invoiceNumber'},
            {data: 'e.invoiceDate'},
            {data: 'cu.name'},
            {data: 'cu.surname'},
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
                targets: [2, 3],
                "render": function ( data, type, row ) {
                    return '<a href="/customers/edit/'+row.cu.id+'" title="' + translate("showProfileOf") + ' '+row.cu.name+' '+row.cu.surname+'">'+data+'</a>';
                }
            },
            {
                targets: 4,
                "render": function ( data, type, row ) {
                    return renderType(data);
                }
            },
            {
                targets: 5,
                className: "sng-dt-right",
                "render": function ( data, type, row ) {
                    return renderAmount(data);
                }
            },
            {
                targets: 6,
                sortable: false,
                className: "sng-dt-center",
                "render": function ( data, type, row ) {
                    return renderLink(data);
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
        searchValue.val('');
        searchValue.prop('disabled', false);
        typeClean.hide();
        searchValue.show();
        column.val('select');
        filterWithoutLike = false;
    });

    $(column).change(function() {
        var value = $(this).val();
        searchValue.val('');
        typePayment.hide();
        searchValue.show();
    });

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
                return translate("renderFirstPayment");
            case 'TRIP':
                return translate("renderTrip");
            case 'PENALTY':
                return translate("renderPenality");
            case 'BONUS_PACKAGE':
                return translate("renderBonusPackage");
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
