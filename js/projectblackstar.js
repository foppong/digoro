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
var idmember;
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


var MEMBER = {

	loadDialog: function() {
		$( "#AddMemberForm" ).dialog({
			autoOpen: false,
			height: 550,
			width: 450,
			modal: true,
			buttons: {
				"Add Member": function() {
					// Add member to database
					MEMBER.add();					
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
			height: 550,
			width: 450,
			modal: true,
			buttons: {
				"Edit Member": function() {
					// Edit member in database
					MEMBER.edit();					
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
			height: 150,
			width: 275,
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
	        	$( '.status' ).text( 'Update failed. Try again.' ).slideDown( 'slow' );
	     	},
	      	success: function( data ) {   
	        	ROSTER.loadRoster(); //Call to roster.js to refresh table
	        	$( '.status' ).text( data ).slideDown( 'slow' );
	        	MISCFUNCTIONS.clearForm( '#AddMemberForm form' );
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
		$( '#EditMemberForm form' ).append( '<input type="hidden" id="z" name="z" value="' + idmember + '"/>' );
    	var form_data = $( '#EditMemberForm form' ).serialize();
	    $.ajax({
	      	type: "POST",
	      	url: "../manager/edit_member.php",
	      	data: form_data, // Data that I'm sending
	      	error: function() {
	        	$( '.status' ).text( 'Edit failed. Try again.' ).slideDown( 'slow' );
	     	},
	      	success: function( data ) { 
	        	ROSTER.loadRoster(); //Call to roster.js
	        	$( '.status' ).text( data ).slideDown( 'slow' );
	        	$( '#EditMemberForm form #z' ).remove();
	        	MISCFUNCTIONS.clearForm( '#EditMemberForm form' );   	
	      	},
	      	complete: function() {
	        	setTimeout(function() {
	          		$( '.status' ).slideUp( 'slow' );
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
			height: 545,
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
			height: 545,
			width: 400,
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
		
		$( "#DelEventForm" ).dialog({
			autoOpen: false,
			height: 150,
			width: 275,
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
	        	$( '.status' ).text( 'Edit Event failed. Try again.' ).slideDown( 'slow' );
	     	},
	      	success: function( data ) { 
	        	SCHEDULE.loadSchedule(); //Call to schedule.js to refresh table
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
			height: 520,
			width: 320,
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
			height: 520,
			width: 320,
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

		$( "#TransferTeamForm" ).dialog({
			autoOpen: false,
			height: 250,
			width: 300,
			modal: true,
			buttons: {
				"Transfer": function() {
					TEAM.transferTM();
					$( this ).dialog( "close" );
				},
				Cancel: function() {
					$( this ).dialog( "close" );
				}
			}
		});
		
		$( "#DeleteTeamForm" ).dialog({
			autoOpen: false,
			height: 250,
			width: 300,
			modal: true,
			buttons: {
				"Delete": function() {
					TEAM.deleteTM();					
					$( this ).dialog( "close" );
				},
				Cancel: function() {
					$( this ).dialog( "close" );
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
				$( '#status' ).text( 'Add failed. Try again.' ).slideDown( 'slow' );
			},
			success: function( data ) {
				_team.teamMenu(); // Refresh the team selection menu
				$( '#status' ).text( data ).slideDown( 'slow' );
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
    var form_data = $( '#EditTeamForm form' ).serialize();
	  $.ajax({
	  	type: "POST",
	    url: "../manager/edit_team.php",
	    data: form_data, // Data that I'm sending
	    error: function() {
	    	$( '.status' ).text( 'Edit failed. Try again.' ).slideDown( 'slow' );
	    },
	    success: function( data ) { 
				//_team.teamMenu(); // Refresh the team selection menu
	      $( '.status' ).text( data ).slideDown( 'slow' );   	
	     },
	    complete: function() {
	    	setTimeout(function() {
	      	$( '.status' ).slideUp( 'slow' );
	    	}, 2000);
	    },
	    cache: false
    });
	},
	
	// Function to delete team
	deleteTM: function() {
		var _team = this;
		var form_data = $( '#DeleteTeamForm form' ).serialize();
		$.ajax({
			type: "Post",
			url: "../manager/delete_team.php",
			data: form_data, // Data that i'm sending
	    error: function() {
	    	$( '.status' ).text( 'Delete failed. Try again.' ).slideDown( 'slow' );
	    },
	    success: function( data ) { 
				_team.teamMenu(); // Refresh the team selection menu
	      $( '.status' ).text( data ).slideDown( 'slow' );   	
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
	},
	
	pullTeamData: function( data ) {
  	var _team = this;
		//var data_send = { idSubReq: idsubrequest };

	  $.ajax({
	  	type: "POST",
	    dataType: 'json',
	    url: "../data/team_info_data.php",
	    //data: data_send, // Data that I'm sending
	    error: function() {
	      alert('Error: Pull Team Data failed');
	   	},
	    success: function( data ) { 
				_team.setTeamInfoPageVars( data );
				ABOUTTM.make_Edit_Team_Form_sticky( data ); // Call to abtm.js
	    },
	    cache: false
   	});
		
	},
	
	setTeamInfoPageVars: function ( data ) {
		$('.teamdisplay').html(""); // clear out any prior info
	  $( 'form #z' ).remove(); // clear out any prior info

		var teamInfo_array = new Array(); // set up array to store data pulled from database
	  $(data).each(function(key, val) {
			var i = 0;
	  	for (var propertyName in val) {
	    	teamInfo_array[i] = val[propertyName];
	    	i++;
	    }
	  });	
		$( '.teamdisplay' ).append( '<h4>' + teamInfo_array[2] + ' Team Info</h4>' );		
		$( '#EditTeamForm form' ).append( '<input type="hidden" id="z" name="z" value="' + teamInfo_array[8] + '"/>' );	
		$( '#TransferTeamForm form' ).append( '<input type="hidden" id="z" name="z" value="' + teamInfo_array[8] + '"/>' );			
		$( '#DeleteTeamForm form' ).append( '<input type="hidden" id="z" name="z" value="' + teamInfo_array[8] + '"/>' );			
		
	}	
	
	
}

/*  NOT BEING USED AT MOMENT
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

*/

var FINDSUB = {

 	loadDialog: function() { 
		$( "#Create-SubRequest-Form" ).dialog({
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
					// Delete member from database
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
//		var form_data = { idSubReq: '5' };

	   $.ajax({
	     	type: "POST",
	     	url: "../manager/create_subrequest.php",
	     	data: form_data, // Data that I'm sending
	     	error: function() {
	       	$( '.status' ).text( 'Update failed. Try again.' ).slideDown( 'slow' );
	    	},
	     	success: function( data ) {   
	       	SUBREQUEST.loadOpenSubRequests(); //Call to subrequest.js to refresh table
	       	$( '.status' ).text( data ).slideDown( 'slow' );	
	       	//MISCFUNCTIONS.clearForm( '#Create-SubRequest-Form form' );
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
				SUBREQUEST.loadOpenSubRequests(); //Call to subrequest.js to refresh table
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
 		$(form).children('input[type=checkbox]').each(function() {
     		this.checked = false; // for checkboxes
     		// or
     		$(this).attr('checked', false); // for radio buttons
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
						SUBREQUEST.pullSubRequestData( idsubrequest );
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
						idmember = this.value;
						$( "#EditPlayerForm" ).dialog( "open" );
					});

					// Binds click to ajax loaded delete button
					$( "#roster" ).on("click", ".delete_player", function() {
						idmember = this.value;
						$( "#DelMemberForm" ).dialog( "open" );
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
					// Load about team dialogs
					TEAM.loadDialog();
					
					// Load Selected Team Data
					TEAM.pullTeamData();
					
					// Opens Edit Team Form dialog
					$( "#edit-team" ).on("click", function() {
						$( "#EditTeamForm" ).dialog( "open" );
					});
					
					// Opens Transfer Team Form dialog
					$( "#transfer-team" ).on("click", function() {
						$( "#TransferTeamForm" ).dialog( "open" );
					});

					// Opens Delete Team Form dialog
					$( "#delete-team" ).on("click", function() {
						$( "#DeleteTeamForm" ).dialog( "open" );
					});

					break;
				case 1:
					// Load member dialogs
					MEMBER.loadDialog();

					// Load Selected Team Data
					TEAM.pullTeamData();
	
					$( "#add-member" ).on("click", function() {
						$( "#AddMemberForm" ).dialog( "open" );
					});
					
					// Binds click to ajax loaded edit button
					$( "#roster" ).on("click", ".edit_member", function() {
						idmember = this.value;
						ROSTER.pullMemberData( idmember );
						$( "#EditMemberForm" ).dialog( "open" );
					});

					// Binds click to ajax loaded delete button
					$( "#roster" ).on("click", ".delete_member", function() {
						idmember = this.value;
						$( "#DelMemberForm" ).dialog( "open" );
					});
					break;
				case 2:
					// Load event dialogs
					EVENT.loadDialog();

					// Load Selected Team Data
					TEAM.pullTeamData();
	
					$( "#add-event" ).on("click", function() {
						$( "#AddEventForm" ).dialog( "open" );
					});

					// Binds click to ajax loaded edit button
					$( "#schedule" ).on("click", ".edit_event", function() {
						idevent = this.value;
						SCHEDULE.pullEventData(idevent);
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


	

