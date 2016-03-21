/* global $, confirm, location, translate*/

$(function () {
    'use strict';

    $("#assignBonusBtn").click(function () {
        if (confirm(translate("confirmAssignBonus"))) {
            var url = $("#assignPromoUrl").val();
            var bonusId = $("#assignBonusId").val();
            $.post(url, {
                    bonusId: bonusId
                })
                .always(function () {
                    location.reload();
                });
        }
    });

    $("#setTripAsPayedBtn").click(function () {
        if (confirm(translate("confirmAssignTripPayed"))) {
            var url = $("#confirmTripPaymentUrl").val();
            $.post(url)
                .always(function () {
                    location.reload();
                });
        }
    });
});
