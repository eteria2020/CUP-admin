var currentType;
setCurrentPaymentType();
var paymentContainerClass = "sng-payment-row";

/**
 * Set all listeners and setup
 */
$(function() {
    "use strict";

    // Remove add payment button
    toggleAddPaymentButton(false);

    // Respond to fleet change
    $('#fleet').change(function () {
        if ($("#fleet option:selected").text() !== "") {
            toggleFleetOptionNull(false);
        }
    });

    // Respond to type change
    $('#type').change(function () {
        var newType = $("#type option:selected").text();
        // Do something only if user selected a different value
        if (currentType != newType) {
            toggleTypeOptionNull(false);
            removePaymentBlocks();
            addPaymentRow(newType == "penalty");
            currentType = newType;
            toggleAddPaymentButton(true);
        }
    });

    // Respond to add payment row button
    $('#js-add-row').click(function (e) {
        if (currentType !== "") {
            addPaymentRow(currentType == "penalty");
        }
    });

    // Respond to pay button
    $('#js-extra-payment').click(function (e) {
        startPaymentProcess();
    });
});

/**
 * Get values and handle payment sequence
 */
function startPaymentProcess()
{
    var customerId = $('#customer').val();
    var fleetId = parseInt($('#fleet').val());
    var type = $('#type').val();
    var reasons = [];
    var amounts = [];

    var paymentBlocks = document.getElementsByClassName(paymentContainerClass);
    for (var i = 0; i < paymentBlocks.length; i++) {
        var number = getBlockNumber(paymentBlocks[i]);
        reasons[i] = $('#reason' + number).val();
        amounts[i] = $('#amount' + number).val();
    }

    var canProceed = checkAndFormatFields(customerId, fleetId, type, reasons, amounts);
    if (canProceed) {
        proceedWithPayment(customerId, fleetId, type, reasons, amounts);
    }
}

/**
 * Check if all values are entered correctly and format amounts.
 * This function changes the values in amounts from string to integer.
 *
 * @param integer customerId
 * @param integer fleetId
 * @param string type
 * @param string[] reasons
 * @param string[] amounts
 */
function checkAndFormatFields(customerId, fleetId, type, reasons, amounts)
{
    if (!customerId || customerId < 0) {
        alert('Formato id cliente non corretto');
        return false;
    }

    if (!fleetId || fleetId <= 0) {
        alert('Selezionare una flotta');
        return false;
    }

    if (type === "---") {
        alert('Selezionare una tipologia');
        return false;
    }

    // Check all payment blocks for errors or omissions
    for (var i = 0; i < reasons.length; i++) {
        var reason = reasons[i];
        var amount = amounts[i];
        if (reason.length === 0) {
            alert('Inserire una causale valida');
            return false;
        }
        amount = amount.replace(",", ".");
        amount = parseFloat(amount);
        amount = Math.floor(amount * 100);
        if (!amount || amount < 0) {
            alert('Inserire un importo valido in euro');
            return false;
        } else {
            // If the amount is valid, sobstitute the formatted value in the
            // array
            amounts[i] = amount;
        }
    }

    return true;
}

/**
 * Ask for confirmation and proceed with payment
 *
 * @param integer customerId
 * @param integer fleetId
 * @param string type
 * @param string[] reasons
 * @param integer[] amounts
 */
function proceedWithPayment(customerId, fleetId, type, reasons, amounts)
{
    $.get('/customers/info/' + customerId)
        .done(function (data) {
            // Calculate total amount
            var amount = 0;
            for (var i = 0; i < amounts.length; i++) {
                amount += amounts[i];
            }
            if (confirm('Confermi il pagamento al cliente ' +
                data.name + ' ' + data.surname +
                ' di un importo di ' + amount / 100 + ' euro')) {
                sendPaymentRequest(customerId, fleetId, type, reasons, amounts);
            }
        })
        .fail(function (data) {
            var message = JSON.parse(data.responseText).error;
            alert(message);
            clearFields();
        });
}

/**
 * Request payment with post
 *
 * @param integer customerId
 * @param integer fleetId
 * @param string type
 * @param string[] reasons
 * @param integer[] amounts
 */
function sendPaymentRequest(customerId, fleetId, type, reasons, amounts) {
    $.post('/payments/pay-extra', {
        customerId: customerId,
        fleetId: fleetId,
        type: type,
        reasons: reasons,
        amounts: amounts
    }).done(function (data) {
        alert(data.message);
        clearFields();
    }).fail(function (data) {
        var message = JSON.parse(data.responseText).error;
        alert(message);
        clearFields();
    });
}

/**
 * Set all values to default
 */
function clearFields() {
    $('#customer').val('');
    toggleFleetOptionNull(true);
    toggleTypeOptionNull(true);
    removePaymentBlocks();
    toggleAddPaymentButton(false);
}

/**
 * Adds a payment block that consists of two or three rows based on isPenalty.
 * If isPenalty is true, the rows are three and the first row is a selector
 * that when changed, automatically assignes values to the other rows.
 *
 * @param boolean isPenalty
 */
function addPaymentRow(isPenalty)
{
    var blockNumber = getNextBlockNumber();
    var removeButton = "<button id=\"remove-button" + blockNumber + "\" class=\"sng-close-payment\"><i class=\"fa fa-close\"></i></button>";

    // This is the html that corresponds to the penalty selector.
    // It is used only if isPenalty is true
    var penaltyContent = "<!-- PENALTY SELECTOR -->" +
        "<div id=\"penaltyRow\" class=\"row\">" +
            "<div class=\"col-lg-12\">" +
                "<label>Penale</label>" +
                "<select id=\"penalty" + blockNumber + "\" class=\"form-control\">" +
                    penaltyOptions +
                "</select>" +
            "</div>" +
        "</div>";

    // This is the html that corresponds to the reason row
    var reasonContent = "<!-- REASON INPUT -->" +
        "<div class=\"row sng-margin-top\">" +
            "<div class=\"col-lg-12\">" +
                "<label>Causale</label>" +
                "<input id=\"reason" + blockNumber + "\" class=\"form-control\" type=\"text\"></input>" +
            "</div>" +
        "</div>";

    // This is the html that corresponds to the amount row
    var amountContent = "<!-- AMOUNT INPUT -->" +
        "<div class=\"row sng-margin-top\">" +
            "<div class=\"col-lg-4\">" +
                "<label>Importo (&euro;)</label>" +
                "<input id=\"amount" + blockNumber + "\" class=\"form-control\" type=\"text\"></input>" +
            "</div>" +
        "</div>";

    // Finally add the row. Omitt penalty selector if isPenalty is false
    var $newRow = $("<div>");
    $newRow.attr("id", "payment-row" + blockNumber);
    $newRow.addClass(paymentContainerClass);
    $newRow.html(removeButton + (isPenalty ? penaltyContent : "") + reasonContent + amountContent);
    $newRow.appendTo($("#payment-rows"));

    // Automatically assign values to reason and amount when penalty selector
    // changes
    if (isPenalty) {
        $("#penalty" + blockNumber).change(function () {
            var selected = $(this).find('option:selected'),
                reason = selected.data('reason') || '',
                amount = parseFloat(selected.data('amount')) / 100 || '';
            $("#reason" + blockNumber).val(reason);
            $("#amount" + blockNumber).val(amount);
        });
    }

    // Remove block on removeButton press
    $('#remove-button' + blockNumber).click(function (e) {
        $('#payment-row' + blockNumber).remove();
    });
}

/**
 * Generate a new number to assign to a new payment block
 *
 * @return integer
 */
function getNextBlockNumber()
{
    var paymentBlocks = document.getElementsByClassName(paymentContainerClass);
    var nextNumber = 0;
    for (var i = paymentBlocks.length - 1; i >= 0; i--) {
        var number = getBlockNumber(paymentBlocks[i]) + 1;
        if (number > nextNumber) {
            nextNumber = number;
        }
    }
    return nextNumber;
}

/**
 * Get the number associated with the payment block
 *
 * @param div block
 * @return integer
 */
function getBlockNumber(block)
{
    return parseInt(block.id.substring(11, block.id.length));
}

/**
 * Removes all extra payment rows
 */
function removePaymentBlocks()
{
    var paymentBlocks = document.getElementsByClassName(paymentContainerClass);
    for (var i = paymentBlocks.length - 1; i >= 0; i--) {
        paymentBlocks[i].remove();
    }
    window.scrollTo(0, 0);
}

/**
 * Add or remove the "---" option from the fleet selector
 *
 * @param boolean on if true add button, otherwise remove
 */
function toggleFleetOptionNull(on)
{
    if (on) {
        $("#fleet").prepend("<option id='fleet-option-null' value='' selected='selected'>---</option>");
    } else {
        $("#fleet-option-null").remove();
    }
}

/**
 * Add or remove the "---" option from the type selector
 *
 * @param boolean on if true add button, otherwise remove
 */
function toggleTypeOptionNull(on)
{
    if (on) {
        $("#type").prepend("<option id='type-option-null' value='' selected='selected'>---</option>");
        setCurrentPaymentType();
    } else {
        $("#type-option-null").remove();
    }
}

/**
 * Show or hide the add payment button
 *
 * @param boolean on if true show button, otherwise hide
 */
function toggleAddPaymentButton(on)
{
    if (on) {
        $('#js-add-row').show();
    } else {
        $('#js-add-row').hide();
    }
}

function setCurrentPaymentType()
{
    currentType = $("#type option:selected").text();
}
