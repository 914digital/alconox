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
						'tab_4_section',         // ID used to identify this section and with which to register options.
						esc_html__('Woo Lost Password Captcha', 'recaptcha_verification'),                  // Title to be displayed on the administration page.
						'tab_4_section_callback', // Callback used to render the description of the section.
						'recaptcha-woo-lpass'                           // Page on which to add this section of options.
					);


					add_settings_field(    
						'woo_lpass_add_captcha_enable_check', // ID used to identify the field throughout the theme.
						esc_html__('reCaptcha on Woo-commerce Lost Password', 'recaptcha_verification'), // The label to the left of the option interface element.
						'lpass_captcha_enable_check',   // The name of the function responsible for rendering.
						'recaptcha-woo-lpass', // The page on which this option will be displayed.
						'tab_4_section'// The name of the section to which this field belongs.    
					);
					register_setting(
						'captcha-woo-lpass',
						'woo_lpass_add_captcha_enable_check'
					);

					add_settings_field(    
						'lpass_add_captcha_field_title', // ID used to identify the field throughout the theme.
						esc_html__('Recaptcha Field Title', 'recaptcha_verification'), // The label to the left of the option interface element.
						'lpass_captcha_field_title_callback',   // The name of the function responsible for rendering.
						'recaptcha-woo-lpass', // The page on which this option will be displayed.
						'tab_4_section'// The name of the section to which this field belongs.    
					);
					register_setting(
						'captcha-woo-lpass',
						'lpass_add_captcha_field_title'
					);


					add_settings_field(    
						'woo_lpass_add_captcha_authentication_key_fields', // ID used to identify the field throughout the theme.
						esc_html__('Recaptcha Themes ', 'recaptcha_verification'), // The label to the left of the option interface element.
						'woo_lpass_captcha_reg_authentication_callback',   // The name of the function responsible for rendering.
						'recaptcha-woo-lpass', // The page on which this option will be displayed.
						'tab_4_section'// The name of the section to which this field belongs.    
					);
					register_setting(
						'captcha-woo-lpass',
						'woo_lpass_add_captcha_authentication_key_fields'
					);

					add_settings_field(    
						'woo_lpass_add_captcha_size_radio', // ID used to identify the field throughout the theme.
						esc_html__('Recaptcha size', 'recaptcha_verification'), // The label to the left of the option interface element.
						'woo_lpass_captcha_reg_size_radio_callback',   // The name of the function responsible for rendering.
						'recaptcha-woo-lpass', // The page on which this option will be displayed.
						'tab_4_section'// The name of the section to which this field belongs.    
					);
					register_setting(
						'captcha-woo-lpass',
						'woo_lpass_add_captcha_size_radio'
					);

					if (! function_exists('lpass_captcha_enable_check') ) {   
						function lpass_captcha_enable_check() {
							?>
		<input type="checkbox" name="woo_lpass_add_captcha_enable_check"value="1"<?php checked(get_option('woo_lpass_add_captcha_enable_check'), '1'); ?>><label><?php echo esc_html__(' Eables captcha for Woo Commerce lost password page', 'recaptcha_verification'); ?></label><br>
							<?php
						}
					}
					if (! function_exists('lpass_captcha_field_title_callback') ) {  
						function lpass_captcha_field_title_callback() {
							?>
		<input type="text" name="lpass_add_captcha_field_title" value="<?php echo esc_attr(get_option('lpass_add_captcha_field_title')); ?>"><label><?php echo esc_html__(' Field Text for Woo Commerce lost password page', 'recaptcha_verification'); ?></label><br>
							<?php
						}
					}

					if (! function_exists('woo_lpass_captcha_reg_authentication_callback') ) {
						function woo_lpass_captcha_reg_authentication_callback() {
							?>
		<input type="radio" name="woo_lpass_add_captcha_authentication_key_fields" value="1"<?php checked(get_option('woo_lpass_add_captcha_authentication_key_fields'), '1'); ?> checked  >
		<label for="normal"><?php echo esc_html__('light', 'recaptcha_verification'); ?></label><br>
		<label><em><?php echo esc_html__(' Eables light theme for captcha', 'recaptcha_verification'); ?></em></label><br><br>
		<input type="radio" name="woo_lpass_add_captcha_authentication_key_fields" value="0"<?php checked(get_option('woo_lpass_add_captcha_authentication_key_fields'), '0'); ?> >
		<label for="compact"><?php echo esc_html__('dark', 'recaptcha_verification'); ?></label><br>
		<label><em><?php echo esc_html__(' Eables dark theme for captcha', 'recaptcha_verification'); ?></em></label><br><br>
							<?php
						}
					}
					if (! function_exists('woo_lpass_captcha_reg_size_radio_callback') ) {
						function woo_lpass_captcha_reg_size_radio_callback() {
							?>
		<input type="radio" name="woo_lpass_add_captcha_size_radio" value="1"<?php checked(get_option('woo_lpass_add_captcha_size_radio'), '1'); ?> checked>
		<label for="normal"><?php echo esc_html__('Normal', 'recaptcha_verification'); ?></label><br>
		<label><em><?php echo esc_html__(' Eables normal size for captcha', 'recaptcha_verification'); ?></em></label><br><br>
		<input type="radio" name="woo_lpass_add_captcha_size_radio" value="0"<?php checked(get_option('woo_lpass_add_captcha_size_radio'), '0'); ?>>
		<label for="compact"><?php echo esc_html__('Compact', 'recaptcha_verification'); ?></label><br>
		<label><em><?php echo esc_html__(' Eables compact size for captcha', 'recaptcha_verification'); ?></em></label><br><br>
							<?php
						}
					}
					if (! function_exists('tab_4_section_callback') ) { 
						function tab_4_section_callback() {
		 

 
						}
					}
