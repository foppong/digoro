/**
 * @author Frank
 */



var FINDPLAYER = {

	loadMatched_Players: function() {
	var _findplayers = this;

		var form_data = $( '#Search-Players-Form form' ).serialize();
		// AJAX call
		$.ajax({
			type: "POST",
			dataType: 'json',
			data: data_to_send, // Data that I'm sending
			url: "../data/browse_player_matches_data.php",
			success: function(data) {
				_findplayers.buildMatchesPLTable(data);
			},
			error: function() {
				alert('loadMatched_Players: an error occured!');
			}
		});			
	},
	
	buildMatchesPLTable: function(data) {
		var table = $("#player-search-results");
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
	   $("#player-search-results").prepend('<caption>Player Matches</caption>');    	
	}

}

$(document).ready(function() {


	// Code for triggering account edit
	$( "#searchforplayers" ).on("click", function() {
		// Load subrequests that match user
		FINDPLAYER.loadMatched_Players();
	});


});
