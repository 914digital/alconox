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
						'tab_1_section',         // ID used to identify this section and with which to register options.
						esc_html__('General Settings', 'recaptcha_verification'),                  // Title to be displayed on the administration page.
						'tab_1_section_callback', // Callback used to render the description of the section.
						'wp_recaptcha_general'                           // Page on which to add this section of options.
					);

					add_settings_field(
						'captcha_type_option', // ID used to identify the field throughout the theme.
						esc_html__('Recaptcha Version', 'recaptcha_verification'), // The label to the left of the option interface element.
						'set_recaptcha_version',   // The name of the function responsible for rendering.
						'wp_recaptcha_general', // The page on which this option will be displayed.
						'tab_1_section'// The name of the section to which this field belongs.    
					);
						register_setting(
							'wp_captcha_general',
							'captcha_type_option'
						);

						add_settings_field(
						'add_captcha_site_key_field', // ID used to identify the field throughout the theme.
						esc_html__('Site Key', 'recaptcha_verification'), // The label to the left of the option interface element.
						'captcha_fields_callback',   // The name of the function responsible for rendering.
						'wp_recaptcha_general', // The page on which this option will be displayed.
						'tab_1_section'// The name of the section to which this field belongs.    
						);
						register_setting(
							'wp_captcha_general',
							'add_captcha_site_key_field'
						);
						add_settings_field(
							'add_captcha_secret_key_field', // ID used to identify the field throughout the theme.
							esc_html__('Secret Key', 'recaptcha_verification'), // The label to the left of the option interface element.
							'captcha_fields_callback_1',   // The name of the function responsible for rendering.
							'wp_recaptcha_general', // The page on which this option will be displayed.
							'tab_1_section'// The name of the section to which this field belongs.    
						);
						register_setting(
							'wp_captcha_general',
							'add_captcha_secret_key_field'
						);




						if (! function_exists('set_recaptcha_version') ) {
							function set_recaptcha_version() {
								?>
		<input type="radio" name="captcha_type_option" value="1" <?php checked(get_option('captcha_type_option'), '1'); ?> checked >
		<label for="v2"><?php echo esc_html__('V2', 'recaptcha_verification'); ?></label><br>
		<label><em><?php echo esc_html__(' Add recaptcha V2 ', 'recaptcha_verification'); ?></em></label><br><br>
		<input type="radio" name="captcha_type_option" value="0" <?php checked(get_option('captcha_type_option'), '0'); ?>   >
		<label for="V3"><?php echo esc_html__('V3', 'recaptcha_verification'); ?></label><br>
		<label><em><?php echo esc_html__(' Add recaptcha V3 ', 'recaptcha_verification'); ?></em></label><br><br>
								<?php
							}
						}
					


						if (! function_exists('captcha_fields_callback') ) {
							function captcha_fields_callback() {
								?>
		<input type="text" name="add_captcha_site_key_field"  value="<?php echo esc_attr(get_option('add_captcha_site_key_field')); ?>">&nbsp<label><?php echo esc_html__('  Add site key for Recaptcha API ', 'recaptcha_verification'); ?></label><br>
		<label><?php echo esc_html__('Get site key for reCaptcha from ', 'recaptcha_verification'); ?><a href="https://www.google.com/recaptcha/admin/create" target="_blank">here</a></label>
		 
		 
								<?php
							}
						}
						if (! function_exists('captcha_fields_callback_1') ) {
							function captcha_fields_callback_1() {
								?>
		<input type="text" value="<?php echo esc_attr(get_option('add_captcha_secret_key_field')); ?>" name="add_captcha_secret_key_field"  value="<?php echo esc_attr(get_option('add_captcha_secret_key_field')); ?>">&nbsp<label><?php echo esc_html__('  Add secret key for Recaptcha API ', 'recaptcha_verification'); ?></label><br>
		<label >Get secret key for reCaptcha from </label><a href="https://www.google.com/recaptcha/admin/create" target="_blank">here</a>
								<?php
							}
						}
						
					
						if (! function_exists('tab_1_section_callback') ) {
							function tab_1_section_callback() {
							}
						}
	 
