/* global $ retryUrl tripPaymentId */

$(function () {
    "use strict";

    $('#js-new-try').click(function () {
        $.post(retryUrl, {
            tripPaymentId: tripPaymentId
        }, function (data) {
            if (data.outcome === 'OK') {
                $('#js-completed-message').removeClass('hidden');
            } else {
                $('#js-refused-message').removeClass('hidden');
                if (data.message.length > 0) {
                    $('#js-wrong-reason').html(data.message);
                }
            }
        });
    });
});
