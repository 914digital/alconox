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
			'tab_6_section',         // ID used to identify this section and with which to register options.
			esc_html__('WP Registration Page reCaptcha Settings', 'recaptcha_verification'),                  // Title to be displayed on the administration page.
			'tab_6_section_callback', // Callback used to render the description of the section.
			'wp_recaptcha_registration'                           // Page on which to add this section of options.
		);
		add_settings_field(    
			'wp_regs_captcha_check', // ID used to identify the field throughout the theme.
			esc_html__('reCaptcha on WP Register', 'recaptcha_verification'), // The label to the left of the option interface element.
			'wp_regs_captcha_check_setting',   // The name of the function responsible for rendering.
			'wp_recaptcha_registration', // The page on which this option will be displayed.
			'tab_6_section'// The name of the section to which this field belongs.    
		);
		register_setting(
			'wp_captcha_registration',
			'wp_regs_captcha_check'
		);


		add_settings_field(    
			'wp_regs_captcha_title', // ID used to identify the field throughout the theme.
			esc_html__('Recaptcha Field Title', 'recaptcha_verification'), // The label to the left of the option interface element.
			'wp_regs_captcha_title_set',   // The name of the function responsible for rendering.
			'wp_recaptcha_registration', // The page on which this option will be displayed.
			'tab_6_section'// The name of the section to which this field belongs.    
		);
		register_setting(
			'wp_captcha_registration',
			'wp_regs_captcha_title'
		);


		add_settings_field(    
			'wp_regs_captcha_theme_fields', // ID used to identify the field throughout the theme.
			esc_html__('Recaptcha Themes', 'recaptcha_verification'), // The label to the left of the option interface element.
			'wp_regs_captcha_themes_settings',   // The name of the function responsible for rendering.
			'wp_recaptcha_registration', // The page on which this option will be displayed.
			'tab_6_section'// The name of the section to which this field belongs.    
		);
		register_setting(
			'wp_captcha_registration',
			'wp_regs_captcha_theme_fields'
		);

		add_settings_field(    
			'wp_regs_add_captcha_size_radio', // ID used to identify the field throughout the theme.
			esc_html__('Recaptcha size', 'recaptcha_verification'), // The label to the left of the option interface element.
			'wp_regs_captcha_size_setting',   // The name of the function responsible for rendering.
			'wp_recaptcha_registration', // The page on which this option will be displayed.
			'tab_6_section'// The name of the section to which this field belongs.    
		);
		register_setting(
			'wp_captcha_registration',
			'wp_regs_add_captcha_size_radio'
		);



		if (! function_exists('wp_regs_captcha_check_setting') ) {
			function wp_regs_captcha_check_setting() {
				?>
		<input type="checkbox" name="wp_regs_captcha_check" value="1"<?php checked(get_option('wp_regs_captcha_check'), '1'); ?>><label><?php echo esc_html__(' Eables captcha for WordPress registraton page', 'recaptcha_verification'); ?></label><br><br><br>
				<?php
			}
		}
		if (! function_exists('wp_regs_captcha_title_set') ) {   
			function wp_regs_captcha_title_set() {
				?>
		<input type="text" name="wp_regs_captcha_title" value="<?php echo esc_attr(get_option('wp_regs_captcha_title')); ?>"><label><?php echo esc_html__(' Field text for WordPress registration captcha', 'recaptcha_verification'); ?></label><br><br><br>
				<?php
			}
		}
		if (! function_exists('wp_regs_captcha_themes_settings') ) {   
			function wp_regs_captcha_themes_settings() {
				?>
		<input type="radio" name="wp_regs_captcha_theme_fields"value="1"<?php checked(get_option('wp_regs_captcha_theme_fields'), '1'); ?>  checked >
		<label for="Light"><?php echo esc_html__('Light', 'recaptcha_verification'); ?></label><br>
		<label><em><?php echo esc_html__(' Eables light theme for captcha', 'recaptcha_verification'); ?></em></label><br><br>
		<input type="radio" name="wp_regs_captcha_theme_fields" value="0"<?php checked(get_option('wp_regs_captcha_theme_fields'), '0'); ?>  >
		<label for="Dark"><?php echo esc_html__('Dark', 'recaptcha_verification'); ?></label><br>
		<label><em><?php echo esc_html__(' Eables dark theme for captcha', 'recaptcha_verification'); ?></em></label><br><br>
				<?php
			}
		}
		if (! function_exists('wp_regs_captcha_size_setting') ) {  
			function wp_regs_captcha_size_setting() {
				?>
		<input type="radio" name="wp_regs_add_captcha_size_radio" value="1"<?php checked(get_option('wp_regs_add_captcha_size_radio'), '1'); ?>  checked  >
		<label for="Light"><?php echo esc_html__('Normal', 'recaptcha_verification'); ?></label><br>
		<label><em><?php echo esc_html__(' Eables normal size for captcha', 'recaptcha_verification'); ?></em></label><br><br>
		<input type="radio" name="wp_regs_add_captcha_size_radio"value="0"<?php checked(get_option('wp_regs_add_captcha_size_radio'), '0'); ?>  >
		<label for="Dark"><?php echo esc_html__('Compact', 'recaptcha_verification'); ?></label><br>
		<label><em><?php echo esc_html__(' Eables compact size for captcha', 'recaptcha_verification'); ?></em></label><br><br>
				<?php
			}
		}

   

		function tab_7_section_callback() {
		 

		}
