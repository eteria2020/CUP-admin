/* global $ alert confirm */

$(function() {
    "use strict";

    function sendPaymentRequest(customerId, paymentType, reason, amount) {
        $.post('/payments/pay-extra', {
            customerId: customerId,
            paymentType: paymentType,
            reason: reason,
            amount: amount
        }).done(function (data) {
            alert(data.message);
        }).fail(function (data) {
            var message = JSON.parse(data.responseText).error;

            alert(message);
        });
    }

    $('#js-extra-payment').click(function (e) {
        var customerId = $('#customerId').val(),
            paymentType = $('#paymentType').val(),
            reason = $('#reason').val(),
            amount = $('#amount').val();

        e.preventDefault();

        if (!customerId || customerId < 0) {
            alert('Formato id cliente non corretto');
            return;
        }

        if (reason.length === 0) {
            alert('Inserire una causale valida');
            return;
        }

        if (!amount || amount < 0 || amount !== String(parseInt(amount, 10))) {
            alert('Inserire un importo valido in centesimi di euro');
            return;
        }

        if (confirm('Confermi il pagamento al cliente ' + customerId +
            ' di un importo di ' + amount / 100 + ' euro')) {
            sendPaymentRequest(customerId, paymentType, reason, amount);
        }
    });
});
