$(document).ready(function() {
    // Create the datatable and order it by descending date
    var newTable = $('#new-table').DataTable().order([1,"desc"]).draw();
    var loadedTable = $('#loaded-table').DataTable().order([1,"desc"]).draw();
    var unresolvedTable = $('#unresolved-table').DataTable().order([1,"desc"]).draw();
    var resolvedTable = $('#resolved-table').DataTable().order([1,"desc"]).draw();
});
