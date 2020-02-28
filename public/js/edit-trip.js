$(function() {
    'use strict';

    $('#timestampEnd').datetimepicker({
        format: "DD-MM-YYYY HH:mm:ss"
    });
    $('#timestampBeginning').datetimepicker({
        format: "DD-MM-YYYY HH:mm:ss"
    });
});

function removeTries(e)
{
    if (!confirm(translate("confirmRemoveTries"))) {
        e.preventDefault();
    }
}
