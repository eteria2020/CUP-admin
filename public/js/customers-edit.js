$(document).ready(function () {
    $("#customerForm").validate();
    $("#driverForm").validate();
    $("#settingForm").validate();
    $('.date-picker').datepicker({
        autoclose: true,
        format: 'yyyy-mm-dd',
        weekStart: 1
    });
});

function deactivate(e)
{
    if (!confirm('Disattivare utente?')) {
        e.preventDefault();
    }
}

function reactivate(e)
{
    if (!confirm('Riattivare utente?')) {
        e.preventDefault();
    }
}

function removeDeactivation(e)
{
    if (!confirm('Rimuovere disattivazione?')) {
        e.preventDefault();
    }
}
