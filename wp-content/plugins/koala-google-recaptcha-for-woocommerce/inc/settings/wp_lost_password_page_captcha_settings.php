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
		'tab_8_section',         // ID used to identify this section and with which to register options.
		esc_html__('WP Lost Password Captcha', 'recaptcha_verification'),                  // Title to be displayed on the administration page.
		'tab_8_section_callback', // Callback used to render the description of the section.
		'wp_recaptcha_lostpassword'                           // Page on which to add this section of options.
	);


	add_settings_field(    
		'wp_lpass_page_enable_check', // ID used to identify the field throughout the theme.
		esc_html__('reCaptcha on Woo-commerce Lost Password', 'recaptcha_verification'), // The label to the left of the option interface element.
		'wp_lpass_captcha_enable_check',   // The name of the function responsible for rendering.
		'wp_recaptcha_lostpassword', // The page on which this option will be displayed.
		'tab_8_section'// The name of the section to which this field belongs.    
	);
			register_setting(
				'wp_captcha_lostpassword',
				'wp_lpass_page_enable_check'
			);

			add_settings_field(    
				'wp_lpass_add_captcha_field_title', // ID used to identify the field throughout the theme.
				esc_html__('Recaptcha Field Title', 'recaptcha_verification'), // The label to the left of the option interface element.
				'wp_lpass_captcha_field_title_callback',   // The name of the function responsible for rendering.
				'wp_recaptcha_lostpassword', // The page on which this option will be displayed.
				'tab_8_section'// The name of the section to which this field belongs.    
			);
			register_setting(
				'wp_captcha_lostpassword',
				'wp_lpass_add_captcha_field_title'
			);


			add_settings_field(    
				'wp_lpass_add_captcha_authentication_key_fields', // ID used to identify the field throughout the theme.
				esc_html__('Recaptcha Themes', 'recaptcha_verification'), // The label to the left of the option interface element.
				'wp_lpass_captcha_reg_authentication_callback',   // The name of the function responsible for rendering.
				'wp_recaptcha_lostpassword', // The page on which this option will be displayed.
				'tab_8_section'// The name of the section to which this field belongs.    
			);
			register_setting(
				'wp_captcha_lostpassword',
				'wp_lpass_add_captcha_authentication_key_fields'
			);

			add_settings_field(    
				'wp_lpass_add_captcha_size_radio', // ID used to identify the field throughout the theme.
				esc_html__('Recaptcha Size', 'recaptcha_verification'), // The label to the left of the option interface element.
				'wp_lpass_captcha_reg_size_radio_callback',   // The name of the function responsible for rendering.
				'wp_recaptcha_lostpassword', // The page on which this option will be displayed.
				'tab_8_section'// The name of the section to which this field belongs.    
			);
			register_setting(
				'wp_captcha_lostpassword',
				'wp_lpass_add_captcha_size_radio'
			);

	
	 
			if (! function_exists('wp_lpass_captcha_enable_check') ) {
				function wp_lpass_captcha_enable_check() {
					?>
		<input type="checkbox" name="wp_lpass_page_enable_check" value="1"<?php checked(get_option('wp_lpass_page_enable_check'), '1'); ?>><label><?php echo esc_html__(' Eables captcha for WordPress lost password', 'recaptcha_verification'); ?></label><br><br>
					<?php
				}
			}

	 
			if (! function_exists('wp_lpass_captcha_field_title_callback') ) {
				function wp_lpass_captcha_field_title_callback() {
					?>
		<input type="text" name="wp_lpass_add_captcha_field_title" value="<?php echo esc_attr(get_option('wp_lpass_add_captcha_field_title')); ?>"><label><?php echo esc_html__(' Field text for WordPress lost password captcha', 'recaptcha_verification'); ?></label><br><br>
					<?php
				}
			}
			if (! function_exists('wp_lpass_captcha_reg_authentication_callback') ) {
				function wp_lpass_captcha_reg_authentication_callback() {
					?>
		<input type="radio" name="wp_lpass_add_captcha_authentication_key_fields" value="1"<?php checked(get_option('wp_lpass_add_captcha_authentication_key_fields'), '1'); ?>  checked >
		<label for="Light"><?php echo esc_html__('Light', 'recaptcha_verification'); ?></label><br>
		<label><em><?php echo esc_html__(' Eables light theme for captcha', 'recaptcha_verification'); ?></em></label><br><br>
		<input type="radio" name="wp_lpass_add_captcha_authentication_key_fields" value="0"<?php checked(get_option('wp_lpass_add_captcha_authentication_key_fields'), '0'); ?>     >
		<label for="Dark"><?php echo esc_html__('Dark', 'recaptcha_verification'); ?></label><br>
		<label><em><?php echo esc_html__(' Eables dark theme for captcha', 'recaptcha_verification'); ?></em></label><br><br>
					<?php
				}
			}
			if (! function_exists('wp_lpass_captcha_reg_size_radio_callback') ) {
				function wp_lpass_captcha_reg_size_radio_callback() {
					?>
		<input type="radio" name="wp_lpass_add_captcha_size_radio"value="1"<?php checked(get_option('wp_lpass_add_captcha_size_radio'), '1'); ?> checked>
		<label for="Light"><?php echo esc_html__('Normal', 'recaptcha_verification'); ?></label><br>
		<label><em><?php echo esc_html__(' Eables normal size for captcha', 'recaptcha_verification'); ?></em></label><br><br>
		<input type="radio" name="wp_lpass_add_captcha_size_radio" value="0"<?php checked(get_option('wp_lpass_add_captcha_size_radio'), '0'); ?>  >
		<label for="Dark"><?php echo esc_html__('Compact', 'recaptcha_verification'); ?></label><br>
		<label><em><?php echo esc_html__(' Eables compact size for captcha', 'recaptcha_verification'); ?></em></label><br><br>
					<?php
				}
			}

			if (! function_exists('tab_8_section_callback') ) {
				function tab_8_section_callback() {
		 

 
				}
			}
