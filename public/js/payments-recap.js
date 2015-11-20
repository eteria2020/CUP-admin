$(document).ready(function() {
    selectMonth(selectedMonth);
    highlightLastRows();
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

// Changes the background color of the last row of each table
function highlightLastRows()
{
    var tableBodies = document.getElementsByTagName('tbody');
    for(var i = tableBodies.length - 1; i >= 0; i--) {
        if (tableBodies[i].id == "daily-body" && !isLastMonth) {
            continue;
        }
        var rows = tableBodies[i].getElementsByTagName('tr');
        var lastRow = rows[rows.length - 1];
        var elements = lastRow.getElementsByTagName('td');
        for(var j = elements.length - 1; j >= 0; j--) {
            elements[j].className = elements[j].className + " sng-last-row";
        }
    }
}
