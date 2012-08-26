/**
 * @author Frank
 */


var SUBREQUEST = {

  teamMenu: function() {
   	var _team = this;

		// Ajax call to retreive list of teams assigned to user	
		$.ajax({
	    type: "POST",
			dataType: 'json',
			url: "../data/subrequest_team_data.php",
			success: function(data) {
				_team.showTeams(data);
			},
			error: function() {
				alert('teamMenu: an error occured!');
			}
		});	
  },
	
	showTeams: function( data ) {    
		var tmp = '';
		var menu = $(".SR-myteams-menu");
		menu.html(""); // clear out slection menu if it was previously populated
		menu.append("<option value=''>-Which team is this for?-</options>");
	
		$(data).each(function( key, val ) {
			tmp += "<option value=" + val.TeamID + ">" + val.TeamName + "</options>";
		});
		
		menu.append(tmp);
	},

	showEvents: function( data ) {
		var _event = this;

		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: "../data/subrequest_event_data.php",
			data: {teamID: data}, // Data I'm sending, the selected team
			success: function( data ) {
				_event.buildEventMenu(data);
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert('showEvents: an error occured!');
			}
		});
	},
	
	buildEventMenu: function( data ) {
		var tmp = '';
		var menu = $(".SR-teamevents-menu");
		menu.html(""); // clear out slection menu if it was previously populated
		menu.append("<option value=''>-Which event?-</options>");
		$(data).each(function( key, val ) {
			tmp += "<option value=" + val.EventID + ">" + val.DateInfo + "</option>";
		});
		
		menu.append(tmp);
	},

	loadOpenSubRequests: function() {
		var _subrequest = this;
		
		// AJAX call to retrieve list of events associated with team
		$.ajax({
	    type: "POST",
			dataType: 'json',
			url: "../data/subrequest_data.php",
			success: function(data) {
				_subrequest.buildOpenSRTable(data);
			},
			error: function() {
				alert('loadOpenSubRequests: an error occured!');
			}
		});	
	},
	
	buildOpenSRTable: function(data) {
	    var table = $("#open-subrequests");
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
		
  pullSubRequestData: function ( data ) {
  	var _subrequest = this;
		var data_send = { subRequestID: idsubrequest };

	  $.ajax({
	  	type: "POST",
	    dataType: 'json',
	    url: "../data/single_subreq_data.php",
	    data: data_send, // Data that I'm sending
	    error: function() {
	      alert('Error: Pull Subrequest Data failed');
	   	},
	    success: function( data ) { 
				_subrequest.make_Edit_SubRequest_Form_sticky( data );
	    },
	    cache: false
   	});
	},

  make_Edit_SubRequest_Form_sticky: function( data ) {
  	var _subrequest = this;
		var subReqInfo_array = new Array(); // set up array to store data pulled from database
	  $(data).each(function(key, val) {
			var i = 0;
	  	for (var propertyName in val) {
	    	subReqInfo_array[i] = val[propertyName];
	    	i++;
	    }
	  });	
	  	
		$( '#edit-SR-sel-teams' ).val( subReqInfo_array[0] );
		_subrequest.showEvents( subReqInfo_array[0] );
alert(ubReqInfo_array[0]);
		// Hack to allow time for dynamic selection to me made
		setTimeout(function () {
			$( '#edit-SR-sel-events' ).val( subReqInfo_array[1] );		
		}, 50);
		
		$( '#edit-SR-sel-sex' ).val( subReqInfo_array[2] );
		$( '#edit-SR-sel-exp' ).val( subReqInfo_array[3] );
		$( '#edit-SR-sel-reg' ).val( subReqInfo_array[4] );
  }			
		
		
}

$(document).ready(function() {

	// Load teams associated with manager into select menu
	SUBREQUEST.teamMenu();	

	// Load open sub requests associated with user
	SUBREQUEST.loadOpenSubRequests();

});
