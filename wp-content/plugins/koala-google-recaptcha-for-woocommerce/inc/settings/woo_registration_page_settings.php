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
			'tab_2_section',         // ID used to identify this section and with which to register options.
			esc_html__('reCaptcha on Registration', 'recaptcha_verification'),                  // Title to be displayed on the administration page.
			'tab_2_section_callback', // Callback used to render the description of the section.
			'recaptcha-woo-regs'                           // Page on which to add this section of options.
		);

		add_settings_field(    
			'add_captcha_enable_check', // ID used to identify the field throughout the theme.
			esc_html__('reCaptcha on Registration', 'recaptcha_verification'), // The label to the left of the option interface element.
			'captcha_enable_check',   // The name of the function responsible for rendering.
			'recaptcha-woo-regs', // The page on which this option will be displayed.
			'tab_2_section'// The name of the section to which this field belongs.    
		);
					register_setting(
						'captcha-woo-regs',
						'add_captcha_enable_check'
					);

					add_settings_field(    
						'woo_regs_enable_captcha', // ID used to identify the field throughout the theme.
						esc_html__('Recaptcha Field Title', 'recaptcha_verification'), // The label to the left of the option interface element.
						'captcha_field_title_callback',   // The name of the function responsible for rendering.
						'recaptcha-woo-regs', // The page on which this option will be displayed.
						'tab_2_section'// The name of the section to which this field belongs.    
					);
					register_setting(
						'captcha-woo-regs',
						'woo_regs_enable_captcha'
					);


					add_settings_field(    
						'woo_regs_add_captcha_authentication_key_fields', // ID used to identify the field throughout the theme.
						esc_html__('Recaptcha themes', 'recaptcha_verification'), // The label to the left of the option interface element.
						'captcha_reg_authentication_callback',   // The name of the function responsible for rendering.
						'recaptcha-woo-regs', // The page on which this option will be displayed.
						'tab_2_section'// The name of the section to which this field belongs.    
					);
					register_setting(
						'captcha-woo-regs',
						'woo_regs_add_captcha_authentication_key_fields'
					);

					add_settings_field(    
						'add_captcha_size_radio', // ID used to identify the field throughout the theme.
						esc_html__('Recaptcha Size', 'recaptcha_verification'), // The label to the left of the option interface element.
						'captcha_reg_size_radio_callback',   // The name of the function responsible for rendering.
						'recaptcha-woo-regs', // The page on which this option will be displayed.
						'tab_2_section'// The name of the section to which this field belongs.    
					);
					register_setting(
						'captcha-woo-regs',
						'add_captcha_size_radio'
					);

					if (! function_exists('captcha_enable_check') ) {
						function captcha_enable_check() {
							?>
		<input type="checkbox" name="add_captcha_enable_check" value="1"<?php checked(get_option('add_captcha_enable_check'), '1'); ?>><label><?php echo esc_html__(' Eables captcha for Woo Commerce registration page', 'recaptcha_verification'); ?></label><br>
							<?php
						}
					}
					if (! function_exists('captcha_field_title_callback') ) {
						function captcha_field_title_callback() {
							?>
		<input type="text" name="woo_regs_enable_captcha" value="<?php echo esc_attr(get_option('woo_regs_enable_captcha')); ?>"><label><?php echo esc_html__(' Field text for Woo Comerce registration captcha', 'recaptcha_verification'); ?></label><br>
							<?php
						}
					}
					if (! function_exists('captcha_reg_authentication_callback') ) {
						function captcha_reg_authentication_callback() {
							?>
		<input type="radio" name="woo_regs_add_captcha_authentication_key_fields" value="1"<?php checked(get_option('woo_regs_add_captcha_authentication_key_fields'), '1'); ?>  checked >
		<label for="Light"><?php echo esc_html__('Light', 'recaptcha_verification'); ?></label><br>
		<label><em><?php echo esc_html__(' Eables light theme for captcha', 'recaptcha_verification'); ?></em></label><br><br>
		<input type="radio" name="woo_regs_add_captcha_authentication_key_fields" value="0"<?php checked(get_option('woo_regs_add_captcha_authentication_key_fields'), '0'); ?>  >
		<label for="Dark"><?php echo esc_html__('Dark', 'recaptcha_verification'); ?></label><br>
		<label><em><?php echo esc_html__(' Eables dark theme for captcha', 'recaptcha_verification'); ?></em></label><br><br>
							<?php
						}
					}
					if (! function_exists('captcha_reg_size_radio_callback') ) {
						function captcha_reg_size_radio_callback() {
							?>
		<input type="radio" name="add_captcha_size_radio" value="1"<?php checked(get_option('add_captcha_size_radio'), '1'); ?>  checked  >
		<label for="Light"><?php echo esc_html__('Normal', 'recaptcha_verification'); ?></label><br>
		<label><em><?php echo esc_html__(' Eables normal size for captcha', 'recaptcha_verification'); ?></em></label><br><br>
		<input type="radio" name="add_captcha_size_radio" value="0"<?php checked(get_option('add_captcha_size_radio'), '0'); ?>  >
		<label for="Dark"><?php echo esc_html__('Compact', 'recaptcha_verification'); ?></label><br>
		<label><em><?php echo esc_html__(' Eables compact size for captcha', 'recaptcha_verification'); ?></em></label><br><br>
							<?php
						}
					}

   
					if (! function_exists('tab_2_section_callback') ) {
						function tab_2_section_callback() {
		 

						}
					}
