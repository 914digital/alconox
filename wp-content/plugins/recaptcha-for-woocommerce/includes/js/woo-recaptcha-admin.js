jQuery( document ).ready( function( $ ) {
	
	woo_recaptcha_display_checkout();
	
	function woo_recaptcha_display_checkout() {
		
		if( $('#woo_recaptcha_checkout').is(':checked') ) {
			$('#woo_recaptcha_checkout').parents('tr').next().fadeIn();		
		} else {
			$('#woo_recaptcha_checkout').parents('tr').next().fadeOut();
		}
	}
	
	// on click of checkout display settings
	$( document ).on( 'click', '#woo_recaptcha_checkout', function() {		
		woo_recaptcha_display_checkout();				
	});
});