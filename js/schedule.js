/**
 * @author Frank
 */


$.ajaxSetup({"error":function(XMLHttpRequest,textStatus, errorThrown) {   
      alert(textStatus);
      alert(errorThrown);
      alert(XMLHttpRequest.responseText);
  }});


var SCHEDULE = {
	
	loadSchedule: function() {
		var _schedule = this;

		// AJAX call to retrieve list of events associated with team
		$.ajax({
	      	type: "POST",
			dataType: 'json',
			url: "../data/schedule_data.php",
			success: function(data) {
				_schedule.buildTable(data);
			},
			error: function() {
				alert('an error occured!');
			}
		});	
	},
	
	buildTable: function(data) {
	    var table = $("#schedule");
	    table.html("");  //clear out the table if it was previously populated
	
	    table.append('<thead><tr></tr></thead>');
	    var thead = $('thead tr', table);                                        
	    
	    //create the table headers
	    for (var propertyName in $(data)[0]) {                
	        thead.append('<th>' + propertyName + '</th>');
	    }
	
	    //add the table rows
	    $(data).each(function(key, val) {
	        table.append('<tr></tr>');
	        var tr = $('tr:last', table);
	        for (var propertyName in val) {
	            tr.append('<td>' + val[propertyName] + '</td>');
	        }
	    });		
	}
}

$(document).ready(function() {

	// Load schedule associated with team
	SCHEDULE.loadSchedule();

});
