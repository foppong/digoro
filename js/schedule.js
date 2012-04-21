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
		url: "../data/schedule_data.php",
		success: function(data) {
			buildTable(data);
		},
		error: function() {
			alert('an error occured!');
		}
	});	
});


function buildTable(data) {    
    var table = $("#schedule");
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
