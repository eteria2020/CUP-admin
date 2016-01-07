$(function() {
    'use strict';

    $('#timestampEnd').datetimepicker({
        format: "DD-MM-YYYY HH:mm:ss"
    });
});

function removeTries(e)
{
    if (!confirm('Confermi di voler rimuovere tutti i tentativi di pagamento?')) {
        e.preventDefault();
    }
}
