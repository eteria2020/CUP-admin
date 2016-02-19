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
    if (!confirm(translate("deactivateUser"))) {
        e.preventDefault();
    }
}

function reactivate(e)
{
    if (!confirm(translate("reactivateUser"))) {
        e.preventDefault();
    }
}

function removeDeactivation(e)
{
    if (!confirm(translate("removeDeactivation"))) {
        e.preventDefault();
    }
}
