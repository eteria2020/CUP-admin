$(function() {
    'use strict';

    $('#timestampEnd').datetimepicker({
        format: "DD-MM-YYYY HH:mm:ss",
        defaultDate: timestampEnd
    });

    $('#payable').checked = payable;
});
