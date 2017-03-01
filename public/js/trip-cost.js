/*global $ moment window alert*/

$(function() {
    'use strict';

    $('.datetime-picker').datetimepicker({
        format: "DD-MM-YYYY HH:mm:ss"
    });

    $('.time-picker').datetimepicker({
        format: "HH:mm:ss",
        useCurrent: false
    });

    $('#trip-beginning, #trip-end').on('dp.change', function () {
        var tripBeginning = convertStringToDatetime($('#trip-beginning').val(), "DD-MM-YYYY HH:MM:SS");
        var tripEnd = convertStringToDatetime($('#trip-end').val(), "DD-MM-YYYY HH:MM:SS");
        if (tripEnd === null){
            tripEnd = tripBeginning;
        }
        var milliseconds = tripEnd - tripBeginning,
            duration = moment.duration(milliseconds),
            length = duration.hours() + ':' + duration.minutes() + ':' + duration.seconds();


        $('#trip-length').data('DateTimePicker').date(length);
    });

    $('#trip-length').on('dp.change', function (e) {
        var tripBeginning = $('#trip-beginning').val(),
            beginningMoment,
            seconds;

        if (tripBeginning && $('#trip-length').val() !== '') {
            beginningMoment = moment(tripBeginning, 'DD-MM-YYYY HH:mm:ss');
            seconds = e.date.subtract(moment().startOf('day'));

            $('#trip-end').data('DateTimePicker').date(beginningMoment.add(seconds, 's'));
        }
    });

    var costMinTrip = 28;
    var costMinPark = 10;

    // Return num. of minutes between dtBeginning and dtEnd
    function getDateDiffByMin(dtBeginning, dtEnd) {
            var timeDiff = Math.abs(dtEnd.getTime() - dtBeginning.getTime());
            return Math.round(timeDiff / (1000 * 60));
    }


// Convert a string "DD-MM-YYYY hh:mm:ss" or YYYY/MM/DD HH:MM:SS to a Date object
    function convertStringToDatetime(dtString, format) {
        var result = null;

        try {
            if (format.toUpperCase()=="DD-MM-YYYY HH:MM:SS"){
                var arrayDateTime = dtString.trim().split(' ');
                var arrayDate = arrayDateTime[0].trim().split('-');
                var arrayTime = arrayDateTime[1].trim().split(':');

                result = new Date(
                        parseInt(arrayDate[2].trim()),
                        parseInt(arrayDate[1].trim()) - 1,
                        parseInt(arrayDate[0].trim()),
                        parseInt(arrayTime[0].trim()),
                        parseInt(arrayTime[1].trim()),
                        parseInt(arrayTime[2].trim()),
                        0);
            }
            else if (format.toUpperCase=="YYYY/MM/DD HH:MM:SS"){
                var arrayDateTime = dtString.trim().split(' ');
                var arrayDate = arrayDateTime[0].trim().split('/');
                var arrayTime = arrayDateTime[1].trim().split(':');

                result = new Date(
                        parseInt(arrayDate[0].trim()),
                        parseInt(arrayDate[1].trim()) - 1,
                        parseInt(arrayDate[2].trim()),
                        parseInt(arrayTime[0].trim()),
                        parseInt(arrayTime[1].trim()),
                        parseInt(arrayTime[2].trim()),
                        0);
            }


        } catch (err) {
                result = null;
        }

        return result;
    }

    // Convert string hh:mm:ss in to minutes
    function convertTimeStampToMinutes(dtString) {
        var result = 0;

        try {
            var arrayTime = dtString.trim().split(':');
            var seconds = parseInt(arrayTime[0]) * 3600 + parseInt(arrayTime[1]) * 60 + parseInt(arrayTime[2]);
            result = Math.round(seconds / 60);
        } catch (err) {
                result = 0;
        }

        return result;
    }

    // Return the minimun value between val1 and val2
    function minimum(val1, val2) {
        if (val1 < val2){
            return val1;
        }
        else {
            return val2;
        }
    }

    // Retour trip cost in euro cent, from billiable mitutes of trips
    function tripCostBaseEuroCent(minutes) {
        var cost = 0;
        var result = 0;

        if (minutes >= 1440) {
            result = 5000 + tripCostBaseEuroCent(minutes - 1440);
        } else if (minutes >= 240){
            cost = 3000 + tripCostBaseEuroCent(minutes - 240);
            result = minimum(5000, cost);
        } else if (minutes >= 60) {
            cost = 1200 + tripCostBaseEuroCent(minutes - 60);
            result = minimum(3000, cost);
        } else {
            cost = minutes * costMinTrip;
            result = minimum(1200, cost);
        }

        return result;
    }

    // Calcolate a trip cost (new version with no parking discount)
    function tripCostEuroCent(minTripTotal, minPark, minBonus, discountPerc ) {
        var cost1 = 0;
        var cost2 = 0;
        var minBill = 0;
        var discount = 0;

        minBill = minTripTotal - minBonus;
        discount = (100 - discountPerc) / 100;

        cost1 = tripCostBaseEuroCent(minBill);
        cost2 = tripCostBaseEuroCent(minBill - minPark);

        var costFloat1 = parseFloat(cost1) * discount;
        var costFloat2 = parseFloat(cost2) * discount;

        return Math.round(minimum(costFloat1, costFloat2 + parseFloat(costMinPark * minPark)));

    }


    $('#tripCostForm').submit(function (e) {
        var tripLength = $('#trip-length').val(),
            tripParkMinutes = $('#trip-park-minutes').val(),
            customerBonus = $('#customer-bonus').val(),
            customerDiscount = $('#customer-discount').val();

        e.preventDefault();

        if (tripLength.length !== 8) {
            alert(translate("alertEmptyFields"));
        } else {
            var totalMinTrip = convertTimeStampToMinutes(tripLength);
            //var minParking = Math.round(tripParkSeconds / 60);
            var minBonus = parseInt(customerBonus);
            var discountPerc = parseInt(customerDiscount);

            $('#trip-cost-discounted').html(tripCostEuroCent(totalMinTrip, tripParkMinutes, minBonus, discountPerc) / 100);
            $('#trip-cost').html(tripCostEuroCent(totalMinTrip, tripParkMinutes, minBonus, 0) / 100);

        }
    });
});