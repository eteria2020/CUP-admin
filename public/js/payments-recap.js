$(document).ready(function() {
    selectMonth(selectedMonth);
    
    highlightLastRows();
});

$("#month-selector").change(function()
{
    var selectedMonth = $(this).find(":selected").val();
    var selectedFleet = $('#fleet-selector').find(":selected").val();
    var selectedYear = $('#year-selector').find(":selected").val();
    reloadPageWithMonthYear(selectedMonth, selectedYear, selectedFleet, 1);
});

$("#fleet-selector").change(function()
{
    var selectedFleet = $(this).find(":selected").val();
    var selectedMonth = $('#month-selector').find(":selected").val();
    var selectedYear = $('#year-selector').find(":selected").val();
    reloadPageWithMonthYear(selectedMonth, selectedYear, selectedFleet, 1);
});

/* months */
$("#fleet-selector2").change(function()
{
    var selectedFleet = $(this).find(":selected").val();
    var selectedMonth = $('#month-selector').find(":selected").val();
    var selectedYear = $('#year-selector').find(":selected").val();
    reloadPageWithMonthYear(selectedMonth, selectedYear, selectedFleet, 2);
});

$("#year-selector").change(function()
{
    var selectedYear = $(this).find(":selected").val();
    var selectedFleet = $('#fleet-selector2').find(":selected").val();
    var selectedMonth = $('#month-selector').find(":selected").val();
    reloadPageWithMonthYear(selectedMonth, selectedYear, selectedFleet, 2);
});

// Reload the page after selector value has changed displaying new month data
function reloadPageWithMonth(month, fleet, tab)
{
    window.location.replace(reloadUrl + "?date=" + month + "&fleet="+ fleet + "&tab=" + tab);
}
// Reload the page after selector value has changed displaying new month data
function reloadPageWithMonthYear(month, year, fleet, tab)
{
    if (year == 0) {
        reloadPageWithMonth(month, fleet, tab);
    } else {
        window.location.replace(reloadUrl + "?date=" + month + "&fleet="+ fleet + "&year=" + year + "&tab=" + tab);
    }
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
        if(typeof lastRow !== "undefined"){
            var elements = lastRow.getElementsByTagName('td');
            for(var j = elements.length - 1; j >= 0; j--) {
                elements[j].className = elements[j].className + " sng-last-row";
            }
        }
    }
}
