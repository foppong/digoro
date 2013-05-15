/**
 * @author Frank
 */

$.ajaxSetup({"error":function( XMLHttpRequest,textStatus, errorThrown ) {   
      alert(textStatus);
      alert(errorThrown);
      alert(XMLHttpRequest.responseText);
  }});
  
var CAROUSEL = {

	mycarousel_itemLoadCallback: function(carousel, state) {
	    // Check if the requested items already exist
	    if (carousel.has(carousel.first, carousel.last)) {
	        return;
	    }
alert(carousel.first);
alert(carousel.last);	    
		// AJAX call to retreive data
		$.ajax({
			type: 'GET',
			dataType: 'xml',
			url: "../data/home_data.php",
			data: { // data we're sending to server
				first: carousel.first,
				last: carousel.last
			},
			success: function(indata) {
				CAROUSEL.mycarousel_itemAddCallback(carousel, carousel.first, carousel.last, indata);
			},
			error: function() {
				alert('an error occured!');
			}
		});    
	},

 	mycarousel_itemAddCallback: function(carousel, first, last, indata) {
	    // Set the size of the carousel
	    carousel.size(parseInt(jQuery('total', indata).text()));
	
	    jQuery('image', indata).each(function(i) {
	        carousel.add(first + i, CAROUSEL.mycarousel_getItemHTML(jQuery(this).text()));
	    });
	},
	
	// Item html creation helper.
	mycarousel_getItemHTML: function(url) {
	    return '<img src="' + url + '" width="75" height="75" alt="" />';
	}


}


// jQuery Code for when page is loaded
$(document).ready(function() {

 	$( '#mycarousel' ).jcarousel({
        // Configuration goes here
 		scroll: 2, //The number of items to scroll by.
 		       
        // Uncomment the following option if you want items
        // which are outside the visible range to be removed
        // from the DOM.
        // Useful for carousels with MANY items.
        // itemVisibleOutCallback: {onAfterAnimation: function(carousel, item, i, state, evt) { carousel.remove(i); }},
        itemLoadCallback: CAROUSEL.mycarousel_itemLoadCallback
    });
    
});