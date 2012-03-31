/**
 * @author Frank
 */


$.ajaxSetup({"error":function(XMLHttpRequest,textStatus, errorThrown) {   
      alert(textStatus);
      alert(errorThrown);
      alert(XMLHttpRequest.responseText);
  }});

$(document).ready(function() {
	$.ajax({
		dataType: 'json',
		url: "about_data.php",
		success: function(data) {
			printAbout(data);
		},
		error: function() {
			alert('an error occured!');
		}
	});	
});



function printAbout(data) {    
	var tmp = '';
	var pg = $("#about");
	pg.html(""); // clear out slection menu if it was previously populated

	$(data).each(function(key, val) {
		tmp += "<p>" + val.TeamAbout + "</p>";
	});
	
	pg.append(tmp);
}