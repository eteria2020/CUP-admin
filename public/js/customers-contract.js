/* global $ */

$(function () {
    "use strict";

    $('#disable-contract').click(function () {
        var contractId = $(this).data('contract-id');

        $(this).attr('disabled');

        $.post('/customers/disable-contract', {
            contractId: contractId
        }).done(function (data) {
            console.log(data);
        }).fail(function (data) {
            console.log('fail', data);
        });
    });
});
