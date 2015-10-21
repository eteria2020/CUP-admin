$(document).ready(function() {
    selectMonth(selectedMonth);
});

$("#month-selector").change(function()
{
    var selectedValue = $(this).find(":selected").val();
    reloadPageWithMonth(selectedValue);
});

// Reload the page after selector value has changed displaying new month data
function reloadPageWithMonth(month)
{
    window.location.replace(reloadUrl + "?date=" + month);
}

// Change the selected value in the selector to match the current month displayed
function selectMonth(month)
{
    $("#month-selector").val(month);
}
