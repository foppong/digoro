/**
 * @author Frank
 */


$.ajaxSetup({"error":function(XMLHttpRequest,textStatus, errorThrown) {   
      alert(textStatus);
      alert(errorThrown);
      alert(XMLHttpRequest.responseText);
  }});

var ROSTER = {
	
	loadRoster: function() {
  	var _roster= this;
		var data_to_send = { actionvar: 'pullRosterData' };
    	
		// Ajax call to retrieve list of users assigned to team	
		$.ajax({
			type: "POST",
			dataType: 'json',
			url: "../data/roster_data.php",
			data: data_to_send,
			success: function(data) {
				_roster.buildTable(data);
			},
			error: function() {
				alert('load roster: error occured!');
			}
		});	
  },

	edit: function() {
    	var form_data = $('form').serialize();
	    $.ajax({
	      	type: "POST",
	      	url: "../manager/edit_member.php",
	      	data: form_data, // Data that I'm sending
	      	error: function() {
	        	alert("Error");
	     	},
	      	success: function() {
				window.location.replace("#");
	      	},
	      	cache: false
    	});		
	},

	buildTable: function(data) {
	    var table = $("#roster");
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
	
  pullMemberData: function ( data ) {
  	var _roster = this;
		var data_send = { memberID: idmember };

	  $.ajax({
	  	type: "POST",
	    dataType: 'json',
	    url: "../data/single_member_data.php",
	    data: data_send, // Data that I'm sending
	    error: function() {
	      alert('Error: Pull Member Data failed');
	   	},
	    success: function( data ) { 
				_roster.make_Edit_Member_Form_sticky( data );
	    },
	    cache: false
   	});
	},

  make_Edit_Member_Form_sticky: function( data ) {

		var eventInfo_array = new Array(); // set up array to store data pulled from database
	  $(data).each(function(key, val) {
			var i = 0;
	  	for (var propertyName in val) {
	    	eventInfo_array[i] = val[propertyName];
	    	i++;
	    }
	  });	
	  	
		$( '#edit-member-fname' ).val( eventInfo_array[0] );
		$( '#edit-member-lname' ).val( eventInfo_array[1] );
		$( '#edit-member-sel-sex' ).val( eventInfo_array[2] );
		$( '#edit-member-ppos' ).val( eventInfo_array[3] );
		$( '#edit-member-spos' ).val( eventInfo_array[4] );
		$( '#edit-member-jernum' ).val( eventInfo_array[5] );
  }	
}
	
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



$(document).ready(function() {
	
	// Load roster associated with team
	ROSTER.loadRoster();	

	// Load member dialogs
	MEMBER.loadDialog();

	// Load Selected Team Data
	TEAMDATA.pullTeamData(); // Global function call from projectlbackstar.js

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

});
