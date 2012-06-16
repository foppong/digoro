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

var PLAYER = {

	loadDialog: function() {
		$( "#AddPlayerForm" ).dialog({
			autoOpen: false,
			height: 250,
			width: 275,
			modal: true,
			buttons: {
				"Add Player": function() {
					// Add player to database
					PLAYER.add();					
					$( this ).dialog( "close" );
				},
				Cancel: function() {
					$( this ).dialog( "close" );
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
	        	$( '#status' ).text( 'Update failed. Try again.' ).slideDown( 'slow' );
	     	},
	      	success: function( data ) {   
	        	$( '#status' ).text( data ).slideDown( 'slow' );
	        	ROSTER.loadRoster(); //Call to roster.js
	        	MISCFUNCTIONS.clearForm( '#AddPlayerForm form' );
	      	},
	      	complete: function() {
	        	setTimeout(function() {
	          		$( '#status' ).slideUp( 'slow' );
	        	}, 2500);
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
	        	$( '#status' ).text( 'Edit failed. Try again.' ).slideDown( 'slow' );
	     	},
	      	success: function( data ) { 
	        	$( '#status' ).text( data ).slideDown( 'slow' );
	        	ROSTER.loadRoster(); //Call to roster.js
	        	MISCFUNCTIONS.clearForm( '#EditPlayerForm form' );   	
	      	},
	      	complete: function() {
	        	setTimeout(function() {
	          		$( '#status' ).slideUp( 'slow' );
	        	}, 2500);
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
	        	$( '#status' ).text( 'Delete failed. Try again.' ).slideDown( 'slow' );
	     	},
	      	success: function( data ) {   
	        	$( '#status' ).text( data ).slideDown( 'slow' );
	        	ROSTER.loadRoster(); //Call to roster.js	    
	      	},
	      	complete: function() {
	        	setTimeout(function() {
	          		$( '#status' ).slideUp( 'slow' );
	        	}, 2500);
	      	},
	      	cache: false
    	});
    }

} 


var GAME = {

	loadDialog: function() {
		$("#date").datepicker();
		$( "#AddGameForm" ).dialog({
			autoOpen: false,
			height: 400,
			width: 400,
			modal: true,
			buttons: {
				"Add New Game": function() {
					// Add game to database
					GAME.add();					
					$( this ).dialog( "destroy" ).remove();
				},
				Cancel: function() {
					$( this ).dialog( "close" );
				}
			}
		});		
	},

	// add game information to database from dialog form
  	add: function() { 
    	var form_data = $( 'form' ).serialize();
	    $.ajax({
	      	type: "POST",
	      	url: "../manager/add_game.php",
	      	data: form_data, // Data that I'm sending
	      	error: function() {
	        	$( '#status' ).text( 'Update failed. Try again.' ).slideDown( 'slow' );
	     	},
	      	success: function() {   
	        	$( '#status' ).text( 'Update successful!' ).slideDown( 'slow' ); // DEBUG NOTE: THis happends even if no changes  	
	      	},
	      	complete: function() {
	        	setTimeout(function() {
	          		$( '#status' ).slideUp( 'slow' );
	        	}, 1000);
	        	setTimeout(function() {
	        		$( '#tabmenu' ).tabs( 'load', 2 ); // Reloads the tab so that new change can be displayed instanly
	        	}, 1005);
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
  
  	loadTeams: function() {
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
				alert('an error occured!');
			}
		});	
  	},
  
  	update: function() { 
    	var form_data = $('form').serialize();
	    $.ajax({
	      	type: "POST",
	      	url: "../manager/edit_team.php",
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
	
	showLeagues: function(data)	{
		var _league = this;
		// AJAX call to retreive all leagues based on entered state
		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: "../data/league_data.php",
			data: {state: data},
			success: function(data) {
				_league.buildLeagueMenu(data);
			},
			error: function() {
				alert('an error occured!');
			}
		});
	},
	
	buildLeagueMenu: function(data) {
		var tmp = '';
		var menu = $("#league");
		menu.html(""); // clear out slection menu if it was previously populated
		menu.append("<option value=''>-Select League-</options>");
		$(data).each(function(key, val) {
			tmp += "<option value=" + val.LeagueID + ">" + val.LeagueName + "</option>";
		});
		
		menu.append(tmp);
	}
}


var MISCFUNCTIONS = {
	
	clearForm: function( form ) {
  		$(form).children('input, select, textarea').val('');
 		$(form).children('input[type=radio], input[type=checkbox]').each(function()
  		{
     		this.checked = false;
     		// or
     		$(this).attr('checked', false);
  		});
	}
}

// jQuery Code for when page is loaded
$(document).ready(function()
{

	// Warning recommendation for those who do not have javascript enabled
	$('#no-script').remove();

	// Load teams associated with user into select menu
	TEAM.loadTeams();

	// jQuery UI Tabs
	$('#tabmenu').tabs({
		//spinner: '<img src="../css/imgs/ajax-loader.gif" />', [NEED A SMALLER SPINNER]
		ajaxOptions: {
			error: function( xhr, status, index, anchor ) {
				$(anchor.hash).html(
					"Couldn't load this tab. We'll try to fix this as soon as possible. "
				);				
			}
		},
		load: function ( event, ui ) {
			switch (ui.index) {
				case 1:
					// Load player dialog
					PLAYER.loadDialog();
	
					$( "#add-player" ).on("click", function() {
						$( "#AddPlayerForm" ).dialog( "open" );
					});
					
					// Binds click to ajax loaded edit button
					$( "#roster" ).on("click", ".edit-player", function() {
						idplayer = this.value;
						$( "#EditPlayerForm" ).dialog( "open" );
					});

					// Binds click to ajax loaded delete button
					$( "#roster" ).on("click", ".delete-player", function() {
						idplayer = this.value;
						$( "#DelPlayerForm" ).dialog( "open" );
					});
					break;
				case 2:
					// Load game dialog
					GAME.loadDialog();
	
					$( "#add-game" ).on("click", function() {
						$( "#AddGameForm" ).dialog( "open" );
					});
					break;
				default:		
			}
		}
	});

	// Update team edits in database
	$("#update").on("click", function() {
		TEAM.update();
	});

  	
});


	

