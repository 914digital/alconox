<?php

/**
 * Define fille.
 *
 * @package WOOCP
 */
if (! defined('ABSPATH') ) {
	die;
}
/**
 * Class start.
 *
 * @package WOOCP
 */
		add_settings_section(
			'tab_5_section',         // ID used to identify this section and with which to register options.
			 esc_html__('Woo Guest Checkout', 'recaptcha_verification'),                  // Title to be displayed on the administration page.
			'tab_5_section_callback', // Callback used to render the description of the section.
			'recaptcha-woo-guest-checkout'                           // Page on which to add this section of options.
		);
		add_settings_field(    
			'guest_checkout_add_captcha_enable_check', // ID used to identify the field throughout the theme.
			esc_html__('reCaptcha on guest Checkout', 'recaptcha_verification'), // The label to the left of the option interface element.
			'guest_checkout_captcha_enable_check',   // The name of the function responsible for rendering.
			'recaptcha-woo-guest-checkout', // The page on which this option will be displayed.
			'tab_5_section'// The name of the section to which this field belongs.    
		);
		register_setting(
			'captcha-woo-guest-checkout',
			'guest_checkout_add_captcha_enable_check'
		);


		add_settings_field(    
			'guest_checkout_add_captcha_field_title', // ID used to identify the field throughout the theme.
			__('Recaptcha Field Title', 'recaptcha_verification'), // The label to the left of the option interface element.
			'guest_checkout_authentication_callback',   // The name of the function responsible for rendering.
			'recaptcha-woo-guest-checkout', // The page on which this option will be displayed.
			'tab_5_section'// The name of the section to which this field belongs.    
		);
		register_setting(
			'captcha-woo-guest-checkout',
			'guest_checkout_add_captcha_field_title'
		);


		add_settings_field(    
			'check_out_page_captcha_themes', // ID used to identify the field throughout the theme.
			esc_html__('Recaptcha Themes', 'recaptcha_verification'), // The label to the left of the option interface element.
			'guest_checkout_captcha_reg_authentication_callback',   // The name of the function responsible for rendering.
			'recaptcha-woo-guest-checkout', // The page on which this option will be displayed.
			'tab_5_section'// The name of the section to which this field belongs.    
		);
		register_setting(
			'captcha-woo-guest-checkout',
			'check_out_page_captcha_themes'
		);

		add_settings_field(    
			'guest_checkout_add_captcha_size_radio', // ID used to identify the field throughout the theme.
			esc_html__('Recaptcha size', 'recaptcha_verification'), // The label to the left of the option interface element.
			'guest_checkout_size_radio_callback',   // The name of the function responsible for rendering.
			'recaptcha-woo-guest-checkout', // The page on which this option will be displayed.
			'tab_5_section'// The name of the section to which this field belongs.    
		);
		register_setting(
			'captcha-woo-guest-checkout',
			'guest_checkout_add_captcha_size_radio'
		);

	

		if (! function_exists('guest_checkout_captcha_enable_check') ) {
			function guest_checkout_captcha_enable_check() {
				?>
		<input type="checkbox" name="guest_checkout_add_captcha_enable_check"value="1"<?php checked(get_option('guest_checkout_add_captcha_enable_check'), '1'); ?>><label><?php echo esc_html__('  Enable Captcha for checkout page. ', 'recaptcha_verification'); ?></label><br>
				<?php
			}
		}
		if (! function_exists('guest_checkout_authentication_callback') ) {
			function guest_checkout_authentication_callback() {
				?>
		<input type="text" name="guest_checkout_add_captcha_field_title" value="<?php echo esc_attr(get_option('guest_checkout_add_captcha_field_title')); ?>"><label><?php echo esc_html__('  Adds Field Title to Recaptcha', 'recaptcha_verification'); ?></label><br>
				<?php
			}
		}
		if (! function_exists('guest_checkout_captcha_reg_authentication_callback') ) {
			function guest_checkout_captcha_reg_authentication_callback() {
				?>
		<input type="radio" name="check_out_page_captcha_themes" value="1"<?php checked(get_option('check_out_page_captcha_themes'), '1'); ?> checked >
		<label for="Light"><?php echo esc_html__('Light', 'recaptcha_verification'); ?></label><br>
		<label><em><?php echo esc_html__(' Eables light theme for captcha', 'recaptcha_verification'); ?></em></label><br><br>
		<input type="radio" name="check_out_page_captcha_themes" value="0"<?php checked(get_option('check_out_page_captcha_themes'), '0'); ?>   >
		<label for="Dark"><?php echo esc_html__('Dark', 'recaptcha_verification'); ?></label><br>
		<label><em><?php echo esc_html__(' Eables Dark theme for captcha', 'recaptcha_verification'); ?></em></label><br><br>
				<?php
			}
		}
		if (! function_exists('guest_checkout_size_radio_callback') ) {
			function guest_checkout_size_radio_callback() {
				?>
		<input type="radio" name="guest_checkout_add_captcha_size_radio" value="1" <?php checked(get_option('guest_checkout_add_captcha_size_radio'), '1'); ?> checked >
		<label for="Light"><?php echo esc_html__('Normal', 'recaptcha_verification'); ?></label><br>
		<label><em><?php echo esc_html__(' Eables normal size for captcha', 'recaptcha_verification'); ?></em></label><br><br>
		<input type="radio" name="guest_checkout_add_captcha_size_radio" value="0"<?php checked(get_option('guest_checkout_add_captcha_size_radio'), '0'); ?>>
		<label for="Dark"><?php echo esc_html__('Compact', 'recaptcha_verification'); ?></label><br>
		<label><em><?php echo esc_html__(' Eables compact size for captcha', 'recaptcha_verification'); ?></em></label><br>
				<?php
			}
		}

   
		if (! function_exists('tab_5_section_callback') ) {
			function tab_5_section_callback() {
		 

			}
		}
