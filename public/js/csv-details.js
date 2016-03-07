/* global $, confirm */

$(function () {

    $("#assignBonusBtn").click(function () {
        if (confirm("Sei sicuro di voler assegnare questo bonus all'utente?")) {
            var url = $("#assignPromoUrl").val();
            var bonusId = $("#assignBonusId").val();
            var packageTransaction = $("#assignPackageTransaction").val();
            $.post(url, {
                    bonusId: bonusId,
                    transactionId: packageTransaction
                })
                .always(function () {
                    location.reload();
                });
        }
    });

    $("#setTripAsPayedBtn").click(function () {
        if (confirm("Sei sicuro di voler contrassegnare questa corsa come pagata?")) {
            var url = $("#confirmTripPaymentUrl").val();
            $.post(url)
                .done(function (data) {
                    //alert(data.message);
                    //$("#setTripAsPayedBtn").hide();
                })
                .fail(function (data) {
                    //alert(data.message);
                })
                .always(function () {
                    location.reload();
                });
        }
    });

});