/* global  filters:true */
$(function() {
    // DataTables
    var table = $("#js-trips-table");

    // Define DataTables Filters
    var searchValue = $("#js-value");
    var column = $("#js-column");
    var iSortCol_0 = 0;
    var sSortDir_0 = "desc";
    var iDisplayLength = 100;
    var from = $("#js-date-from");
    var columnFromDate = "e.timestampBeginning";
    var to = $("#js-date-to");
    var columnFromEnd = "e.timestampEnd";

    var filterWithNull = false;

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
        if(typeof filters.from !== "undefined"){
            from.val(filters.from);
        }
        if(typeof filters.columnFromDate !== "undefined"){
            columnFromDate=filters.columnFromDate;
        }
        if(typeof filters.to !== "undefined"){
            to.val(filters.to);
        }
        if(typeof filters.columnFromEnd !== "undefined"){
            columnFromEnd=filters.columnFromEnd;
        }
    }

    table.dataTable({
        "processing": true,
        "serverSide": true,
        "bStateSave": false,
        "bFilter": false,
        "sAjaxSource": "/trips/datatable",
        "fnServerData": function ( sSource, aoData, fnCallback, oSettings ) {
            oSettings.jqXHR = $.ajax( {
                "dataType": 'json',
                "type": "POST",
                "url": sSource,
                "data": aoData,
                "success": fnCallback,
                "error": function() {}
            });
        },
        "fnServerParams": function ( aoData ) {
            if (filterWithNull) {
                aoData.push({ "name": "column", "value": ''});
                aoData.push({ "name": "searchValue", "value": ''});
                aoData.push({ "name": "columnNull", "value": "e.timestampEnd"});
            } else {
                aoData.push({ "name": "column", "value": $(column).val()});
                aoData.push({ "name": "searchValue", "value": searchValue.val().trim()});
            }
            aoData.push({ "name": "from", "value": $(from).val().trim()});
            aoData.push({ "name": "to", "value": $(to).val().trim()});
            aoData.push({ "name": "columnFromDate", "value": columnFromDate});
            aoData.push({ "name": "columnFromEnd", "value": columnFromEnd});
        },
        "order": [[iSortCol_0, sSortDir_0]],
        "columns": [
            {data: 'e.id'},
            {data: 'cu.email'},
            {data: 'cu.surname'},
            {data: 'cu.name'},
            {data: 'cu.mobile'},
            {data: 'cc.code'},
            {data: 'c.plate'},
            {data: 'e.kmBeginning'},
            {data: 'e.kmEnd'},
            {data: 'e.timestampBeginning'},
            {data: 'e.timestampEnd'},
            {data: 'duration'},
            {data: 'e.parkSeconds'},
            {data: 'c.keyStatus'},
            {data: 'c.parking'},
            {data: 'e.payable'},
            {data: 'payed'},
            {data: 'e.totalCost'},
            {data: 'f.name'},
            {data: 'e.idLink'}
        ],
        "columnDefs": [
	        {
                targets: 1,
                visible: false
            },
            {
                targets: [2, 3],
                "render": function (data, type, row) {
                    return '<a href="/customers/edit/'+row.cu.id+'" title="' + translate("showProfile") + ' '+row.cu.name+' '+row.cu.surname+' ">'+data+'</a>';
                }
            },
            {
                targets: 11,
                sortable: false
            },
            {
                targets: 13,
                sortable: false
            },
            {
                targets: 14,
                sortable: false
            },
            {
                targets: 17,
                sortable: false,
                "render": function ( data, type, row ) {
                    return renderCostButton(data);
                }
            },
            {
                targets: 19,
                sortable: false,
                "render": function ( data, type, row ) {
                    return renderInfoButton(data);
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
            "sEmptyTable":     translate("sTripEmptyTable"),
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
        from.val('');
        to.val('');
        column.val('select');
        searchValue.prop('disabled', false);
        filterWithNull = false;
        searchValue.show();
    });

    $('.date-picker').datepicker({
        autoclose: true,
        format: 'yyyy-mm-dd',
        weekStart: 1
    });

    $(column).change(function() {
        var value = $(this).val();

        searchValue.show();
        searchValue.val('');

        if(value == 'c.timestampEnd') {
            filterWithNull = true;
            searchValue.prop('disabled', true);
        } else {
            filterWithNull = false;
            searchValue.prop('disabled', false);
        }
    });

    function renderCostButton(data)
    {
        var amount = data['amount'];
        if (amount !== 'FREE') {
            return amount !== '' ?
                '<a href="/trips/details/' + data['id'] + '?tab=cost">' + renderAmount(parseInt(amount)) + '</a>' :
                '';
        } else {
            return amount;
        }
    }

    function renderInfoButton(data)
    {
        return '<div class="btn-group">' +
                    '<a href="/trips/details/' + data + '" class="btn btn-default">' + translate("details") + '</a> ' +
                '</div>';
    }

    function renderAmount(amount)
    {
        return (Math.floor(amount / 100)) +
            ',' +
            toStringKeepZero(amount % 100) +
            ' \u20ac';
    }

    function toStringKeepZero(value)
    {
        return ((value < 10) ? '0' : '') + value;
    }
});
