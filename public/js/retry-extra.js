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


function setPayableExtra(payable, id) {
    if(confirm("Sei sicuro di rendere non pagabile questo extra/penale")){
        $.ajax({
            type: "POST",
            url: "/payments/set-payable",
            data: {'payable': payable, 'id': id},
            success: function (data) {
                switch (data.toString()) {
                    case 'success':
                        location.reload();
                        break;
                    case 'error':
                        alert("Errore...");
                        break;
                }
            },
            error: function () {
            }
        });
    }else{
        $('#check_payable').prop('checked', true);
    }
}
