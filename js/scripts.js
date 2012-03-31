/**
 * @author Frank
 */

function stylesheetToggle() {
	if ($('body').width() > 900) {
		$('<link rel="stylesheet" href="css/testwide.css" type="text/css" />').appendTo('head');
	} else {
		$('link[href="css/testwide.css"]').remove();
	}
}

function positionLightboxImage() {
  var top = ($(window).height() - $('#lightbox').height()) / 2;
  var left = ($(window).width() - $('#lightbox').width()) / 2;
  $('#lightbox')
    .css({
      'top': top + $(document).scrollTop(),
      'left': left
    })
    .fadeIn();
}

function removeLightbox() {
  $('#overlay, #lightbox')
    .fadeOut('slow', function() {
      $(this).remove();
      $('body').css('overflow-y', 'auto'); // show scrollbars!
    });
}

$(document).ready(function() {

	$('p').resizable();

	// Warning recommendation for those who do not have javascript enabled
	$('#no-script').remove();
	
	// Script alternate the background color of a roster table
	$('#roster tbody tr:even').addClass('zebra');

	// Script to highlight roster table when mouse runs over the table
	$('#roster tr').hover(function() {
		$(this).addClass('zebraHover');
	}, function() {
		$(this).removeClass('zebraHover');
	});

	/**	
	// Script to allow user to select multiple rows and toggle selection
	$('#roster tbody tr').click(function() {
		$(this).toggleClass('zebraHover');
	});
	*/

	/** Modifying content example
	//$('p').html('<strong>Warning!</strong> Text has been replaced ... ');
	//$('h2').text('<strong>Warning!</strong> Title elements can be ...');
	*/

	$('<input type="button" value="toggle" id="toggleButton">').insertAfter('#disclaimer');
	$('#toggleButton').click(function() {
		$('#disclaimer').toggle('slow');
	});

	
	/**Moves elements side to side as mouse hovers
	$('#navigation li').hover(function() {
		$(this).animate({paddingLeft: '+=15px'}, 200);
	}, function() {
		$(this).animate({paddingLeft: '-=15px'}, 200);
	});
	*/
	
	$('p.test:eq(3)').toggle(function() {
		$(this).animate({'height' : '+=50px'}, 1000, 'linear');
	}, function() {
		$(this).animate({'height' : '-=50px'}, 1000, 'swing');
	});
	
	$('p.test:last').animate({'height' : '+=100px'}, 2000, 'easeOutBounce');
	$('p.test:last').animate({'height' : '-=100px'}, 2000, 'easeInOutExpo');
	$('p.test:last').animate({'height' : 'hide'}, 2000, 'easeOutCirc');
	$('p.test:last').animate({'height' : 'show'}, 2000, 'easeOutElastic');

	$('#bio > div').hide();
	$('#bio > div:first').show();
	$('#bio h3').click(function() {
		$(this).next().animate(
			{'height' : 'toggle'}, 'slow', 'easeOutBounce'
		);
	});
	
	$('p:first')
		.effect('shake', {times:3}, 300)
		.effect('highlight', {}, 3000)
		.hide('explode', {}, 1000);

	$('#menu > li > ul')
		.hide()
		.click(function(e) {
			e.stopPropagation();
		});
	
	$('#menu > li').toggle(function() {
		$(this)
		.css('background-position', 'right -20px')
		.find('ul').slideDown();
	}, function() {
		$(this)
		.css('background-position', 'right top')
		.find('ul').slideUp();
	});

	$('#menu > li').hover(function() {
		$(this).addClass('waiting');
		setTimeout(function() {
			$('#menu .waiting')
			.click()
			.removeClass('waiting');
		}, 600);
	}, function() {
		$('#menu .waiting').removeClass('waiting');
	});

	// Simple Tabs
	// Code hides all the panes except the first one
	$('#info p:not(:first)').hide();
	
	// Code to switch between tabs
	$('#info-nav li').click(function(e) {
    $('#info p').hide();
    $('#info-nav .current').removeClass("current");
    $(this).addClass('current');
    
    var clicked = $(this).find('a:first').attr('href');
    $('#info ' + clicked).fadeIn('fast');
    e.preventDefault();
  	}).eq(0).addClass('current');
	
	// jQuery UI Tabs
	$('#info').tabs({
		cache: false,
		spinner: 'Retrieving data...'
		//spinner: '<img src="../css/imgs/ajax-loader.gif"'
	});
	
	//getter
	var spinner = $("#info").tabs( "option", "spinner");
	//setter
	$("#info").tabs("option", "spinner", 'Retrieving data...');
	
	// jQuery Sliding Overlay
	$('<div></div>')
    .attr('id', 'overlay')
    .css('opacity', 0.65)
    .hover(function(){
      $(this).addClass('active');
    }, function() {
      $(this).removeClass('active');
      setTimeout(function(){
        $('#overlay:not(.active)').slideUp(function(){
          $('a.cart-hover').removeClass('cart-hover');
        });
      }, 800);
    }).appendTo('body');
    
	$('.cart a').mouseover(function(){
	    $(this).addClass('cart-hover');
	    $('#overlay:not(:animated)')
	      .addClass('active')
	      .html('<h1>Welcome to Digoro!</h1><a href="#">View Cart</a>&nbsp;<a href="#">Checkout</a>')
	      .slideDown();      
	  });
	
	  $('#signup form').validate({
	    rules: {
	      name: {
	        required: true
	      },
	      email: {
	        required: true,
	        email: true
	      },
	      website: {
	        url: true
	      },
	      password: {
	        minlength: 6,
	        required: true
	      },
	      passconf: {
	        equalTo: "#password"
	      }
	    },
	    success: function(label) {
	      label.text('OK!').addClass('valid');
	    }
	  });
	
	
}); // End of document ready.
