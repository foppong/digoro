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
	// Hide all error messages:
	$('.errorMessage').hide();

	$('#FirstTeamForm').validate( {
		rules: {
			tname: {
				required: true			
			},
			sport: {
				required: true
			},
			city: {
				required: true
			},
			state: {
				required: true
			},
			league: {
				required: true
			},
			abouttm: {
				required: true
			}
		},
		messages: {
			tname: "Please enter the team name.",
			sport: "Please select your sport.",
			city: "Please enter the team home city.",
			state: "Please enter the team home state.",
			league: "Please select the team league.",
			abouttm: "Please enter some info on your team"
		},
		success: function(label) {
			label.text('Ok!').addClass('valid');
		}
	});

/*	
	$('#AddTeamForm').validate( {
		rules: {
			tname: {
				required: true			
			},
			sport: {
				required: true
			},
			city: {
				required: true
			},
			state: {
				required: true
			},
			league: {
				required: true
			},
			abouttm: {
				required: true
			}
		},
		messages: {
			tname: "Please enter the team name.",
			sport: "Please select your sport.",
			city: "Please enter the team home city.",
			state: "Please enter the team home state.",
			league: "Please select the team league.",
			abouttm: "Please enter some info on your team"
		},
		success: function(label) {
			label.text('Ok!').addClass('valid');
		}
	});
*/
	$('#AddPlayerForm').validate( {
		rules: {
			first_name: {
				required: true			
			},
			last_name: {
				required: true
			},
			email: {
				required: true,
				email: true
			}
		},
		messages: {
			first_name: "Please enter the players first name.",
			last_name: "Please enter the players last name",
			email: {
				required: "Please enter the players email address.",
				email: "Please enter a valid email address."
			}
		},
		success: function(label) {
			label.text('Ok!').addClass('valid');
		}
	});
/*	
	// Add Game to Schedule Form validation using jquery
	$('#AddGameForm').validate( {
		rules: {
			date: {
				required: true
			},
			time: {
				required: true
			}
		},
		messages: {
			date: "Please enter the date of the game.",
			time: "Please enter the time of the game."
		},
		success: function(label) {
			label.text('Ok!').addClass('valid');
		}
	});	
*/	

	// Add method to jquery validator plugin. This method checks that all dates are selected
	$.validator.addMethod("fulldate", function() {
		var y = $('#bdayM').val();
		var w = $('#bdayD').val();
		var z = $('#bdayY').val();
		if (y=="" || w=="" || z=="")
		{
			return false;
		}
		else
		{
			return true;
		}
	},"Please enter your birthdate");

	// Add method to jquery validator plugin.  This method checks that user is 18 and older
	$.validator.addMethod("agecheck", function() {
		/* minimum age to enter site */
		var min_age = 18;
			
		/* collect user entered birth information */
		var user_day = parseInt(document.forms["EditAccountForm"]["DateOfBirth_Day"].value);
		var user_month = parseInt(document.forms["EditAccountForm"]["DateOfBirth_Month"].value) - 1;
		var user_year = parseInt(document.forms["EditAccountForm"]["DateOfBirth_Year"].value);	
		var user_date = new Date((user_year + min_age), user_month, user_day);
			
		/* setup current date */
		var today = new Date();
		
		/* compare dates */
		if ((today.getTime() - user_date.getTime()) < 0)
		{
			return false;
		}
		else
		{
			return true;
		}
	}, "You must be 18 and older to register on this site!");

	// Account Edit Form Validation function using jQuery
	$('#EditAccountForm').validate( {
		rules: {
			first_name: {
				required: true
			},
			last_name: {
				required: true
			},
			email: {
				required: true,
				email: true
			},
			zipcode: {
				required: true,
				digits: true,
				minlength: 5
			},
			sex: {
				required: true
			},
			DateOfBirth_Year: {
				fulldate: true,
				agecheck: true
			}
		},
		messages: {
			first_name: "Please enter your first name.",
			last_name: "Please enter your last name.",
			email: {
				required: "Please enter your email address.",
				email:  "Please enter a valid email address."
			},
			zipcode: {
				required: "Please enter your zipcode.",
				digits: "Please enter a valid zipcode",
				minlength: "Please enter a valid zipcode."
			},
			sex: "Please enter your gender."
		},
		success: function(label) {
			label.text('Ok!').addClass('valid');
		}
	});

	// Edit Player Form validation using jquery
	$('#EditPlayerForm').validate( {
		rules: {
			position: {
				required: true
			},
			jersey_num: {
				required: true,				
				digits: true
			}
		},
		messages: {
			position: "Please enter the position of the player.",
			jersey_num: {
				required: "Please confirm your jersey number.",
				digits: "Please enter a numerical value."
			}
		},
		success: function(label) {
			label.text('Ok!').addClass('valid');
		}
	});	

	// Edit Player Form validation using jquery
	$('#EditGameForm').validate( {
		rules: {
			date: {
				required: true
			},
			time: {
				required: true
			}
		},
		messages: {
			date: "Please enter the date of the game.",
			time: "Please enter the time of the game."
		},
		success: function(label) {
			label.text('Ok!').addClass('valid');
		}
	});	

	// Edit Team Form validation using jquery
	$('#EditTeamForm').validate( {
		rules: {
			tname: {
				required: true
			}, 
			abouttm: {
				required: true
			}
		},
		messages: {
			tname: "Please enter the new team name.",
			abouttm: "Please enter some info about your team."
		},
		success: function(label) {
			label.text('Ok!').addClass('valid');
		}
	});	

	// Transfer Team Form validation using jquery
	$('#TransfTeamForm').validate( {
		rules: {
			transfer: {
				required: true
			}, 
			email: {
				email: true
			}
		},
		messages: {
			email: {
				required: "Please enter your email address.",
				email:  "Please enter a valid email address."
			}
		},
		success: function(label) {
			label.text('Ok!').addClass('valid');
		}
	});	

	// Change Password Form validation using jquery
	$('#ChgPassForm').validate( {
		rules: {
			email: {
				required: true,
				email: true
			},
			oldpass: {
				required: true
			},
			pass1: {
				required: true,
				minlength: 6
			},
			pass2: {
				required: true,
				equalTo: "#pass1"
			},
		},
		messages: {
			email: {
				required: "Please enter your email address.",
				email:  "Please enter a valid email address."
			},
			oldpass: "Please enter your old password.",
			pass1: {
				required: "Please enter a password.",
				minlength: "Please enter a password that is of the correct length."
			},
			pass2: {
				required: "Please confirm your password.",
				equalTo: "Please make sure your passwords match."
			}
		},		
		success: function(label) {
			label.text('Ok!').addClass('valid');
		}
	});
	
}); // End of document ready.


