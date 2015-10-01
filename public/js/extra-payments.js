/* global $ alert confirm */

$(function() {
    "use strict";

    $('#paymentType').change(function () {
        $('#penaltyField').toggle();
        $('#reason').val('');
        $('#amount').val('');
    });

    $('#penalty').change(function () {
        var selected = $(this).find('option:selected'),
            reason = selected.data('reason') || '',
            amount = parseFloat(selected.data('amount')) / 100 || '';

        $('#reason').val(reason);
        $('#amount').val(amount);
    });

    function sendPaymentRequest(customerId, fleetId, paymentType, reason, amount) {
        $.post('/payments/pay-extra', {
            customerId: customerId,
            fleetId: fleetId,
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

    function clearFields() {
        $('#customerId').val('');
        $('#fleet').prop('selectedIndex', 0);
        $('#paymentType').val('extra');
        $('#penaltyField').hide();
        $('#penalty').prop('selectedIndex', 0);
        $('#reason').val('');
        $('#amount').val('');
    }

    $('#js-extra-payment').click(function (e) {
        var customerId = $('#customerId').val(),
            fleetId = parseInt($('#fleet').val()),
            customer = null,
            paymentType = $('#paymentType').val(),
            reason = $('#reason').val(),
            amount = $('#amount').val();
            amount = amount.replace(",", ".");
            amount = parseFloat(amount);
            amount = Math.floor(amount * 100);

        e.preventDefault();

        if (!customerId || customerId < 0) {
            alert('Formato id cliente non corretto');
            return;
        }

        $.get('/customers/info/' + customerId)
            .done(function (data) {
                customer = data;

                if (fleetId <= 0) {
                    alert('Selezionare una flotta');
                    return;
                }

                if (reason.length === 0) {
                    alert('Inserire una causale valida');
                    return;
                }

                if (!amount || amount < 0) {
                    alert('Inserire un importo valido in euro');
                    return;
                }

                if (confirm('Confermi il pagamento al cliente ' +
                    customer.name + ' ' + customer.surname +
                    ' di un importo di ' + amount / 100 + ' euro')) {
                    sendPaymentRequest(customerId, fleetId, paymentType, reason, amount);

                    clearFields();
                }
            })
            .fail(function (data) {
                var message = JSON.parse(data.responseText).error;

                alert(message);
                clearFields();
            });
    });
});
