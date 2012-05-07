/**
 * @author Frank
 */


$.ajaxSetup({"error":function(XMLHttpRequest,textStatus, errorThrown) {   
      alert(textStatus);
      alert(errorThrown);
      alert(XMLHttpRequest.responseText);
  }});

var ABOUTTM = {
	
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
	
	printAbout: function(data) {
		var tmp = '';
		var pg = $("#about");
		pg.html(""); // clear out slection menu if it was previously populated
	
		$(data).each(function(key, val) {
			tmp += "<p>" + val.TeamAbout + "</p>";
		});
		
		pg.append(tmp);
	}
}


$(document).ready(function() {
	
	//Load about team information associated with team
	ABOUTTM.loadAbout();

});