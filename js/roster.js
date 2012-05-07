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
    	
		// Ajax call to retrieve list of users assigned to team	
		$.ajax({
			type: "POST",
			dataType: 'json',
			url: "../data/roster_data.php",
			success: function(data) {
				_roster.buildTable(data);
			},
			error: function() {
				alert('an error occured!');
			}
		});	
  	},

	edit: function() {
    	var form_data = $('form').serialize();
	    $.ajax({
	      	type: "POST",
	      	url: "../manager/edit_player.php",
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
	}
}


$(document).ready(function() {
	
	// Load roster associated with team
	ROSTER.loadRoster();	

});
