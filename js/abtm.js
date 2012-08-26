/**
 * @author Frank
 */


$.ajaxSetup({"error":function(XMLHttpRequest,textStatus, errorThrown) {   
      alert(textStatus);
      alert(errorThrown);
      alert(XMLHttpRequest.responseText);
  }});

var ABOUTTM = {
/*	
	loadAbout: function() {
		var _abouttm = this;
	
		// AJAX call to retrieve about information associated with team
		$.ajax({
	    type: "POST",
			dataType: 'json',
			url: "../data/about_data.php",
			success: function(data) {
				_abouttm.printAbout(data);
			},
			error: function() {
				alert('an error occured!');
			}
		});		
	},
	
	printAbout: function( data ) {
		var tmp = '';
		var pg = $("#about");
		pg.html(""); // clear out slection menu if it was previously populated
	
		$(data).each(function( key, val ) {
			tmp += "<p>" + val.TeamAbout + "</p><br />" + val.Edit;
		});
		
		pg.append(tmp);
	},
*/	
	pullTeamData: function( data ) {
  	var _abouttm = this;
		//var data_send = { idSubReq: idsubrequest };

	  $.ajax({
	  	type: "POST",
	    dataType: 'json',
	    url: "../data/team_info_data.php",
	    //data: data_send, // Data that I'm sending
	    error: function() {
	      alert('Error: Response failed');
	   	},
	    success: function( data ) { 
				_abouttm.displayinfo( data );
	    },
	    cache: false
   	});
		
	},
	
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
	
	//Load about team information associated with team
	//ABOUTTM.loadAbout();
	
	// Load team data
	//ABOUTTM.pullTeamData();
});