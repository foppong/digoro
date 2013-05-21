/**
 * @author Frank
 */


var TEAM = {
 
 	loadDialog: function() { 
		$("#AddTeamForm").dialog({
			autoOpen: false,
			height: 'auto',
			width: 'auto',
			modal: true,
			buttons: {
				"Add Team": function(){
					TEAM.add();
					$( this ).dialog( "close" );
				},
				Cancel: function() {
					$( this ).dialog( "close" );
					MISCFUNCTIONS.clearForm( '#AddTeamForm form' );
				}
			}
		});
 	},		
		

	// add team to database
	add: function() {
		var _team = this;
		var form_data = $( '#AddTeamForm form' ).serialize();
		$.ajax({
			type: "POST",
			url: "../core/add_team.php",
			data: form_data, // Data that I'm sending
			error: function() {
				$( '#status' ).append( '<div class="alert alert-error">Add failed</div>' ).slideDown( 'slow' );
			},
			success: function( data ) {
				_team.teamMenu(); // Refresh the team selection menu
				$( '#status' ).append( data ).slideDown( 'slow' );
				MISCFUNCTIONS.clearForm( '#AddTeamForm' );
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
	
  teamMenu: function() {
  	var _team = this;
		var data_to_send = { actionvar: 'teammenu' }
		
		// Ajax call to retreive list of teams assigned to user	
		$.ajax({
			type: "POST",
			dataType: 'json',
			url: "../data/team_data.php",
			data: data_to_send,
			success: function(data) {
				_team.buildTeamMenu(data);
			},
			error: function() {
				alert('teamMenu: an error occured!');
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
	},
	
	setTeamName: function() {
		$( '.teamdisplay' ).append( SelectedTeamName );
	},

	selectTeam: function () {
    	var form_data = $( '#SelectTeamForm' ).serialize();
	    $.ajax({
	      	type: "POST",
	      	url: "../manager/manager_home.php",
	      	data: form_data, // Data that I'm sending
	      	error: function(jqXHR, textStatus, errorThrown) {
	        	$( '#status' ).append( '<div class="alert alert-error">Selection failed</div>' ).slideDown( 'slow' );
	     	},
	      	success: function( data ) {   
	        	MISCFUNCTIONS.clearForm( '#SelectTeamForm' );
	      	},
	      	complete: function() {
	        	setTimeout(function() {
	          		$( '#status' ).slideUp( 'slow' );
	        	}, 2000);
	      	},
	      	cache: false
    	});			
	}

	
}


$(document).ready(function() {

	// Load teams associated with user into select menu
	TEAM.teamMenu();	

	// Load about team dialogs
	TEAM.loadDialog();

	// Load Selected Team Data
	TEAMDATA.pullTeamData(); // Global function call from projectlbackstar.js
	
	// Select team from select team form
	$( "#selectTeam" ).on("submit", function() {
		TEAM.selectTeam();
	})
	
	// Code for triggering add team dialog
	$( "#addTeam" ).on("click", function() {
		// Load add Team dialog
		TEAM.loadDialog();

		$( "#AddTeamForm" ).dialog( "open" );
	});


});