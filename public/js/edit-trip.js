$(function() {
    'use strict';

    $('#timestampEnd').datetimepicker({
        format: "DD-MM-YYYY HH:mm:ss"
    });

    $('#payable').checked = payable;
});
