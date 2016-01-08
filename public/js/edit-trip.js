$(function() {
    'use strict';

    $('#timestampEnd').datetimepicker({
        format: "DD-MM-YYYY HH:mm:ss"
    });
});

function removeTries(e)
{
    if (!confirm('La procedura di pagamento è già iniziata. Per procedere sarà necessario annullare tutti i tentativi di pagamento effettuati.\nConfermi di voler rimuovere tutti i tentativi di pagamento?')) {
        e.preventDefault();
    }
}
