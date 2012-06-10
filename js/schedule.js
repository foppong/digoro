/**
 * @author Frank
 */


$.ajaxSetup({"error":function(XMLHttpRequest,textStatus, errorThrown) {   
      alert(textStatus);
      alert(errorThrown);
      alert(XMLHttpRequest.responseText);
  }});


var GAME = {

	loadDialog: function() {
		$( "#date" ).datepicker();
		$( "#AddGameForm" ).dialog({
			autoOpen: false,
			height: 400,
			width: 400,
			modal: true,
			buttons: {
				"Add New Game": function() {
					// Add game to database
					GAME.add();						
					$( this ).dialog( "close" );
				},
				Cancel: function() {
					$( this ).dialog( "close" );
				}
			}
		});
		
		$( "#add-game" )
			.button()
			.click(function() {
				$( "#AddGameForm" ).dialog( "open" );
		});		
	},

  	add: function() { 
    	var form_data = $('form').serialize();
	    $.ajax({
	      	type: "POST",
	      	url: "../manager/add_game.php",
	      	data: form_data, // Data that I'm sending
	      	error: function() {
	        	$('#status').text('Update failed. Try again.').slideDown('slow');
	     	},
	      	success: function() {
	        	$('#status').text('Update successful!').slideDown('slow'); // DEBUG NOTE: THis happends even if no changes
	      	},
	      	complete: function() {  // LATER ON I COULD PASS THE DATA BACK AND POSSIBLY USE IT TO BUILD THE STICKY FORM, have to put jsonencode on php end
	        	setTimeout(function() {
	          		$('#status').slideUp('slow');
	        		}, 2000);
	      	},
	      	cache: false
    	});
    }
} 

var SCHEDULE = {
	
	loadSchedule: function() {
		var _schedule = this;

		// AJAX call to retrieve list of games associated with team
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
