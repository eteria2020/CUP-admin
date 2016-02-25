$(function() {

    var table    = $('#js-pois-table');
    var search   = $('#js-value');
    var column   = $('#js-column');
    var filterWithoutLike = false;
    var columnWithoutLike = false;
    var columnValueWithoutLike  = false;

    search.val('');
    column.val('select');

    table.dataTable({
        "processing": true,
        "serverSide": true,
        "bStateSave": false,
        "bFilter": false,
        "sAjaxSource": "/configurations/manage-pois/datatable",
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

            if(filterWithoutLike) {
                aoData.push({ "name": "column", "value": ''});
                aoData.push({ "name": "searchValue", "value": ''});
                aoData.push({ "name": "columnWithoutLike", "value": columnWithoutLike});
                aoData.push({ "name": "columnValueWithoutLike", "value": columnValueWithoutLike});
            } else {
                aoData.push({ "name": "column", "value": $(column).val()});
                aoData.push({ "name": "searchValue", "value": search.val().trim()});
            }

        },
        "order": [[0, 'desc']],
        "columns": [
            {data: 'e.id'},
            {data: 'e.type'},
            {data: 'e.code'},
            {data: 'e.name'},
            {data: 'e.address'},
            {data: 'e.town'},
            {data: 'e.zipCode'},
            {data: 'e.province'},
            {data: 'button'}
        ],

        "columnDefs": [
            {
                targets: 8,
                data: 'button',
                searchable: false,
                sortable: false,
                render: function (data) {
                    return '<div class="btn-group">' +
                        '<a href="/configurations/manage-pois/edit/' + data + '" class="btn btn-default">'+ translate("modify")+'</a> ' +
                        '<a href="/configurations/manage-pois/delete/' + data + '" class="btn btn-default js-delete">'+translate("delete")+'</a>' +
                        '</div>';
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
            "sEmptyTable":     translate("sPoisEmptyTable"),
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
        search.show();
        search.show();
        column.val('select');
        filterWithoutLike = false;
    });

    $('#js-pois-table').on('click', '.js-delete', function() {
        return confirm(translate("confirmPaymentDelete"));
    });

});
