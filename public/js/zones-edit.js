/* global $, confirm, dataTable, zones */
$(function () {
    "use strict";

    $("#useKmlFile").change(function(){
        $("div.row.areaUse").toggleClass("hidden");
        $("div.row.kmlUpload").toggleClass("hidden");
    });
});
