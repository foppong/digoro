/**
 * @author Frank
 * This is a library of Frank's JavaScript Functions used for the Website.
 */

$.ajaxSetup({"error":function(XMLHttpRequest,textStatus, errorThrown) {   
      alert(textStatus);
      alert(errorThrown);
      alert(XMLHttpRequest.responseText);
  }});

// jQuery Code for when page is loaded
$(document).ready(function()
{

	// Warning recommendation for those who do not have javascript enabled
	$('#no-script').remove();

	// Calls jquery UI datepicker selection plugin - used in add_game and edit_game pages
	$("#date").datepicker();

	// Script alternate the background color of a roster table
	$('#roster tbody tr:even').addClass('zebra');

	// Script to highlight roster table when mouse runs over the table
	$('#roster tr').hover(function() {
		$(this).addClass('zebraHover');
	}, function() {
		$(this).removeClass('zebraHover');
	});

	// jQuery UI Tabs
	$('#tabmenu').tabs({
		//spinner: '<img src="/css/imgs/ajax-loader.gif" />',
		ajaxOptions: {
			error: function( xhr, status, index, anchor ) {
				$(anchor.hash).html(
					"Couldn't load this tab. We'll try to fix this as soon as possible. "
				);
			}
		}
	});
	
	/** Load tab menu contents within same page
	$('#tabmenu').tabs({ 
	    load: function(event, ui) { 
			$('a', ui.panel).live('click', function() {
				$(ui.panel).load(this.href);
			    return false;
			});
		 }
	}); 
	*/

	// Ajax call to retreive list of teams assigned to user	
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

function showLeagues(data)
{ 
	$.ajax({
		type: 'POST',
		dataType: 'json',
		url: "league_data.php",
		data: {state: data},
		success: function(data) {
			buildLeagueMenu(data);
		},
		error: function() {
			alert('an error occured!');
		}
	});
}
	
function buildLeagueMenu(data) {    
	var tmp = '';
	var menu = $("#league");
	menu.html(""); // clear out slection menu if it was previously populated
	menu.append("<option value=''>-Select League-</options>");
	$(data).each(function(key, val) {
		tmp += "<option value=" + val.LeagueID + ">" + val.LeagueName + "</option>";
	});
	
	menu.append(tmp);
}	


	

