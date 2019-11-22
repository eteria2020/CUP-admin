$(document).ready(function() {
//    selectMonth(selectedMonth);
//    
//    highlightLastRows();
});

$("#tab_2").on('click', function(event) {
    window.location.replace(reloadUrl + "?tab=2");
    event.preventDefault();
    event.stopPropagation();
});

$("#tab_1").on('click', function(event) {
    window.location.replace(reloadUrl + "?tab=1");
    event.preventDefault();
    event.stopPropagation();
});

$("#day-selector1").change(function()
{
    var selectedDay = $(this).find(":selected").val();
    var selectedMonth = $('#month-selector1').find(":selected").val();
    var selectedFleet = $('#fleet-selector1').find(":selected").val();
    var selectedYear = $('#year-selector1').find(":selected").val();
    window.location.replace(reloadUrl + "?fleet="+ selectedFleet + "&year=" + selectedYear + "&month=" + selectedMonth + "&day=" + selectedDay + "&tab=1");
});

$("#month-selector1").change(function()
{
    var selectedMonth = $(this).find(":selected").val();
    var selectedFleet = $('#fleet-selector1').find(":selected").val();
    var selectedYear = $('#year-selector1').find(":selected").val();
    window.location.replace(reloadUrl + "?fleet="+ selectedFleet + "&year=" + selectedYear + "&month=" + selectedMonth + "&tab=1");
});

$("#fleet-selector1").change(function()
{
    var selectedFleet = $(this).find(":selected").val();
    window.location.replace(reloadUrl + "?fleet="+ selectedFleet + "&tab=1");
});

$("#year-selector1").change(function()
{
    var selectedYear = $(this).find(":selected").val();
    var selectedFleet = $('#fleet-selector1').find(":selected").val();
    window.location.replace(reloadUrl + "?fleet="+ selectedFleet + "&year=" + selectedYear + "&tab=1");
});

/* months */
$("#fleet-selector2").change(function()
{
    var selectedFleet = $(this).find(":selected").val();
    window.location.replace(reloadUrl + "?fleet2="+ selectedFleet + "&tab=2");
});

$("#year-selector2").change(function()
{
    var selectedYear = $(this).find(":selected").val();
    var selectedFleet = $('#fleet-selector2').find(":selected").val();
    window.location.replace(reloadUrl + "?fleet2="+ selectedFleet + "&year2=" + selectedYear + "&tab=2");
});

$("#month-selector2").change(function()
{
    var selectedMonth = $(this).find(":selected").val();
    var selectedFleet = $('#fleet-selector2').find(":selected").val();
    var selectedYear = $('#year-selector2').find(":selected").val();
    window.location.replace(reloadUrl + "?fleet2="+ selectedFleet + "&year2=" + selectedYear + "&month2=" + selectedMonth + "&tab=2");
});

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
