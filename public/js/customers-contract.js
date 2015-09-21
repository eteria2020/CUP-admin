/* global $ alert */

$(function () {
    "use strict";

    $('#disable-contract').click(function () {
        var contractId = $(this).data('contract-id');

        $(this).attr('disabled', 'disabled');

        $.post('/customers/disable-contract', {
            contractId: contractId
        }).done(function () {
            alert('Contratto disabilitato');
        }).fail(function (data) {
            alert(data.responseJSON.error);
        });
    });
});
