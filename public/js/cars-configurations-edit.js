/* global dataTable:true, $:true */

var renderDatatable = function(data){
        var table = $('#js-cars-configurations-element-table');

        table.dataTable({
            "processing": true,
            "serverSide": false,
            "bStateSave": false,
            "bFilter": false,
            "data": data,
            "order": [[0, 'desc']],
            "columns": getColumns(data),
            "columnDefs": [
                {
                    targets:(getColumns(data).length - 1),
                    data: 'button',
                    sortable: false,
                    render: function (data, type, full) {
                        return '<div class="btn-group">' +
                            '<a class="btn btn-default edit" data-id="' + full['id'] + '">Modifica</a> ' +
                            '<a href="/cars-configurations/delete/' + full['id'] + '" class="btn btn-default js-delete">Elimina</a>' +
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
            "pageLength": 5,
            "pagingType": "bootstrap_full_number",
            "language": {
                "sEmptyTable": "Nessuna configurazione presente nella tabella",
                "sInfo": "Vista da _START_ a _END_ di _TOTAL_ elementi",
                "sInfoEmpty": "Vista da 0 a 0 di 0 elementi",
                "sInfoFiltered": "(filtrati da _MAX_ elementi totali)",
                "sInfoPostFix": "",
                "sInfoThousands": ",",
                "sLengthMenu": "Visualizza _MENU_ elementi",
                "sLoadingRecords": "Caricamento...",
                "sProcessing": "Elaborazione in corso...",
                "sSearch": "Cerca:",
                "sZeroRecords": "La ricerca non ha portato alcun risultato.",
                "oPaginate": {
                    "sFirst": "Inizio",
                    "sPrevious": "Precedente",
                    "sNext": "Successivo",
                    "sLast": "Fine"
                },
                "oAria": {
                    "sSortAscending": ": attiva per ordinare la colonna in ordine crescente",
                    "sSortDescending": ": attiva per ordinare la colonna in ordine decrescente"
                }
            }
        });

        table.on('click', '.js-delete', function () {
            return confirm("Confermi l'eliminazione della configurazione? L'operazione non è annullabile");
        });
    };
    
var getColumns = function(data){
    var array = [];
    array.push({data:"id"});
    $.each(data[0],function(key){
        array.push({data:key});
    });
    array.push({data:"button"});
    return array;
}

$(document).on('click','.btn.edit',function(e) {
    var optionId = $(this).data('id');

    $.ajax({
        url: '/cars-configurations/edit/' + thisId + '/ajax-edit/' + optionId,
        type: 'GET',
        dataType: 'json',
        cache: true,
        beforeSend: function(){
            $('input').val('');
            $("#data-processing").show();
        }
    }).success(function(data){
        if(typeof data !== "undefined"){
            $("#data-processing").hide();
            $.each(data,function(key,val){
                $('input[name="' + key + '"]').val(val); 
            });
            $('input[name="id"]').val(optionId);
        }
    });
});