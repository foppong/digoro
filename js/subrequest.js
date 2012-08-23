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
		// AJAX call to retreive all leagues based on entered state
		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: "../data/subrequest_event_data.php",
			data: {teamID: data}, // Data I'm sending, the selected team'
			success: function( data ) {
				_event.buildEventMenu(data);
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert('showEvents: an error occured!');
				console.log(jqXHR, textStatus, errorThrown);
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
	}
		
		
		
		
}

$(document).ready(function() {

	// Load teams associated with manager into select menu
	SUBREQUEST.teamMenu();	

	// Load open sub requests associated with user
	SUBREQUEST.loadOpenSubRequests();

});
