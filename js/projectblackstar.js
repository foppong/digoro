/**
 * @author Frank
 * This is a library of Frank's JavaScript Functions used for the Website.
 */

$.ajaxSetup({"error":function(XMLHttpRequest,textStatus, errorThrown) {   
      alert(textStatus);
      alert(errorThrown);
      alert(XMLHttpRequest.responseText);
  }});

// Global variables
var idplayer;
var idevent;
var idteam;
var idsubrequest;


// Namespace

var USER = {
	signIn: function() {
    	var form_data = $( '#loginform form' ).serialize();
	    $.ajax({
	      	type: "POST",
	      	url: "../fatbar.php",
	      	data: form_data, // Data that I'm sending
	      	error: function() {
	        	$( '.status' ).text( 'Sign in failed. Try again.' ).slideDown( 'slow' );
	     	},
	      	success: function( data ) {   
	        	$( '.status' ).text( data ).slideDown( 'slow' );
	      	},
	      	cache: false
    	});	
	},
	
	selectTeam: function () {
    	var form_data = $( '#SelectTeamForm' ).serialize();
	    $.ajax({
	      	type: "POST",
	      	url: "../manager/manager_home.php",
	      	data: form_data, // Data that I'm sending
	      	error: function(jqXHR, textStatus, errorThrown) {
	        	$( '.status' ).text( 'Selection failed. Try again.' ).slideDown( 'slow' );
	     	},
	      	success: function( data ) {   
	        	$( '.status' ).text( data ).slideDown( 'slow' );
	        	MISCFUNCTIONS.clearForm( '#SelectTeamForm' );
	      	},
	      	complete: function() {
	        	setTimeout(function() {
	          		$( '.status' ).slideUp( 'slow' );
	        	}, 2000);
	      	},
	      	cache: false
    	});			
	}
}


var PLAYER = {

	loadDialog: function() {
		$( "#AddPlayerForm" ).dialog({
			autoOpen: false,
			height: 250,
			width: 550,
			modal: true,
			buttons: {
				"Add Player": function() {
					// Add player to database
					PLAYER.add();					
					$( this ).dialog( "close" );
				},
				Cancel: function() {
					$( this ).dialog( "close" );
	       	MISCFUNCTIONS.clearForm( '#AddPlayerForm form' );
				}
			}
		});
		
		$( "#EditPlayerForm" ).dialog({
			autoOpen: false,
			height: 250,
			width: 275,
			modal: true,
			buttons: {
				"Edit Player": function() {
					// Edit player in database
					PLAYER.edit();					
					$( this ).dialog( "close" );
				},
				Cancel: function() {
					$( this ).dialog( "close" );
	       	MISCFUNCTIONS.clearForm( '#EditPlayerForm form' );
				}
			}			
		});
		
		$( "#DelPlayerForm" ).dialog({
			autoOpen: false,
			height: 150,
			width: 275,
			modal: true,
			buttons: {
				"Delete Player": function() {
					// Delete player from database
					PLAYER.del();					
					$( this ).dialog( "close" );
				},
				Cancel: function() {
					$( this ).dialog( "close" );
				}
			}			
		});		
	},

	// add player information to database from dialog form
  	add: function() { 
    	var form_data = $( '#AddPlayerForm form' ).serialize();
	    $.ajax({
	      	type: "POST",
	      	url: "../manager/add_player.php",
	      	data: form_data, // Data that I'm sending
	      	error: function() {
	        	$( '.status' ).text( 'Update failed. Try again.' ).slideDown( 'slow' );
	     	},
	      	success: function( data ) {   
	        	ROSTER.loadRoster(); //Call to roster.js
	        	$( '.status' ).text( data ).slideDown( 'slow' );
	        	MISCFUNCTIONS.clearForm( '#AddPlayerForm form' );
	      	},
	      	complete: function() {
	        	setTimeout(function() {
	          		$( '.status' ).slideUp( 'slow' );
	        	}, 2000);
	      	},
	      	cache: false
    	});
    },
    
	// edit player information to database from dialog form
  	edit: function() { 
		$( '#EditPlayerForm form' ).append( '<input type="hidden" id="z" name="z" value="' + idplayer + '"/>' );
    	var form_data = $( '#EditPlayerForm form' ).serialize();
	    $.ajax({
	      	type: "POST",
	      	url: "../manager/edit_player.php",
	      	data: form_data, // Data that I'm sending
	      	error: function() {
	        	$( '.status' ).text( 'Edit failed. Try again.' ).slideDown( 'slow' );
	     	},
	      	success: function( data ) { 
	        	ROSTER.loadRoster(); //Call to roster.js
	        	$( '.status' ).text( data ).slideDown( 'slow' );
	        	$( '#EditPlayerForm form #z' ).remove();
	        	MISCFUNCTIONS.clearForm( '#EditPlayerForm form' );   	
	      	},
	      	complete: function() {
	        	setTimeout(function() {
	          		$( '.status' ).slideUp( 'slow' );
	        	}, 2000);
	      	},
	      	cache: false
    	});
    },

	// delete player information to database from dialog form
  	del: function() { 
		$( '#DelPlayerForm form' ).append( '<input type="hidden" id="z" name="z" value="' + idplayer + '"/>' );
    	var form_data = $( '#DelPlayerForm form' ).serialize();
	    $.ajax({
	      	type: "POST",
	      	url: "../manager/delete_player.php",
	      	data: form_data, // Data that I'm sending
	      	error: function() {
	        	$( '.status' ).text( 'Delete failed. Try again.' ).slideDown( 'slow' );
	     	},
	      	success: function( data ) {   
	        	ROSTER.loadRoster(); //Call to roster.js
	        	$( '.status' ).text( data ).slideDown( 'slow' );	    
	      	},
	      	complete: function() {
	        	setTimeout(function() {
	          		$( '.status' ).slideUp( 'slow' );
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
			height: 450,
			width: 400,
			modal: true,
			buttons: {
				"Add New Event": function() {
					// Add event to database
					EVENT.add();					
					$( this ).dialog( "close" );
				},
				Cancel: function() {
					$( this ).dialog( "close" );
	       	MISCFUNCTIONS.clearForm( '#AddEventForm form' );
				}
			}
		});		

		$( "#EditEventForm" ).dialog({
			autoOpen: false,
			height: 450,
			width: 400,
			modal: true,
			buttons: {
				"Edit Event": function() {
					// Edit event in database
					EVENT.edit();					
					$( this ).dialog( "close" );
				},
				Cancel: function() {
					$( this ).dialog( "close" );
	       	MISCFUNCTIONS.clearForm( '#EditEventForm form' );					
				}
			}
		});	
		
		$( "#DelEventForm" ).dialog({
			autoOpen: false,
			height: 150,
			width: 275,
			modal: true,
			buttons: {
				"Delete Event": function() {
					// Delete player from database
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
	        	$( '.status' ).text( 'Update failed. Try again.' ).slideDown( 'slow' );
	     	},
	      	success: function( data ) {   
	        	SCHEDULE.loadSchedule(); //Call to schedule.js
	        	$( '.status' ).text( data ).slideDown( 'slow' );	
	        	MISCFUNCTIONS.clearForm( '#AddEventForm form' );
	      	},
	      	complete: function() {
	        	setTimeout(function() {
	          		$( '.status' ).slideUp( 'slow' );
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
	        	$( '.status' ).text( 'Edit failed. Try again.' ).slideDown( 'slow' );
	     	},
	      	success: function( data ) { 
	        	SCHEDULE.loadSchedule(); //Call to schedule.js
	        	$( '.status' ).text( data ).slideDown( 'slow' );
	        	$( '#EditEventForm form #z' ).remove();	        	
	        	MISCFUNCTIONS.clearForm( '#EditEventForm form' );   	
	      	},
	      	complete: function() {
	        	setTimeout(function() {
	          		$( '.status' ).slideUp( 'slow' );
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
	        	$( '.status' ).text( 'Delete failed. Try again.' ).slideDown( 'slow' );
	     	},
	      	success: function( data ) {   
	        	SCHEDULE.loadSchedule(); //Call to schedule.js
	        	$( '.status' ).text( data ).slideDown( 'slow' );	    
	      	},
	      	complete: function() {
	        	setTimeout(function() {
	          		$( '.status' ).slideUp( 'slow' );
	        	}, 2000);
	      	},
	      	cache: false
    	});
    }
} 



var id = $('input#id').val();
var tname = $('input#tname').val();
var abouttm = $('input#abouttm').val();
  
var TEAM = {

	id: id,
	tname: tname,
	abouttm: abouttm,
 
 	loadDialog: function() { 
		$("#AddTeamForm").dialog({
			autoOpen: false,
			height: 500,
			width: 450,
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

		$( "#EditTeamForm" ).dialog({
			autoOpen: false,
			height: 450,
			width: 375,
			modal: true,
			buttons: {
				"Edit": function() {
					// Edit info in database
					TEAM.edit();					
					$( this ).dialog( "close" );
				},
				Cancel: function() {
					$( this ).dialog( "close" );
					MISCFUNCTIONS.clearForm( '#EditTeamForm form' );
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
			url: "../manager/add_team.php",
			data: form_data, // Data that I'm sending
			error: function() {
				$( '#tmstatus' ).text( 'Edit failed. Try again.' ).slideDown( 'slow' );
			},
			success: function( data ) {
				_team.teamMenu(); // Refresh the team selection menu
				$( '#tmstatus' ).text( data ).slideDown( 'slow' );
				MISCFUNCTIONS.clearForm( '#AddTeamForm' );
			},
			complete: function() {
				setTimeout(function() {
					$( '#tmstatus' ).slideUp( 'slow' );
				}, 2000);
			},
			cache: false
		});
	},

	// edit team information to database from dialog form
  	edit: function() { 
    	var _team = this;
		$( '#EditTeamForm form' ).append( '<input type="hidden" id="z" name="z" value="' + idteam + '"/>' );
		var teamname = $( '#tname' ).val();
    	var form_data = $( '#EditTeamForm form' ).serialize();
	    $.ajax({
	      	type: "POST",
	      	url: "../manager/edit_team.php",
	      	data: form_data, // Data that I'm sending
	      	error: function() {
	        	$( '.status' ).text( 'Edit failed. Try again.' ).slideDown( 'slow' );
	     	},
	      	success: function( data ) { 
	        	ABOUTTM.loadAbout(); // Call to abtm.js
				_team.teamMenu(); // Refresh the team selection menu
	        	$( '.status' ).text( data ).slideDown( 'slow' );
	        	if (teamname != "") { //Update team name on main page
	        		$( '#TeamName' ).html( '<h2>' + teamname + '</h2>' ); };
	        	MISCFUNCTIONS.clearForm( '#EditTeamForm form' );   	
	      	},
	      	complete: function() {
	        	setTimeout(function() {
	          		$( '.status' ).slideUp( 'slow' );
	        	}, 2000);
	      	},
	      	cache: false
    	});
    },

  	teamMenu: function() {
    	var _team = this;

			// Ajax call to retreive list of teams assigned to user	
			$.ajax({
		    type: "POST",
				dataType: 'json',
				url: "../data/team_data.php",
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
	}
}


var LEAGUE = {
	
	showLeagues: function( data ) {
		var _league = this;
		// AJAX call to retreive all leagues based on entered state
		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: "../data/league_data.php",
			data: {state: data},
			success: function( data ) {
				_league.buildLeagueMenu(data);
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert('showLeagues: an error occured!');
				console.log(jqXHR, textStatus, errorThrown);
			}
		});
	},
	
	buildLeagueMenu: function( data ) {
		var tmp = '';
		var menu = $("#league");
		menu.html(""); // clear out slection menu if it was previously populated
		menu.append("<option value=''>-Select League-</options>");
		$(data).each(function( key, val ) {
			tmp += "<option value=" + val.LeagueID + ">" + val.LeagueName + "</option>";
		});
		
		menu.append(tmp);
	}
}


var FINDSUB = {

 	loadDialog: function() { 
		$("#Create-SubRequest-Form").dialog({
			autoOpen: false,
			height: 300,
			width: 350,
			modal: true,
			buttons: {
				"Create": function() {
					FINDSUB.create();
					$( this ).dialog( "close" );
				},
				Cancel: function() {
					$( this ).dialog( "close" );
	       	MISCFUNCTIONS.clearForm( '#Create-SubRequest-Form form' );	
				}
			}
		});
		
		$( "#Edit-SubRequest-Form" ).dialog({
			autoOpen: false,
			height: 450,
			width: 375,
			modal: true,
			buttons: {
				"Edit": function() {
					// Edit info in database
					FINDSUB.edit();					
					$( this ).dialog( "close" );
				},
				Cancel: function() {
					$( this ).dialog( "close" );
					MISCFUNCTIONS.clearForm( '#Edit-SubRequest-Form form' );
				}
			}
		});			

		$( "#Del-SubRequest-Form" ).dialog({
			autoOpen: false,
			height: 150,
			width: 275,
			modal: true,
			buttons: {
				"Delete SubRequest": function() {
					// Delete player from database
					FINDSUB.del();					
					$( this ).dialog( "close" );
				},
				Cancel: function() {
					$( this ).dialog( "close" );
				}
			}			
		});	
	},

	// add subrequest information to database from dialog form
  create: function() { 
   	var form_data = $( '#Create-SubRequest-Form form' ).serialize();
	   $.ajax({
	     	type: "POST",
	     	url: "../manager/create_subrequest.php",
	     	data: form_data, // Data that I'm sending
	     	error: function() {
	       	$( '.status' ).text( 'Update failed. Try again.' ).slideDown( 'slow' );
	    	},
	     	success: function( data ) {   
	       	SUBREQUEST.loadOpenSubRequests(); //Call to subrequest.js
	       	$( '.status' ).text( data ).slideDown( 'slow' );	
	       	MISCFUNCTIONS.clearForm( '#Create-SubRequest-Form form' );
	     	},
	     	complete: function() {
	       	setTimeout(function() {
	        		$( '.status' ).slideUp( 'slow' );
	       	}, 2000);
	     	},
	     	cache: false
   	});
  },
	
	// edit subrequest information to database from dialog form
  edit: function() { 
		$( '#Edit-SubRequest-Form form' ).append( '<input type="hidden" id="z" name="z" value="' + idsubrequest + '"/>' );
    var form_data = $( '#Edit-SubRequest-Form form' ).serialize();
	  $.ajax({
	  	type: "POST",
	    url: "../manager/edit_subrequest.php",
	    data: form_data, // Data that I'm sending
	    error: function() {
	    	$( '.status' ).text( 'Edit failed. Try again.' ).slideDown( 'slow' );
	    },
	    success: function( data ) {   
				SUBREQUEST.loadOpenSubRequests(); //Call to subrequest.js
	    	$( '.status' ).text( data ).slideDown( 'slow' );
	      MISCFUNCTIONS.clearForm( '#Edit-SubRequest-Form form' );    
	    },
	    complete: function() {
	    	setTimeout(function() {
	     		$( '.status' ).slideUp( 'slow' );
	      }, 2000);
	    },
	    cache: false
   	});
   },

	// delete event information in database from dialog form
  del: function() { 
		$( '#Del-SubRequest-Form form' ).append( '<input type="hidden" id="z" name="z" value="' + idsubrequest + '"/>' );
    	var form_data = $( '#Del-SubRequest-Form form' ).serialize();
	  $.ajax({
	    type: "POST",
	    url: "../manager/delete_subrequest.php",
	    data: form_data, // Data that I'm sending
	    error: function() {
	      $( '.status' ).text( 'Delete failed. Try again.' ).slideDown( 'slow' );
	    },
	    success: function( data ) {   
				SUBREQUEST.loadOpenSubRequests(); //Call to subrequest.js
	      $( '.status' ).text( data ).slideDown( 'slow' ); 
	    },
	    complete: function() {
	    	setTimeout(function() {
	      	$( '.status' ).slideUp( 'slow' );
	      }, 2000);
	    },
	    cache: false
   	});
   }
}





// Function to clear out form contents in DOM
var MISCFUNCTIONS = {
	
	clearForm: function( form ) {
  		$(form).children('input, select, textarea').val('');
 		$(form).children('input[type=checkbox]').each(function()
  		{
     		this.checked = false; // for checkboxes
     		// or
     		//$(this).attr('checked', false); // for radio buttons
  		});
	}
}

// jQuery Code for when page is loaded
$(document).ready(function()
{

	// Warning recommendation for those who do not have javascript enabled
	$('#no-script').remove();

	// Sign in user login page
	$( "#signin" ).on("submit", function() {
		USER.signIn();
	});

	// Load teams associated with user into select menu
	TEAM.teamMenu();	

	// Select team from select team form
	$( "#selectTeam" ).on("submit", function() {
		USER.selectTeam();
	})

	// Find Players Tabs
	$('#find-players-tabs').tabs({
		spinner: '<img src="../css/imgs/ajax-loader.gif" />',
		ajaxOptions: {
			error: function( xhr, status, index, anchor ) {
				$(anchor.hash).html(
					"Couldn't load this tab. We'll try to fix this as soon as possible. "
				);				
			}
		},		
		load: function ( event, ui ) {
			switch (ui.index) {
				case 0:
					// Load subrequest dialogs [but blocked from loading initially]
					FINDSUB.loadDialog();
										
					$( "#create-sub-request" ).on("click", function() {
						$( "#Create-SubRequest-Form" ).dialog( "open" );
					});
					
					$( "#open-subrequests" ).on("click", "#edit-subreq", function() {
						idsubrequest = this.value;
						$( "#Edit-SubRequest-Form" ).dialog( "open" );
					});

					$( "#open-subrequests" ).on("click", "#delete-subreq", function() {
						idsubrequest = this.value;
						$( "#Del-SubRequest-Form" ).dialog( "open" );					
					});
					
					break;
				case 1:
					// Load player dialog
					PLAYER.loadDialog();
	
					$( "#add-player" ).on("click", function() {
						$( "#AddPlayerForm" ).dialog( "open" );
					});
					
					// Binds click to ajax loaded edit button
					$( "#roster" ).on("click", ".edit_player", function() {
						idplayer = this.value;
						$( "#EditPlayerForm" ).dialog( "open" );
					});

					// Binds click to ajax loaded delete button
					$( "#roster" ).on("click", ".delete_player", function() {
						idplayer = this.value;
						$( "#DelPlayerForm" ).dialog( "open" );
					});
					break;
				default:		
			}
		}
	});




	// jQuery UI Tabs
	$('#tabmenu').tabs({
		spinner: '<img src="../css/imgs/ajax-loader.gif" />',
		ajaxOptions: {
			error: function( xhr, status, index, anchor ) {
				$(anchor.hash).html(
					"Couldn't load this tab. We'll try to fix this as soon as possible. "
				);				
			}
		},
		load: function ( event, ui ) {
			switch (ui.index) {
				case 0:
					// Load about team dialog
					TEAM.loadDialog();
										
					$( "#about" ).on("click", "#editTeam", function() {
						idteam = this.value;
						$( "#EditTeamForm" ).dialog( "open" );
					});
					break;
				case 1:
					// Load player dialog
					PLAYER.loadDialog();
	
					$( "#add-player" ).on("click", function() {
						$( "#AddPlayerForm" ).dialog( "open" );
					});
					
					// Binds click to ajax loaded edit button
					$( "#roster" ).on("click", ".edit_player", function() {
						idplayer = this.value;
						$( "#EditPlayerForm" ).dialog( "open" );
					});

					// Binds click to ajax loaded delete button
					$( "#roster" ).on("click", ".delete_player", function() {
						idplayer = this.value;
						$( "#DelPlayerForm" ).dialog( "open" );
					});
					break;
				case 2:
					// Load event dialog
					EVENT.loadDialog();
	
					$( "#add-event" ).on("click", function() {
						$( "#AddEventForm" ).dialog( "open" );
					});

					// Binds click to ajax loaded edit button
					$( "#schedule" ).on("click", ".edit_event", function() {
						idevent = this.value;
						$( "#EditEventForm" ).dialog( "open" );
					});

					// Binds click to ajax loaded delete button
					$( "#schedule" ).on("click", ".delete_event", function() {
						idevent = this.value;
						$( "#DelEventForm" ).dialog( "open" );
					});
					break;
				default:		
			}
		}
	});
	

	// Code for triggering add team dialog
	$( "#addTeam" ).on("click", function() {
		// Load add Team dialog
		TEAM.loadDialog();

		$( "#AddTeamForm" ).dialog( "open" );
	});

  	
});


	

