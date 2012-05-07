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

	var initialData = [
	    { firstName: "Danny", lastName: "LaRusso", email: "foppong@gmail.com", gender: "m", position: "forward", phone: "(555) 121-2121" }
	];
	 
	var PlayersModel = function(players) {
	    var self = this;
	    self.players = ko.observableArray(ko.utils.arrayMap(players, function(player) {
	        return { firstName: player.firstName, lastName: player.lastName, email: player.email, 
	        	gender: player.gender, position: player.position, phone: player.phone };
	    }));
	 
	    self.addPlayer= function() {
	        self.players.push({
	            firstName: "",
	            lastName: "",
	            email: "",
	            gender: "",
	            position: "",
	            phone: ""
	        });
	    };
	 
	    self.removePlayer = function(player) {
	        self.players.remove(player);
	    };
	 
	    self.save = function() {
	        self.lastSavedJson(JSON.stringify(ko.toJS(self.players), null, 2));
	    };
	 
	    self.lastSavedJson = ko.observable("")
	};
	 
	ko.applyBindings(new PlayersModel(initialData));

});
	

