/* global $ retryUrl abilitateUrl extraPaymentId window listUrl */

var extraPaymentTryId = null;

$(function () {
    "use strict";

    $('#js-new-try').click(function () {
        $.post(retryUrl, {
            extraPaymentId: extraPaymentId
        }, function (data) {
            $('#js-new-try').hide();
            if (data.outcome === 'OK') {
                extraPaymentTryId = data.extraPaymentTriesId;
            } else {
                $('#js-refused-message').removeClass('hidden');
                if (data.message.length > 0) {
                    $('#js-wrong-reason').html(data.message);
                }
            }
            setTimeout(function () {
                location.reload();
            }, 2500);
        });

        $('#js-new-try').attr('disabled', true);
    });

    $('#js-abilitate-customer').click(function() {
        $.post(abilitateUrl, {
            extraPaymentTriesId: extraPaymentId
        }).done(function () {
            window.location.replace(listUrl);
        });
    });
});
