/**
 * @author Frank
 * This is a library of Frank's JavaScript Functions used for the Website.
 */

$.ajaxSetup({"error":function(XMLHttpRequest,textStatus, errorThrown) {   
      alert(textStatus);
      alert(errorThrown);
      alert(XMLHttpRequest.responseText);
  }});

var id = $('input#id').val();
var tname = $('input#tname').val();
var abouttm = $('input#abouttm').val();
  
var TEAM = {

	id: id,
	tname: tname,
	abouttm: abouttm,
  
  	loadTeams: function() {
    	var _team = this;

		// Ajax call to retreive list of teams assigned to user	
		$.ajax({
	      	type: "POST",
			dataType: 'json',
			url: "../data/team_data.php",
			success: function(data) {
				_team.buildTeamMenu(data);
			},
			error: function() {
				alert('an error occured!');
			}
		});	
  	},
  
  	update: function() { 
    	var form_data = $('form').serialize();
	    $.ajax({
	      	type: "POST",
	      	url: "../manager/edit_team.php",
	      	data: form_data, // Data that I'm sending
	      	error: function() {
	        	$('#status').text('Update failed. Try again.').slideDown('slow');
	     	},
	      	success: function() {
	        	$('#status').text('Update successful!').slideDown('slow'); // DEBUG NOTE: THis happends even if no changes
	      	},
	      	complete: function() {  // LATER ON I COULD PASS THE DATA BACK AND POSSIBLY USE IT TO BUILD THE STICKY FORM, have to put jsonencode on php end
	        	setTimeout(function() {
	          		$('#status').slideUp('slow');
	        		}, 2000);
	      	},
	      	cache: false
    	});
  	},
  	
	buildTeamMenu: function(data) {    
		var tmp = '';
		var menu = $("#y");
		menu.html(""); // clear out slection menu if it was previously populated
		menu.append("<option value=''>-Select Team-</options>");
	
		$(data).each(function(key, val) {
			tmp += "<option value=" + val.TeamID + ">" + val.TeamName + "</options>";
		});
		
		menu.append(tmp);
	}

}

var LEAGUE = {
	
	showLeagues: function(data)	{
		var _league = this;
		// AJAX call to retreive all leagues based on entered state
		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: "../data/league_data.php",
			data: {state: data},
			success: function(data) {
				_league.buildLeagueMenu(data);
			},
			error: function() {
				alert('an error occured!');
			}
		});
	},
	
	buildLeagueMenu: function(data) {
		var tmp = '';
		var menu = $("#league");
		menu.html(""); // clear out slection menu if it was previously populated
		menu.append("<option value=''>-Select League-</options>");
		$(data).each(function(key, val) {
			tmp += "<option value=" + val.LeagueID + ">" + val.LeagueName + "</option>";
		});
		
		menu.append(tmp);
	}
}

/*
var TABLE = {};

TABLE.formwork = function(table){
  var $tables = $(table);
  
  $tables.each(function () {
    var _table = $(this);
    _table.find('thead tr').append($('<th class="edit">&nbsp;</th>'));
    _table.find('tbody tr').append($('<td class="edit"><input type="button" value="Edit"/></td>'))
  });
  
  $tables.find('.edit :button').on('click', function() {
    TABLE.editable(this);
  });
  
}

TABLE.editable = function(button) {
  var $button = $(button);
  var $row = $button.parents('tbody tr');
  var $cells = $row.children('td').not('.edit');
  
  if($row.data('flag')) { // in edit mode, move back to table
    // cell methods
    $cells.each(function () {
      var _cell = $(this);
      _cell.html(_cell.find('input').val());
    })
    
    $row.data('flag',false);
    $button.val('Edit');
  } 
  else { // in table mode, move to edit mode 
    // cell methods
    $cells.each(function() {
      var _cell = $(this);
      _cell.data('text', _cell.html()).html('');
      
      var $input = $('<input type="text" />')
        .val(_cell.data('text'))
        .width(_cell.width() - 16);
        
      _cell.append($input);
    })
    
    $row.data('flag', true);
    $button.val('Save');
  }
}
*/


// jQuery Code for when page is loaded
$(document).ready(function()
{

	// Warning recommendation for those who do not have javascript enabled
	$('#no-script').remove();

	// Calls jquery UI datepicker selection plugin - used in add_game and edit_game pages
	$("#date").datepicker();

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

	// Load teams associated with user into select menu
	TEAM.loadTeams();

	// Update team edits in database
	$("#update").on("click", function() {
		TEAM.update();
	});
	
	// Create editable table of my teams
  	//TABLE.formwork('#myTeams');
  	
});


	

