$(document).ready(function () {
    $("#customerForm").validate();
    $("#driverForm").validate();
    $("#settingForm").validate();
    $('.date-picker').datepicker({
        autoclose: true,
        format: 'yyyy-mm-dd',
        weekStart: 1
    });

    refreshResidenceCountryProvince( $("select#country").val());

    $("select#country").change(function () {
        //console.log("country " + $(this).val() + " " + $("select#country").val() + " " +  $("select#country.form-control.valid").val());
        refreshResidenceCountryProvince($(this).val());
    });

});



$("select#province").change(function (event, params) {
    console.log("provinceOnChange");

    var province = $(this).val(),
        promise;

    // clear present options
    $("#town option").remove();

    if (province !== 0 && province !== "0") {
        promise = $.get(municipalitiesUrl + "/" + province, function (data) {
//            if (province === $("select#province").val()) {
                $.each(data, function (i, item) {
                    $("select#town").append($("<option>", {
                        value: item.name,
                        text: item.name
                    }));
                });
                $("select#town").trigger('change');
 //           }
        });

        if (typeof params !== "undefined" && params.hasOwnProperty("townValue")) {
            promise.done(function () {
                $("select#town").val(params.townValue.toUpperCase());
            });
        }
    } else {
        $("#town").append($("<option>"));
    }
});

$("select#town").change(function (event, params) {
    console.log("townOnChange");
    $("select#zipCode option").remove();

    var province = $("select#province.form-control.valid").children("option:selected").val();

    if (province !== 0 && province !== "0") {
        promise = $.get(municipalitiesUrl + "/" + province, function (data) {
            if (province === $("select#province.form-control.valid").val()) {
                $.each(data, function (i, item) {
                    console.log("for each " + item.value);
                    if(item.name === $("select#town").val()) {
                        if(item.zip_codes!==null) {
                            $.each(item.zip_codes, function(j, itemZip) {
                                $("select#zipCode").append($("<option>", {
                                    value: itemZip,
                                    text: itemZip
                                }));
                            });
                        } else {
                            $("select#zipCode").append($("<option>", {
                                value: "00000",
                                text: "00000"
                            }));
                        }
                    }
                });
            }
        });

        // if (typeof params !== "undefined" && params.hasOwnProperty("townValue")) {
        //     promise.done(function () {
        //
        //     });
        // }
    } else {

    }
});


function refreshResidenceCountryProvince(country) {

    // var country = $("select#country.form-control.valid").val();
    //
    // if(country==null) {
    //     country = $("select#country").val();
    // }


    if( country!=="it" ) {
        $("select#province").hide();
        $("select#province").prop( "disabled", true );

        $("select#town").hide();
        $("select#town").prop( "disabled", true );

        $("select#zipCode").hide();
        $("select#zipCode").prop( "disabled", true );

        $("input#province").show();
        $("input#province").prop( "disabled", false );

        $("input#town").show();
        $("input#town").prop( "disabled", false );

        $("input#zipCode").show();
        $("input#zipCode").prop( "disabled", false );
    } else {
        $("input#province").hide();
        $("input#province").prop( "disabled", true );

        $("input#town").hide();
        $("input#town").prop( "disabled", true );

        $("input#zipCode").hide();
        $("input#zipCode").prop( "disabled", true );


        $("select#province").show();
        $("select#province").prop( "disabled", false );

        $("select#town").show();
        $("select#town").prop( "disabled", false );

        $("select#zipCode").show();
        $("select#zipCode").prop( "disabled", false );
    }

}



function deactivate(e)
{
    if (!confirm(translate("deactivateUser"))) {
        e.preventDefault();
    }
}

function reactivate(e)
{
    if (!confirm(translate("reactivateUser"))) {
        e.preventDefault();
    }
}

function removeDeactivation(e)
{
    if (!confirm(translate("removeDeactivation"))) {
        e.preventDefault();
    }
}
