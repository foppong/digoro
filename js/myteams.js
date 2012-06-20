/**
 * @author Frank
 */


$.ajaxSetup({"error":function(XMLHttpRequest,textStatus, errorThrown) {   
      alert(textStatus);
      alert(errorThrown);
      alert(XMLHttpRequest.responseText);
  }});


var MYTEAMS = {
	
	loadDialog: function() { 
		$( "#EditTeamForm" ).dialog({
			autoOpen: false,
			height: 300,
			width: 300,
			modal: true,
			buttons: {
				"Edit": function() {
					// Edit info in database
					MYTEAMS.edit();					
					$( this ).dialog( "close" );
				},
				Cancel: function() {
					$( this ).dialog( "close" );
				}
			}
		});	
		
		$( "#DelTeamForm" ).dialog({
			autoOpen: false,
			height: 300,
			width: 300,
			modal: true,
			buttons: {
				"Delete": function() {
					// Edit info in database
					MYTEAMS.del();					
					$( this ).dialog( "close" );
				},
				Cancel: function() {
					$( this ).dialog( "close" );
				}
			}
		});	
 	},

	// edit team information to database from dialog form
  	edit: function() { 
    	var _myteam = this;
		$( '#EditTeamForm form' ).append( '<input type="hidden" id="z" name="z" value="' + idteam + '"/>' );

    	var form_data = $( '#EditTeamForm form' ).serialize();
	    $.ajax({
	      	type: "POST",
	      	url: "../manager/myteams_team.php",
	      	data: form_data, // Data that I'm sending
	      	error: function() {
	        	$( '.status' ).text( 'Edit failed. Try again.' ).slideDown( 'slow' );
	     	},
	      	success: function( data ) { 
				_team.teamMenu(); // Refresh the team selection menu
	        	MYTEAMS.loadMyTeams(); //Refresh table of teams
	        	$( '.status' ).text( data ).slideDown( 'slow' );
	        	MISCFUNCTIONS.clearForm( '#EditTeamForm form' );   	
	      	},
	      	complete: function() {
	        	setTimeout(function() {
	          		$( '.status' ).slideUp( 'slow' );
	        	}, 1500);
	      	},
	      	cache: false
    	});
    },

	// delete team information in database from dialog form
  	del: function() { 
		$( '#DelTeamForm form' ).append( '<input type="hidden" id="z" name="z" value="' + idgame + '"/>' );
    	var form_data = $( '#DelTeamForm form' ).serialize();
	    $.ajax({
	      	type: "POST",
	      	url: "../manager/delete_team.php",
	      	data: form_data, // Data that I'm sending
	      	error: function() {
	        	$( '.status' ).text( 'Delete failed. Try again.' ).slideDown( 'slow' );
	     	},
	      	success: function( data ) {   
	        	MYTEAMS.loadMyTeams(); //Refresh table of teams
	        	$( '.status' ).text( data ).slideDown( 'slow' );	    
	      	},
	      	complete: function() {
	        	setTimeout(function() {
	          		$( '.status' ).slideUp( 'slow' );
	        	}, 1500);
	      	},
	      	cache: false
    	});
    },

	loadMyTeams: function() {
		var _myteam = this;
		
		// Ajax call to retreive list of teams assigned to user	
		$.ajax({
	      	type: "POST",
			dataType: 'json',
			url: "../data/myteams_data.php",
			success: function(data) {
				_myteam.buildTeamTable(data);
			},
			error: function() {
				alert('an error occured!');
			}
		});	
  	},

	buildTeamTable: function(data) {
	    var table = $("#MyTeams");
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
	
  	teamMenu: function() {
    	var _myteams = this;

		// Ajax call to retreive list of teams assigned to user	
		$.ajax({
	      	type: "POST",
			dataType: 'json',
			url: "../data/team_data.php",
			success: function(data) {
				_team.buildTeamMenu(data);
			},
			error: function() {
				alert('an error occured!');
			}
		});	
  	},
	
	buildTeamMenu: function(data) {    
		var tmp = '';
		var menu = $("#y");
		menu.html(""); // clear out slection menu if it was previously populated
		menu.append("<option value=''>-Select Team-</options>");
	
		$(data).each(function(key, val) {
			tmp += "<option value=" + val.TeamID + ">" + val.TeamName + "</options>";
		});
		
		menu.append(tmp);
	}
}

$(document).ready(function() {
	
	// Load teams associated with user into table
	MYTEAMS.loadMyTeams();

	// Load MYTEAMS dialogs
	MYTEAMS.loadDialog();
										
	$( "#MyTeams" ).on("click", "#edit_team", function() {
		idteam = this.value;
		$( "#EditTeamForm" ).dialog( "open" );
	});

	$( "#MyTeams" ).on("click", "#delete_team", function() {
		idteam = this.value;
		$( "#DelTeamForm" ).dialog( "open" );
	});

});