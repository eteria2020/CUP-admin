
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
