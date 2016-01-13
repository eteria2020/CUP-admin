if (typeof $.oe === 'undefined') {
    $.extend({
	    oe: {
	    	city: {},
	    	fn: {}
    	}
    });
}else{
	$.oe.city = {};
}


/**
 *	This page make an AJAX request to the controller getting the "city" number,
 *	name, and id.
 *
 *	It also makes the Submenu and populate it.
 *
 */
$.oe.fn.getCityData = function(callback){
	$.ajax({
		method: "GET",
		dataType: "json",
		url: '/reports/api/get-cities',
		data: {},
		success: function(d){
			$('#navbar li:eq(0)').after('<li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="true">City Trips <span class="caret"></span></a><ul class="dropdown-menu"></ul></li>')

			$.oe.city = d.city;

            $.each( $.oe.city, function( key, value ) {
				$("#navbar ul.dropdown-menu").append('<li><a href="/reports/tripscity/' + value.fleet_id + '">' + value.fleet_name + '</a></li>');
			});
			
		},
		complete: function(){
			typeof callback === 'function' && callback();
		}
	});
}

/**
 *  This function print the specified filter
 *
 *  @param  filter  A filter (d3.dimension)
 *  @case   DEBUG
 */
$.oe.fn.printFilter = function(filter){
    var f=eval(filter);
    if (typeof(f.length) != "undefined") {}else{}
    if (typeof(f.top) != "undefined") {f=f.top(Infinity);}else{}
    if (typeof(f.dimension) != "undefined") {f=f.dimension(function(d) { return "";}).top(Infinity);}else{}
    //console.log(filter+"("+f.length+") = "+JSON.stringify(f).replace("[","[\n\t").replace(/}\,/g,"},\n\t").replace("]","\n]"));
}