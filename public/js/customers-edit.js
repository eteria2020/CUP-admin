$(document).ready(function () {
    $("#customerForm").validate();
    $("#driverForm").validate();
    $("#settingForm").validate();
    $('.date-picker').datepicker({
        autoclose: true,
        format: 'yyyy-mm-dd',
        weekStart: 1
    });

    $('#js-deactivate').click(function (e) {
        deactivate();
    });

    $('#js-reactivate').click(function (e) {
        reactivate();
    });
});

function deactivate()
{
    console.log(deactivateUrl);
}

function reactivate()
{
    console.log(reactivateUrl);
}
