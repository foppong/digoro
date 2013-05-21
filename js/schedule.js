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
		var data_to_send = { actionvar: 'pullScheduleData' };

		// AJAX call to retrieve list of events associated with team
		$.ajax({
	    type: "POST",
			dataType: 'json',
			url: "../data/schedule_data.php",
			data: data_to_send,
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


var EVENT = {
		
	loadDialog: function() {
		$( ".pickdate" ).each( function() {
			$( this ).datepicker({
				showOn: "button", //Could select both if I separate out the edit and add button b/c that date is triggering when loaded
				buttonImage: "../css/imgs/calendar.gif",
				buttonImageOnly: true
			});
		});
		
		$( "#AddEventForm" ).dialog({
			autoOpen: false,
			height: 'auto',
			width: 'auto',
			modal: true,
			buttons: {
				"Add": function() {
					// Add event to database
					EVENT.add();					
					$( this ).dialog( "close" );
				},
				"Save and Add Another": function() {
					// Add event to database
					EVENT.add();					
				},
				Cancel: function() {
					$( this ).dialog( "close" );
	       	MISCFUNCTIONS.clearForm( '#AddEventForm form' );
				}
			}
		});		

		$( "#EditEventForm" ).dialog({
			autoOpen: false,
			height: 'auto',
			width: 'auto',
			modal: true,
			buttons: {
				"Edit Event": function() {
					EVENT.edit();	// Edit event in database
					$( this ).dialog( "close" );
				},
				Cancel: function() {
					$( this ).dialog( "close" );
	       	MISCFUNCTIONS.clearForm( '#EditEventForm form' );					
				}
			}
		});	

		$( "#ViewEventForm" ).dialog({
			autoOpen: false,
			height: 'auto',
			width: 'auto',
			modal: true,
			buttons: {
				"Close": function() {
					$( this ).dialog( "close" );
				}
			}
		});	
		
		$( "#DelEventForm" ).dialog({
			autoOpen: false,
			height: 'auto',
			width: 'auto',
			modal: true,
			buttons: {
				"Delete Event": function() {
					// Delete member from database
					EVENT.del();					
					$( this ).dialog( "close" );
				},
				Cancel: function() {
					$( this ).dialog( "close" );
				}
			}			
		});	
	},

	// add event information to database from dialog form
  	add: function() { 
    	var form_data = $( '#AddEventForm form' ).serialize();
	    $.ajax({
	      	type: "POST",
	      	url: "../manager/add_event.php",
	      	data: form_data, // Data that I'm sending
	      	error: function() {
	        	$( '#status' ).append( '<div class="alert alert-error">Add Event failed</div>' ).slideDown( 'slow' );
	     	},
	      	success: function( data ) {   
	        	SCHEDULE.loadSchedule(); //Call to schedule.js
	        	$( '#status' ).append( data ).slideDown( 'slow' );		
	        	MISCFUNCTIONS.clearForm( '#AddEventForm form' );
	      	},
	      	complete: function() {
	        	setTimeout(function() {
	          		$( '#status' ).slideUp( 'slow' );
	          		$( '#status .alert' ).remove();
	        	}, 2000);
	      	},
	      	cache: false
    	});
    },
    
	// edit event information to database from dialog form
  	edit: function() { 
		$( '#EditEventForm form' ).append( '<input type="hidden" id="z" name="z" value="' + idevent + '"/>' );
    	var form_data = $( '#EditEventForm form' ).serialize();
	    $.ajax({
	      	type: "POST",
	      	url: "../manager/edit_event.php",
	      	data: form_data, // Data that I'm sending
	      	error: function() {
	        	$( '#status' ).append( '<div class="alert alert-error">Edit failed</div>' ).slideDown( 'slow' );
	     	},
	      	success: function( data ) { 
	        	SCHEDULE.loadSchedule(); //Call to schedule.js to refresh table
	        	$( '#status' ).append( data ).slideDown( 'slow' );
	        	$( '#EditEventForm form #z' ).remove();	        	
	        	MISCFUNCTIONS.clearForm( '#EditEventForm form' );   	
	      	},
	      	complete: function() {
	        	setTimeout(function() {
	          		$( '#status' ).slideUp( 'slow' );
	          		$( '#status .alert' ).remove();	          		
	        	}, 2000);
	      	},
	      	cache: false
    	});
    },
    
	// delete event information in database from dialog form
  	del: function() { 
		$( '#DelEventForm form' ).append( '<input type="hidden" id="z" name="z" value="' + idevent + '"/>' );
    	var form_data = $( '#DelEventForm form' ).serialize();
	    $.ajax({
	      	type: "POST",
	      	url: "../manager/delete_event.php",
	      	data: form_data, // Data that I'm sending
	      	error: function() {
	        	$( '#status' ).append( '<div class="alert alert-error"><div class="alert alert-error">Delete failed</div>' ).slideDown( 'slow' );
	     	},
	      	success: function( data ) {   
	        	SCHEDULE.loadSchedule(); //Call to schedule.js
	        	$( '#status' ).append( data ).slideDown( 'slow' );	    
	      	},
	      	complete: function() {
	        	setTimeout(function() {
	          		$( '#status' ).slideUp( 'slow' );
	          		$( '#status .alert' ).remove();	          		
	        	}, 2000);
	      	},
	      	cache: false
    	});
    }
}


$(document).ready(function() {

	// Load schedule associated with team
	SCHEDULE.loadSchedule();

	// Load event dialogs
	EVENT.loadDialog();

	// Load Selected Team Data
	TEAMDATA.pullTeamData(); // Global function call from projectlbackstar.js
	
	$( "#add-event" ).on("click", function() {
		$( "#AddEventForm" ).dialog( "open" );
	});

	// Binds click to ajax loaded edit button
	$( "#schedule" ).on("click", ".edit_event", function() {
		idevent = this.value;
		SCHEDULE.pullEventData(idevent);
		$( "#EditEventForm" ).dialog( "open" );
	});

	// Binds click to ajax loaded view button
	$( "#schedule" ).on("click", ".view_event", function() {
		idevent = this.value;
		SCHEDULE.pullEventData(idevent);
		$( "#ViewEventForm" ).dialog( "open" );
	});

	// Binds click to ajax loaded delete button
	$( "#schedule" ).on("click", ".delete_event", function() {
		idevent = this.value;
		$( "#DelEventForm" ).dialog( "open" );
	});

});
