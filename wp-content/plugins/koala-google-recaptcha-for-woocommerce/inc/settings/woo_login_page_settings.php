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
if (! defined('ABSPATH') ) {
	exit; // Exit if accessed directly.
}

					add_settings_section(
						'tab_3_section',         // ID used to identify this section and with which to register options.
						esc_html__('Woo Login Captcha', 'recaptcha_verification'),                  // Title to be displayed on the administration page.
						'tab_3_section_callback', // Callback used to render the description of the section.
						'recaptcha-woo-login'                           // Page on which to add this section of options.
					);

					add_settings_field(    
						'login_add_captcha_enable_check', // ID used to identify the field throughout the theme.
						esc_html__('reCaptcha on Woo Login page', 'recaptcha_verification'), // The label to the left of the option interface element.
						'woo_login_enable_captcha',   // The name of the function responsible for rendering.
						'recaptcha-woo-login', // The page on which this option will be displayed.
						'tab_3_section'// The name of the section to which this field belongs.    
					);
						register_setting(
							'captcha-woo-login',
							'login_add_captcha_enable_check'
						);

						add_settings_field(    
							'woo_login_add_captcha_field_title', // ID used to identify the field throughout the theme.
							esc_html__('Recaptcha Field Title', 'recaptcha_verification'), // The label to the left of the option interface element.
							'login_captcha_field_title_callback',   // The name of the function responsible for rendering.
							'recaptcha-woo-login', // The page on which this option will be displayed.
							'tab_3_section'// The name of the section to which this field belongs.    
						);
						register_setting(
							'captcha-woo-login',
							'woo_login_add_captcha_field_title'
						);


						add_settings_field(    
							'login_add_captcha_authentication_key_fields', // ID used to identify the field throughout the theme.
							esc_html__('Recaptcha Themes', 'recaptcha_verification'), // The label to the left of the option interface element.
							'login_captcha_reg_authentication_callback',   // The name of the function responsible for rendering.
							'recaptcha-woo-login', // The page on which this option will be displayed.
							'tab_3_section'// The name of the section to which this field belongs.    
						);
						register_setting(
							'captcha-woo-login',
							'login_add_captcha_authentication_key_fields'
						);

						add_settings_field(    
							'login_add_captcha_size_radio', // ID used to identify the field throughout the theme.
							esc_html__('Recaptcha Size', 'recaptcha_verification'), // The label to the left of the option interface element.
							'login_captcha_reg_size_radio_callback',   // The name of the function responsible for rendering.
							'recaptcha-woo-login', // The page on which this option will be displayed.
							'tab_3_section'// The name of the section to which this field belongs.    
						);
						register_setting(
							'captcha-woo-login',
							'login_add_captcha_size_radio'
						);        

						if (! function_exists('woo_login_enable_captcha') ) {
							function woo_login_enable_captcha() {
								?>
		<input type="checkbox" name="login_add_captcha_enable_check" value="1"<?php checked(get_option('login_add_captcha_enable_check'), '1'); ?>> <label><?php echo esc_html__(' Eables Captcha for Woo Commerce login page', 'recaptcha_verification'); ?></label><br>
								<?php
							}
						}
						if (! function_exists('login_captcha_field_title_callback') ) {
							function login_captcha_field_title_callback() {
								?>
		<input type="text" name="woo_login_add_captcha_field_title" value="<?php echo esc_attr(get_option('woo_login_add_captcha_field_title')); ?>"><label><?php echo esc_html__(' Field Text for Woo Commerce login page captcha', 'recaptcha_verification'); ?></label><br>
								<?php
							}
						}
						if (! function_exists('login_captcha_reg_authentication_callback') ) {
							function login_captcha_reg_authentication_callback() {
								?>
		<input type="radio" name="login_add_captcha_authentication_key_fields" value="1"<?php checked(get_option('login_add_captcha_authentication_key_fields'), '1'); ?> checked >
		<label for="Light"><?php echo esc_html__('Light', 'recaptcha_verification'); ?></label><br>
		<label><em><?php echo esc_html__(' Eables light theme for captcha', 'recaptcha_verification'); ?></em></label><br><br>
		<input type="radio" name="login_add_captcha_authentication_key_fields"  value="0"
								<?php checked(get_option('login_add_captcha_authentication_key_fields'), '0'); ?>>
		<label for="Dark"><?php echo esc_html__('Dark', 'recaptcha_verification'); ?></label><br>
		<label><em><?php echo esc_html__(' Eables Dark theme for captcha', 'recaptcha_verification'); ?></em></label><br><br>
								<?php
							}
						}
						if (! function_exists('login_captcha_reg_size_radio_callback') ) {
							function login_captcha_reg_size_radio_callback() {
								?>
		<input type="radio" name="login_add_captcha_size_radio"  value="1" <?php checked(get_option('login_add_captcha_size_radio'), '1'); ?> checked>
		<label for="normal"><?php echo esc_html__('Normal', 'recaptcha_verification'); ?></label><br>
		<label><em><?php echo esc_html__(' Eables normal size for captcha', 'recaptcha_verification'); ?></em></label><br><br>
		<input type="radio" name="login_add_captcha_size_radio"  value="0" <?php checked(get_option('login_add_captcha_size_radio'), '0'); ?> >
		<label for="compact"><?php echo esc_html__('Compact', 'recaptcha_verification'); ?></label><br>
		<label><em><?php echo esc_html__(' Eables compact size for captcha', 'recaptcha_verification'); ?></em></label><br><br>
								<?php
							}
						}
						if (! function_exists('tab_3_section_callback') ) {
							function tab_3_section_callback() {
		 

 
							}
						}
