/* global $ retryUrl abilitateUrl tripPaymentId window listUrl */

var tripPaymentTryId = null;

$(function () {
    "use strict";

    $('#js-new-try').click(function () {
        $.post(retryUrl, {
            tripPaymentId: tripPaymentId
        }, function (data) {
            $('#js-new-try').hide();
            if (data.outcome === 'OK') {
                $('#js-completed-message').removeClass('hidden');
                tripPaymentTryId = data.tripPaymentTriesId;
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
            tripPaymentTriesId: tripPaymentTryId
        }).done(function () {
            window.location.replace(listUrl);
        });
    });
});
