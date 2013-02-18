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
  	var _team = this;
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
				_make_Edit_User_Form_sticky( data );
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
	  	
		$( '#edit-team-sel-region' ).val( UserInfo_array[0] );
		$( '#edit-user-fname' ).val( UserInfo_array[1] );
		$( '#edit-user-lname' ).val( UserInfo_array[2] );
		$( '#edit-user-email' ).val( UserInfo_array[3] );
		$( '#edit-user-sel-sex' ).val( UserInfo_array[4] );
		$( '#edit-user-bday' ).val( UserInfo_array[5] );
		
  }

}

$(document).ready(function() {

	// Load  data associated with user
	ACCOUNT.loadSchedule();

});
