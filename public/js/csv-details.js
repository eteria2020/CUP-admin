$(document).ready(function () {

    $("#assignPromoBtn").click(function () {
        var url = $("#assignPromoUrl").val();
        var promoCode = $("#assignPromoCode").val();
        $.post(url, {
                promocode: promoCode
            })
            .done(function (data) {
                alert(data.message);
            })
            .fail(function (data) {
                alert(data.message);
            });
    });

    $("#setTripAsPayedBtn").click(function () {
        var url = $("#confirmTripPaymentUrl").val();
        $.post(url)
            .done(function (data) {
                $("#setTripAsPayedBtn").addClass('disabled');
                alert(data.message);
            })
            .fail(function (data) {
                alert(data.message);
            });
    });
});