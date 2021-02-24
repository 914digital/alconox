jQuery( document ).ready( function( $ ) {		
	
	$( document ).on( 'updated_checkout', function( data ) {
		
		var recaptcha_field_id = $('.woocommerce-checkout-payment').find('.g-recaptcha').attr('id');
		if( recaptcha_field_id != null ) {
			recaptcha_field_id = grecaptcha.render( recaptcha_field_id, {
				'sitekey' : WooRecaptchaPulicVar.sitekey,
				'theme' : WooRecaptchaPulicVar.theme,
				'size' : WooRecaptchaPulicVar.size
			});			
		}
	});
});