 
function readyFn( jQuery ) {
	jQuery(document).find('div.woo_payment').closest('form').find('input[type="radio"]').prop("disabled",true);
  
	jQuery(document).find('div.g-recaptcha').closest('form').find('button[type="submit"]').addClass('disabled');
	jQuery(document).find('div.g-recaptcha').closest('form').find('input[type="submit"]').addClass('disabled');
	jQuery(document).find('div.g-recaptcha').closest('form').find('button[type="submit"]').click(function( event ) {
		event.preventDefault();}); 
	jQuery(document).find('div.g-recaptcha').closest('form').find('input[type="submit"]').click(function( event ) {
		event.preventDefault()});
	jQuery(document).find('login').closest('.g-recaptcha').addClass('login');
	jQuery(document).find('register').closest('.g-recaptcha').addClass('register');
	jQuery(document).find('.woo_payment').next('#payment').find('input[type="radio"]').prop({
		"disabled": true,
		"checked": false
	});
	jQuery(window).load(function(){
		jQuery(document).find('.woo_payment').next('#payment').find('input[type="radio"]').prop({
			"disabled": true,
			"checked": false
		});
		jQuery(document).find('.woo_payment').siblings('div').find('input[type=radio]').prop({
			"disabled": true,
			"checked": false
		});
		jQuery('div.woo_payment').closest('form').find('label[id="ka_captcha_failed"]').css("display","");
  
	});
 

 
  
 
	
}


 
jQuery( document ).ready( readyFn );

function ka_captcha_validation_expired(response) {
   
	
	jQuery(document).find('div.g-recaptcha').closest('form').find('button[type="submit"]').addClass('disabled');
	jQuery(document).find('div.g-recaptcha').closest('form').find('input[type="submit"]').addClass('disabled');
	jQuery(document).find('div.g-recaptcha').closest('form').find('button[type="submit"]').click(function( event ) {
		event.preventDefault();});
	jQuery(document).find('div.g-recaptcha').closest('form').find('input[type="submit"]').click(function( event ) {
		event.preventDefault();});
	jQuery('div.g-recaptcha').closest('form').find('label[id="ka_captcha_failed"]').css("display","");
  


}
function ka_captcha_validation_failed(response) {
   
	
	jQuery(document).find('div.g-recaptcha').closest('form').find('button[type="submit"]').addClass('disabled');
	jQuery(document).find('div.g-recaptcha').closest('form').find('input[type="submit"]').addClass('disabled');
	jQuery(document).find('div.g-recaptcha').closest('form').find('button[type="submit"]').click(function( event ) {
		event.preventDefault();});
	jQuery(document).find('div.g-recaptcha').closest('form').find('input[type="submit"]').click(function( event ) {
		event.preventDefault();});
   
}




function ka_captcha_validation_success(response) {
	 jQuery('div.g-recaptcha').closest('form').find('button[type="submit"]').removeClass('disabled').unbind('click');
	 jQuery('div.g-recaptcha').closest('form').find('input[type="submit"]').removeClass('disabled').unbind('click');
	jQuery('.g-recaptcha').closest('form').find('#ka_captcha_failed').hide();
 
} 
function login_ka_captcha_validation_success(response) {
  
	   
	 jQuery('div.woo_login').closest('form').find('button').removeClass('disabled').unbind('click');
  
   
	jQuery('div.woo_login').closest('form').find('label[id="ka_captcha_failed"]').css("display","none");
  


} 
function regs_ka_captcha_validation_success(response) {
  
	   
	 jQuery('div.woo_regs').closest('form').find('button').removeClass('disabled').unbind('click');
	 
	jQuery('div.woo_regs').closest('form').find('label[id="ka_captcha_failed"]').css("display","none");
  


}
function ka_checkout_captcha_validation_success(response) {
  
	   
	 jQuery('div.woo_checkout').closest('form').find('button').removeClass('disabled').unbind('click');
	 
	jQuery('div.woo_checkout').siblings('div').find('label[id="ka_captcha_failed"]').css("display","none");
  


}
function pay_ka_captcha_validation_expired(response) {
	jQuery('div.woo_payment').closest('form').find('label[id="ka_captcha_failed"]').css("display","");
	jQuery(document).find('.woo_payment').siblings('div').find('input[type=radio]').prop({
		"disabled": true,
		"checked": false
		});
   
}
function pay_ka_captcha_validation_failed(response) {
	jQuery('div.woo_payment').closest('form').find('label[id="ka_captcha_failed"]').css("display","");
	jQuery(document).find('.woo_payment').siblings('div').find('input[type=radio]').prop({
		"disabled": true,
		"checked": false
		});
} 
function pay_ka_captcha_validation_success(response) {
	jQuery('.woo_payment').closest('form').find('#ka_captcha_failed').css("display","none");
	jQuery(document).find('.woo_payment').siblings('div').find('input[type=radio]').prop({
		"disabled": false,
			
		});


} 
