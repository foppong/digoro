/**
 * @author Frank
 */

var MATCHES = {
	
		loadMatched_SubRequests: function() {
		var _matches = this;

		// AJAX call to retrieve list of events associated with team
		$.ajax({
	    type: "POST",
			dataType: 'json',
			url: "../data/subrequest_matches_data.php",
			success: function(data) {
				_matches.buildMatchesSRTable(data);
			},
			error: function() {
				alert('loadMatched_SubRequests: an error occured!');
			}
		});			
	},
	
	buildMatchesSRTable: function(data) {
	    var table = $("#subrequests-matches");
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
		}  
}


var RESPONDSR = {

 	loadDialog: function() { 
		$("#Respond-SubRequest-Form").dialog({
			autoOpen: false,
			height: 'auto',
			width: 'auto',
			modal: true,
			buttons: {
				"I can play!": function() {
					RESPONDSR.respond();
					$( this ).dialog( "close" );
					MISCFUNCTIONS.clearForm( '#Respond-SubRequest-Form form' );
				},
				"Exit": function() {
					$( this ).dialog( "close" );
				}
			}
		});	
	},

	// function for user to respond to subrequest
  respond: function() { 
		$( '#Respond-SubRequest-Form form' ).append( '<input type="hidden" id="z" name="z" value="' + idsubrequest + '"/>' );
   	var form_data = $( '#Respond-SubRequest-Form form' ).serialize();
	   $.ajax({
	     	type: "POST",
	     	url: "../core/create_subresponse.php",
	     	data: form_data, // Data that I'm sending
	     	error: function() {
	       	$( '.status' ).text( 'Response failed. Try again.' ).slideDown( 'slow' );
	    	},
	     	success: function( data ) { 
	       	$( '.status' ).text( data ).slideDown( 'slow' );	
	       	MISCFUNCTIONS.clearForm( '#Respond-SubRequest-Form form' );
	     	},
	     	complete: function() {
	       	setTimeout(function() {
	        		$( '.status' ).slideUp( 'slow' );
	       	}, 2000);
	     	},
	     	cache: false
   	});
  },
  
  pullSRData: function ( data ) {
  	var _respondsr = this;
		var data_send = { idSubReq: idsubrequest };

	  $.ajax({
	  	type: "POST",
	    dataType: 'json',
	    url: "../data/single_subreq_data.php",
	    data: data_send, // Data that I'm sending
	    error: function() {
	      alert('Error: Response failed');
	   	},
	    success: function( data ) { 
				_respondsr.buildSRResponseForm( data );
	    },
	    cache: false
   	});

  },
  
  buildSRResponseForm: function( data ) {
  	
		$('#dynamicSRinfo').html(""); // clear out any prior info
		var teamInfo_array = new Array(); // set up array to store data pulled from database
	  $(data).each(function(key, val) {
			var i = 0;
	  	for (var propertyName in val) {
	    	teamInfo_array[i] = val[propertyName];
	    	i++;
	    }
	  });	
		
		$( '#Respond-SubRequest-Form form #dynamicSRinfo' )
			.append( '<p>Team Name: ' + teamInfo_array[0] + '</p>' )
			.append( '<p>Level of Play: ' + teamInfo_array[1] + '</p>')
			.append( '<p>Venue Name: ' + teamInfo_array[2] + '</p>')
			.append( '<p>Venue Address: ' + teamInfo_array[3] + '</p>')
		
  }
  
}


var MISFUNCTIONS = {

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



$(document).ready(function() {

	// Load subrequests that match user
	MATCHES.loadMatched_SubRequests();
	
	// Triggers respond subrequest dialog box
	RESPONDSR.loadDialog();
	
	$( "#subrequests-matches" ).on("click", "#view-subreq", function() {
		idsubrequest = this.value;
		RESPONDSR.pullSRData(idsubrequest);
		$( "#Respond-SubRequest-Form" ).dialog( "open" );
	});


});
