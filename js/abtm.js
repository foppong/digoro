/**
 * @author Frank
 */


$.ajaxSetup({"error":function(XMLHttpRequest,textStatus, errorThrown) {   
      alert(textStatus);
      alert(errorThrown);
      alert(XMLHttpRequest.responseText);
  }});


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

		$( "#EditTeamForm" ).dialog({
			autoOpen: false,
			height: 'auto',
			width: 'auto',
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
			height: 'auto',
			width: 'auto',
			modal: true,
			buttons: {
				"Transfer": function() {
					TEAM.transferTM();
					$( this ).dialog( "close" );
				},
				Cancel: function() {
					$( this ).dialog( "close" );
					MISCFUNCTIONS.clearForm( '#TransferTeamForm form' );
				}
			}
		});
		
		$( "#DeleteTeamForm" ).dialog({
			autoOpen: false,
			height: 'auto',
			width: 'auto',
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

	// edit team information to database from dialog form
  edit: function() { 
  	var _team = this;
		$( '#EditTeamForm form' ).append( '<input type="hidden" id="z" name="z" value="' + SelectedTeamID + '"/>' );	
    var form_data = $( '#EditTeamForm form' ).serialize();
	  $.ajax({
	  	type: "POST",
	    url: "../manager/edit_team.php",
	    data: form_data, // Data that I'm sending
	    error: function() {
	    	$( '#status' ).append( '<div class="alert alert-error">Edit failed</div>' ).slideDown( 'slow' );
	    },
	    success: function( data ) { 
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
	},
	
	// Function to transfer team
	transferTM: function() {
		var _team = this;
		$( '#TransferTeamForm form' ).append( '<input type="hidden" id="z" name="z" value="' + SelectedTeamID + '"/>' );	
		var form_data = $( '#TransferTeamForm form' ).serialize();
		$.ajax({
			type: "Post",
			url: "../manager/transfer_team.php",
			data: form_data, // Data that i'm sending
	    error: function() {
	    	$( '#status' ).append( '<div class="alert alert-error">Transfer Team failed</div>' ).slideDown( 'slow' );
	    },
	    success: function( data ) { 
				_team.teamMenu(); // Refresh the team selection menu
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
		
	},	
	
	// Function to delete team
	deleteTM: function() {
		var _team = this;
		$( '#DeleteTeamForm form' ).append( '<input type="hidden" id="z" name="z" value="' + SelectedTeamID + '"/>' );	
		var form_data = $( '#DeleteTeamForm form' ).serialize();
		$.ajax({
			type: "Post",
			url: "../manager/delete_team.php",
			data: form_data, // Data that i'm sending
	    error: function() {
	    	$( '#status' ).append( '<div class="alert alert-error">Delete Team failed</div>' ).slideDown( 'slow' );
	    },
	    success: function( data ) { 
				_team.teamMenu(); // Refresh the team selection menu
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
		
	},
	

	
	pullTeamData: function( data ) {
  	var _team = this;
		var data_to_send = { actionvar: 'pullTeamData' };

	  $.ajax({
	  	type: "POST",
	    dataType: 'json',
	    url: "../data/team_data.php",
	    data: data_to_send, 
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
	
	setTeamInfoPageVars: function( data ) {
		$('.teamdisplay').html(""); // clear out any prior info

		var teamInfo_array = new Array(); // set up array to store data pulled from database
	  $(data).each(function(key, val) {
			var i = 0;
	  	for (var propertyName in val) {
	    	teamInfo_array[i] = val[propertyName];
	    	i++;
	    }
	  });
	  
	  SelectedTeamName = teamInfo_array[2];  // Assign team name to global variable
	  SelectedTeamID = teamInfo_array[8]; // Assign team id to global variable
		$( '.teamdisplay' ).append( SelectedTeamName ); // Set team name for Team Info Tab	  	
		
	},
	
	
	setTeamName: function() {
		$( '.teamdisplay' ).append( SelectedTeamName );
	},


	displayTeamInfo: function() {
  	var _team = this;
		var data_to_send = { actionvar: 'pullDisplayTeamData' };

	  $.ajax({
	  	type: "POST",
	    dataType: 'json',
	    url: "../data/team_data.php",
	    data: data_to_send, 
	    error: function() {
	      alert('Error: displayTeamInfo failed');
	   	},
	    success: function( data ) { 
				_team.buildTeamDisplay( data );
	    },
	    cache: false
   	});		
	},
	
	buildTeamDisplay: function( data ) {
		$('#teamInfo').html(""); // clear out any prior info
	  $( 'form #z' ).remove(); // clear out any prior info

		var teamInfo_array = new Array(); // set up array to store data pulled from database
	  $(data).each(function(key, val) {
			var i = 0;
	  	for (var propertyName in val) {
	    	teamInfo_array[i] = val[propertyName];
	    	i++;
	    }
	  });
	  
		$( '#teamInfo' )
			.append( '<p>Sport: ' + teamInfo_array[3] + '</p>' )
			.append( '<p>Team Email: ' + teamInfo_array[4] + '</p>')
			.append( '<p>Team Gender: ' + teamInfo_array[5] + '</p>')
			.append( '<p>Level of Play: ' + teamInfo_array[6] + '</p>')
			.append( '<p>Manager Info:</p>')
			.append( '<p>Name: ' + teamInfo_array[0] + '</p>')
			.append( '<p>Email: ' + teamInfo_array[1] + '</p>')
			.append( '<p>Location: ' + teamInfo_array[7] + '</p>')
			.append( '<p>Other info: ' + teamInfo_array[8] + '</p>')	  
	},
	
	loadTransferList: function() {
  	var _team = this;
		var data_to_send = { actionvar: 'pullTransferListData' };
  			
		// Ajax call to retrieve list of users on team	
		$.ajax({
			type: "POST",
			dataType: 'json',
			url: "../data/roster_data.php",
			data: data_to_send,
			success: function( data ) {
				_team.buildTransferSelect( data );
			},
			error: function() {
				alert('load transfer list: error occured!');
			}
		});			
	},
	
	buildTransferSelect: function( data ) {    
		var tmp = '';
		var menu = $( "#transferlist" );
		menu.html(""); // clear out slection menu if it was previously populated
		menu.append("<option value=''>-Select Member-</options>");
	
		$(data).each(function(key, val) {
			tmp += "<option value=" + val.MemberUserID + ">" + val.MemberName + "</options>";
		});
		
		menu.append(tmp);	
	}
}

var ABOUTTM = {
	
	displayinfo: function ( data ) {

		var teamInfo_array = new Array(); // set up array to store data pulled from database
	  $(data).each(function(key, val) {
			var i = 0;
	  	for (var propertyName in val) {
	    	teamInfo_array[i] = val[propertyName];
	    	i++;
	    }
	  });	
		
		$( '#page-header' ).append( '<h3>' + teamInfo_array[2] + ' Team Info</h3>' );
	},
	
  make_Edit_Team_Form_sticky: function( data ) {

		var teamInfo_array = new Array(); // set up array to store data pulled from database
	  $(data).each(function(key, val) {
			var i = 0;
	  	for (var propertyName in val) {
	    	teamInfo_array[i] = val[propertyName];
	    	i++;
	    }
	  });	
  	
		$( '#edit-team-sel-sport' ).val( teamInfo_array[0] );
		$( '#edit-team-name' ).val( teamInfo_array[2] );
		$( '#edit-team-sel-sex' ).val( teamInfo_array[6] );
		$( '#edit-team-sel-region' ).val( teamInfo_array[5] );
		$( '#edit-team-sel-level-play' ).val( teamInfo_array[4] );
		$( '#edit-team-email' ).val( teamInfo_array[7] );
		$( '#edit-team-abouttm' ).val( teamInfo_array[3] );
		
  }


	
}


$(document).ready(function() {
	
	//Load team data
	//TEAM.pullTeamData();

	// Load about team dialogs
	TEAM.loadDialog();
					
	// Load Selected Team Data
	TEAM.pullTeamData();
	TEAM.displayTeamInfo();

					
	// Opens Edit Team Form dialog
	$( "#edit-team" ).on("click", function() {
		$( "#EditTeamForm" ).dialog( "open" );
	});
					
	// Opens Transfer Team Form dialog
	$( "#transfer-team" ).on("click", function() {
		TEAM.loadTransferList();
			$( "#TransferTeamForm" ).dialog( "open" );
	});

	// Opens Delete Team Form dialog
	$( "#delete-team" ).on("click", function() {
		$( "#DeleteTeamForm" ).dialog( "open" );
	});
	
});