/**
 * @author Frank
 * This is a library of Frank's JavaScript Functions used for the index page.
 */



// jQuery Code for when page is loaded
$(document).ready(function()
{

	// Warning recommendation for those who do not have javascript enabled
	$('#no-script').remove();

	// Hide all error messages:
	$('.errorMessage').hide();

	$('#loginform').validate( {
		rules: {
			email: {
				required: true,				
				email: true
			},
			pass: {
				required: true
			}
		},
		messages: {
			email: {
				required: "Please enter your email address.",
				email: "Please enter a valid email address."
			},
			pass: {
				required: "Please enter your password."
			}
		},
		success: function(label) {
			label.text('Ok!').addClass('valid');
		}
	});

	$('#ForgotPassForm').validate( {
		rules: {
			email: {
				required: true,
				email: true
			},
			email2: {
				required: true,				
				equalTo: "#email"
			}
		},
		messages: {
			email: {
				required: "Please enter your email address.",
				email: "Please enter a valid email address."
			},
			email2: {
				required: "Please confirm your email address.",
				equalTo: "Please make sure your email matches."
			}
		},
		success: function(label) {
			label.text('Ok!').addClass('valid');
		}
	});


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
		var user_day = parseInt(document.forms["SignUpForm"]["DateOfBirth_Day"].value);
		var user_month = parseInt(document.forms["SignUpForm"]["DateOfBirth_Month"].value) - 1;
		var user_year = parseInt(document.forms["SignUpForm"]["DateOfBirth_Year"].value);	
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


	$('#SignUpForm').validate( {
		rules: {
			role: {
				required: true
			},
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
			pass1: {
				required: true,
				minlength: 6
			},
			pass2: {
				required: true,
				equalTo: "#pass1"
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
			role: "Please select your role.",
			first_name: "Please enter your first name.",
			last_name: "Please enter your last name.",
			email: {
				required: "We need your email to regsiter you.",
				email:  "Please enter a valid email address."
			},
			pass1: {
				required: "Please enter a password.",
				minlength: "Please enter a password that is of the correct length."
			},
			pass2: {
				required: "Please confirm your password.",
				equalTo: "Please make sure your passwords match."
			},
			zipcode: {
				required: "Please enter your zipcode.",
				digits: "Please enter a valid zipcode",
				minlength: "Please enter a valid zipcode."
			},
			sex: "Please select your gender."
		},
		success: function(label) {
			label.text('Ok!').addClass('valid');
		}
	});

});
