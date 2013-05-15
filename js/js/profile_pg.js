/**
 * @author Frank
 */


var MYPROFILE = {
	
	loadDialog: function () {
		var _myprofile = this;
		$("#AddProfileForm").dialog({
			autoOpen: false,
			height: 'auto',
			width: 'auto',
			modal: true,
			buttons: {
				"Add New Profile": function(){
					_myprofile.add();
					_myprofile.loadProfiles(); // Refresh table	
					$( this ).dialog( "close" );
				},
				Cancel: function() {
					$( this ).dialog( "close" );
					MISCFUNCTIONS.clearForm( '#AddProfileForm form' );
				}
			}
		});

		$( "#EditProfileForm" ).dialog({
			autoOpen: false,
			height: 'auto',
			width: 'auto',
			modal: true,
			buttons: {
				"Edit Profile": function() {
					_myprofile.edit();		// Edit info in database
					_myprofile.loadProfiles(); // Refresh table	
					$( this ).dialog( "close" );
				},
				Cancel: function() {
					$( this ).dialog( "close" );
					MISCFUNCTIONS.clearForm( '#EditProfileForm form' );
				}
			}
		});

		$( "#DelProfileForm" ).dialog({
			autoOpen: false,
			height: 'auto',
			width: 'auto',
			modal: true,
			buttons: {
				"Delete": function() {
					_myprofile.del();
					_myprofile.loadProfiles(); // Refresh table						
					$( this ).dialog( "close" );
				},
				Cancel: function() {
					$( this ).dialog( "close" );
				}
			}
		});	
	},
	
	// add profile to database
  add: function() {
  	var _myprofile = this;
  	var form_data = $( '#AddProfileForm form' ).serialize();
	  $.ajax({
	  	type: "POST",
	    url: "../core/add_profile.php",
	    data: form_data, // Data that I'm sending
	    error: function() {
	    	$( '#status' ).append( '<div class="alert alert-error">Add failed</div>' ).slideDown( 'slow' );
	    },
	    success: function( data ) {   
	    	_myprofile.loadProfiles(); //Call to refresh profiles table
	      $( '#status' ).append( data ).slideDown( 'slow' );
	      MISCFUNCTIONS.clearForm( '#AddProfileForm form' );
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

	// edit profile information to database from dialog form
  edit: function() {
  	var _myprofile = this;
		$( '#EditProfileForm form' ).append( '<input type="hidden" id="z" name="z" value="' + idprofile + '"/>' );
    var form_data = $( '#EditProfileForm form' ).serialize();
	  $.ajax({
	  	type: "POST",
	    url: "../core/edit_profile.php",
	    data: form_data, // Data that I'm sending
	    error: function() {
	    	$( '#status' ).append( '<div class="alert alert-error">Edit failed</div>.' ).slideDown( 'slow' );
	    },
	    success: function( data ) {   
	    	_myprofile.loadProfiles(); //Call to refresh profiles table
	    	$( '#status' ).append( data ).slideDown( 'slow' );	
	      MISCFUNCTIONS.clearForm( '#EditProfileForm form' );    
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

	// delete profile in database from dialog form
  del: function() {
  	var _myprofile = this;
		$( '#DelProfileForm form' ).append( '<input type="hidden" id="z" name="z" value="' + idprofile + '"/>' );
    var form_data = $( '#DelProfileForm form' ).serialize();
	  $.ajax({
	    type: "POST",
	    url: "../core/delete_profile.php",
	    data: form_data, // Data that I'm sending
	    error: function() {
	      $( '#status' ).append( '<div class="alert alert-error">Delete failed</div>' ).slideDown( 'slow' );
	    },
	    success: function( data ) {   
	    	_myprofile.loadProfiles(); //Call to refresh profiles table
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


	loadProfiles: function( data ) {
		var _myprofile = this;
		var data_to_send = { actionvar: 'pullProfiles' };

		// Ajax call to retrieve list of profiles assigned to user	
		$.ajax({
			type: "POST",
			dataType: 'json',
			url: "../data/profile_data.php",
			data: data_to_send,
			success: function(data) {
				_myprofile.buildProfilesTable(data);
			},
			error: function() {
				alert('loadProfiles: error occured!');
			}
		});	
  },
	
	buildProfilesTable: function( data ) {
	    var table = $("#sport-profiles");
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

			$("#sport-profiles").prepend('<caption>My Sports Profiles</caption>');
	},
	
  pullProfileData: function ( data ) {
  	var _myprofile = this;
		var data_send = { actionvar: 'pullSingleProfile', profileID: idprofile };

	  $.ajax({
	  	type: "POST",
	    dataType: 'json',
	    url: "../data/profile_data.php",
	    data: data_send, // Data that I'm sending
	    error: function() {
	      alert('pullProfileData Error');
	   	},
	    success: function( data ) { 
				_myprofile.make_Edit_Profile_Form_sticky( data );
	    },
	    cache: false
   	});
	},

  make_Edit_Profile_Form_sticky: function( data ) {
  	var _myprofile = this;
		var profileInfo_array = new Array(); // set up array to store data pulled from database
	  $(data).each(function(key, val) {
			var i = 0;
	  	for (var propertyName in val) {
	    	profileInfo_array[i] = val[propertyName];
	    	i++;
	    }
	  });	
	  	
		$( '#edit-profile-sel-sport' ).val( profileInfo_array[0] );
		$( '#edit-profile-sel-sex' ).val( profileInfo_array[1] );
		$( '#edit-profile-sel-region' ).val( profileInfo_array[2] );
		$( '#edit-profile-sel-exp' ).val( profileInfo_array[3] );
		$( '#edit-profile-ppos' ).val( profileInfo_array[4] );		
		$( '#edit-profile-spos' ).val( profileInfo_array[5] );		
		$( '#edit-profile-comments' ).val( profileInfo_array[6] );			
  }
	
	
}



$(document).ready(function() {

	// Load profiles associated with user
	MYPROFILE.loadProfiles();

	// Load about team dialogs
	MYPROFILE.loadDialog();

	$( "#add-profile" ).on("click", function() {
		$( "#AddProfileForm" ).dialog( "open" );
	});

	// Opens Edit Profile Form dialog
	$( "#sport-profiles" ).on("click", "#edit-profile", function() {
		idprofile = this.value;
		MYPROFILE.pullProfileData( idprofile );		
		$( "#EditProfileForm" ).dialog( "open" );
	});

	// Opens Delete Profile Form dialog
	$( "#sport-profiles" ).on("click", "#delete-profile", function() {	
		idprofile = this.value;
		$( "#DelProfileForm" ).dialog( "open" );
	});

});
