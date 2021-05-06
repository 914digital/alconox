jQuery(document).ready(function(){
	var sitekey = php_vars.v3_sitekey;
	var ajaxUrl = php_vars.admin_url;
	
	
	var captcha_button = jQuery(document).find('input.g-grecaptcha').closest('form').find('button[type="submit"], input[type="submit"]');
	captcha_button.addClass('g-recaptcha');
	captcha_button.attr("data-sitekey" , sitekey);

	
	var captcha_form =jQuery(document).find('input.g-grecaptcha').closest('form');
	   
	var captcha_button =jQuery(document).find('#place_order');
	

	captcha_form.submit(function(e){
		console.log('hello');
		e.preventDefault();
		console.log(sitekey);
		grecaptcha.ready(function() {

			grecaptcha.execute(sitekey, {action: 'submit'}).then(function(token) {
		  

	   
				var ajaxUrl  = php_vars.admin_url;
				var nonce    = php_vars.nonce;
				current_form = jQuery(e.target);
				console.log(ajaxUrl);
				console.log(token);
				jQuery.ajax({
					url: ajaxUrl,
					type: 'POST',
					data: {
						action       : 'validation_captchav3',
						nonce        : nonce,
						captcha_token : token,
					},
					success: function (response) {
						if ( false == response['success'] ) {
							console.log(response['success']);
							current_form.before('you are a robot!');
						} else {
							console.log(response['success']);
							current_form.unbind('submit');
							current_form.find('input[type="submit"], button[type="submit"]').click();
						}
					},
					error: function (response) {
				
					}
				});
			  


			});
		});
	});

 
 
		
	


});

	



jQuery(document).ajaxComplete(function(){
	var captcha_button = jQuery(document).find('input.grecaptcha_required').closest('form').find('button[type="submit"]');
	if (captcha_button.hasClass('g-recaptcha')) {
		return;

	} else {
		var sitekey = php_vars.v3_sitekey;
		captcha_button.addClass('g-recaptcha');
		captcha_button.attr("data-sitekey", sitekey);
	
	}

	

});
