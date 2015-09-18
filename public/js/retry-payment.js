/* global $ retryUrl abilitateUrl tripPaymentId alert */

$(function () {
    "use strict";

    $('#js-new-try').click(function () {
        $.post(retryUrl, {
            tripPaymentId: tripPaymentId
        }, function (data) {
            $('#js-new-try').hide();
            if (data.outcome === 'OK') {
                $('#js-completed-message').removeClass('hidden');
            } else {
                $('#js-refused-message').removeClass('hidden');
                if (data.message.length > 0) {
                    $('#js-wrong-reason').html(data.message);
                }
            }
        });

        $('#js-new-try').attr('disabled', true);
    });

    $('#js-abilitate-customer').click(function() {
        $.post(abilitateUrl, {
            sendMail: $('#send-mail').is(':checked')
        }).done(function () {
            alert('utente riattivato');
        });
    });
});
