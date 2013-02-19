/**
 * @author Frank
 */


$.ajaxSetup({"error":function(XMLHttpRequest,textStatus, errorThrown) {   
      alert(textStatus);
      alert(errorThrown);
      alert(XMLHttpRequest.responseText);
  }});


var ACCOUNT = {
	
  pullUserData: function ( data ) {
  	var _account = this;
		var data_to_send = { actionvar: 'pullUserData' };
  			
		// Ajax call to pull user data	
	  $.ajax({
	  	type: "POST",
	    dataType: 'json',
	    url: "../data/user_data.php",
	    data: data_to_send, // Data that I'm sending
	    error: function() {
	      alert('Error: Response failed');
	   	},
	    success: function( data ) { 
				_account.make_Edit_User_Form_sticky( data );
	    },
	    cache: false
   	});
	},

	edit: function() {
	  var form_data = $( '#EditAccountForm form' ).serialize();
		// Ajax call to update user data	
	 	$.ajax({
	  	type: "POST",
	  	dataType: 'json',
	   	url: "../core/account.php",
	   	data: data_to_send, // Data that I'm sending
	   	error: function() {
				$( '#status' ).append( '<div class="alert alert-error">Update failed</div>' ).slideDown( 'slow' );
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

  make_Edit_User_Form_sticky: function( data ) {

		var UserInfo_array = new Array(); // set up array to store data pulled from database
	  $(data).each(function(key, val) {
			var i = 0;
	  	for (var propertyName in val) {
	    	UserInfo_array[i] = val[propertyName];
	    	i++;
	    }
	  });	
	  	
		$( '#edit-user-fname' ).val( UserInfo_array[0] );
		$( '#edit-user-lname' ).val( UserInfo_array[1] );
		$( '#edit-user-city' ).val( UserInfo_array[2] );
		$( '#edit-user-state' ).val( UserInfo_array[3] );
		$( '#edit-user-zip' ).val( UserInfo_array[4] );
		$( '#edit-user-sel-sex' ).val( UserInfo_array[5] );
		$( '#edit-user-phone' ).val( UserInfo_array[6] );						
		$( '#bdayY' ).val( UserInfo_array[7] );	
		$( '#bdayM' ).val( UserInfo_array[8] );	
		$( '#bdayD' ).val( UserInfo_array[9] );	
		
  }

}

$(document).ready(function() {

	// Load data associated with user
	ACCOUNT.pullUserData();

	// Code for triggering account edit
	$( "#editaccount" ).on("click", function() {
		// Edit account
		ACCOUNT.edit();
	});

});
