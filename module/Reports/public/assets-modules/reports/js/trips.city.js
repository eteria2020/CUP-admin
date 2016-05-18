// Define Glbal vars
/* global translate:true, thiscity:true, d3:true, dc:true, crossfilter:true $ document window clearTimeout setTimeout */

// Check if var $.oe has been declared
if (typeof $.oe === "undefined") {
    $.extend({
        oe: {}
    });
}


///////////// $.oe Vars Definition /////////////
// Obj containing the trips data
$.oe.trips = {};

$.oe.filters = {};

$.oe.today = new Date();
$.oe.todayFormatted = $.oe.today.getFullYear() + "-" +
("0" + ($.oe.today.getMonth() + 1)).slice(-2) + "-" +
("0" + $.oe.today.getDate()).slice(-2);

// Charts definition
$.oe.charts = {
    day: dc.barChart("#days-chart"),
    dayOfWeek: dc.rowChart("#day-of-week-chart"),
    duration: dc.barChart("#duration-chart"),
    beginningHour: dc.barChart("#beginning-hour-chart"),
    age: dc.pieChart("#age-chart"),
    gender: dc.pieChart("#gender-chart"),
    arealist: dc.rowChart("#area-list-chart"),
    areamap: dc.geoChoroplethChart("#area-map-chart")
};

// Set the timeout needed for the page resize bind function
$.oe.timeout = 0;

// Set this city from the local var declared on the tripscity.phtml view
$.oe.thiscity = thiscity;
////////////////////////////////////////////////

// The magic!
$(document).ready(function () {

    $.oe.fn.getCityData($.oe.fn.setCityNameOnPage);
    $.oe.fn.getCharts();

    // Print the DC.js version
    //console.log("Version:"+dc.version);
    d3.selectAll("#version").text(dc.version);
});

// Window Resize Action Bind
$(window).resize(function () {
    clearTimeout($.oe.timeout);
    $.oe.timeout = setTimeout($.oe.fn.resizeCharts, 500);
});


$.oe.fn.setCityNameOnPage = function () {
    if (typeof $.oe.thiscity !== "undefined" && typeof $.oe.city !== "undefined") {

        var thisvars = $.grep($.oe.city, function (element) {
            return element.fleet_id === $.oe.thiscity;
        })[0];
        $.oe.params = thisvars.params;

        $("div.page-header h1").prepend(thisvars.fleet_name + " ");
        $("div#area-map > div.panel-heading").append(" " + thisvars.fleet_name);
        $("div#area-list > div.panel-heading").append(" " + thisvars.fleet_name);
    }
};


$.oe.fn.getCharts = function () {
    $.oe.fn.deactivePageInteraction();
    // Get the data records
    d3.csv("/reports/api/get-city-trips")
        .header("Content-Type", "application/x-www-form-urlencoded")
        .post("end_date=" + $.oe.todayFormatted + "&city=" + $.oe.thiscity, function (error, trips_record) {
            // Variables
            var formatDate = d3.time.format("%Y-%m-%d %H:%M:%S"),
                ddmin = null,
                ddmax = null,
                maxval = 0;


            //     A little coercion, since the CSV is untyped.
            trips_record.forEach(function (d) {
                d.dd = formatDate.parse(d.time_beginning_parsed);
                if (ddmin === null || d.dd < ddmin) {
                    ddmin = d.dd;
                }
                if (ddmax === null || d.dd > ddmax) {
                    ddmax = d.dd;
                }
            });

            // Create the crossfilter for the relevant dimensions and groups.
            $.oe.trips = crossfilter(trips_record);
            $.oe.filters.all = $.oe.trips.groupAll();

            var
                // dc.barChart("#days-chart")
                date_beginning = $.oe.trips.dimension(function (d) {
                    return d.dd;
                }),
                days = date_beginning.group(d3.time.day),

                // dc.rowChart("#day-of-week-chart")
                dayOfWeek = $.oe.trips.dimension(function (d) {
                    var name = ["", "0." + translate("Lunedi'"), "1." + translate("Martedi'"), "2." + translate("Mercoledi'"), "3." + translate("Giovedi'"), "4." + translate("Venerdi'"), "5." + translate("Sabato"), "6." + translate("Domenica")];
                    return name[d.time_dow];
                }),
                dayOfWeeks = dayOfWeek.group(),

                // dc.barChart("#beginning-hour-chart")
                beginningHour = $.oe.trips.dimension(function (d) {
                    return d.dd.getHours();
                }),
                beginningHours = beginningHour.group(),

                // dc.barChart("#duration-chart")
                duration = $.oe.trips.dimension(function (d) {
                    return Math.min(d.time_total_minute, 61);
                }),
                durations = duration.group(),

                // dc.pieChart("#gender-chart")
                gender = $.oe.trips.dimension(function (d) {
                    return d.customer_gender;
                }),
                genders = gender.group(),

                real_area = $.oe.trips.dimension(function (d) {
                    return +d.area_id;
                }),
                real_areas = real_area.group().reduceCount(),

                // dc.rowChart("#area-chart")
                area = $.oe.trips.dimension(function (d) {
                    return [d.area_id, "" + d.area_name];
                }),
                areas = area.group(),

                // dc.pieChart("#age-chart")
                age = $.oe.trips.dimension(function (d) {
                    if (d.customer_age >= 18 && d.customer_age <= 24) {
                        return 0;
                    } else if (d.customer_age >= 25 && d.customer_age <= 34) {
                        return 1;
                    } else if (d.customer_age >= 35 && d.customer_age <= 44) {
                        return 2;
                    } else if (d.customer_age >= 45 && d.customer_age <= 54) {
                        return 3;
                    } else if (d.customer_age >= 55 && d.customer_age <= 64) {
                        return 4;
                    } else if (d.customer_age > 64) {
                        return 5;
                    }
                }),
                ages = age.group();

            // Get the urban areas
            d3.json("/reports/api/get-urban-areas/" + $.oe.thiscity, function (areasjson) {
                // Calculate the max value of record recursion
                maxval = real_areas.top(1)[0].value;

                date_beginning.filterAll();
                $.oe.charts.day.width(900)
                    .margins({ top: 30, left: 40, right: 10, bottom: 20 })
                    .renderLabel(false)
                    .x(d3.time.scale().domain(d3.extent(trips_record, function (d) { return d.dd; })))
                    .xUnits(d3.time.days)
                    .height(250)
                    .gap(2)
                    .group(days)
                    .dimension(date_beginning)
                    .mouseZoomable(true)
                    .elasticY(true)
                    .xAxisPadding(1)
                    .centerBar(true)
                    .elasticX(true)
                    .round(d3.time.month.round)
                    .renderHorizontalGridLines(true)
                    .brushOn(true)
                    .on("filtered", function () {
                        $.oe.fn.rearrangeFilterHelper("#data-range");
                    });
                $.oe.charts.day.yAxis().ticks(2);

                age.filterAll();
                $.oe.charts.age.width(180)
                    .height(180)
                    .radius(80)
                    .innerRadius(30)
                    .dimension(age)
                    .group(ages)
                    .label(function (d) {
                        switch (d.key) {
                        case 0: return "18-24";
                        case 1: return "25-34";
                        case 2: return "35-44";
                        case 3: return "45-54";
                        case 4: return "55-65";
                        case 5: return translate("oltre") + " 64";
                        }
                    });

                gender.filterAll();
                $.oe.charts.gender.width(180)
                    .height(180)
                    .radius(80)
                    .innerRadius(30)
                    .dimension(gender)
                    .group(genders)
                    .label(function (d) {
                        var lbl = d.key === "male" ? translate("Uomini") + " " : translate("Donne") + " ",
                            percent = 0;

                        if ($.oe.charts.gender.hasFilter() && !$.oe.charts.gender.hasFilter(d.key)) {
                            percent = 0;
                        } else {
                            percent = (d.value / $.oe.trips.groupAll().reduceCount().value() * 100);
                        }

                        lbl += percent.toFixed(2) + "%";

                        return lbl;
                        //return d.key == "male" ? "Uomini " + Math.round((d.value*100)/(gender.top(Number.POSITIVE_INFINITY).length)) + "%": "Donne " + Math.round((d.value*100)/(gender.top(Number.POSITIVE_INFINITY).length)) + "%";
                    });

                dayOfWeek.filterAll();
                $.oe.charts.dayOfWeek.width(400)
                    .height(250)
                    .margins({ top: 20, left: 10, right: 10, bottom: 20 })
                    .group(dayOfWeeks)
                    .dimension(dayOfWeek)
                    .elasticX(true)
                    .label(function (d) {
                        return d.key.split(".")[1];//return d.key;
                    })
                    .ordering(function () {
                        return [
                            translate("Lunedi") + "'",
                            translate("Martedi") + "'",
                            translate("Mercoledi") + "'",
                            translate("Giovedi") + "'",
                            translate("Venerdi") + "'",
                            translate("Sabato"),
                            translate("Domenica")
                        ];
                    })
                    .title(function (d) {
                        return d.value;
                    });

                beginningHour.filterAll();
                $.oe.charts.beginningHour.width(450)
                    .height(250)
                    .margins({ top: 50, right: 10, bottom: 30, left: 50 })
                    .dimension(beginningHour)
                    .group(beginningHours)
                    .elasticY(true)
                    .centerBar(true)
                    .mouseZoomable(true)
                    .round(d3.time.hour.round)
                    .x(
                        d3.scale.linear()
                            .domain([0, 24])
                        )
                    .renderHorizontalGridLines(true)
                    .on("filtered", function () {
                        $.oe.fn.rearrangeFilterHelper("#beginning-hour");
                    });
                $.oe.charts.beginningHour.xAxis().tickFormat(
                    function (v) {
                        return v + "h";
                    });
                $.oe.charts.beginningHour.yAxis().ticks(5);

                duration.filterAll();
                $.oe.charts.duration.width(450)
                    .height(250)
                    .margins({ top: 50, right: 10, bottom: 30, left: 50 })
                    .dimension(duration)
                    .group(durations)
                    .elasticY(true)
                    .centerBar(true)
                    .mouseZoomable(true)
                    .round(d3.time.minute.round)//dc.round.floor)
                    .x(
                        d3.scale.linear()
                            .domain([0, 60])
                            .rangeRound([0, 10 * 60])
                        )
                    .renderHorizontalGridLines(true)
                    .on("filtered", function () {
                        $.oe.fn.rearrangeFilterHelper("#duration");
                    });
                $.oe.charts.duration.xAxis().tickFormat(
                    function (v) {
                        return v + "m";
                    });
                $.oe.charts.duration.yAxis().ticks(5);

                /**
                 * This functuon return a shade of a color,
                 */
                var getColor = d3.scale.linear().domain([0, maxval]).range(["yellow", "red"]);


                area.filterAll();

                var width = 500,
                    height = 500;


                real_area.filterAll();
                $.oe.charts.areamap
                    .width(width)
                    .height(height)
                    .dimension(real_area)
                    .group(real_areas)

                    .colorCalculator(function (d) {
                        return d ? getColor(d) : "#ffffff";
                    })
                    .overlayGeoJson(areasjson.features, "quartiere", function (d) {
                        return d.properties.to_char;
                    })
                    .title(function (d) {
                        return translate("Area") + ": " + d.key + "\n" + translate("Concentrazione") + ": " + d.value;
                    })
                    .projection(
                        d3.geo.stereographic()
                            .center([
                                $.oe.params.center.longitude,
                                $.oe.params.center.latitude
                            ])//[3.9,43.0])
                            .scale($.oe.params.scale)
                            .translate([
                                width / $.oe.params.translation.width,
                                height / $.oe.params.translation.height
                            ])
                        );

                var maxColor = getColor(maxval),
                    minColor = getColor(0);

                $(".area-chart").append("<div><span style=\"float:left;color: " + maxColor + "\">&nbsp;0</span><span style=\"float:right;color:" + minColor + "\">" + maxval + "&nbsp;</span>" + "<div style=\"" + "height: 20px;" +
                    "background: " + maxColor + ";" + /* For browsers that do not support gradients */
                    "background: -webkit-linear-gradient(left, " + minColor + " , " + maxColor + ");" + /* For Safari 5.1 to 6.0 */
                    "background: -o-linear-gradient(right, " + minColor + ", " + maxColor + ");" + /* For Opera 11.1 to 12.0 */
                    "background: -moz-linear-gradient(right, " + minColor + ", " + maxColor + ");" + /* For Firefox 3.6 to 15 */
                    "background: linear-gradient(to right, " + minColor + " , " + maxColor + ");" + /* Standard syntax (must be last) */
                    "\"></div></div>");

                $.oe.charts.arealist.width(500)
                    .gap(1)
                    .margins({ top: 10, left: 10, right: 10, bottom: 30 })
                    .group(areas)
                    .dimension(area)
                    .ordering(function (d) { return -d.value; })
                    .rowsCap(100)
                    .elasticX(true)
                    .label(function (d) {
                        return d.key[1];
                    })
                    .title(function (d) {
                        return d.key + " : " + d.value;
                    });

                $("svg").css("background-color", "red");

                dc.dataCount("#data-count")
                    .dimension($.oe.trips)
                    .group($.oe.filters.all)
                    .html({
                        some: "<strong>%filter-count</strong> " + translate("selezionate di") + " <strong>%total-count</strong> " + translate("corse") +
                        " | <a href=\"javascript:dc.filterAll(); dc.renderAll();\"\">" + translate("Azzera tutti i filtri") + "</a>",
                        all: translate("Tutte le") + " <strong>%total-count</strong> " + translate("corse sono selezionate. Clicca su uno dei grafici per applicarne dei filtri.")
                    });

                //dc.renderAll();
                //dc.redrawAll();

                //console.log("1");

                // Graphics are loaded, so I resize the graphs
                $.oe.fn.resizeCharts();

                $.oe.fn.activePageInteraction();

                // Recompose the chart structure (to adapt for Bootstrap)
                //$("div.panel-body > div:not(.chart-label) >span, div.panel-body > div:not(.chart-label) > a").appendTo("div.panel-heading");
                //$("div.panel-body > div:not(.chart-label) >span, div.panel-body > div:not(.chart-label) > a").clone().appendTo("div.panel-heading");
                //$("div.panel-body > div:not(.chart-label) >span, div.panel-body > div:not(.chart-label) > a")
                $("svg").on("click", function () {
                    var parentID = "#" + $(this).parent().parent().parent().prop("id");
                    $.oe.fn.rearrangeFilterHelper(parentID);
                });


                // Coloring the Age Pie Chart Legend
                $("div.panel .chart-label > span:nth-of-type(3)").css("color", $("div.panel#customer-age g.pie-slice._5 path").css("fill"));
                $("div.panel .chart-label > span:nth-of-type(2)").css("color", $("div.panel#customer-age g.pie-slice._4 path").css("fill"));
                $("div.panel .chart-label > span:nth-of-type(1)").css("color", $("div.panel#customer-age g.pie-slice._3 path").css("fill"));

                // Setting the correct svg width of the Map Chart
                // Doing this the chart is vertically centered
                $(".area-list-chart").css("width", width + "px");

                //dc.renderAll();
            });
        }
            );
};


$.oe.fn.activePageInteraction = function () {
    $("div#over").remove();
};

$.oe.fn.deactivePageInteraction = function () {
    $("div.container > div.row").append("<div id=\"over\"><span class=\"glyphicon glyphicon- refresh glyphicon- refresh - animate\" aria-hidden=\"true\"></span></div>");
};

/**
 *
 * This function resize the Charts, adapting it to the body width.
 *
 */
$.oe.fn.resizeCharts = function () {
    var newWidth = $(".panel-body").width(),
        newRadiusChartsWidth = $(".col-xs-12 .panel-body").width();

    $.oe.charts.day.width(newWidth)
        .transitionDuration(0);

    $.oe.charts.dayOfWeek.width(newWidth)
        .transitionDuration(0);

    $.oe.charts.beginningHour.width(newWidth)
        .transitionDuration(0);

    $.oe.charts.duration.width(newWidth)
        .transitionDuration(0);

    $.oe.charts.arealist
        .width(newWidth)
        .height($.oe.charts.arealist.height() + 800)
        .transitionDuration(0);

    $.oe.charts.age
        .width(newRadiusChartsWidth - 5)
        .height(newRadiusChartsWidth - 5)
        .radius((newRadiusChartsWidth / 2.2) - 20)
        .transitionDuration(0);

    $.oe.charts.gender
        .width(newRadiusChartsWidth - 5)
        .height(newRadiusChartsWidth - 5)
        .radius((newRadiusChartsWidth / 2.2) - 20)
        .transitionDuration(0);

    // Render the charts
    dc.renderAll();
};

/**
 *
 * This function move the .reset (containing the filter value) from the original
 * position to the bootstrap panel header
 *
 */
$.oe.fn.rearrangeFilterHelper = function (parentID) {
    // Remove the actual helpers from the panel header
    $(parentID + " div.panel-heading .reset").remove();

    // Clone the hidden helpers to the panel header
    $(parentID + " .reset").clone().appendTo(parentID + " div.panel-heading");
};
