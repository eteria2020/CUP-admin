/* global $ */

$(function () {
    "use strict";

    $('#datetime').datetimepicker({
        format: "DD-MM-YYYY HH:mm:ss"
    });

    $('.events-table tbody tr').click(function () {
        var date = $(this).find('td.event-time').html();

        $('#datetime').val(date);
    });
});
