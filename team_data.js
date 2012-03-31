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
		url: "team_data.php",
		success: function(data) {
			buildTeamMenu(data);
		},
		error: function() {
			alert('an error occured!');
		}
	});	
});

function buildTeamMenu(data) {    
	var tmp = '';
	var menu = $("#y");
	menu.html(""); // clear out slection menu if it was previously populated
	menu.append("<option value=''>-Select Team-</options>");

	$(data).each(function(key, val) {
		tmp += "<option value=" + val.TeamID + ">" + val.TeamName + "</options>";
	});
	
	menu.append(tmp);
}