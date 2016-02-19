$(function() {

    var table    = $('#js-trips-table');
    var search   = $('#js-value');
    var column   = $('#js-column');
    var from = $('#js-date-from');
    var to = $('#js-date-to');
    var filterWithNull = false;
    search.val('');
    column.val('select');


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

            if(filterWithNull) {
                aoData.push({ "name": "column", "value": ''});
                aoData.push({ "name": "searchValue", "value": ''});
                aoData.push({ "name": "columnNull", "value": "e.timestampEnd"});
            } else {
                aoData.push({ "name": "column", "value": $(column).val()});
                aoData.push({ "name": "searchValue", "value": search.val().trim()});
            }

            aoData.push({ "name": "from", "value": $(from).val().trim()});
            aoData.push({ "name": "to", "value": $(to).val().trim()});
            aoData.push({ "name": "columnFromDate", "value": "e.timestampBeginning"});
            aoData.push({ "name": "columnFromEnd", "value": "e.timestampEnd"});
        },
        "order": [[0, 'desc']],
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
                targets: 16,
                sortable: false,
                "render": function ( data, type, row ) {
                    return renderCostButton(data);
                }
            },
            {
                targets: 18,
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
        "pageLength": 100,
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
        search.val('');
        from.val('');
        to.val('');
        column.val('select');
        search.prop('disabled', false);
        filterWithNull = false;
        search.show();
    });

    $('.date-picker').datepicker({
        autoclose: true,
        format: 'yyyy-mm-dd',
        weekStart: 1
    });

    $(column).change(function() {
        var value = $(this).val();

        search.show();
        search.val('');

        if(value == 'c.timestampEnd') {
            filterWithNull = true;
            search.prop('disabled', true);
        } else {
            filterWithNull = false;
            search.prop('disabled', false);
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
