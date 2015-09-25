/* global $ window infoUrl */

$(function () {
    "use strict";

    $('#disable-contract').click(function () {
        var contractId = $(this).data('contract-id');

        $(this).attr('disabled', 'disabled');

        $.post('/customers/disable-contract', {
            contractId: contractId
        }).done(function () {
            window.location.replace(infoUrl);
        });
    });
});
