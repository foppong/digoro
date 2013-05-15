/**
 * @author Frank
 */



var MEMBER = {

	loadDialog: function() {

		$( "#AddMemberForm" ).dialog({
			autoOpen: false,
			height: 'auto',
			width: 'auto',
			modal: true,
			buttons: {
				"Add Member": function() {
					MEMBER.add();	// Add member to database
					$( this ).dialog( "close" );
	       	MISCFUNCTIONS.clearForm( '#AddMemberForm form' );
				},
				"Save and Add Another": function() {
					MEMBER.add();
	       	MISCFUNCTIONS.clearForm( '#AddMemberForm form' );
				},
				Cancel: function() {
					$( this ).dialog( "close" );
	       	MISCFUNCTIONS.clearForm( '#AddMemberForm form' ); 
				}
			}
		});
		
		$( "#EditMemberForm" ).dialog({
			autoOpen: false,
			height: 'auto',
			width: 'auto',
			modal: true,
			buttons: {
				"Edit Member": function() {
					// Edit member in database
					MEMBER.edit();
					MISCFUNCTIONS.jDialogHack( '#edit-member-fname' )
					$( this ).dialog( "close" );

				},
				Cancel: function() {
					$( this ).dialog( "close" );
	       	MISCFUNCTIONS.clearForm( '#EditMemberForm form' );
				}
			}			
		});
		
		$( "#DelMemberForm" ).dialog({
			autoOpen: false,
			height: 'auto',
			width: 'auto',
			modal: true,
			buttons: {
				"Delete Member": function() {
					// Delete member from database
					MEMBER.del();					
					$( this ).dialog( "close" );
				},
				Cancel: function() {
					$( this ).dialog( "close" );
				}
			}			
		});		
	},

	// add member information to database from dialog form
  	add: function() { 
    	var form_data = $( '#AddMemberForm form' ).serialize();
	    $.ajax({
	      	type: "POST",
	      	url: "../manager/add_member.php",
	      	data: form_data, // Data that I'm sending
	      	error: function() {
	        	$( '#status' ).append( '<div class="alert alert-error">Add failed</div>' ).slideDown( 'slow' );
	     	},
	      	success: function( data ) {   
	        	ROSTER.loadRoster(); //Call to roster.js to refresh table
	        	$( '#status' ).append( data ).slideDown( 'slow' );
	        	MISCFUNCTIONS.clearForm( '#AddMemberForm form' );
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
    
	// edit player information to database from dialog form
  	edit: function() { 
		$( '#EditMemberForm form' ).append( '<input type="hidden" id="z" name="z" value="' + idmember + '"/>' );
    	var form_data = $( '#EditMemberForm form' ).serialize();
	    $.ajax({
	      	type: "POST",
	      	url: "../manager/edit_member.php",
	      	data: form_data, // Data that I'm sending
	      	error: function() {
	        	$( '#status' ).append( '<div class="alert alert-error">Edit failed</div>' ).slideDown( 'slow' );
	     	},
	      	success: function( data ) { 
	        	ROSTER.loadRoster(); //Call to roster.js
	        	$( '#status' ).append( data ).slideDown( 'slow' );
	        	$( '#EditMemberForm form #z' ).remove();
	        	MISCFUNCTIONS.clearForm( '#EditMemberForm form' );   	
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

	// delete member information to database from dialog form
  	del: function() { 
		$( '#DelMemberForm form' ).append( '<input type="hidden" id="z" name="z" value="' + idmember + '"/>' );
    	var form_data = $( '#DelMemberForm form' ).serialize();
	    $.ajax({
	      	type: "POST",
	      	url: "../manager/delete_member.php",
	      	data: form_data, // Data that I'm sending
	      	error: function() {
	        	$( '#status' ).append( '<div class="alert alert-error">Delete failed</div>' ).slideDown( 'slow' );
	     	},
	      	success: function( data ) {   
	        	ROSTER.loadRoster(); //Call to roster.js
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
	//TEAM.displayTeamInfo();

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