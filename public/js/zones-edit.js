/* global $, confirm, dataTable, zones */
$(function () {
    $("#useKmlFile").change(function(){
        $("div.row.areaUse").toggleClass("hidden");
        $("div.row.kmlUpload").toggleClass("hidden");
    });
});