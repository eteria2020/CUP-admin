/* global dataTable:true, $:true, thisId, translate, confirm */
var renderDatatable = function(data){
    // DataTables
    var table = $("#js-cars-configurations-element-table");

    // Define DataTables Filters
    var dataTableVars = {
        iSortCol_0: 0,
        sSortDir_0: "desc",
        iDisplayLength: 5
    };

    table.dataTable({
        "processing": true,
        "serverSide": false,
        "bStateSave": false,
        "bFilter": false,
        "data": data,
        "order": [[dataTableVars.iSortCol_0, dataTableVars.sSortDir_0]],
        "columns": getColumns(data),
        "columnDefs": [
            {
                targets:(getColumns(data).length - 1),
                data: "button",
                sortable: false,
                render: function (data, type, full) {
                    return '<div class="btn-group">' +
                        '<a class="btn btn-default edit" data-id="' + full['id'] + '">' + translate("Modifica") + '</a> ' +
                        '<a href="/cars-configurations/delete/' + thisId + '/' + full['id'] + '" class="btn btn-default js-delete">' + translate("Elimina") + '</a>' +
                        '</div>';
                },
                "min-width": "180px"
            },
            {
                targets:(getColumns(data).length - 2),
                visible: false
            },
            {
                targets:(0),
                visible: false
            }
        ],
        "lengthMenu": [
            [5, 10, 50],
            [5, 10, 50]
        ],
        "pageLength": dataTableVars.iDisplayLength,
        "pagingType": "bootstrap_full_number",
        "language": {
            "sEmptyTable": translate("sCarsEmptyTable"),
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

    table.on("click", ".js-delete", function () {
        return confirm(translate("confirmCarConfigurationDelete"));
    });
};

var getColumns = function(data){
    var array = [];
    array.push({data: "id"});
    $.each(data[0], function(key){
        array.push({data: key});
    });
    array.push({data: "button"});
    return array;
};

$(document).on("click", ".btn.edit", function() {
    var optionId = $(this).data("id");

    $.ajax({
        url: "/cars-configurations/edit/" + thisId + "/ajax-edit/" + optionId,
        type: "GET",
        dataType: "json",
        cache: true,
        beforeSend: function(){
            $("input").val("");
            $("#data-processing").show();
        }
    }).success(function(data){
        if (typeof data !== "undefined"){
            $("#data-processing").hide();
            $.each(data,function(key,val){
                $('input[name="' + key + '"]').val(val); 
            });
            $('input[name="id"]').val(optionId);
        }
    });
});