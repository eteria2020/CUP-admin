/*global $ moment window alert*/
$(function() {
    'use strict';
    $('.datetime-picker').datetimepicker({
        format: "YYYY/MM/DD HH:mm:ss"
    });
    $('.time-picker').datetimepicker({
        format: "HH:mm:ss",
        useCurrent: false
    });
    $('#trip-beginning, #trip-end').on('dp.change', function () {
        var tripBeginning = new Date($('#trip-beginning').val()),
            tripEnd = new Date($('#trip-end').val()),
            milliseconds = tripEnd - tripBeginning,
            duration = moment.duration(milliseconds),
            length = duration.hours() + ':' + duration.minutes() + ':' + duration.seconds();
        $('#trip-length').data('DateTimePicker').date(length);
    });
    $('#trip-length').on('dp.change', function (e) {
        var tripBeginning = $('#trip-beginning').val(),
            beginningMoment,
            seconds;
        if (tripBeginning && $('#trip-length').val() !== '') {
            beginningMoment = moment(tripBeginning, 'YYYY-MM-DD HH:mm:ss');
            seconds = e.date.subtract(moment().startOf('day'));
            $('#trip-end').data('DateTimePicker').date(beginningMoment.add(seconds, 's'));
        }
    });
    $('#tripCostForm').submit(function (e) {
        var tripBeginning = $('#trip-beginning').val(),
            tripEnd = $('#trip-end').val(),
            tripLength = $('#trip-length').val(),
            tripParkSeconds = Math.floor($('#trip-park-minutes').val() * 60),
            customerGender = $('#customer-gender').val(),
            customerBonus = $('#customer-bonus').val(),
            customerDiscount = $('#customer-discount').val();
        e.preventDefault();
        if (!tripBeginning || !tripEnd) {
            alert(translate("alertEmptyFields"));
        } else {
            $.post(
                window.location.origin + '/trips/cost-computation',
                {
                    tripBeginning: tripBeginning,
                    tripEnd: tripEnd,
                    tripLength: tripLength,
                    tripParkSeconds: tripParkSeconds,
                    customerGender: customerGender,
                    customerBonus: customerBonus,
                    customerDiscount: customerDiscount
                },
                function (data) {
                    $('#trip-cost').html(parseFloat(data.costNoDiscount/100).toFixed(2));
                    $('#trip-cost-discounted').html(parseFloat(data.cost/100).toFixed(2));
                }
            );
        }
    });
});