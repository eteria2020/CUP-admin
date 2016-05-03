/* global  filters:true */
$(function() {
    // DataTables
    var table = $("#js-cars-table");

    // Define DataTables Filters
    var searchValue = $("#js-value");
    var column = $("#js-column");
    var typeClean = $("#js-clean-type");
    var columnWithoutLike = false;
    var columnValueWithoutLike = false;
    var iSortCol_0 = 0;
    var sSortDir_0 = "desc";
    var iDisplayLength = 100;

    var filterWithoutLike = false;

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
        "sAjaxSource": "/cars/datatable",
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
                aoData.push({ "name": "searchValue", "value": searchValue.val().trim()});
            }
        },
        "order": [[iSortCol_0, sSortDir_0]],
        "columns": [
            {data: 'e.plate'},
            {data: 'e.label'},
            {data: 'f.name'},
            {data: 'e.battery'},
            {data: 'e.lastContact'},
            {data: 'e.km'},
            {data: 'clean'},
            {data: 'position'},
            {data: 'e.status'},
            {data: 'ci.gps'},
            {data: 'ci.firmwareVersion'},
            {data: 'ci.softwareVersion'},
            {data: 'positionLink'},
            {data: 'button'}
        ],
        "columnDefs": [
            {
                targets: 6,
                sortable: false
            },
            {
                targets: 7,
                sortable: false
            },
            {
                targets: 9,
                sortable: true,
                type: 'string'
            },
            {
                targets: 10,
                sortable: true,
                type: 'string'
            },
            {
                targets: 11,
                sortable: true,
                type: 'string'
            },
            {
                targets: 12,
                sortable: false
            },
            {
                targets: 13,
                data: 'button',
                searchable: false,
                sortable: false,
                render: function (data) {
                    return '<div class="btn-group">' +
                        '<a href="/cars/edit/' + data + '" class="btn btn-default">' + translate("modify")  + '</a> ' +
                        '<a href="/cars/delete/' + data + '" class="btn btn-default js-delete">' + translate("delete")  + '</a>' +
                        '</div>';
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
            "sEmptyTable":     translate("sCarsEmptyTable"),
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

    $('#js-cars-table').on('click', '.js-delete', function() {
        return confirm(translate("confirmCarDelete"));
    });

    $(column).change(function() {
        var value = $(this).val();

        if (value == 'e.plate' || value == 'e.label' || value == 'f.name' || value == 'ci.gps' || value == 'ci.firmwareVersion' || value == 'ci.softwareVersion') {

            filterWithoutLike = false;
            searchValue.val('');
            searchValue.prop('disabled', false);
            typeClean.hide();
            searchValue.show();

        } else {

            filterWithoutLike = true;
            searchValue.val('');
            searchValue.prop('disabled', true);
            typeClean.hide();
            searchValue.show();

            switch (value) {

                case 'e.running':
                case 'e.hidden':
                case 'e.active':
                case 'e.busy':
                    columnWithoutLike = value;
                    columnValueWithoutLike = true;
                    break;
                case 'e.intCleanliness':
                    typeClean.show();
                    searchValue.hide();
                    columnWithoutLike = value;
                    columnValueWithoutLike = typeClean.val();
                    $(typeClean).change(function() {
                        columnValueWithoutLike = typeClean.val();
                    });
                    break;
                case 'e.extCleanliness':
                    typeClean.show();
                    searchValue.hide();
                    columnWithoutLike = value;
                    columnValueWithoutLike = typeClean.val();
                    $(typeClean).change(function() {
                        columnValueWithoutLike = typeClean.val();
                    });
                    break;
                case 'e.statusMaintenance':
                    columnWithoutLike = 'e.status';
                    columnValueWithoutLike = 'maintenance';
                    break;

                case 'e.statusOperative':
                    columnWithoutLike = 'e.status';
                    columnValueWithoutLike = 'operative';
                    break;
                case 'e.statusNotOperative':
                    columnWithoutLike = 'e.status';
                    columnValueWithoutLike = 'out_of_order';
                    break;
            }
        }
    });

});
