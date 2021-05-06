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
						'tab_9_section',         // ID used to identify this section and with which to register options.
						esc_html__('Woo payment method Captcha', 'recaptcha_verification'),                  // Title to be displayed on the administration page.
						'tab_9_section_callback', // Callback used to render the description of the section.
						'recaptcha-p_method-settings'                           // Page on which to add this section of options.
					);

					add_settings_field(    
						'p_method_add_captcha_enable_check', // ID used to identify the field throughout the theme.
						esc_html__('reCaptcha on Woo payment method', 'recaptcha_verification'), // The label to the left of the option interface element.
						'woo_p_method_enable_captcha',   // The name of the function responsible for rendering.
						'recaptcha-p_method-settings', // The page on which this option will be displayed.
						'tab_9_section'// The name of the section to which this field belongs.    
					);
						register_setting(
							'captcha-p_method-settings',
							'p_method_add_captcha_enable_check'
						);

						add_settings_field(    
							'add_p_method_captcha_field_title', // ID used to identify the field throughout the theme.
							esc_html__('Recaptcha Field Title', 'recaptcha_verification'), // The label to the left of the option interface element.
							'p_method_captcha_field_title_callback',   // The name of the function responsible for rendering.
							'recaptcha-p_method-settings', // The page on which this option will be displayed.
							'tab_9_section'// The name of the section to which this field belongs.    
						);
						register_setting(
							'captcha-p_method-settings',
							'add_p_method_captcha_field_title'
						);


						add_settings_field(    
							'p_method_captcha_themes', // ID used to identify the field throughout the theme.
							esc_html__('Recaptcha Themes', 'recaptcha_verification'), // The label to the left of the option interface element.
							'p_method_captcha_reg_authentication_callback',   // The name of the function responsible for rendering.
							'recaptcha-p_method-settings', // The page on which this option will be displayed.
							'tab_9_section'// The name of the section to which this field belongs.    
						);
						register_setting(
							'captcha-p_method-settings',
							'p_method_captcha_themes'
						);

						add_settings_field(    
							'p_method_add_captcha_size_radio', // ID used to identify the field throughout the theme.
							esc_html__('Recaptcha Size', 'recaptcha_verification'), // The label to the left of the option interface element.
							'p_method_captcha_reg_size_radio_callback',   // The name of the function responsible for rendering.
							'recaptcha-p_method-settings', // The page on which this option will be displayed.
							'tab_9_section'// The name of the section to which this field belongs.    
						);
						register_setting(
							'captcha-p_method-settings',
							'p_method_add_captcha_size_radio'
						);        

						if (! function_exists('woo_p_method_enable_captcha') ) {
							function woo_p_method_enable_captcha() {
								?>
		<input type="checkbox" name="p_method_add_captcha_enable_check" value="1"<?php checked(get_option('p_method_add_captcha_enable_check'), '1'); ?>><label><?php echo esc_html__(' Eables captcha for payment method', 'recaptcha_verification'); ?></label><br>
								<?php
							}
						}
						if (! function_exists('p_method_captcha_field_title_callback') ) {
							function p_method_captcha_field_title_callback() {
								?>
		<input type="text" name="add_p_method_captcha_field_title" value="<?php echo esc_attr(get_option('add_p_method_captcha_field_title')); ?>"><label><?php echo esc_html__(' Field text for payment method captcha', 'recaptcha_verification'); ?></label><br>
								<?php
							}
						}
						if (! function_exists('p_method_captcha_reg_authentication_callback') ) {
							function p_method_captcha_reg_authentication_callback() {
								?>
		<input type="radio" name="p_method_captcha_themes"value="1"<?php checked(get_option('p_method_captcha_themes'), '1'); ?> checked >
		<label for="Light"><?php echo esc_html__('Light', 'recaptcha_verification'); ?></label><br>
		<label><em><?php echo esc_html__(' Eables light theme for captcha', 'recaptcha_verification'); ?></em></label><br><br>
		<input type="radio" name="p_method_captcha_themes" value="0"<?php checked(get_option('p_method_captcha_themes'), '0'); ?>>
		<label for="Dark"><?php echo esc_html__('Dark', 'recaptcha_verification'); ?></label><br>
		<label><em><?php echo esc_html__(' Eables dark theme for captcha', 'recaptcha_verification'); ?></em></label><br><br>
								<?php
							}
						}
						if (! function_exists('p_method_captcha_reg_size_radio_callback') ) {
							function p_method_captcha_reg_size_radio_callback() {
								?>
		<input type="radio" name="p_method_add_captcha_size_radio" value="1"<?php checked(get_option('p_method_add_captcha_size_radio'), '1'); ?> checked>
		<label for="Light"><?php echo esc_html__('Normal', 'recaptcha_verification'); ?></label><br>
		<label><em><?php echo esc_html__(' Eables normal size for captcha', 'recaptcha_verification'); ?></em></label><br><br>
		<input type="radio" name="p_method_add_captcha_size_radio" value="0"<?php checked(get_option('p_method_add_captcha_size_radio'), '0'); ?>>
		<label for="Dark"><?php echo esc_html__('Compact', 'recaptcha_verification'); ?></label><br>
		<label><em><?php echo esc_html__(' Eables compact size for captcha', 'recaptcha_verification'); ?></em></label><br><br>
								<?php
							}
						}
						if (! function_exists('tab_9_section_callback') ) {
							function tab_9_section_callback() {
		 

 
							}
						}
