/**
 * @author Frank
 */



var FINDSUB = {

 	loadDialog: function() { 
		$( "#Create-SubRequest-Form" ).dialog({
			autoOpen: false,
			height: 'auto',
			width: 'auto',
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
			height: 'auto',
			width: 'auto',
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
			height: 'auto',
			width: 'auto',
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
		
		$( "#Respond-SubResponse-Form" ).dialog({
			autoOpen: false,
			height: 'auto',
			width: 'auto',
			modal: true,
			buttons: {
				"Confirm": function() {
					FINDSUB.confirm();					
					$( this ).dialog( "close" );
				},
				"Decline": function() {
					FINDSUB.decline();					
					$( this ).dialog( "close" );
				},
				Cancel: function() {
					$( this ).dialog( "close" );
					MISCFUNCTIONS.clearForm( '#Respond-SubResponse-Form form' );
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
	       	$( '#status' ).append( '<div class="alert alert-error">Addition failed</div>' ).slideDown( 'slow' );
	    	},
	     	success: function( data ) {   
	       	SUBREQUEST.loadOpenSubRequests(); //Call to subrequest.js to refresh table
	        $( '#status' ).append( data ).slideDown( 'slow' );	
	       	//MISCFUNCTIONS.clearForm( '#Create-SubRequest-Form form' );
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
	
	// edit subrequest information to database from dialog form
  edit: function() { 
		$( '#Edit-SubRequest-Form form' ).append( '<input type="hidden" id="z" name="z" value="' + idsubrequest + '"/>' );
    var form_data = $( '#Edit-SubRequest-Form form' ).serialize();
	  $.ajax({
	  	type: "POST",
	    url: "../manager/edit_subrequest.php",
	    data: form_data, // Data that I'm sending
	    error: function() {
	    	$( '#status' ).append( '<div class="alert alert-error">Edit failed</div>' ).slideDown( 'slow' );
	    },
	    success: function( data ) {   
				SUBREQUEST.loadOpenSubRequests(); //Call to subrequest.js to refresh table
	    	$( '#status' ).append( data ).slideDown( 'slow' );	
	      MISCFUNCTIONS.clearForm( '#Edit-SubRequest-Form form' );    
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

	// delete event information in database from dialog form
  del: function() { 
		$( '#Del-SubRequest-Form form' ).append( '<input type="hidden" id="z" name="z" value="' + idsubrequest + '"/>' );
    var form_data = $( '#Del-SubRequest-Form form' ).serialize();
	  $.ajax({
	    type: "POST",
	    url: "../manager/delete_subrequest.php",
	    data: form_data, // Data that I'm sending
	    error: function() {
	      $( '#status' ).append( '<div class="alert alert-error">Delete failed</div>' ).slideDown( 'slow' );
	    },
	    success: function( data ) {   
				SUBREQUEST.loadOpenSubRequests(); //Call to subrequest.js to refresh table
				SUBREQUEST.loadSubReqResponses(); //Call to subrequest.js to refresh table
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
   
	// confirm subresponse
  confirm: function() { 
		$( '#Respond-SubResponse-Form form' ).append( '<input type="hidden" id="z" name="z" value="' + idsubresponse + '"/>' );
  	$( '#SR-response' ).val( 'confirm' );
    
    var form_data = $( '#Respond-SubResponse-Form form' ).serialize();
	  $.ajax({
	  	type: "POST",
	    url: "../manager/respond_subresponse.php",
	    data: form_data, // Data that I'm sending
	    error: function() {
	    	$( '#status' ).append( '<div class="alert alert-error">Response failed</div>' ).slideDown( 'slow' );
	    },
	    success: function( data ) {   
				SUBREQUEST.loadSubReqResponses(); //Call to subrequest.js to refresh table
	      $( '#status' ).append( data ).slideDown( 'slow' );		      
	      MISCFUNCTIONS.clearForm( '#Respond-SubResponse-Form form' );    
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
   
	// decline subresponse
  decline: function() { 
		$( '#Respond-SubResponse-Form form' ).append( '<input type="hidden" id="z" name="z" value="' + idsubresponse + '"/>' );
  	$( '#SR-response' ).val( 'decline' );
    
    var form_data = $( '#Respond-SubResponse-Form form' ).serialize();
	  $.ajax({
	  	type: "POST",
	    url: "../manager/respond_subresponse.php",
	    data: form_data, // Data that I'm sending
	    error: function() {
	    	$( '#status' ).append( '<div class="alert alert-error">Decline failed</div>' ).slideDown( 'slow' );
	    },
	    success: function( data ) {   
				SUBREQUEST.loadSubReqResponses(); //Call to subrequest.js to refresh table
	      $( '#status' ).append( data ).slideDown( 'slow' );	
	      MISCFUNCTIONS.clearForm( '#Respond-SubResponse-Form form' );    
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
		load: function( event, ui ) {
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

					$( "#open-subrequests" ).on("click", "#delete-subreq", function() {
						idsubrequest = this.value;
						$( "#Del-SubRequest-Form" ).dialog( "open" );					
					});

					$( "#subrequests-responses" ).on("click", "#respond-subres", function() {
						idsubresponse = this.value;
						SUBREQUEST.pullSubResponseData(idsubresponse);						
						$( "#Respond-SubResponse-Form" ).dialog( "open" );					
					});


					break;
				case 1:
	
					break;
				default:		
			}
		}
	});

});
