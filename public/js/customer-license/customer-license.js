$(document).ready(function() {
    // Create the datatable and order it by descending id
    var table = $('#validations-table').DataTable().order([0,"desc"]).draw();
});
