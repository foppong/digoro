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
				alert('SCHEDULE: an error occured!');
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
	},

  pullEventData: function ( data ) {
  	var _schedule = this;
		var data_send = { eventID: idevent };

	  $.ajax({
	  	type: "POST",
	    dataType: 'json',
	    url: "../data/single_event_data.php",
	    data: data_send, // Data that I'm sending
	    error: function() {
	      alert('Error: Response failed');
	   	},
	    success: function( data ) { 
				_schedule.make_Edit_Event_Form_sticky( data );
				_schedule.buildViewEvent( data );
	    },
	    cache: false
   	});
	},

  make_Edit_Event_Form_sticky: function( data ) {

		var eventInfo_array = new Array(); // set up array to store data pulled from database
	  $(data).each(function(key, val) {
			var i = 0;
	  	for (var propertyName in val) {
	    	eventInfo_array[i] = val[propertyName];
	    	i++;
	    }
	  });	
	  	
		$( '#edit-event-sel-type' ).val( eventInfo_array[0] );
		$( '#edit-event-sel-date' ).val( eventInfo_array[1] );
		$( '#edit-event-time' ).val( eventInfo_array[2] );
		$( '#edit-event-opname' ).val( eventInfo_array[3] );
		$( '#edit-event-vname' ).val( eventInfo_array[4] );
		$( '#edit-event-vadd' ).val( eventInfo_array[5] );
		$( '#edit-event-note' ).val( eventInfo_array[6] );
		$( '#edit-event-res' ).val( eventInfo_array[7] );
		
  },
  
  buildViewEvent: function( data ) {
  	
		$('#dynamicEventinfo').html(""); // clear out any prior info
		var eventInfo_array = new Array(); // set up array to store data pulled from database
	  $(data).each(function(key, val) {
			var i = 0;
	  	for (var propertyName in val) {
	    	eventInfo_array[i] = val[propertyName];
	    	i++;
	    }
	  });	
		
		$( '#ViewEventForm form #dynamicEventinfo' )
			.append( '<p>Type: ' + eventInfo_array[8] + '</p>' )
			.append( '<p>Date: ' + eventInfo_array[1] + '</p>')
			.append( '<p>Time: ' + eventInfo_array[2] + '</p>')
			.append( '<p>Opponent: ' + eventInfo_array[3] + '</p>')
			.append( '<p>Venue Name: ' + eventInfo_array[4] + '</p>')
			.append( '<p>Address: ' + eventInfo_array[5] + '</p>')
			.append( '<p>Notes: ' + eventInfo_array[6] + '</p>')
			.append( '<p>Results: ' + eventInfo_array[7] + '</p>')
		
  }


}

$(document).ready(function() {

	// Load schedule associated with team
	SCHEDULE.loadSchedule();

});
