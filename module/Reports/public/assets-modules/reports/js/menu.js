if (typeof global === 'undefined') {
    var global = {
	    city: {}
    };
}else{
	global.city = {};
}


/**
 *	This page make an AJAX request to the controller getting the "city" number,
 *	name, and id.
 *
 *	It also makes the Submenu and populate it.
 *
 */
function getCityData(callback){
	$.ajax({
		method: "GET",
		dataType: "json",
		url: '/reports/api/get-cities',
		data: {},
		success: function(d){
			$('#navbar li:eq(0)').after('<li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="true">City Trips <span class="caret"></span></a><ul class="dropdown-menu"></ul></li>')

			global.city = d.city;

            $.each( global.city, function( key, value ) {
				$("#navbar ul.dropdown-menu").append('<li><a href="/reports/tripscity/' + value.fleet_id + '">' + value.fleet_name + '</a></li>');
			});
			
		},
		complete: function(){
			typeof callback === 'function' && callback();
		}
	});
}
