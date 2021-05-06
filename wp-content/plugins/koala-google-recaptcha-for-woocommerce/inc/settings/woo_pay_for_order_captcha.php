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
						'tab_10_section',         // ID used to identify this section and with which to register options.
						esc_html__('Woo payment method Captcha', 'recaptcha_verification'),                  // Title to be displayed on the administration page.
						'tab_10_section_callback', // Callback used to render the description of the section.
						'recaptcha-p-order-settings'                           // Page on which to add this section of options.
					);

					add_settings_field(    
						'pay_order_add_captcha_enable_check', // ID used to identify the field throughout the theme.
						esc_html__('Woo pay_order', 'recaptcha_verification'), // The label to the left of the option interface element.
						'woo_pay_order_enable_captcha',   // The name of the function responsible for rendering.
						'recaptcha-p-order-settings', // The page on which this option will be displayed.
						'tab_10_section'// The name of the section to which this field belongs.    
					);
						register_setting(
							'captcha-p-order-settings',
							'pay_order_add_captcha_enable_check'
						);

						add_settings_field(    
							'add_p_order_captcha_field_title', // ID used to identify the field throughout the theme.
							esc_html__('Recaptcha Field Title', 'recaptcha_verification'), // The label to the left of the option interface element.
							'p_order_captcha_field_title_callback',   // The name of the function responsible for rendering.
							'recaptcha-p-order-settings', // The page on which this option will be displayed.
							'tab_10_section'// The name of the section to which this field belongs.    
						);
						register_setting(
							'captcha-p-order-settings',
							'add_p_order_captcha_field_title'
						);


						add_settings_field(    
							'p_order_authentication_key_radio', // ID used to identify the field throughout the theme.
							esc_html__('Recaptcha Themes', 'recaptcha_verification'), // The label to the left of the option interface element.
							'p_order_captcha_reg_authentication_callback',   // The name of the function responsible for rendering.
							'recaptcha-p-order-settings', // The page on which this option will be displayed.
							'tab_10_section'// The name of the section to which this field belongs.    
						);
						register_setting(
							'captcha-p-order-settings',
							'p_order_authentication_key_radio'
						);

						add_settings_field(    
							'p_order_add_captcha_size_radio', // ID used to identify the field throughout the theme.
							esc_html__('Recaptcha Size', 'recaptcha_verification'), // The label to the left of the option interface element.
							'p_order_captcha_reg_size_radio_callback',   // The name of the function responsible for rendering.
							'recaptcha-p-order-settings', // The page on which this option will be displayed.
							'tab_10_section'// The name of the section to which this field belongs.    
						);
						register_setting(
							'captcha-p-order-settings',
							'p_order_add_captcha_size_radio'
						);    


						if (! function_exists('woo_pay_order_enable_captcha') ) {
							function woo_pay_order_enable_captcha() {
								?>
		<input type="checkbox" name="pay_order_add_captcha_enable_check" value="1"<?php checked(get_option('pay_order_add_captcha_enable_check'), '1'); ?>><label><?php echo esc_html__(' Eables captcha for pay for order', 'recaptcha_verification'); ?></label><br>
								<?php
							}
						}
						if (! function_exists('p_order_captcha_field_title_callback') ) {
							function p_order_captcha_field_title_callback() {
								?>
		<input type="text" name="add_p_order_captcha_field_title" value="<?php echo esc_attr(get_option('add_p_order_captcha_field_title')); ?>" ><label><?php echo esc_html__(' Field text for pay for order captcha', 'recaptcha_verification'); ?></label><br>
								<?php
							}
						}
						if (! function_exists('p_order_captcha_reg_authentication_callback') ) {
							function p_order_captcha_reg_authentication_callback() {
								?>
		<input type="radio" name="p_order_authentication_key_radio" value="1"<?php checked(get_option('p_order_authentication_key_radio'), '1'); ?> checked>
		<label for="Light"><?php echo esc_html__('Light', 'recaptcha_verification'); ?></label><br>
		<label><em><?php echo esc_html__(' Eables light theme for captcha', 'recaptcha_verification'); ?></em></label><br><br>
		<input type="radio" name="p_order_authentication_key_radio" value="0"<?php checked(get_option('p_order_authentication_key_radio'), '0'); ?>>
		<label for="Dark"><?php echo esc_html__('Dark', 'recaptcha_verification'); ?></label><br>
		<label><em><?php echo esc_html__(' Eables dark theme for captcha', 'recaptcha_verification'); ?></em></label><br><br>
								<?php
							}
						}
						if (! function_exists('p_order_captcha_reg_size_radio_callback') ) {
							function p_order_captcha_reg_size_radio_callback() {
								?>
		<input type="radio" name="p_order_add_captcha_size_radio" value="1"<?php checked(get_option('p_order_add_captcha_size_radio'), '1'); ?> checked>
		<label for="Light"><?php echo esc_html__('Normal', 'recaptcha_verification'); ?></label><br>
		<label><em><?php echo esc_html__(' Eables normal size for captcha', 'recaptcha_verification'); ?></em></label><br><br>
		<input type="radio" name="p_order_add_captcha_size_radio" value="0"<?php checked(get_option('p_order_add_captcha_size_radio'), '0'); ?> >
		<label for="Dark"><?php echo esc_html__('Compact', 'recaptcha_verification'); ?></label><br>
		<label><em><?php echo esc_html__(' Eables compact size for captcha', 'recaptcha_verification'); ?></em></label><br><br>
								<?php
							}
						}

						if (! function_exists('tab_10_section_callback') ) {
							function tab_10_section_callback() {
		 

 
							}
						}
