$(document).ready(function() {
	// Create the datatable and order it by descending date
    var table = $('#notes-table').DataTable().order([0,"desc"]).draw();
    $('#new-note').html('');
});
