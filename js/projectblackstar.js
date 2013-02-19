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
var idsubresponse;
var idprofile;
var SelectedTeamName;
var SelectedTeamID;


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
	}

}


// Function to clear out form contents in DOM
var MISCFUNCTIONS = {
	
	clearForm: function( form ) {
		$( 'form #z' ).remove();  	
  	$(form).children('input, select, textarea').val('');
 		$(form).children('input[type=checkbox]').each(function() {
     		this.checked = false; // for checkboxes
     		// or
     		$(this).attr('checked', false); // for radio buttons
  	});
	},
	
	jDialogHack: function( form ) {

		$('[id]').each(function(){
		  var ids = $('[id="'+this.id+'"]');
		  if(ids.length>1 && ids[0]==this)
		    console.warn('Multiple IDs #'+this.id);
		});


/*		var formcount = $( form ).get();
		alert(formcount.length);
		newformcount = jQuery.unique(formcount);
		alert(formcount.length);
*/	}
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

});


	

